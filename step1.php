<?php
include 'inc/bootstrap.php';

if(!isset($_SESSION['order']['patent'])){
//	header('Location: index.php');
	//exit;
	}

if(isset($_POST) && count($_POST)){
	foreach($_POST as $k => $v){
		$_SESSION['order']['patent'][$k] = intval($v);
		}
	if($_SESSION['order']['patent']['independent_claims_num'] > $_SESSION['order']['patent']['claims_num']){
		$_SESSION['order']['patent']['independent_claims_num'] = $_SESSION['order']['patent']['claims_num'];
		}
	$_SESSION['order']['patent']['dependent_claims_num'] = $_SESSION['order']['patent']['claims_num'] - $_SESSION['order']['patent']['independent_claims_num'];
	}

$order = $_SESSION['order'];

$constants = array(
	'bank_spread' => BANK_SPREAD,
	'priority_claims' => $order['patent']['priority_claims_num'],
	'pages' => $order['patent']['pages_num'],
	'claims' => $order['patent']['claims_num'],
	'independent_claims' => $order['patent']['independent_claims_num'],
	);
foreach($db->query("SELECT * FROM currencies")->fetchAll(PDO::FETCH_ASSOC) as $rate){
	$constants[$rate['currency']] = $rate['value'];
	if($rate['currency'] == $order['currency']){
		$_SESSION['order']['exchange_rate'] = $exchange_rate = $rate['value'];
		}
	}


$regions = $db->query('SELECT * FROM regions RIGHT JOIN region_entities ON region_id = regions.id')->fetchAll(PDO::FETCH_ASSOC);
foreach($regions as $i => $region){
	
	$fees = array();
	foreach(range(1, 3) as $phase){
		$fees[$phase] = current($db->query("SELECT formula FROM fees WHERE phase = {$phase} AND region_entity_id = {$region['id']}")->fetch(PDO::FETCH_ASSOC));
		}
	
	$fee_amounts = $db->query("SELECT * FROM fee_amounts WHERE region_entity_id = {$region['id']}")->fetchAll(PDO::FETCH_ASSOC);
	foreach($fee_amounts as $fee_amount){
		if($order['vat'] && $fee_amount['fee_type_id'] == 9){
			$regions[$i]['vat'] = (substr_count($fees[1], '{9}')) ? eval('return ' . $fee_amount['formula'] . ';') * VAT / $exchange_rate : 0;
			}
		foreach(range(1, 3) as $phase){
			$fees[$phase] = str_replace("{{$fee_amount['fee_type_id']}}", "({$fee_amount['formula']})", $fees[$phase]);
			}
		}
	
	foreach(range(1, 3) as $phase){
		foreach($constants as $s => $r){
			$fees[$phase] = str_replace("{{$s}}", $r, $fees[$phase]);
			}
		$regions[$i]['cost'][$phase - 1] = eval('return ' . $fees[$phase] . ';') / $exchange_rate;
		}
	if($order['patent']['language'] != $region['lang']){
		$translation_cost = $db->query("SELECT * FROM translation_cost WHERE patent_lang = '{$order['patent']['language']}' AND region_lang = '{$region['lang']}'")->fetch(PDO::FETCH_ASSOC);
		$regions[$i]['cost'][0] += $order['patent']['words_num'] * (($translation_cost) ? $translation_cost['price_pw'] : DEFAULT_TRANSLATION_PRICE);
		}
	}

//echo '<pre>'; print_r($regions); exit;
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
			
			<h1><?php echo $order['patent']['number'] ?> <span>- "<?php echo $order['patent']['title'] ?>"</span></h1>
			
			<?php
			$sort_options = array(
                                '-' => '-',
				'gdp' => 'GDP',
				'population' => 'Population',
				'real_gdp_growth' => 'Real GDP growth',
				'population_growth' => 'Population growth',
				'gdp_per_population' => 'GDP / population',
				'median_age' => 'Median age',
				'health_expenditure_per_capita' => 'Health expenditure per capita',
				);
			?>
			<div class="filter">
				<label for="sort-choice">Ordered by</label>
				<select id="sort-choice" onchange="sortTable()">
					<?php foreach($sort_options as $id => $title): ?>
						<option value="<?php echo $id ?>"><?php echo $title ?></option>
					<?php endforeach ?>
				</select>
				
			<!--	<a href="#" class="hint hint--right" data-hint="Select sort criteria for country rank">?</a> -->
			</div><br/>
	                <b>* Total Filing Fee includes official, professional and priority claim fees</b>
			<form method="post" action="step2.php" onsubmit="return checkOrder()">
			<table class="countries">
				<thead>
					<tr>
						<th></th>
						<th>Countries</th>
						<th>Total Filing Fee*</th>
					</tr>
				</thead>
				<tbody>
					<?php $total = $total_vat = 0; foreach($regions as $i => $region): ?>
						<?php
							$checked = false;
							if(isset($order['regions'][$region['id']])){
								$checked = true;
								$total += $region['cost'][0];
								if($order['vat']){
									$total_vat += $region['vat'];
									}
								}
						?>
						<tr<?php if(0): ?> class="hilite"<?php endif ?> data-list="<?php echo 100 - $region['id'] ?>"<?php foreach($sort_options as $id => $title): ?> data-<?php echo $id ?>="<?php echo $region[$id] ?>"<?php endforeach ?>>
							<td>
								<span class="data"></span>
								<input type="checkbox" name="<?php echo $region['id'] ?>" value="<?php echo implode('|', $region['cost']) ?>" data-value="<?php echo $region['cost'][0] ?>"<?php if($order['vat']): ?> data-vat="<?php echo $region['vat'] ?>"<?php endif ?> onclick="orderSum()"<?php if($checked): ?> checked<?php endif ?>>
							</td>
							<td>
								<?php echo $region['region_entity'] ?>
								<?php foreach($sort_options as $id => $title): if($region[$id]): ?>
									<?php
										$val = $region[$id];
       										if (is_numeric($val)) {
											if($val < 100){
												$val = number_format($val, 2, '.', ' ');
												if(substr_count($id, 'growth')){
													$val .= '%';
												}
											} else {
												$val = number_format($val, 0, '', ' ');
											}
 										}
										?> 
									<a href="#" class="hint hint--right hint-multiline hint-<?php echo $id ?>" data-hint="<?php echo $title ?>: <?php echo $val ?>" style="display: none">?</a>
								<?php endif; endforeach ?>
                                                        </td>
							<td>
								<?php echo number_format($region['cost'][0], 2) ?>
								<small>
									<?php echo $order['currency'] ?>
								</small><!--
								<?php if($order['patent']['language'] != $region['lang']): ?>
									<img style="position: absolute; margin-left: 10px;" src="/img/Google-Translate-icon.png">
								<?php endif ?>
-->
							</td>
						</tr>
					<?php endforeach ?>
					<?php $sort_options['list'] = ''; $sort_options = array_keys($sort_options); ?>
					<?php if($order['vat']): ?>
						<!--
						<tr<?php foreach($sort_options as $id): ?> data-<?php echo $id ?>="<?php echo -1 ?>"<?php endforeach ?>>
							<td></td>
							<td>+1 Free South African Renewal</td>
							<td>
								0
								<small>
									<?php echo $order['currency'] ?>
								</small>
							</td>
						</tr>
						-->
						<tr<?php foreach($sort_options as $id): ?> data-<?php echo $id ?>="<?php echo -2 ?>"<?php endforeach ?>>
							<td></td>
							<td>+ VAT (<?php echo VAT * 100 ?>%)</td>
							<td>
								<span id="vat">
									<?php echo number_format($total_vat, 2) ?>
								</span>
								<small>
									<?php echo $order['currency'] ?>
								</small>
								<input type="hidden" name="vat" value="<?php echo $total_vat ?>">
							</td>
						</tr>
						<?php $total ?>
					<?php endif ?>
					<tr class="totals"<?php foreach($sort_options as $id): ?> data-<?php echo $id ?>="<?php echo -3 ?>"<?php endforeach ?>>
						<th></th>
						<th>Total:</th>
						<th>
							<span id="total">
								<?php echo number_format($total + $total_vat, 2) ?>
							</span>
							<small>
								<?php echo $order['currency'] ?>
							</small>
						</th>
					</tr>
				</tbody>
			</table>
			<!--
			<div class="coupon">
				<label for="name">Coupon code:</label>
				<input type="text" name="coupon" id="coupon" value=""/> <a href="#" class="hint hint--right" data-hint="Lorem ipsum dolor sit amet">?</a>
			</div>
			-->
			
			<input type="submit" class="btn right" value="Continue ›">
			</form>
			
		</div><!-- end wrapper -->				
	</section><!-- end main-content -->	
		
	<?php include 'inc/footer.php' ?>
<script>
	function formatMoney(n){
		return n.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
		}
	function sortTable(){
		$('table.countries > tbody > tr').tsort({
			'data': $('#sort-choice').val(),
			'order': 'desc'
			});
		$('table.countries .hint').hide();
		$('table.countries .hint-' + $('#sort-choice').val()).show();
		}
	function orderSum(){
		var sum = 0.00;
		var vat = 0.00;
		<?php if($order['vat']): ?>
			var vat = 0.00;
			$('table.countries input[type=checkbox]:checked').each(function(){
				vat += Number($(this).data('vat'));
				});
			$('#vat').html(formatMoney(vat));
			$('input[name=vat]').val(vat);
		<?php endif ?>
		$('table.countries input[type=checkbox]:checked').each(function(){
			sum += Number($(this).data('value'));
			});
		$('#total').html(formatMoney(sum + vat));
		}
	function checkOrder(){
		var valid = false;
		$('table.countries input[type=checkbox]:checked').each(function(){
			valid = true;
			});
		if(!valid){
			vex.dialog.alert('Must select at least one country');
			return false;
			}
		return true;
		}
	$(function(){
		<?php if(isset($_POST) && count($_POST)): ?>
			$('form').submit();
		<?php endif ?>
		$('#sort-choice').focus();
		});
sortTable();
</script>
</body>
</html>
