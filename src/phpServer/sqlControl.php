<?php

$createThrd = 'create table thrd (
	private_thrdid int not null auto_increment,
	public_thrdid varchar(100) not null,
	thrd_name varchar(100) not null,
	user_name varchar(100) not null,
	thrd_date datetime not null,
	primary key(private_thrdid)
	
)';

$createMessage = 'CREATE TABLE message (
	public_id varchar(100) not null,
	message_user varchar(255) not null,
	message varchar(255) not null,
	message_date datetime not null
)';


$username = "enterusername";
$password = "enterpassword";


try {
	$db = new PDO("mysql:host=localhost;dbname=test", $username, $password);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//	$db->query($createThrd);	
//	$db->query($createMessage);
	

} catch(PDOException $e){
	echo ($e);
	die("");
} 


?>
