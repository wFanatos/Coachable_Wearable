/* FILE : CustomerController.cs
*PROJECT : Coachable Website
*PROGRAMMER : Blake Ribble (Stardust Crusaders)
*FIRST VERSION : 2020-01-15
*DESCRIPTION : The file contains the methods called by external sources (user)
*/

using DemoAPI.Models;
using System.Web.Http;

namespace DemoAPI.Controllers
{
    /// <summary>
    /// Class that controls the customer
    /// </summary>
    public class CustomerController : ApiController
    {
        /// <summary>
        /// This method checks to see if the customer is in the DB
        /// http://localhost:51825/Customer/CheckCustomer/username
        /// </summary>
        /// <param name="username"></param>
        /// <returns>-1 or users ID</returns>
        [HttpGet]
        [Route("Customer/CheckCustomer/{username}")]
        public int CheckCustomer(string username)
        {
            return new CustomerList().GetCustomer(username);
        }

        /// <summary>
        /// This method adds the user into the DB
        /// http://localhost:51825/Customer POST
        /// </summary>
        /// <param name="cust"></param>
        /// <returns>successful or error</returns>
        // POST: api/Run
        public string Post([FromBody]Customer cust)
        {
            return new CustomerList().Insert(cust);
        }    
    }
}
