/* FILE : RunController.cs
*PROJECT : Coachable Website
*PROGRAMMER : Blake Ribble (Stardust Crusaders)
*FIRST VERSION : 2020-01-15
*DESCRIPTION : The file contains the methods called by external sources (user)
*/

using DemoAPI.Models;
using System.Collections.Generic;
using System.Web.Http;

namespace DemoAPI.Controllers
{
    /// <summary>
    /// Class that controls the run
    /// </summary>
    public class RunController : ApiController
    {
        /// <summary>
        /// This method adds a new run
        /// http://localhost:51825/Run POST
        /// </summary>
        /// <param name="newRun"></param>
        /// <returns>Successful or not</returns>
        /// 
        public string Post([FromBody]List<Run> newRuns)
        {
            return new RunList().Insert(newRuns);
        }      
    }
}
