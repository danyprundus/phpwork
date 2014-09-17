<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('ORDERS_EMAIL', 'info@iptica.com');
define('VAT', 0.14);
define('BANK_SPREAD', 0.1);
define('DEFAULT_TRANSLATION_PRICE', 1.2);

$db = new PDO('mysql:host=localhost;dbname=iptica', 'root', '',array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

session_start();

