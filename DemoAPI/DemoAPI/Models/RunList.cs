/* FILE : RunList.cs
*PROJECT : Coachable Website
*PROGRAMMER : Blake Ribble (Stardust Crusaders)
*FIRST VERSION : 2020-01-15
*DESCRIPTION : The file contains methods for accessing DB for runs
*/

using System;
using System.Configuration;
using System.Data;
using System.Data.SqlClient;

namespace DemoAPI.Models
{
    /// <summary>
    /// Class for accessing the DB for run information
    /// </summary>
    public class RunList
    {
        private static string myConnection;
        SqlConnection conn;

        /// <summary>
        /// Constructor
        /// </summary>
        public RunList()
        {
            myConnection = ConfigurationManager.ConnectionStrings["UserString"].ConnectionString;
            conn = new SqlConnection(myConnection);
        }      

        /// <summary>
        /// Method that adds a new run
        /// </summary>
        /// <param name="newRun"></param>
        /// <returns>Successful or not</returns>
        public string Insert(Run newRun)
        {
            //LIST OF QUERIES NEEDED. ALSO CHANGE EVERYTHING TO MYSQL NOT SQLSERVER

            //WITH DEVICE NAME, FIND THE USER ID ATTACHED
            //WITH THAT USER ID, FIND LIST OF EVENTS
            //MATCH THE DATE OF THE RUN TO THE EVENT
            //SET THE EVENT_ID WITH PREV RESULT
            //INSERT INTO DB



            try
            {
                // Query to retrieve data
                const string runQuery = @" 
                   INSERT INTO Run([User_ID], Run_Date, Speed, Distance)  Values
                   (@userID, @date, @speed, @distance)";

                // Create the command
                var runCommand = new SqlCommand(runQuery, conn);

                //Fill in the parameter
                runCommand.Parameters.AddWithValue("userID", newRun.UserID);

                //Fill in the parameter
                runCommand.Parameters.AddWithValue("date", newRun.Run_Date);

                //Fill in the parameter
                runCommand.Parameters.AddWithValue("speed", newRun.Speed);

                //Fill in the parameter
                runCommand.Parameters.AddWithValue("distance", newRun.Distance);

                // Open the connection
                conn.Open();

                runCommand.ExecuteNonQuery();

                return "SUCCESSFUL";
            }

            catch(Exception)
            {
                return "Error";
            }         
        }
    }
}