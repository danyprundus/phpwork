<?php
include 'inc/bootstrap.php';

if(!isset($_GET['email']) || !isset($_GET['password'])){
	exit('Line: ' . __LINE__);
	}

$query = $db->prepare('SELECT * FROM users WHERE email = ?');
$query->execute(array($_GET['email']));

$users = $query->fetchAll(PDO::FETCH_ASSOC);

if(count($users) != 1){
	exit('Line: ' . __LINE__);
	}

$user = $users[0];

if(sha1($_GET['password']) != $user['password']){
	exit('Line: ' . __LINE__);
	}

$_SESSION['order']['user'] = $user;

echo 'ok';
