/* FILE : Run.cs
*PROJECT : Coachable Website
*PROGRAMMER : Blake Ribble (Stardust Crusaders)
*FIRST VERSION : 2020-01-15
*DESCRIPTION : The file contains class definition for Runs
*/

namespace DemoAPI.Models
{
    /// <summary>
    /// Run class which contains run information
    /// </summary>
    public class Run
    {
        /// <summary>
        /// ID that identifies run
        /// </summary>
        public int RunID { get; set; }

        /// <summary>
        /// User ID belonging to a run
        /// </summary>
        public int UserID { get; set; }

        /// <summary>
        /// Date when the run took place
        /// </summary>
        public string Run_Date { get; set; }

        /// <summary>
        /// Speed metric
        /// </summary>
        public float Speed { get; set; }

        /// <summary>
        /// Distance the user skied
        /// </summary>
        public float Distance { get; set; }

        /// <summary>
        /// Constructor
        /// </summary>
        public Run()
        {
            RunID = -1;
            UserID = -1;
            Run_Date = "";
            Speed = -1;
            Distance = -1;
        }
    }
}