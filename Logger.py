"""
#    FILE:          Logger.py
#    PROGRAMMER:    William Bicknell
#    FIRST VERSION: February 3, 2020
#    DESCRIPTION:   This file contains functions to log messages.
"""

import datetime

LOG_FILE = "./wearable.log"
DEBUG = True


def LogStr(module, logType, string):
    """
    Logs a string to the log file
    
    Args:
    module - The module the message came from
    logType - The type of log message
    string - The string to log
    """
    date = datetime.datetime.now().strftime("%F %T")
    file = open(LOG_FILE, "a")
    file.write("%s - %s - %s - %s\n" % (date, module, logType, string))
    file.close()


def LogInfo(module, string):
    """
    Logs an info message
    
    Args:
    module - The module the message came from
    string - The string to log
    """
    LogStr(module, "INFO", string)
    
    
def LogDebug(module, string):
    """
    Logs a debug message
    
    Args:
    module - The module the message came from
    string - The string to log
    """
    if (DEBUG):
        LogStr(module, "DEBUG", string)
    
    
def LogError(module, string):
    """
    Logs an error message
    
    Args:
    module - The module the message came from
    string - The string to log
    """
    LogStr(module, "ERROR", string)
    
    
def LogWarn(module, string):
    """
    Logs a warning message
    
    Args:
    module - The module the message came from
    string - The string to log
    """
    LogStr(module, "WARN", string)
