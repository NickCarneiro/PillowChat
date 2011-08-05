<?php
class singleUserView extends phpillowView {
    protected $viewDefinitions = array(
        // Index chat messages by their usernames
        'singleuser' => 'function( doc ){
		
			if ( doc.type == "chat_message" ){
				if(doc.message == ""){
					emit( doc.username, {username:doc.username, tripcode:doc.tripcode, id:doc.id, timestamp:doc.timestamp});
				}
				

			}
		}',
    );

    protected function getViewName(){
        return 'singleuser';
    }
}

class chatView extends phpillowView {
    protected $viewDefinitions = array(
        // Index chat messages by their usernames
        'messages' => 'function( doc ){
		
			if ( doc.type == "chat_message" && doc.message != ""){
			
				emit( doc.timestamp, {message:doc.message, username:doc.username, tripcode:doc.tripcode} );

			}
		}',
    );

    protected function getViewName(){
        return 'messages';
    }
}

class userView extends phpillowView {
    protected $viewDefinitions = array(
        // Index chat messages by their usernames
        'users' => 'function( doc ){
		
			if ( doc.type == "chat_message" ){
				if(doc.message == ""){
					emit( doc.timestamp, {username:doc.username, tripcode:doc.tripcode});
				}
				

			}
		}',
    );

    protected function getViewName(){
        return 'users';
    }
}
?>