<?php

$message = $_GET['message'];
$chatRoomName = ($_GET['chatroomname']) ;
$logfile =  "data/".$_GET['chatroomname'].".txt";

switch($_GET['a']){
	case 'get':
		if(file_exists($logfile)){
			$response = file_get_contents($logfile);
			if(substr_count($response,"|") > 1){
				$response = explode("|",$response);
				array_pop($response);
				$response = implode(",",$response);
				//$response = str_replace("|", "," ,$response);
			}else{
				$response = str_replace("|", "" ,$response);
			}
			echo "[".$response."]";

		}
	break;

	case 'send':
	default:
	echo "asdasd";
		$data["nickname"] =  $_COOKIE['nickname'];
		$data["message"] =  $message;
		$data["timestamp"] =  time();
		$data = json_encode($data);
		file_put_contents($logfile,$data."|", FILE_APPEND);
	break;
}

?>