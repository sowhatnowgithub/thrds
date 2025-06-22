/*
 * 	this code essentially tries to talk with a webSocket and dynamically communicate with it, and update the dom
 * 	And give a sense of real time communication
 * 	I used WebSocket for connecting to the remote socket, and implimented a exponential back off algo, for reconnection
 * 	The remote socket will be configured, to close the connection if 30 secs of ideality is present or lost
 * 	Hence, we have to reload to establish the connection again, so to make this more user-friendly, will implement 
 *  A better way to make the connection with the remote server
 * */


// Creating the webSocket Manager
// When the server is busy, we create a exponential back off algorithm, defined to retry the connection


class webSocketManager {
		constructor(url, maxRetries=15){
			this.url = url;
			this.maxRetries = maxRetries;
			this.retryCount = 0;
			this.socket = 0;
			this.connect();
		}

		connect() {
			this.socket = new WebSocket(this.url);
			this.socket.onopen = () => {
				console.log("WebSocketConnected");
			};
			this.listen();
			this.socket.onclose = (event) => {
				console.log("Websocket is closed");
				this.retryWithBackoff();
			};

		}
		listen(){
			this.socket.onmessage = (event) => {
			const data = JSON.parse(event.data);
				if(data['is_thrd']) {
				//	alert($data['divCreatedBy']);
				//	alert($data['divClassName']);
				//	alert($data['divCreatedAt']);
				const messageArea = `<div class="messageArea"> 
						<div class="close-thrd">
							<button class="close-thrd-btn">X</button>
						</div>
						<div class="${data['divThrdId']} message-input">
						<form id="message-input-form">
						<input type="text" id="message-input"  placeholder="Enter message" >
						<button type="submit">💩</button>
						</form>
						</div> 
						<div class="message-panel"> 
							<div class="thrd-messages">
							<div class="broadcasted-messages">
							</div>
							</div>
						</div>
						</div>`;
				const div = document.createElement('div');
				div.classList.add(data['divThrdId'].trim().replace(/ /g,"" ));	
				div.classList.add('thrd-area');

				document.querySelector('.thrd-area')?.parentNode?.insertBefore(div, 
					document.querySelector('.thrd-area') ) || 
						document.querySelector('.thrds.area').appendChild(div);
				div.innerHTML = `<div class="thrd-details"><div class="thrd-main">Thread: <span class="thrd-title">${data['divClassName']}</span>  by<span class="thrd-user"> ${data['divCreatedBy']}</span> </div> <br> <div class="thrd-date">${data['divCreatedAt']}</div></div> ${messageArea}`;	 

				} else {
					// This is for messaging in thread	
					//console.log(data);	
					// data['is_thrd']
					// data['message']
					// data['messageDate']
					// data['thrdId']
					// data['userName']
					
					// Clean the data
					const ThrdId = data['thrdId'].trim();
					const userName = data['userName'].trim();
					const date = data['messageDate']?.trim();
					const thrdFind = document.querySelector(`.${ThrdId}`);
					const messageDiv = document.createElement('div');
					messageDiv.classList.add('broadcasted-message-server');
					var messageFormat = `${data['userName']} : ${data['message']} </p><p class="message-date">${date}</p>`;
					if(thrdFind) {
						const messagePanel = thrdFind.querySelector('.broadcasted-messages');
				messagePanel.querySelector('.broadcasted-message-server')?.parentNode?.insertBefore(messageDiv, 
					messagePanel.querySelector('.broadcasted-message-server') ) || 
						messagePanel.appendChild(messageDiv);

						if(userName === userSetName) {
							messageFormat = `<p style="color: red" class="message-text">${messageFormat}`;
							messageDiv.innerHTML = messageFormat;
						} else {
							messageFormat = `<p class="message-text">${messageFormat}`;
							messageDiv.innerHTML = messageFormat;
						}
					
					}		
				}
			}
		}
	    retryWithBackoff() {
        	if (this.retryCount >= this.maxRetries) {
            	console.log("Max retries reached. Giving up.");
            	return;
        	}
        	const delay = Math.floor(Math.random() * Math.pow(2, this.retryCount) * 1000);
        	console.log(`Retrying in ${delay} ms`);

        	setTimeout(() => {
            	this.retryCount++;
            	this.connect();
        	}, delay);
    	}
}


//const endPoint  = `wss://${window.location.host}:9501`;
const endPoint = "ws://localhost:9501/";
const ws = new webSocketManager(endPoint);


// The following is to create the data for a thrd creation and sent it via socket to the endPoint
//
//
const thrdCreate = document.querySelector("#create-thrd");
thrdCreate.querySelector('button').addEventListener('click',(e)=>{
	e.preventDefault();
	if(thrdCreate.querySelector('.thrd-create.title')?.value === "" 
		|| thrdCreate.querySelector('.thrd-create.user')?.value === "" ) {
			thrdCreate.querySelector('.thrd-create.error').innerHTML = "Please Enter the fields to create a thread";
	}	else 	{
		thrdCreate.querySelector('.thrd-create.error').innerHTML = "";
		var thrd_title = thrdCreate.querySelector('.thrd-create.title')?.value;
		var thrd_create_user = thrdCreate.querySelector('.thrd-create.user')?.value;
		thrdCreate.querySelector('.thrd-create.title').value = "";
		thrdCreate.querySelector('.thrd-create.user').value = "";
		createThrd(thrd_title, thrd_create_user);
	}	
});
function createThrd(thrd_title,thrd_create_user){	
	var data = {
		thrd_user: thrd_create_user,
		thrd_title: thrd_title,
		is_message : false
	};
	if(ws.socket.readyState !== WebSocket.CLOSED)
		ws.socket.send(JSON.stringify(data));
	else{ 
		ws.connect();
		setTimeout(ws.socket.send(JSON.stringify(data)), 1000);
	}
}

function createBroadcastMessage(message, thrdId, userName){
	const data = {
		userName : userName,
		thrdId : thrdId,
		message : message,
		is_message : true
	};
	if(ws.socket.readyState !== WebSocket.CLOSED)
	ws.socket.send(JSON.stringify(data));
	else {
		ws.connect();
		setTimeout(ws.socket.send(JSON.stringify(data)), 1000);

	}
}

var userSetName = `Anonymous-${Math.floor((Math.random()*10000)+100)}`;
document.querySelector('.user-name-input #set-user button').addEventListener('click',(e)=>{
	e.preventDefault();
	userSetName = document.querySelector('.set-username')?.value;
	document.querySelector('.set-username').value = "";
	document.querySelector('.thrd-header .userName').innerHTML = `UserName: ${userSetName}`;
});

// From here, the handling of the single thread i.e, messaging with in a thread starts

document.querySelector('.thrds.area').addEventListener('click', (e) => {
	const thrd = e.target.closest('.thrd-area');
	const closedBtn = e.target?.closest('.close-thrd');
	const messageButton = e.target?.closest('#message-input-form button');
	if(thrd) {
		thrd.classList.add('thrd-selected');
	};
	if(closedBtn) thrd.classList.remove('thrd-selected');
	if(messageButton) {
		e.preventDefault();
		const messageInput = messageButton?.closest('#message-input-form');
		const message = messageInput.querySelector('#message-input')?.value;;
		messageInput.querySelector('#message-input').value = "";
		const divContainerForThrd = messageInput?.closest('.message-input');
		if(divContainerForThrd){
		const divClassList = divContainerForThrd?.classList;
		const thrdId =  [...divClassList][0].trim();
			if(thrdId) {
				createBroadcastMessage(message, thrdId, userSetName);
			} 
		}
	
	}	
});
