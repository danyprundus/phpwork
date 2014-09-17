<?php
include 'inc/bootstrap.php';

if(!isset($_SESSION['order']['patent'])){
	header('Location: /');
	exit;
	}
if(!isset($_POST['account'])){
	header('Location: /step3.php');
	exit;
	}

$variables = array(
	'pages_num' => 'No Pages',
	'words_num' => 'No Words',
	'priority_claims_num' => 'No Priority Claims',
	'independent_claims_num' => 'No Independent Claims',
	'dependent_claims_num' => 'No Dependent Claims',
	'claims_num' => 'No Total Claims'
	);

$order = $_SESSION['order'];

if($_POST['account'] == 'new'){
	if(strlen($_POST['password']) < 6 || $_POST['password'] != $_POST['password-conf']){
		exit('Line: ' . __LINE__);
		}
	$query = $db->prepare('SELECT * FROM users WHERE email = ?');
	$query->execute(array($_POST['email']));
	if(count($query->fetchAll())){
		exit('Line: ' . __LINE__);
		}
	
	$fields = '`email`, `password`, `agent`';
	$placeholders = '?, ?, ?';
	$values = array($_POST['email'], sha1($_POST['password']), ($_POST['account-type'] == 'agent'));
	foreach(array('first_name', 'surname', 'telephone', 'mobile', 'building', 'street', 'city', 'state', 'country', 'zip', 'vat_id', 'company', 'comments') as $f){
		$fields .= ", `{$f}`";
		$placeholders .= ', ?';
		$values[] = (isset($_POST[$f])) ? $_POST[$f] : '';
		}
	$insert = $db->prepare("INSERT INTO users ({$fields}) VALUES ({$placeholders})");
	$insert->execute($values);
	$order['user'] = $db->query('SELECT * FROM users WHERE id = ' . $db->lastInsertId());
	$order['user'] = $order['user']->fetch(PDO::FETCH_ASSOC);
	}
elseif($_POST['account'] == 'existing'){
	if(!isset($order['user'])){
		exit('Line: ' . __LINE__);
		}
	}
else{
	exit('Line: ' . __LINE__);
	}

//echo '<pre>'; print_r($_POST); exit;

$query = $db->prepare('SELECT * FROM currencies WHERE currency = ?');
$query->execute(array($order['currency']));
$query = $query->fetch(PDO::FETCH_ASSOC);
$order['exchange_rate'] = $query['value'];

$insert = $db->prepare('INSERT INTO orders (`user_id`, `patent_identifier`, `patent_name`, `currency`, `exchange_rate`) VALUES (?, ?, ?, ?, ?)');
$insert->execute(array($order['user']['id'], $order['patent']['number'], $order['patent']['title'], $order['currency'], $order['exchange_rate']));
$order['order'] = $db->query('SELECT * FROM orders WHERE id = ' . $db->lastInsertId());
$order['order'] = $order['order']->fetch(PDO::FETCH_ASSOC);

foreach($order['regions'] as $id => $region){
	$insert = $db->prepare('INSERT INTO order_entries (`order_id`, `region_entity_id`, `fee`) VALUES (?, ?, ?)');
	$insert->execute(array($order['order']['id'], $id, $region['cost'][0]));
	}


$subject = "Iptica " + $order['patent']['number'] . ' Invoice';
$message = "<h4>{$order['patent']['number']} - {$order['patent']['title']}</h4>";
$message .= "<table border='1' cellpadding='5' cellspacing='0'>";

$message .= "<tr><th colspan='2'>Regions</th></tr>";
$total = 0;
foreach($order['regions'] as $id => $region){
	$total += $region['cost'][0];
	$region['cost'][0] = number_format($region['cost'][0]);
	$message .= "<tr><td>{$region['region_entity']}</td><td align='right'>{$region['cost'][0]} {$order['currency']}</td></tr>";
	}
if(isset($order['total_vat'])){
	$total += $order['total_vat'];
	}
$total = number_format($total);
$message .= "<tr><td><b>Total</b></td><td><b>{$total} {$order['currency']}</b></td></tr>";
$message .= "</table><br>";

$message .= "<table border='1' cellpadding='5' cellspacing='0'>";
$message .= "<tr><th colspan='2'>Variables</th></tr>";
foreach($variables as $id => $label){
	$message .= "<tr><td>{$label}:</td><td align='right'>{$order['patent'][$id]}</td></tr>";
	}
$message .= "</table><br>";

$message .= "<table border='1' cellpadding='5' cellspacing='0'>";
$message .= "<tr><th colspan='2'>Customer</th></tr>";
foreach($order['user'] as $key => $value){
	if(in_array($key, array('id', 'password', 'created'))){
		continue;
		}
	if($key == 'agent'){
		$value = ($value) ? 'yes' : 'no';
		}
	$key = ucfirst(str_replace('_', ' ', $key));
	$message .= "<tr><td>{$key}</td><td align='right'>{$value}</td></tr>";
	}
$message .= "</table>";

foreach(array(ORDERS_EMAIL, $order['user']['email']) as $to){
	mail($to, $subject, $message, "MIME-Version: 1.0\r\n" . "Content-Type: text/html; charset=ISO-8859-1\r\n");
	}
//session_destroy();
?>
<!DOCTYPE HTML>
<html>
<head>
	<?php include 'inc/head.php' ?>
	<title>Iptica</title>
	<meta name="description" lang="en-us" content="Iptica">
	<meta name="keywords" lang="en-us" content="Iptica">
</head>
<body>		
	<?php include 'inc/header.php' ?>
	
	<section class="main-content process">
		<div class="wrapper">
			<?php include 'inc/checkout_steps.php' ?>
			
			<h1>Tax invoice #: <?php echo $order['order']['id'] ?></h1>
			
			<div class="half-column left">
				<h5>Invoice to:</h5>
				<p>
					<?php echo $order['user']['first_name'] ?> <?php echo $order['user']['surname'] ?><br>
					<?php echo $order['user']['street'] ?><br>
					<?php echo $order['user']['city'] ?><br>
					<?php echo $order['user']['country'] ?><br>
					<?php echo $order['user']['zip'] ?><br>
					<?php echo $order['user']['telephone'] ?>
				</p>
			</div>
			
			<div class="half-column right">				
				<h5>Invoice from:</h5>
				<p>Iptica<br>
                                Oaktree Corner<br>
				Kruger St<br>
				Oaklands<br>
				Johannesburg<br>
				South Africa<br>
				Tel: +27 (0)11 483 1439<br>
				Fax: +27 (0)86 627 0055</p>
			</div>	
			
			<div class="clearall"></div>
			
			<table>
				<tbody>
					<?php $total = 0; foreach($order['regions'] as $id => $region): $total += $region['cost'][0] ?>
						<tr>
							<td>
								<?php echo $order['patent']['number'] ?> - 
								<?php echo $region['region_entity'] ?>
							</td>
							<td>
								<?php echo number_format($region['cost'][0], 2) ?>
								<small>
									<?php echo $order['currency'] ?>
								</small>
							</td>
						</tr>
					<?php endforeach ?>
					<?php if($order['vat']): ?>
						<!--
						<tr>
							<td>+1 Free South African Renewal</td>
							<td>
								0
								<small>
									<?php echo $order['currency'] ?>
								</small>
							</td>
						</tr>
						-->
						<tr class="vat">
							<td>+ VAT (<?php echo VAT * 100 ?>%)</td>
							<td>
								<?php echo number_format($order['total_vat'], 2) ?>
								<small>
									<?php echo $order['currency'] ?>
								</small>
							</td>
						</tr>
					<?php endif ?>
					<tr class="totals">
						<th>Total:</th>
						<th>
							<?php echo number_format($total, 2) ?>
							<small>
								<?php echo $order['currency'] ?>
							</small>
						</th>
					</tr>
				</tbody>				
			</table>
			
			<div class="clearall"></div>
			<table>
				<thead>
					<tr>
						<th colspan="<?php echo count($variables) ?>">
							<small>
							The above pricing is subject to the following
							</small>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php foreach($variables as $id => $label): ?>
							<td>
								<small>
									<?php echo $label ?>: <?php echo $order['patent'][$id] ?>
								</small>
							</td>
						<?php endforeach ?>
					</tr>
				</tbody>
			</table>
			
			<br>
			<h1>Thank you.<br>We will be contacting you shortly to process the filings.</h1>
	                <h2>Your order will be processed upon full payment</h2>		
			<br>
			<!-- <h3>Kindly transfer funds to:</h3>
			<p>Iptica Pty Ltd<br>
			Bank of America<br>
			New York Branch<br>
			Acc # 6220333333<br>
			Swift AXXXZZ</p>
			
			<p>Registration number: 232333434. <br>Directors: Anthony van Zantwijk, Jamie Paulos.</p>
			-->
			<hr>		
			
			<a href="javascript:window.print()" class="btn right">Print invoice</a>	
			
		</div><!-- end wrapper -->				
	</section><!-- end main-content -->	
	
	<?php include 'inc/footer.php' ?>
</body>
</html>
