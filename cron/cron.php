<?php
require __DIR__ . '/twilio-php/Services/Twilio.php';
require __DIR__ . '/twitter-api/TwitterAPIExchange.php';
require_once(__DIR__ . '/../credentials.php');

$db_link = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$now = new DateTime();
$today_string = $now->format('Y-m-d');

/*
 * Who do we need to notify?
 */
$query = "SELECT email, twitter, sms,
		blue.next_bluebag AS next_bluebag,
		wc.next_blackbin AS next_blackbin,
		wc.next_greenbin AS next_greenbin
	FROM users
	INNER JOIN civics ON civics.id=users.civics_id
	LEFT JOIN civics_zones AS blue ON blue.name=civics.bluebag
	LEFT JOIN civics_zones AS wc ON wc.name=civics.wastecompost
	WHERE blue.next_bluebag='$today_string'
		OR wc.next_blackbin='$today_string'
		OR wc.next_greenbin='$today_string'";

$db_result = $db_link->query($query);

while ($db_row = $db_link->fetchAssoc()) {
	$message = 'Hey ' . $db_row['email'] . '! It\'s time to put our your ';
	if ($db_row['next_bluebag'] == $today_string) {
		$message .= 'blue bags';
	} else if ($db_row['next_blackbin'] == $today_string) {
		$message .= 'black bin';
	} else if ($db_row['next_greenbin'] == $today_string) {
		$message .= 'green bin';
	}
	
	$message .= '.';
	
	/*
	 * SMS?
	 */
	if ($db_row['sms']) {
		$client = new Services_Twilio(TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN);
		$sms = $client->account->sms_messages->create(
				TWILIO_OUTGOING_PHONE_NUMBER, // From this number
				$db_row['sms'], // To this number
				$message
		);
	}
	
	/*
	 * Twitter?
	 */
	if ($db_row['twitter']) {
		$url = 'https://api.twitter.com/1.1/statuses/update.json';
		$settings = array(
			'oauth_access_token' => TWITTER_OAUTH_ACCESS_TOKEN,
			'oauth_access_token_secret' => TWITTER_OAUTH_ACCESS_TOKEN_SECRET,
			'consumer_key' => TWITTER_OAUTH_CONSUMER_KEY,
			'consumer_secret' => TWITTER_OAUTH_CONSUMER_SECRET
		);
		
		// $postfields = array('status' => 'D ' . $db_row['twitter'] . ' ' . $message);
		$postfields = array('status' => '@' . $db_row['twitter'] . ' ' . $message);
		$requestMethod = 'POST';
		
		$twitter = new TwitterAPIExchange($settings);
		$response = $twitter->buildOauth($url, $requestMethod)
			->setPostfields($postfields)
			->performRequest();
		var_dump(json_decode($response));
	}
}