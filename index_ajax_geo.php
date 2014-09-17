<?php
include 'inc/bootstrap.php';

if(!isset($_GET['cc'])){
	exit('Line: ' . __LINE__);
	}

switch($_GET['cc']){
	case 'AU':
		$currency = 'AUD';
		break;
	case 'CA':
		$currency = 'CAD';
		break;
	case 'GB':
		$currency = 'GBP';
		break;
	case 'JP':
		$currency = 'JPY';
		break;
	case 'NZ':
		$currency = 'NZD';
		break;
	case 'CN':
		$currency = 'RMB';
		break;
	case 'RU':
		$currency = 'RUB';
		break;
	case 'ZA':
		$currency = 'ZAR';
		break;
	default:
		$currency = (in_array($_GET['cc'], explode(',', 'BE,BG,CZ,DK,DE,EE,IE,EL,ES,FR,HR,IT,CY,LV,LT,LU,HU,MT,NL,AT,PL,PT,RO,SI,SK,FI,SE,UK'))) ? 'EUR' : 'USD';
	}

$_SESSION['order']['vat'] = ($_GET['cc'] == 'ZA');
$_SESSION['order']['currency'] = $currency;
