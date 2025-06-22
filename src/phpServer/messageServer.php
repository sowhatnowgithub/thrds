<?php

use OpenSwoole\WebSocket\Server;
use OpenSwoole\http\Request;
use OpenSwoole\WebSocket\Frame;

$host = '0.0.0.0';
$port = 9501;

$table = new OpenSwoole\Table(100);
$table->column('fd', OpenSwoole\Table::TYPE_INT);

$table->create();
$server = new Server($host, $port);

$server->set([
	"task_enable_coroutine" => true,
	"task_worker_num" => 4
]);
$server->on('Start', function($server) use($port){
	echo "Server Started at $port\n";
});

$server->on('Open', function(Server $server, Request $request) use($table){
	echo "Server: handshake was success {$request->fd}\n";
	$fd = $request->fd;
	$table->set((string)$fd, ['fd'=>$fd]);
});

$server->on('Message',function($server, $frame)use($table) {
	$data = json_decode($frame->data, true);
	//var_dump($data);
	$server->task($data);

});

$server->on('Close', function($server, $fd) use($table){
	echo "Client {$fd} closed\n";
	$table->del((string)$fd);


});

$server->on('Finish', function($server, $task_id, $data)use($table){
	if($data['is_message']) {
		foreach($table as $row){
			$server->push($row['fd'], json_encode([
			'thrdId' => $data['thrdId'],
			'userName' => $data['userName'],
			'message' => $data['message'],
			'messageDate' => $data['message_date'],
			'is_thrd' => false
			]));
		}
	}else {
		foreach($table as $row) {

			$server->push($row['fd'], json_encode(
		[
			'divThrdId'=> $data['thrd_id'],
			'divClassName' => $data['thrd_title'],
			'divCreatedBy' => $data['thrd_user'],
			'divCreatedAt' => $data['thrd_date'],
			'is_thrd' => true
		]
			));
		}	
	} 
});

$server->on('Task', function($server, $task){
	$data = $task->data;
	$user = "pavan";
	$pass = "pass123";
	try {
	$db = new PDO("mysql:host=localhost;dbname=test", $user, $pass);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	if($data['is_message']) {
		$data['message_date'] = date('Y-m-d H:i:s');
		$query = "INSERT INTO message values ('".$data['thrdId']."','".$data["userName"]."','".$data["message"]."','".$data["message_date"]."')";
		$db->query($query);
	} else {
		$date = date('Y-m-d');
		$time = date('H:i:s');
		$time1 = str_replace(":","_",$time);
		$data["thrd_id"] =  "thrdId_$date".$time1;
		$data['thrd_date'] = "$date"." ".$time;
		$query = "INSERT INTO thrd values (NULL,'".$data['thrd_id']."','".$data['thrd_title']."','".$data['thrd_user']."','".$data['thrd_date']."')" ;
		$db->query($query);
	}
	} catch(PDOException $e){
		echo $e;
	}
	$task->finish($data);
});



/* this is under production, for better resource utilizaiton
$server->tick(30000, function($server, $request){
	foreach($request->connections as )
})
 */
/*
		We can use redis for horizontal scaling
 */

/*

/*
 *Have to add feature, like count of the connected users, and thread-user active users, for sorting the thrds in order of active users
 * */
$server->start();
