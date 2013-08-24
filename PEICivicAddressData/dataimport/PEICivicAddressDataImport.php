<?php
/**
  * PEICivicAddressDataImport.php - A script to import Prince Edward Island 
  * civic address data into a MySQL database.
  *
  * This program is free software; you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation; either version 2 of the License, or (at
  * your option) any later version.
  *
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
  * @version 0.2, October 24, 2011
  * @link http://ruk.ca/wiki/Downloading_PEI_Civic_Address_Data
  * @author Peter Rukavina <peter@rukavina.net> 
  * @copyright Reinvented Inc., 2011
  * @license http://www.fsf.org/licensing/licenses/gpl.txt GNU Public License
  */

// Include User configurable options

include "../settings/PEICivicAddressData.inc";

// Let's go...

DownloadData();
CheckForTable();
DataImport();

/**
  * Download the entire province's civic address data.
  */
function DownloadData() {

	global $CAT,$TMPDIR,$DEBUG;

	$counties = array("QUN" => "Queens County",
				      "KNS" => "Kings County",
				      "PRN" => "Prince County");

	$catstring = "";
	foreach ($counties as $county => $description) {
		if ($DEBUG) { echo "Downloading $description...\n"; }
		DownloadCounty($county);
		$catstring .= "$TMPDIR/" . $county . ".txt ";
	}

	if ($DEBUG) { echo "Concatenating county files into one data file...\n"; }
	system("rm $TMPDIR/civicaddress.txt");
	system("$CAT $catstring > $TMPDIR/civicaddress.txt");
	if ($DEBUG) { 
		echo "Data now downloaded into $TMPDIR/civicaddress.txt\n";
	}
}

/**
  * Download a single county's worth of civic address data.
  * @param string $county Three-character county abbreviation (KNS,QUN or PRN)
  */
function DownloadCounty($county) {

	global $WGET,$TMPDIR;

	$url = "http://www.gov.pe.ca/civicaddress/download/dodownload.php3?county=" . $county . "&downloadformat=tab&downloadfields[]=street_no&downloadfields[]=street_nm&downloadfields[]=comm_nm&downloadfields[]=apt_no&downloadfields[]=county&downloadfields[]=latitude&downloadfields[]=longitude&downloadfields[]=pid&downloadfields[]=unique_id&downloadfields[]=census";

	if (! file_exists($TMPDIR . "/" . $county . ".txt")) {
		system("$WGET \"$url\" --quiet -O $TMPDIR/" . $county . ".txt");
	}
}

/**
  * Import the tab-delimited ASCII file into the MySQL data table.
  */
function DataImport() {

	global $DEBUG,$TMPDIR,$mysql_host,$mysql_user,$mysql_passwd,$mysql_database,$mysql_table;

	MYSQL_CONNECT($mysql_host,$mysql_user,$mysql_passwd);
	MYSQL_SELECT_DB( $mysql_database ) or die( "Unable to select database $mysql_database\n");

	$query = "LOAD DATA INFILE '$TMPDIR/civicaddress.txt' into table $mysql_table";
	$result = MYSQL_QUERY($query);

	if (! $result) {
		die(mysql_error());
	}
	
	if ($DEBUG) { 
		echo "Imported data into $mysql_table...\n";

		$query = "select count(*) as howmany from $mysql_table";
		$result = MYSQL_QUERY($query);
		$currentrecord = 0;
		$howmany = trim(MYSQL_RESULT($result,$currentrecord,"howmany"));

		echo "Total of $howmany addresses now in the table.\n";
	}
}

/**
  * Check for the existince of the data table and create or empty.
  */
function CheckForTable() {

	global $DEBUG,$TMPDIR,$mysql_host,$mysql_user,$mysql_passwd,$mysql_database,$mysql_table;

	MYSQL_CONNECT($mysql_host,$mysql_user,$mysql_passwd);
	MYSQL_SELECT_DB( $mysql_database ) or die( "Unable to select database $mysql_database\n");

	$query = "SHOW TABLES LIKE '$mysql_table'";
	$result = MYSQL_QUERY($query);
	$howmanyrecords = MYSQL_NUM_ROWS($result);

	// The table doesn't exist, so we'll create it

	if ($howmanyrecords == 0) {

		if ($DEBUG) { echo "Table $mysql_table not found, so creating it...\n"; }

		$query = "CREATE table `$mysql_table` (
					`street_no` int(11) NULL,
					`street_nm` char(50) NULL,
					`comm_nm` char(30) NULL,
					`apt_no` char(10) NULL,
					`county` char(3) NULL,
					`latitude` real(11,5) NULL,
					`longitude` real(11,5) NULL)";

		$result = MYSQL_QUERY($query);
		
		if ($DEBUG) { echo "Creating indices for $mysql_table...\n"; }
		
		$query = "CREATE index `StreetNumberDex` ON $mysql_table (street_no)";
		$result = MYSQL_QUERY($query);

		$query = "CREATE index `StreetNameDex` ON $mysql_table (street_nm)";
		$result = MYSQL_QUERY($query);

		$query = "CREATE index `CommNameDex` ON $mysql_table (comm_nm)";
		$result = MYSQL_QUERY($query);

		$query = "CREATE index `CountyDex` ON $mysql_table (county)";
		$result = MYSQL_QUERY($query);
		
	}
	else {

		if ($DEBUG) { echo "Table $mysql_table found, emptying it...\n"; }

		$query = "DELETE from $mysql_table";
		$result = MYSQL_QUERY($query);
	
	}
}
