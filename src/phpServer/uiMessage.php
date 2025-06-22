<!DOCTYPE html>
<head>
<title>thrds</title>
</head>
<body>
<form >
<input type="text"  name="text">Text
<button type="submit" onsubmit="submitHandle">💩</button>
<div class="message">
</div>

<script>
 var endPoint = 'ws://127.0.0.1:9501';
//var endPoint = 'https://d0bf-223-228-106-27.ngrok-free.app';
const socket = new WebSocket(endPoint);
const form = document.querySelector('form');
const inputField = document.querySelector('input[name="text"]');
const messageBody = document.getElementsByClassName('message')[0];
socket.onopen = function(event){
	socket.send("Hi i am shit");
}
socket.onmessage = function(event) {
	messageBody.innerHTML += `<p>${event.data}</p>`;	
}
function sendMessage(socket,message){
	socket.send(message+"\n");
}

form.addEventListener('submit', (event)=>{
event.preventDefault();
	const text =inputField.value.trim();	
	sendMessage(socket, text);	
	inputField.value = "";
});
</script>
</form>
</body>
</html>
