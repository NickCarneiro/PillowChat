
<!DOCTYPE html> 
<html> 
 
  <head> 
    <title>PillowTalk: A Simple jQuery/PHPillow/CouchDB Chat Room</title> 
   
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script> 
	<script src="jquery.timeago.js"></script>
	<script src="jquery.timers-1.2.js.txt"></script>  
	<link rel="stylesheet" href="pillowtalk.css" type="text/css" />
	<script src="pillowtalk.js"></script> 
  </head> 
 
  <body> 
<div id="header">
	<h1>PillowTalk</h1><br />
	<span>A Simple jQuery/PHPillow/CouchDB Chat Room</span>
</div>
  <div id="chat_username_prompt">
	<div id="prompt_controls">
	<fieldset>
	<span id="name_error">Choose any username and password.</span><br />
	<label>Username: <br />
		<input id="chat_username" type="text" /></label><br />
	<label>Password (optional): <br />
		<input id="chat_password" type="password" /></label><br />
		<label>
		 <input id="chat_submit_username" type="submit" value="Enter Chat"/><br />
 <br /><span>Entering a password will generate a tripcode that allows users to identify you.</span>
 </label>
	</fieldset>
	</div>
  </div>
  
<div id="chat_container">
	<div id="top_pane">

		<div id="chat_box">
			<div id="chat_messages"></div>
		</div>

		<div id="chat_users">
			<ul id="user_list">
			<li></li>
			</ul>
		</div>
	</div>
	<div id="bottom_pane">
		<div id="bottom_controls">
			<input type="text" id="chat_input" maxlength="1000"/>
			<input id="chat_submit" type="submit" value="Send"/><br />
			<span id="last_received"></span>
		</div>
	</div>
		
</div>

<div id="footer">
Created by <br /> <a href="http://twitter.com/nickc_dev">Nick Carneiro</a>
</div>
</body> 
 
</html> 