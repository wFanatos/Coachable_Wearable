/* FILE : RunController.cs
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
    /// Class that controls the run
    /// </summary>
    public class RunController : ApiController
    {
        /// <summary>
        /// This method gets the basic run information
        /// http://localhost:51825/Run/GetBasicRuns/userID
        /// </summary>
        /// <param name="userID"></param>
        /// <returns>run information or error</returns>
        [HttpGet]
        [Route("Run/GetBasicRuns/{userID}")]
        public object GetBasicRuns(int userID)
        {
            return new RunList().GetBasicRuns(userID);
        }

        /// <summary>
        /// This method gets all run details for one run
        /// http://localhost:51825/Run/GetRunDetails/runID
        /// </summary>
        /// <param name="runID"></param>
        /// <returns>run information or error</returns>
        [HttpGet]
        [Route("Run/GetRunDetails/{runID}")]
        public object GetRunDetails(int runID)
        {
            return new RunList().GetRunDetails(runID);
        }

        /// <summary>
        /// This method adds a new run
        /// http://localhost:51825/Run POST
        /// </summary>
        /// <param name="newRun"></param>
        /// <returns>Successful or not</returns>
        /// 
        public string Post([FromBody]Run newRun)
        {
            return new RunList().Insert(newRun);
        }      
    }
}
