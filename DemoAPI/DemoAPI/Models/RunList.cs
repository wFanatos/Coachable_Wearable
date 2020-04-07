/* FILE : RunList.cs
*PROJECT : Coachable Website
*PROGRAMMER : Stardust Crusaders
*FIRST VERSION : 2020-03-01
*DESCRIPTION : The file contains methods for accessing DB for runs
*/

using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data;
using MySql.Data.MySqlClient;
using Newtonsoft.Json;

namespace DemoAPI.Models
{
    /// <summary>
    /// Class for accessing the DB for run information
    /// </summary>
    public class RunList
    {
        /// <summary>
        /// String for connecting to the DB
        /// </summary>
        public string ConnectionString { get; set; }

        /// <summary>
        /// Connection instance to DB
        /// </summary>
        public MySqlConnection Conn { get; set; }

        /// <summary>
        /// Constructor
        /// </summary>
        public RunList()
        {
            ConnectionString = ConfigurationManager.ConnectionStrings["CoachableString"].ConnectionString;
            Conn = new MySqlConnection(ConnectionString);
        }      

        /// <summary>
        /// Method that adds runs to the DB
        /// </summary>
        /// <param name="runs"></param>
        /// <returns>Successful or not</returns>
        public string Insert(List<Run> runs)
        {
            DataTable teamTable = new DataTable();
            DataTable eventTable = new DataTable();

            using (Conn)
            {
                // Although this code works, we should add try catches, proper validation, and encryption of data
                try
                {
                    Conn.Open();

                    // Loop through each run sent
                    foreach (Run newRun in runs)
                    {
                        // Create and execute query for finding the users id using the device name
                        const string findUserQuery = @"SELECT user_id FROM devices WHERE device_name = @name";
                        var userCommand = new MySqlCommand(findUserQuery, Conn);
                        userCommand.Parameters.AddWithValue("@name", newRun.DeviceName);
                        var userID = userCommand.ExecuteScalar();
                        newRun.UserID = int.Parse(userID.ToString());

                        // Create and execute query for finding the users team based on their id
                        const string findTeamQuery = @"SELECT team_id FROM user_teams WHERE user_id = @userID";
                        var teamCommand = new MySqlCommand(findTeamQuery, Conn);
                        teamCommand.Parameters.AddWithValue("@userID", newRun.UserID);

                        using (MySqlDataReader teamReader = teamCommand.ExecuteReader())
                        {
                            teamTable.Load(teamReader);
                        }

                        int exitFlag = 0;

                        foreach (DataRow teamRow in teamTable.Rows)
                        {
                            if(exitFlag == 1)
                            {
                                break;
                            }

                            int teamID = int.Parse(teamRow["team_id"].ToString());

                            // Create and execute query for finding an event that matches the date provided by device
                            const string findEventQuery = @"SELECT id, start_time, end_time FROM events WHERE team_id = @teamID AND event_date = @date";
                            var eventCommand = new MySqlCommand(findEventQuery, Conn);
                            eventCommand.Parameters.AddWithValue("@teamID", teamID);
                            eventCommand.Parameters.AddWithValue("@date", newRun.Date);

                            using (MySqlDataReader eventReader = eventCommand.ExecuteReader())
                            {
                                eventTable.Load(eventReader);
                            }

                            foreach (DataRow eventRow in eventTable.Rows)
                            {
                                string id = eventRow["id"].ToString();
                                string event_start = eventRow["start_time"].ToString();
                                string event_end = eventRow["end_time"].ToString();

                                string run_start = newRun.StartTime.Substring(0,newRun.StartTime.IndexOf(" "));
                                string run_end = newRun.EndTime.Substring(0,newRun.EndTime.IndexOf(" "));


                                if ((DateTime.Parse(run_start) >= DateTime.Parse(event_start)) && (DateTime.Parse(run_end) < DateTime.Parse(event_end)) && ((DateTime.Parse(run_start) < DateTime.Parse(event_end)) && (DateTime.Parse(run_end) > DateTime.Parse(event_start))))
                                {
                                    exitFlag = 1;
                                    newRun.EventID = int.Parse(id);
                                    break;
                                }
                            }

                            if (exitFlag != 1)
                            {
                                // Add new training event
                                const string insertQuery = @"INSERT INTO events(team_id, event_name, event_date, start_time, end_time) VALUES
                                                             (@teamID, @eventName, @eventDate, @startTime, @endTime)";

                                string name = "Training " + newRun.Date;

                                var insertCmd = new MySqlCommand(insertQuery, Conn);
                                insertCmd.Parameters.AddWithValue("@teamID", teamID);
                                insertCmd.Parameters.AddWithValue("@eventName", name);
                                insertCmd.Parameters.AddWithValue("@eventDate", newRun.Date);
                                insertCmd.Parameters.AddWithValue("@startTime", "00:00:00");
                                insertCmd.Parameters.AddWithValue("@endTime", "23:59:59");
                                insertCmd.ExecuteNonQuery();
                                newRun.EventID = Convert.ToInt32(insertCmd.LastInsertedId);

                                exitFlag = 1;
                            }
                        }

                        if (exitFlag == 1)
                        {
                            //Create and execute query for inserting the run into the DB
                            const string insertQuery = @"INSERT INTO runs(user_id, event_id, duration, date, start_time, end_time, start_altitude, end_altitude, avg_speed, distance, other_data) VALUES
                                                 (@userID, @eventid, @duration, @date, @startTime, @endTime, @startAltitude, @endAltitude, @AvgSpeed, @distance, @data)";
                            var insertCommand = new MySqlCommand(insertQuery, Conn);
                            insertCommand.Parameters.AddWithValue("@userID", newRun.UserID);
                            insertCommand.Parameters.AddWithValue("@eventid", newRun.EventID);
                            insertCommand.Parameters.AddWithValue("@duration", newRun.Duration);
                            insertCommand.Parameters.AddWithValue("@date", newRun.Date);
                            insertCommand.Parameters.AddWithValue("@startTime", newRun.StartTime);
                            insertCommand.Parameters.AddWithValue("@endTime", newRun.EndTime);
                            insertCommand.Parameters.AddWithValue("@startAltitude", newRun.StartAltitude);
                            insertCommand.Parameters.AddWithValue("@endAltitude", newRun.EndAltitude);
                            insertCommand.Parameters.AddWithValue("@AvgSpeed", newRun.AvgSpeed);
                            insertCommand.Parameters.AddWithValue("@distance", newRun.Distance);

                            //Serialize the data field and fill in
                            string dataString = JsonConvert.SerializeObject(newRun.Data);
                            insertCommand.Parameters.AddWithValue("@data", dataString);
                            insertCommand.ExecuteNonQuery();
                        }
                        else
                        {
                            return "Cannot find event that matches specific timeframes";

                        }                                 
                    }

                    return "Runs have been added!";
                }
                catch(Exception e)
                {
                    return "Error: " + e.Message;
                }          
            }            
        }
    }
}