<?php
session_start();
require_once(__DIR__ . '/credentials.php');
$db_link = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$errors = array();
if (isset($_POST['process'])) {
	/*
	 * Check the username for new users...
	 */
	if (isset($_GET['new'])) {
		if (! $_POST['username']) {
			$errors['username'] = 'Please provide the username you wish to use.';
		} else {
			$query = "SELECT id FROM users WHERE email='" . $db_link->real_escape_string($_POST['username']) . "'";
			$db_result = $db_link->query($query);
			if ($db_result->num_rows) {
				$errors['username'] = 'The username you have chosen is already taken.';
			}
		}
	}
	
	// Other basics...
	if (! ($_POST['password'] && $_POST['password2'])) {
		$errors['password'] = 'Please provide a password and the password confirmation.';
	} else if ($_POST['password'] != $_POST['password2']) {
		$errors['password'] = 'Passwords do not match.';
	}
	
	if (! $_POST['street_number']) {
		$errors['street_number'] = 'You must enter a street number.';
	}
	if (! $_POST['street_name']) {
		$errors['street_name'] = 'You must enter a street name.';
	}
	if (! $_POST['street_type']) {
		$errors['street_type'] = 'You must enter a street type.';
	}
	if (! $_POST['town']) {
		$errors['town'] = 'You must enter a town.';
	}

	$username = $db_link->real_escape_string($_POST['username']);
	$password = $db_link->real_escape_string($_POST['password']);
	$street_number = intval($_POST['street_number']);
	$street_name = $db_link->real_escape_string($_POST['street_name']);
	$street_type = $db_link->real_escape_string($_POST['street_type']);
	$town = $db_link->real_escape_string($_POST['town']);
	$twitter = $db_link->real_escape_string($_POST['twitter']);
	$sms = $db_link->real_escape_string($_POST['sms']);
	
	// Look up the civics ID...
	$civics_id = null;
	if (! count($errors)) {
		$query = "SELECT unique_id FROM civics 
			WHERE street_no=$street_number
			AND street_nm='$street_name $street_type'
			AND comm_nm='$town'";
		
		$db_result = $db_link->query($query);
		
		if (! $db_result->num_rows) {
			$errors['street_name'] = 'Your address cannot be found.';
		} else {
			$db_row = $db_result->fetch_assoc();
			$civics_id = $db_row['unique_id'];
		}
	}
	
	if ($civics_id) {
		if (! $_SESSION['userid']) {
			$query = "INSERT INTO users (email, password, civics_id, twitter, sms) VALUES
			('$username', '$password', '$civics_id', '$twitter', '$sms')";
			$db_result = $db_link->query($query);
			error_log($db_link->error);
			$_SESSION['userid'] = $db_link->insert_id;
			header('Location: index.html');
			die;
		} else {
			$user_id = $_SESSION['userid'];
			$query = "UPDATE users SET 
				password='$password', civics_id='$civics_id', twitter='$twitter', sms='$sms'
				WHERE id=$user_id";
			$db_result = $db_link->query($query);
			header('Location: index.html');
			die;
		}	
	}
}

if (isset($_GET['new']) && $_GET['new']) {
	$title = "Sign Up";
} else {
	$title = 'Edit Your Information';
}
include_once('header.php');

$username = (isset($_POST['username']) ? $_POST['username'] : '');
$password = (isset($_POST['password']) ? $_POST['password'] : '');
$password2 = (isset($_POST['password2']) ? $_POST['password2'] : '');
$street_number = (isset($_POST['street_number']) ? $_POST['street_number'] : '');
$street_type = (isset($_POST['street_type']) ? $_POST['street_type'] : '');
$town = (isset($_POST['town']) ? $_POST['town'] : '');
$twitter = (isset($_POST['twitter']) ? $_POST['twitter'] : '');
$sms = (isset($_POST['sms']) ? $_POST['sms'] : '');

$output = '<div class="content">';
$output .='<form action="edit.php" method="POST">';
$output .='<div class="row">';
$output .='<div class="cell">Username</div>';
$output .='<div class="cell">';
$output .='<input type="text" name="username" value="'.$username.'" />';
$output .='</div>';
if ($errors['username']) {
  $output .= '<div class="error">' . $errors['username'] . '</div>';
}
$output .='</div>';
$output .='<div class="row">';
$output .='<div class="cell">Password</div>';
$output .='<div class="cell">';
$output .='<input type="password" name="password" value="'.$password.'" /></div>';
if ($errors['password']) {
  $output .= '<div class="error">' . $errors['password'] . '</div>';
}
$output .='</div>';
$output .='<div class="row">';
$output .='<div class="cell">Confirm Password</div>';
$output .='<div class="cell">';
$output .= '<input type="password" name="password2" value="'.$password2.'" /></div>';
if ($errors['password2']) {
  $output .= '<div class="error">' . $errors['password2'] . '</div>';
}
$output .='</div>';
$output .='<div class="row">';
$output .='<div class="cell">Street Number</div>';
$output .='<div class="cell">';
$output .='<input type="text" name="street_number" value="'.$street_number.'" /></div>';
if ($errors['street_number']) {
  $output .= '<div class="error">' . $errors['street_number'] . '</div>';
}
$output .='</div>';
$output .='<div class="row">';
$output .='<div class="cell">Street Name</div>';
$output .='<div class="cell">';
$output .='<input type="text" name="street_name" value="'.$street_name.'"/ ></div>';
if ($errors['street_name']) {
  $output .= '<div class="error">' . $errors['street_name'] . '</div>';
}
$output .='</div>';
$output .='<div class="row">';
$output .='<div class="cell">Street Type</div>';
$output .='<div class="cell">';
$output .= '<select name="street_type">';
$output .= '<option value="">Any</option>';
$output .= '<option value="AC">ACRES</option><option value="AL">ALLEY</option><option value="AV">AVENUE</option><option value="BA">BAY</option><option value="BF">BLUFF</option><option value="BL">BOULEVARD</option><option value="BP">BYPASS</option><option value="CW">CAUSEWAY</option><option value="CIR">CIRCLE</option><option value="CI">CIRCUIT</option><option value="CS">CLOSE</option><option value="CN">CONCESSION</option><option value="CRT">COURT</option><option value="CR">CRESCENT</option><option value="CX">CROSS</option><option value="DS">DOWNS</option><option value="DR">DRIVE</option><option value="EV">EVERGREEN</option><option value="ET">EXIT</option><option value="XY">EXPRESSWAY</option><option value="EXT">EXTENSION</option><option value="GN">GARDEN</option><option value="GS">GARDENS</option><option value="GT">GATE</option><option value="GR">GREEN</option><option value="GV">GROVE</option><option value="HT">HEIGHT</option><option value="HWY">HIGHWAY</option><option value="HL">HILL</option><option value="HW">HOLLOW</option><option value="LG">LANDING</option><option value="LN">LANE</option><option value="LW">LAWN</option><option value="LI">LINE</option><option value="LK">LINK</option><option value="LP">LOOP</option><option value="MR">MANOR</option><option value="ME">MEWS</option><option value="PR">PARK</option><option value="PY">PARKWAY</option><option value="PATH">PATH</option><option value="PL">PLACE</option><option value="PD">POND</option><option value="PM">PROMENADE</option><option value="RI">RIDGE</option><option value="RS">RISE</option><option value="RD">ROAD</option><option value="RY">ROADWAY</option><option value="RT">ROUTE</option><option value="RW">ROW</option><option value="SD">SIDE</option><option value="SR">SIDEROAD</option><option value="SQ">SQUARE</option><option value="SP">STRIP</option><option value="ST">STREET</option><option value="TERR">TERRACE</option><option value="TW">THROUGHWAY</option><option value="TK">TOOK</option><option value="TRAIL">TRAIL</option><option value="TU">TURN</option><option value="PK">TURNPIKE</option><option value="VW">VIEW</option><option value="WK">WALK</option><option value="WY">WAY</option><option value="WF">WHARF</option><option value="WD">WOODS</option>';
$output .= '</select>';
$output .= '</div>';
if ($errors['street_type']) {
  $output .= '<div class="error">' . $errors['street_type'] . '</div>';
}
$output .='</div>';

$output .='<div class="row">';
$output .='<div class="cell">Town</div>';
$output .='<div class="cell"><select name="town">';
$output .= '""></option>'; // todo

$query = "SELECT DISTINCT(comm_nm) AS comm_nm FROM civics ORDER BY comm_nm";
$db_result = $db_link->query($query);
while ($db_row = $db_result->fetch_assoc()) {
	$output .= '<option value="' . $db_row['comm_nm'] . '">' . $db_row['comm_nm'] . '</option>';
}
$output .='</select></div>';
if ($errors['town']) {
  $output .= '<div class="error">' . $errors['town'] . '</div>';
}
$output .='</div>';

$output .='<div class="row">';
$output .='<div class="cell">Twitter Handle</div>';
$output .='<div class="cell">';
$output .='<input type="text" name="twitter" value="'.$twitter.'" />';
$output .='</div>';
if ($errors['twitter']) {
	$output .= '<div class="error">' . $errors['twitter'] . '</div>';
}
$output .='</div>';

$output .='<div class="row">';
$output .='<div class="cell">Cell Number</div>';
$output .='<div class="cell">';
$output .='<input type="text" name="sms" value="'.$sms.'" />';
$output .='</div>';
if ($errors['sms']) {
	$output .= '<div class="error">' . $errors['sms'] . '</div>';
}
$output .='</div>';

$output .='</div>';
$output .= '<input type="hidden" name="process">';
$output .= '<input type="submit" value="Submit">';
echo $output;
include_once 'footer.php';