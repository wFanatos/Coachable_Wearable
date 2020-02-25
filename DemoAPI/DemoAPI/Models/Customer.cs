/* FILE : Customer.cs
*PROJECT : Coachable Website
*PROGRAMMER : Blake Ribble (Stardust Crusaders)
*FIRST VERSION : 2020-01-15
*DESCRIPTION : The file contains class definition for customers
*/

namespace DemoAPI.Models
{
    /// <summary>
    /// Customer class which contains information
    /// </summary>
    public class Customer
    {
        /// <summary>
        /// Customer's ID
        /// </summary>
        public int ID { get; set; }

        /// <summary>
        /// Customers Username
        /// </summary>
        public string UserName { get; set; }

        /// <summary>
        /// Customers Password
        /// </summary>
        public string Password { get; set; }

        /// <summary>
        /// Constructor for Customer
        /// </summary>
        public Customer()
        {
            ID = -1;
            UserName = "";
            Password = "";
        }
    }
}