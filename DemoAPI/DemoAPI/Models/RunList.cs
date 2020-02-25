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
        /// Method that retrieves basic run information
        /// </summary>
        /// <param name="userID"></param>
        /// <returns>All runs or null</returns>
        public object GetBasicRuns(int userID)
        {
            using (var myConn = new SqlConnection(myConnection))
            {
                // Query to retrieve data
                const string runQuery = @" 
                   SELECT Run_ID, Run_Date FROM Run WHERE [User_ID] = @id";

                // Create the command
                var runCommand = new SqlCommand(runQuery, myConn);

                //Fill in the parameter
                runCommand.Parameters.AddWithValue("id", userID);

                // Open the connection
                myConn.Open();

                try
                {
                    // Execute the reader which returns all rows into reader
                    var reader = runCommand.ExecuteReader();

                    // Create a new DataTable
                    DataTable data = new DataTable();

                    // Load the reader results into the datatable
                    data.Load(reader);

                    return data;
                }

                catch(Exception)
                {
                    return null;
                }
               
            }
        }

        /// <summary>
        /// Method that retrieves all information for one specific run
        /// </summary>
        /// <param name="runID"></param>
        /// <returns>All details or null</returns>
        public object GetRunDetails(int runID)
        {
            using (var myConn = new SqlConnection(myConnection))
            {
                // Query to retrieve data
                const string runQuery = @" 
                   SELECT * from Run WHERE Run_ID = @id";

                // Create the command
                var runCommand = new SqlCommand(runQuery, myConn);

                //Fill in the parameter
                runCommand.Parameters.AddWithValue("id", runID);

                // Open the connection
                myConn.Open();

                try
                {
                    // Execute the reader which returns all rows into reader
                    var reader = runCommand.ExecuteReader();

                    // Create a new DataTable
                    DataTable data = new DataTable();

                    // Load the reader results into the datatable
                    data.Load(reader);

                    return data;
                }

                catch (Exception)
                {
                    return null;
                }

            }
        }

        /// <summary>
        /// Method that adds a new run
        /// </summary>
        /// <param name="newRun"></param>
        /// <returns>Successful or not</returns>
        public string Insert(Run newRun)
        {
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