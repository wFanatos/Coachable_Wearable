/* FILE : CustomerList.cs
*PROJECT : Coachable Website
*PROGRAMMER : Blake Ribble (Stardust Crusaders)
*FIRST VERSION : 2020-01-15
*DESCRIPTION : The file contains methods for accessing DB
*/

using System;
using System.Configuration;
using System.Data.SqlClient;

namespace DemoAPI.Models
{
    /// <summary>
    /// Class for accessing the DB for customer information
    /// </summary>
    public class CustomerList
    {
        private static string myConnection;
        SqlConnection conn;

        /// <summary>
        /// Constructor for setting up connection
        /// </summary>
        public CustomerList()
        {
            myConnection = ConfigurationManager.ConnectionStrings["UserString"].ConnectionString;
            conn = new SqlConnection(myConnection);
        }

        /// <summary>
        /// Method checks to see if the customer is in the DB
        /// </summary>
        /// <param name="userName"></param>
        /// <returns> -1 or user's ID</returns>
        public int GetCustomer(string userName)
        {
            using (conn)
            {
                // Query to retrieve data
                const string IDQuery = @" 
                    SELECT ID FROM LoginData where UserName = @userName";

                // Create the command
                var IDCommand = new SqlCommand(IDQuery, conn);

                //Fill in the parameter
                IDCommand.Parameters.AddWithValue("userName", userName);

                // Open the connection
                conn.Open();

                try
                {
                    // Execute the scalar
                    int userID = Convert.ToInt32(IDCommand.ExecuteScalar().ToString());

                    return userID;
                }

                // USER IS NOT IN THE SYSTEM, WEBSITE WILL EITHER CREATE NEW ACCOUNT OR WON'T LET THEM LOG IN
                catch (Exception)
                {
                    return -1;
                }
            }
        }
      
        /// <summary>
        /// Method to insert the customer in the DB
        /// </summary>
        /// <param name="customer"></param>
        /// <returns>Successful or not</returns>
        public string Insert(Customer customer)
        {
            try
            {
                // Query to retrieve data
                const string runQuery = @" 
                   INSERT INTO LoginData VALUES (@name, @password)";

                // Create the command
                var runCommand = new SqlCommand(runQuery, conn);

                //Fill in the parameter
                runCommand.Parameters.AddWithValue("name", customer.UserName);

                //Fill in the parameter
                runCommand.Parameters.AddWithValue("password", customer.Password);

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