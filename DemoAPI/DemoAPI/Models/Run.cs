/* FILE : Run.cs
*PROJECT : Coachable Website
*PROGRAMMER : Stardust Crusaders
*FIRST VERSION : 2020-03-01
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
        /// Devices name
        /// </summary>
        public string DeviceName { get; set; }

        /// <summary>
        /// Event ID belonging to run
        /// </summary>
        public int EventID { get; set; }

        /// <summary>
        /// Duration for run
        /// </summary>
        public string Duration { get; set; }

        /// <summary>
        /// Date of the run
        /// </summary>
        public string Date { get; set; }

        /// <summary>
        /// When the run started
        /// </summary>
        public string StartTime { get; set; }

        /// <summary>
        /// When the run ended
        /// </summary>
        public string EndTime { get; set; }

        /// <summary>
        /// The starting altitude
        /// </summary>
        public string StartAltitude { get; set; }

        /// <summary>
        /// The ending altitude
        /// </summary>
        public string EndAltitude { get; set; }

        /// <summary>
        /// The average speed
        /// </summary>
        public string AverageSpeed { get; set; }

        /// <summary>
        /// The distance between start and end
        /// </summary>
        public string Distance { get; set; }
    }
}