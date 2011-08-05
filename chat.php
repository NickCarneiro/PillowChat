<?php
//Install PHPillow using PEAR
require("PHPillow/bootstrap.php");
require ("documents.php");
require ("views.php");
require("settings.php");

phpillowConnection::createInstance('localhost', 5984, COUCHDB_USER, COUCHDB_PASSWORD);
phpillowConnection::setDatabase(COUCHDB_DATABASE);

function tripHash($password){
	//TODO: Talk to someone smart about cryptographic hash functions
	//This appears to work well, but there could be a flaw.

	//based on wikipedia description of algo
	$tripcode = substr(crypt($password, TRIPCODE_SALT), -10, 10);
	return $tripcode;
}

function validMessage($json){
	//make sure username is appropriate length and alphanumeric plus underscore
	if(strlen($json->username) > 30 || strlen($json->username) < 3){
		return false;
	}
	$alphanumeric = '/^[0-9a-zA-Z_]+$/';
	if(!preg_match($alphanumeric, $json->username)){
		return false;
	}
	
	//make sure message isn't too long.
	if(strlen($json->message) > 1000){
		return false;
	}
	
	//sanity checks on password
	if(strlen($json->password) > 100){
		return false;
	}
	return true;
	
}
if (isset($_POST['json'])){
	try{
		$json = json_decode($_POST['json']);
		if(isset($json->message)){
			if(validMessage($json)){
			
				//client is sending message
				$doc = new chatDocument();
				$doc->message = $json->message;
				$doc->username = $json->username;
				//apply tripcode algorithm
				$doc->tripcode = tripHash($json->password);
				
				list( $msecs, $uts ) = split( ' ', microtime());
				$currentMs = strval(floor(($uts+$msecs)*1000));
				
				$doc->timestamp = $currentMs;
				$doc->save();
			}
			
		} 
		
		//send back new messages to client 
		//every time a user sends a message or polls for new messages,
		//we send back all messages newer than the client's lastTimestamp
		if(isset($json->lastTimestamp)){
			
			$startMs = $json->lastTimestamp - 5;
			//send last 3 seconds through 100ms in the future, just in case.
			
			list( $msecs, $uts ) = split( ' ', microtime());
			$currentMs = floor(($uts+$msecs)*1000) + 100;
			
			//When a client first joins the chat, the lastTimestamp is 0.
			//In this case we want to send a handful of recent messages to give some context
			if($startMs < 0){
				$doc = chatView::messages(
				array(    'endkey'=> strval($startMs),'startkey'=>strval($currentMs), 'limit'=>5, 'descending'=>true) );
				$response = array_reverse($doc->rows);
			} else {
				//send client all messages after its latest			
				$doc = chatView::messages(
				array(    'startkey'=> strval($startMs),'endkey'=>strval($currentMs)) );
				$response = $doc->rows;
			}
				echo(json_encode($response));
			
			//To show a list of users in the chat room, we look at all chatDocuments
			//modified in the last 5 seconds. Instead of creating a separate user document
			//we hold a chatDocument with an empty message property for every user. Every time
			//a user polls for new messages, we update the timestamp on this empty chatDocument.
			
			//update last activity timestamp
			$doc = singleUserView::singleuser();
			$recordExists = false;
			foreach($doc->rows as $row){			
				
				if($row['value']['username'] == $json->username 
					&& $row['value']['tripcode'] == tripHash($json->password)){
					$recordExists = true;
					//got id of record to update
					
					$doc = new chatDocument();
					$doc->fetchById($row['id']);
					$doc->timestamp = strval($currentMs);
					$doc->save();
					break;
				}
				
			}
			if(!$recordExists){
				
				//create chat document with empty message field
				//to hold timestamp of last activity
				$doc = new chatDocument();
				$doc->message = "";
				$doc->username = $json->username;
				//apply tripcode algorithm
				$doc->tripcode = tripHash($json->password);
				list( $msecs, $uts ) = split( ' ', microtime());
				$currentMs = strval(floor(($uts+$msecs)*1000));
				$doc->timestamp = $currentMs;
				$doc->save();
			}
			
		}
		
		if(isset($json->getUsers)){
			//send back json list of users currently in the chat
			list( $msecs, $uts ) = split( ' ', microtime());
			$currentMs = floor(($uts+$msecs)*1000) + 100;
			$startMs = $currentMs - 5000;
			//users are considered present in the chat if they have polled for new
			//messages in the last 5 seconds
			$doc = userView::users(
				array(    'startkey'=> strval($startMs),'endkey'=>strval($currentMs)) );
			echo(json_encode($doc->rows));
		}
	} catch(phpillowResponseConflictErrorException $e){
		//couchdb sometimes throws updateconflict exceptions
		//send the client back a json encoded error message
		echo('{"error":"'.$e->getMessage().'"}');
	}
}
?>