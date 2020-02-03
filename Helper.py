"""
#    FILE:          Helper.py
#    PROGRAMMER:    William Bicknell
#    FIRST VERSION: February 3, 2020
#    DESCRIPTION:   This file runs a helper that can clean files and search the log file.
#                   The main purpose for this is to help with development.
"""

import Logger
import Metrics
import os


def DisplayMenu(message, options):
    """
    Displays a message and menu options.
    
    Args:
    message - The message to display
    options - A list of menu options to display
    """
    count = 1
    
    print("\n%s\n" % message)
    for option in options:
        print("%d) %s" % (count, option))
        count += 1


def DisplayInputError():
    """
    Displays a user input error message.
    """
    print("Invalid input!")
    print("Please enter the corresponding number for one of the options above!\n")
    

def DeleteFile(fileName):
    """
    Deletes the specified file if it exists.
    
    Args:
    fileName - The file to delete
    """
    if (os.path.exists(fileName)):
        os.remove(fileName)
        
        
def Confirm(message):
    """
    Gets a yes or no from the user corresponding to a message.

    Args:
    message - The message to display
    """
    while True:
        print("\n%s\n" % message)
        
        userIn = input("Continue (y/n): ")
        
        if (userIn == "Y" or userIn == "y"):
            return True
        elif (userIn == "N" or userIn == "n"):
            return False
        else:
            print("Invalid input!")


def SearchLog(module="", msgType="", last=0):
    """
    Searches the log file with the given search options.
    Only one option is used at once.
    
    Args:
    module - The module to search for
    msgType - The message type to search for
    last - The number of most recent messages to display
    """
    if (not os.path.exists(Logger.LOG_FILE)):
        print("\nThere is no log file!")
    else:
        with open(Logger.LOG_FILE) as file:
            lines = file.readlines()
        lines = [line.strip() for line in lines]
        lines.reverse()
        printLines = []
        
        print()
        
        if (not module == ""):
            for line in lines:
                info = line.split(" - ")
                
                if (info[1] == module):
                    printLines.append(line)
                    if (len(printLines) >= last):
                        break
                    
            print("Displaying last %d messages for module %s" % (last, module))
        elif (not msgType == ""):
            for line in lines:
                info = line.split(" - ")
                
                if (info[2] == msgType):
                    printLines.append(line)
                    if (len(printLines) >= last):
                        break
            
            print("Displaying last %d messages for type %s" % (last, module))
        elif (not last == 0):
            if (last > len(lines)):
                last = len(lines)
                print("Displaying all %d messages" % last)
            else:
                print("Displaying last %d messages" % last)
            
            for i in range(0, last):
                printLines.append(lines[i])
            
        printLines.reverse()
        
        for line in printLines:
            print(line)
                    
        if (len(printLines) == 0):
            print("No log messages found")
        
    print("\nPress ENTER to continue...")
    input()


def CleanMenu():
    """
    Displays the clean files menu.
    """
    cleanMenuOptions = [Metrics.METRICS_FILE_NAME, Metrics.PROG_DATA_FILE_NAME, Logger.LOG_FILE, "All", "Back"]
    while True:
        DisplayMenu("Which file(s) should be deleted?", cleanMenuOptions)
        
        userIn = input("\nEnter a selection: ")
        
        if (userIn == "1"):
            if (Confirm("This action will permanently delete %s" % cleanMenuOptions[0])):
                DeleteFile(cleanMenuOptions[0])
            break
        elif (userIn == "2"):
            if (Confirm("This action will permanently delete %s" % cleanMenuOptions[1])):
                DeleteFile(cleanMenuOptions[1])
            break
        elif (userIn == "3"):
            if (Confirm("This action will permanently delete %s" % cleanMenuOptions[2])):
                DeleteFile(cleanMenuOptions[2])
            break
        elif (userIn == "4"):
            if (Confirm("This action will permanently delete all listed files")):
                DeleteFile(cleanMenuOptions[0])
                DeleteFile(cleanMenuOptions[1])
                DeleteFile(cleanMenuOptions[2])
            break
        elif (userIn == "5"):
            break
        else:
            DisplayInputError()
            
            
def LogMenu():
    """
    Displays the search log menu.
    """
    logMenuOptions = ["Search by module", "Search by type", "View last", "Back"]
    while True:
        DisplayMenu("What would you like to do?", logMenuOptions)
        
        userIn = input("\nEnter a selection: ")
        
        if (userIn == "1"):
            inModule = input("\nEnter a module name: ")
            userIn = input("\nHow many: ")
            
            if (userIn.isdigit() and int(userIn) >= 0):
                SearchLog(module=inModule, last=int(userIn))
            else:
                "Invalid input! Expected a valid positive integer!"
        elif (userIn == "2"):
            inType = input("\nEnter a type: ")
            userIn = input("\nHow many: ")
            
            if (userIn.isdigit() and int(userIn) >= 0):
                SearchLog(msgType=inType, last=int(userIn))
            else:
                "Invalid input! Expected a valid positive integer!"
        elif (userIn == "3"):
            userIn = input("\nHow many: ")
            
            if (userIn.isdigit() and int(userIn) >= 0):
                SearchLog(last=int(userIn))
            else:
                "Invalid input! Expected a valid positive integer!"
        elif (userIn == "4"):
            break
        else:
            DisplayInputError()


mainMenuOptions = ["Clean files", "Search log file", "Exit"]
while True:
    DisplayMenu("How can I help you today?", mainMenuOptions)
    
    userIn = input("\nEnter a selection: ")
    
    if (userIn == "1"):
        CleanMenu()
    elif (userIn == "2"):
        LogMenu()
    elif (userIn == "3"):
        break
    else:
        DisplayInputError()
