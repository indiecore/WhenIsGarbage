<?
include_once 'header.php'
$twitter = $_POST['twitter']; 
$phone = $_POST['phone'];
if (!$_SESSION['userid']) {
  header('index.php');
  die;
}

if ($_POST['process']) {
  // save, 
  header('index.php');
  die;
} else {
  // load
}
$output = '<form action="message_settings.php" method="POST">';
$output .= '<div class="title">Edit Message Settings</div>';
$output .= '<div class="row">';
$output .= '<div class="cell">Twitter Account name</div>';
$output .= '<div class="cell"><input type="text" name="twitter"></input>';
$output .= '</div>';
$output .= '<div class="row">';
$output .= '<div class="cell">Phone Number</div>';
$output .= '<div class="cell"><input type="number" name="phone"></input>';
$output .= '</div>';
$output .= '<input type="hidden" name="process" value="1">';
$output .= '<input type="submit" value="Submit">';
$output .= '</form>';
echo $output;
include_once 'footer.php' 
