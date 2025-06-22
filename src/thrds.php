
<!doctype html>

<head>
	<title>thrds</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="./css/thrds.css" type="text/css">
	</head>
	<body>
	<?php
	$user = "username"; 
	$pass = "password";
	try{
		$db = new PDO("mysql:host=localhost;dbname=test",$user, $pass);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$messages = $db->query("select * from message order by public_id DESC,message_date DESC");
		$messages = $messages->fetchAll();
		$thrds  = $db->query("select * from thrd order by thrd_date DESC");
		$thrds = $thrds->fetchAll();
		echo "<br>";
	} catch(PDOException $e){
		echo $e;	
	}
?>
	<div class="thrds-main-body">
	<div class="thrd-header">
	<div class="thrd-create area">
	<div class="thrd-create-open">
		<button> Create Thrd </button>
		<form id="create-thrd">
	<input type="text" class="thrd-create title" placeholder="ThrdName" style="font-size: 16px" >
	<input type="text" class="thrd-create user" placeholder="UserName" style="font-size: 16px" >
	<button type="submit"  >create Thrd</button>
	<div class="thrd-create error"></div>
	</form>
	</div>

	</div>
	<div class="user-name-input">
		<div class="user-name-open">
			<button >Set Name</button>
			<form id="set-user">
		<input type="text" placeholder="Set anonymous name" class="set-username" style="font-size: 16px">	
		<!--	<input type="text" placeholder="Private Id, If have" style="font-size: 16px"> -->
		<button submit="submit">Set Name</button>
		</form>
		</div>

	</div>
	<div class="userName">
		
	</div>
	</div>
	<div class="thrds body">
		<div class="thrds status">
			<div class="thrds created"></div>	
			<div class="thrds users"></div>
		</div>
		<div class="thrds area">

<?php
	foreach($thrds as $thrd) {	
		$divStart = <<<START
<div class="{$thrd['public_thrdid']} thrd-area"><div class="thrd-details"><div class="thrd-main">Thread: <span class="thrd-title">{$thrd['thrd_name']}</span>  by<span class="thrd-user"> {$thrd["user_name"]}</span> </div> <br> <div class="thrd-date">{$thrd['thrd_date']}</div></div> <div class="messageArea"> 
						<div class="close-thrd">
							<button class="close-thrd-btn">X</button>
						</div>
						<div class="{$thrd['public_thrdid']} message-input">
						<form id="message-input-form">
						<input type="text" id="message-input" placeholder="Enter message">
						<button type="submit">💩</button>
						</form>
						</div> 
						<div class="message-panel"> 
							<div class="thrd-messages">
							<div class="broadcasted-messages">
START;
		foreach( $messages as $message){

			if($message['public_id'] === $thrd['public_thrdid']){
		$divMessage = <<<MID
<div class="broadcasted-message-server"><p style="color: blue" class="message-text">{$message['message_user']} : {$message['message']}</p><p class="message-date">{$message['message_date']}</p></div>
MID;
		$divStart = $divStart.$divMessage;
		$divMessage = "";
			}
		}

		$divEnd = '				
							</div>
							</div>
						</div>
						</div></div>';
		echo $divStart.$divEnd;
		$divStart = "";
	}


	
?>

		</div>
	</div>
	</div>
	<script type="module" src="./thrds.js"></script>

</body>
</html>
