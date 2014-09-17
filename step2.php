<?php
include 'inc/bootstrap.php';

if(!isset($_SESSION['order']['patent'])){
	header('Location: /');
	exit;
	}

if(isset($_POST) && count($_POST)){
	$_SESSION['order']['regions'] = array();
	foreach($_POST as $id => $cost){
		if($id == 'vat'){
			$_SESSION['order']['total_vat'] = $cost;
			}
		else{
			$query = $db->prepare('SELECT * FROM region_entities WHERE id = ?');
			$query->execute(array($id));
			$_SESSION['order']['regions'][$id] = $query->fetch(PDO::FETCH_ASSOC);
			$_SESSION['order']['regions'][$id]['cost'] = explode('|', $cost);
			}
		}
	}
elseif(!isset($_SESSION['order']['regions']) || !count($_SESSION['order']['regions'])){
	header('Location: /step1.php');
	exit;
	}

$patent_fields = array(
	'number' => 'PCT Patent No.',	//W003234
	'date' => 'PCT Filing Date',	//23rd Nov 2013
	'priority_claim' => 'Priority Claim',	//ZA20061452
	'priority_date' => 'Priority Date',	//18th Nov 2013
	'applicants' => 'Applicant/s',	//John Stokes - 123 Howard Rd, Birdhaven, Statford, USA 9434
	'inventors' => 'Inventor/s',	//John Stokes - 123 Howard Rd, Birdhaven, Statford, USA 9434</td>
	);
$edit_fields = array(
	'pages_num' => 'No. Pages',
	'words_num' => 'No. Words',
	'priority_claims_num' => 'No. Priority Claims',
	'independent_claims_num' => 'No. Independent Claims',
	'dependent_claims_num' => 'No. Dependent Claims',
	'claims_num' => 'No. Total Claims'
	);
$patent_fields = array_merge($patent_fields, $edit_fields);
//echo '<pre>'; print_r($_SESSION); exit;

$order = $_SESSION['order'];
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

<style>
	.vex-dialog-button { display: none !important; }
</style>
<div style="display: none">
	<div id="edit">
		<form action="step1.php" method="post">
		<table>
			<tbody>
				<?php foreach($edit_fields as $id => $label): if($id != 'dependent_claims_num'): ?>
					<tr>
						<td>
							<?php echo $label ?>:
						</td>
						<td>
							<input type="text" name="<?php echo $id ?>" value="<?php echo $order['patent'][$id] ?>" style="float: right; text-align: right; width: 100px;" required>
						</td>
					</tr>
				<?php endif; endforeach ?>
				<tr>
					<td colspan="2">
						<input type="submit" value="Update">
					</td>
				</tr>
			</tbody>
		</table>
		</form>
	</div>
</div>
	<?php include 'inc/header.php' ?>
	
	<section class="main-content process">
		<div class="wrapper">
			<?php include 'inc/checkout_steps.php' ?>
			
			<a href="#" onclick="vex.dialog.alert($('#edit').html());return false;">
				<img src="img/edit.png" alt="Edit" style="float: right; margin-top: 20px;">
			</a>
			<h1>Order Summary</h1>			
						
			<table class="summary">
				<tbody>
					<?php foreach($patent_fields as $id => $label): ?>
						<tr>
							<td><?php echo $label ?>:</td>
							<td><?php echo nl2br($order['patent'][$id]) ?></td>
						</tr>
					<?php endforeach ?>
				</tbody>				
			</table>
			<div style="text-align: right">
				<small>
					Wrong data? <a href="#" onclick="vex.dialog.alert($('#edit').html());return false;">Click here</a> to correct it.
				</small>
			</div>
			<br>
			<table>
				<thead>
					<tr>
						<th colspan="2">Filing Cost</th>
					</tr>
				</thead>
				<tbody>
					<?php $total = 0; foreach($order['regions'] as $id => $region): $total += $region['cost'][0] ?>
						<tr>
							<td>
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
						<tr>
							<td>+ VAT (<?php echo VAT * 100 ?>%)</td>
							<td>
								<?php echo number_format($order['total_vat'], 2) ?>
								<small>
									<?php echo $order['currency'] ?>
								</small>
							</td>
						</tr>
						<?php $total += $order['total_vat'] ?>
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
			
			<a href="step3.php" class="btn right">Confirm Order ›</a>
			
			<div class="clearall"></div>
			
			<h4>Estimated Future Costs <a href="#" class="hint hint--right hint-multiline" data-hint="Below is the minimum cost that you can expect to incur during prosecution of the patent. These costs can increase considerably if major objections are raised by examiners.">?</a></h4>
			
			<table class="future-costs">
				<thead>
					<tr>
						<th>Country</th>
						<th>
							Examination (6-18 months)
							<a href="#" class="hint hint--right hint-multiline" data-hint="Minimum anticipated cost incurred between filing and grant of the patent, presuming that no responses to office actions or amendments are required">?</a>
						</th>
						<th>
							Grant
							<a href="#" class="hint hint--right" data-hint="Cost to publish the patent (incl. grant fees)">?</a>
						</th>
						<th>1st Renewal</th>
					</tr>
				</thead>
				<tbody>					
					<?php foreach($order['regions'] as $id => $region): ?>
						<tr>
							<td>
								<?php echo $region['region_entity'] ?>
							</td>
							<td>
								<?php echo number_format($region['cost'][1], 2) ?>
								<small>
									<?php echo $order['currency'] ?>
									<a href="#" class="hint hint--right" data-hint="<?php $region['examination_comment']?>">?</a>
								</small>
							</td>
							<td>
								<?php echo number_format($region['cost'][2], 2) ?>
								<small>
									<?php echo $order['currency'] ?>
								</small>
							</td>
							<td>
								<?php echo number_format($region['first_renewal'] / $order['exchange_rate'], 2) ?>
								<small>
									<?php echo $order['currency'] ?>
									(<?php echo $order['patent']['filing_year'] + $region['annuity_number'] ?>)
								</small>
							</td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
			
		</div><!-- end wrapper -->				
	</section><!-- end main-content -->	
		
	<?php include 'inc/footer.php' ?>
</body>
</html>
