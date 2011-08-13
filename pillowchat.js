//The client side state and settings of the app are in this eponymous global
var pillowchat = {};
pillowchat.settings = {
	message_poll_frequency: 1000, //how often to grab new messages in milliseconds
	user_poll_frequency: 5000 //how often to grab the user list in milliseconds
	
}

//The object format is just the output of couchdb
//An example object is shown below.
pillowchat.state = {
	//all message objects from couch db are kept in the messages array
	messages:[
		{
		id:"chat_message-0",
		key:0,
		value:
			{
			message:"Follow @NickC_dev on Twitter!",
			username:"NickC_dev"
			}
		}
	],
	lastTimestamp: 0,
	username: "",
	password: "",
	poll: false //don't poll for new messages until username is chosen
	
};

function processNewMessages(response){
	//save messages if timestamp is greater than the last stored message
	for(var i = 0; i < response.length; i++){
	
		if(response[i].key > pillowchat.state.lastTimestamp){
			
			pillowchat.state.messages.push(response[i]);
			
		}
	}
	renderNewMessages();
}
function getMessages(){
	var json = JSON.stringify({ 
		getMessages: true,
		lastTimestamp: pillowchat.state.lastTimestamp,
		username: pillowchat.state.username,
		password: pillowchat.state.password
	});
	$.post("chat.php", {
		"json":json
		},
		function(data){
			
			response = JSON.parse(data);
			if(response.error){
				console.log("Serverside error: " + response.error);
			}
			//got new messages
			processNewMessages(response);
		}
	);
}

function renderNewMessages(){
	for(var i = 0; i < pillowchat.state.messages.length; i++){
		
		if(pillowchat.state.messages[i].key > pillowchat.state.lastTimestamp){
			$("#chat_messages").append("<span class=\"username\" title=\"" + 
				pillowchat.state.messages[i].value.tripcode + "\">" + 
				pillowchat.state.messages[i].value.username
				+ ": </span><span class=\"message\">"+ 
				pillowchat.state.messages[i].value.message
				+"</span><br />");
			//scroll to newest messages
			$("#chat_box").prop('scrollTop',$("#chat_box").prop('scrollHeight'));	
		}
		
	}
	pillowchat.state.lastTimestamp = pillowchat.state.messages[pillowchat.state.messages.length - 1].key;
	
	//update last message received text
	$("#last_received").text("Last activity " + 
		jQuery.timeago(new Date(parseInt(pillowchat.state.lastTimestamp))));
	
}
function getUsers(){
	var json = JSON.stringify({
		getUsers: true
	});
	$.post("chat.php", {
		"json":json
		},
		function(data){
			//put userlist into the dom
			renderUsers(JSON.parse(data));
			
		}
	);
}

function renderUsers(json){
	
	//sort usernames in alpha order
	json.sort(compareUsers);
	$("#user_list").empty();
	for(var i = 0; i < json.length; i++){
		$("#user_list").append("<li title=\""+json[i].value.tripcode+"\">"+json[i].value.username+"</li>");
	}
	
}

//Must specify function to sort objects on one property
//http://www.webdotdev.com/nvd/content/view/878/
function compareUsers(a, b) {
	var nameA = a.value.username.toLowerCase( );
	var nameB = b.value.username.toLowerCase( );
	if (nameA < nameB) {return -1}
	if (nameA > nameB) {return 1}
	return 0;
}

function sendMessage(){
	if($("#chat_input").val() == ""){
		return;
	}
	var json = JSON.stringify({
		username: pillowchat.state.username,
		password: pillowchat.state.password,
		message: $("#chat_input").val(),
		lastTimestamp: pillowchat.state.lastTimestamp
	});
	
	$("#chat_input").val("");
	
	$.post("chat.php", {
		"json":json
		},
		function(data){
			response = JSON.parse(data);
			//got new messages
			processNewMessages(response);
		}
	);
}

function showChat(){
	//verify that username is valid
	
	if($("#chat_username").val().length < 3 || $("#chat_username").val() > 20){
		$("#name_error").text("Choose a username between 3 and 20 characters.");
		return;
	}
	var alphanumeric = /^[0-9a-zA-Z_]+$/;
	if(!$("#chat_username").val().match(alphanumeric)){
		$("#name_error").text("Choose a username with only letters, numbers, and the underscore.")
		return;
	}
	pillowchat.state.username = $("#chat_username").val();
	pillowchat.state.password = $("#chat_password").val();
	
	
	$("#chat_username_prompt").hide();
	$("#chat_container").show();
	$("#chat_input").focus();
	pillowchat.state.poll = true;
}

$(function(){
	$("#chat_container").hide();
	$("#chat_username").focus();
	
	$(document).everyTime(pillowchat.settings.message_poll_frequency, function() {
		if(pillowchat.state.poll == true){	
			getMessages();
		}
	});
	
	$(document).everyTime(pillowchat.settings.user_poll_frequency, function() {
		if(pillowchat.state.poll == true){	
			getUsers();
		}
	});
		
	$("#chat_submit_username").click(function(){
		showChat();
	});
	
	$("#chat_password").keypress(function(e){
		if(e.which == 13){
			showChat();
		}
	});
	$("#chat_input").keypress(function(e){
		if(e.which == 13){	
			sendMessage() ;
		}
	});
	
	$("#chat_submit").click(function(){
		sendMessage();
	});
});