<?
if ($_GET['new']) {
$title = "Sign Up";
else 
$title = 'Edit Your Information';
include_once ('header.php')

if ($_GET['edit'] && $_SESSION['userid']) { //if signed in
// query database
  $username = '';
  $password = '';
  $password2 = '';
  $street_number = '';
  $street_type = '';
  $town = '';
}
else if ($_POST['process']) { //if saving
  $username = $_POST['username'];
  $password = $_POST['password'];
  $password2 = $_POST['password2'];
  $street_number = $_POST['street_number'];
  $street_type = $_POST['street_type'];
  $town = $_POST['town'];

  $errors = array();

if($username != '') {
 // qyery for uniqu
 // if found 
$errors['username'] = '<div class="error">Your username has already been chosen. Please choose a different one</div>';
}
if ($password1 != $password2) {
$errors['password'] = '<div class="error">Passwords do not match</div>';
}
if (!$street_number) {
$errors['street_number'] = '<div class="error">You must enter a street number</div>';
}
if ($street_type == 'Select Street') {
$errors['street_type'] = '<div class="error">You must select a street type</div>';
}
if ($town == 'Select Town') {
$errors['town'] = '<div class="error">You must select a town</div>';
}
if (!isset($errors)) {
$query = "SELECT unique_id FROM civics WHERE 
street_no = ".mysql_real_escape_string($street_no)."
AND street_name LIKE \"%".mysql_real_escape_string($street_name)."%\"
AND comm_nm LIKE \"%".mysql_real_escape_string($town)."%\"";
$results = mysql_query($query);

header('message_settings.php');
die;
}

$output = '<div class="content">';
$output .='<form action="edit.php" method="POST">';
$output .='<div class="row">';
$output .='<div class="cell">Username</div>';
$output .='<div class="cell">';
$output .='<input type="text" name="username">'.$username.'</input>';
$output .='</div>';
if ($errors['username']) {
  echo $errors['username'];
}
$output .='</div>';
$output .='<div class="row">';
$output .='<div class="cell">Password</div>';
$output .='<div class="cell">';
$output .='<input type="password" name="password">'.$password.'</input></div>';
if ($errors['password']) {
  echo $errors['password'];
}
$output .='</div>';
$output .='<div class="row">';
$output .='<div class="cell">Confirm Password</div>';
$output .='<div class="cell">';
$output .= '<input type="password" name="password2">'.$password2.'</input></div>';
if ($errors['password2']) {
  echo $errors['password2'];
}
$output .='</div>';
$output .='<div class="row">';
$output .='<div class="cell">Street Number</div>';
$output .='<div class="cell">';
$output .='<input type="text" name="street_number">'.$street_number.'<input></div>';
if ($errors['street_number']) {
  echo $errors['street_number'];
}
$output .='</div>';
$output .='<div class="row">';
$output .='<div class="cell">Street Name</div>';
$output .='<div class="cell">';
$output .='<input type="text" name="street_name">'.$street_name.'</input></div>';
if ($errors['street_name']) {
  echo $errors['street_name'];
}
$output .='</div>';
$output .='<div class="row">';
$output .='<div class="cell">Street Type</div>';
$output .='<div class="cell">';
$output .= '<select name="street_type">';
$output .= '<option value="1"></option>'; // todo
$output .= '</select>';
$output .= '</div>';
$output .='</div>';
$output .='<div class="row">';
$output .='<div class="cell">Town</div>';
$output .='<div class="cell"><select name="town">';
$output .= '<option value=""></option>'; // todo
$output .='</select></div>';
$output .='</div>';

$output .='</div>';
$output .= '<input type="hidden" name="process">';
$output .= '<input type="submit" value="Submit">';
echo $output;
include_once 'footer.php';