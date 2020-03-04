/* FILE : RunList.cs
*PROJECT : Coachable Website
*PROGRAMMER : Stardust Crusaders
*FIRST VERSION : 2020-03-01
*DESCRIPTION : The file contains methods for accessing DB for runs
*/

using System;
using System.Configuration;
using System.Data;
using MySql.Data.MySqlClient;

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
        public MySqlConnection conn { get; set; }

        /// <summary>
        /// Constructor
        /// </summary>
        public RunList()
        {
            ConnectionString = ConfigurationManager.ConnectionStrings["CoachableString"].ConnectionString;
            conn = new MySqlConnection(ConnectionString);
        }      

        /// <summary>
        /// Method that adds a new run
        /// </summary>
        /// <param name="newRun"></param>
        /// <returns>Successful or not</returns>
        public string Insert(Run newRun)
        {
            using (conn)
            {            
                //ADD TRY CATCHES, TEST TO MAKE SURE IT WORKS, MAYBE CHANGE SOME SCALARS TO READERS (LIKE LIST OF TEAMS INSTEAD OF JUST ONE TEAM)

                conn.Open();

                // Create and execute query for finding the devices id using its name
                const string findDeviceQuery = @"SELECT id FROM Devices WHERE Device_Name = @name";
                var deviceCommand = new MySqlCommand(findDeviceQuery, conn);
                deviceCommand.Parameters.AddWithValue("@name", newRun.DeviceName);
                var deviceID = deviceCommand.ExecuteScalar();

                // Create and execute query for finding the users id based on device ID
                const string findUserQuery = @"SELECT id FROM Users WHERE Device_ID = @deviceID";
                var userCommand = new MySqlCommand(findUserQuery, conn);
                userCommand.Parameters.AddWithValue("@deviceID", deviceID);
                var userID = userCommand.ExecuteScalar();
                newRun.UserID = int.Parse(userID.ToString());

                // Create and execute query for finding the users team based on users id
                const string findTeamQuery = @"SELECT team_id FROM UserTeams WHERE user_id = @userID";
                var teamCommand = new MySqlCommand(findTeamQuery, conn);
                teamCommand.Parameters.AddWithValue("@userID", newRun.UserID);
                var teamID = userCommand.ExecuteScalar();

                // Create and execute query for finding an event that matches the date provided by device
                const string findEventQuery = @"SELECT id FROM Events WHERE team_id = @teamID AND Event_Date = @date";
                var eventCommand = new MySqlCommand(findEventQuery, conn);
                eventCommand.Parameters.AddWithValue("@teamID", teamID);
                eventCommand.Parameters.AddWithValue("@userID", newRun.Date);
                var eventID = userCommand.ExecuteScalar();
                newRun.EventID = int.Parse(eventID.ToString());

                //Create and execute query for inserting the run into the DB
                const string insertQuery = @"INSERT INTO Runs(user_id, event_id, duration, date, start_time, end_time, start_altitude, end_altitude, avg_speed, distance) VALUES
                                            (@userID, @eventid, @duration, @date, @startTime, @endTime, @startAltitude, @endAltitude, @AvgSpeed, @distance)";         
                var insertCommand = new MySqlCommand(insertQuery, conn);
                insertCommand.Parameters.AddWithValue("@userID", newRun.UserID);
                insertCommand.Parameters.AddWithValue("@eventid", newRun.EventID);
                insertCommand.Parameters.AddWithValue("@duration", newRun.Duration);
                insertCommand.Parameters.AddWithValue("@date", newRun.Date);
                insertCommand.Parameters.AddWithValue("@startTime", newRun.StartTime);
                insertCommand.Parameters.AddWithValue("@endTime", newRun.EndTime );
                insertCommand.Parameters.AddWithValue("@startAltitude", newRun.StartAltitude);
                insertCommand.Parameters.AddWithValue("@endAltitude", newRun.EndAltitude);
                insertCommand.Parameters.AddWithValue("@AvgSpeed", newRun.AverageSpeed);
                insertCommand.Parameters.AddWithValue("@distance", newRun.Distance);
                insertCommand.ExecuteNonQuery();

                return "SUCCESSFUL";
            }            
        }
    }
}