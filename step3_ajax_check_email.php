<?php
include 'inc/bootstrap.php';

if(!isset($_GET['email'])){
	exit('Line: ' . __LINE__);
	}

$query = $db->prepare('SELECT * FROM users WHERE email = ?');
$query->execute(array($_GET['email']));

if(!count($query->fetchAll())){
	echo 'ok';
	}
