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
        /// User ID belonging to a run
        /// </summary>
        public int UserID { get; set; }

        /// <summary>
        /// User ID belonging to a run
        /// </summary>
        public string DeviceName { get; set; }

        public int EventID { get; set; }

        public string Duration { get; set; }

        public string Date { get; set; }

        public string StartTime { get; set; }

        public string EndTime { get; set; }

        public string StartAltitude { get; set; }

        public string EndAltitude { get; set; }

        public string AverageSpeed { get; set; }

        public string Distance { get; set; }
    }
}