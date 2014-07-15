<?php
//This is probably going to run as a cron job so set a higher execution time
ini_set('max_execution_time', 60);

//using Redis to save the alert (the timestamp of the alert as the key)
require 'Predis.php';

//convert the UTF-16 to UTF-8 (for some reason the response is in UTF-16
function file_get_contents_utf8($fn) {
	$content = file_get_contents($fn);
	return mb_convert_encoding($content, 'UTF-8', 'UTF-16');
}

$orefURL = 'http://www.oref.org.il/WarningMessages/alerts.json';

try {
	$alertsDataRaw = file_get_contents_utf8($orefURL);
	$alertsDataJson = json_decode($alertsDataRaw);
	$areas = $alertsDataJson->data;
	$id = $alertsDataJson->id;
	//check if the latest alert isnt empty
	if ($areas != null){
		//write into redis
		$client = new Predis\Client();
		$client->set($id, json_encode($areas));
		//print something for the nonbeliever
		echo "areas : $areas\n";
	}else{
		echo "no alerts\n";
	}
}
catch (Exception $e){
	echo "error" . $e->getMessage();
}

?>
