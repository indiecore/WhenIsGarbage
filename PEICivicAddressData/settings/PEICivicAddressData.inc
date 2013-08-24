<?php
/**
  * PEICivicAddressData.inc - Settings file.
  *
  * User configurable settings for the PEICivicAddressData applications.
  *
  * This program is free software; you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation; either version 2 of the License, or (at
  * your option) any later version.

  * This program is distributed in the hope that it will be useful, but
  * WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
  * General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with this program; if not, write to the Free Software
  * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
  * USA
  *  
  * @version 0.1, November 7, 2005 
  * @link http://ruk.ca/wiki/http://ruk.ca/wiki/Downloading_PEI_Civic_Address_Data
  * @author Peter Rukavina <peter@rukavina.net> 
  * @copyright Reinvented Inc., 2005
  * @license http://www.fsf.org/licensing/licenses/gpl.txt GNU Public License
  */

//---------------------------------------
// MySQL Server Settings
//---------------------------------------

$mysql_host		= 'localhost';		
$mysql_user 		= 'root';				// Fill with your MySQL username.
$mysql_passwd 	= '';				// Fill with your MySQL password.
$mysql_database 	= 'hackathon-garbage';	// You can change this if you like -- database must exist, though.
$mysql_table    = 'civics';	// You can change this -- it will be created if required.

//---------------------------------------
// Debugging?  Set to 1 (Yes!) or 0 (No)
//---------------------------------------

$DEBUG = 1;

//---------------------------------------
// File Locations
//---------------------------------------

$TMPDIR = __DIR__ . '/cache';

//---------------------------------------
// Command Line Utility Locations
//---------------------------------------

$WGET = "/usr/bin/wget";
$CAT  = "/bin/cat";

//---------------------------------------
// End of user configurable options
//---------------------------------------

?>
