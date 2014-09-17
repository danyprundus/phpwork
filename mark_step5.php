<?php
include 'inc/bootstrap.php';


if(!isset($_POST['account'])){
	header('Location: mark_step4.php');
	exit;
	}

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
//add records in order table
$query = $db->prepare('SELECT * FROM currencies WHERE currency = ?');
$query->execute(array($order['currency']));
$query = $query->fetch(PDO::FETCH_ASSOC);
$order['exchange_rate'] = $query['value'];

if($_SESSION['LogoName'])
{
    $data['logo']=$_SESSION['LogoName'].$_SESSION['LogoExtension'];
    
}
$data['mark_step1']=$_SESSION['mark_step1'];
$data['mark_step2']=$_SESSION['mark_step2'];


$insert = $db->prepare('INSERT INTO orders (`user_id`, `isMark`, `currency`, `exchange_rate`,MarkDetail) VALUES (?, ?, ?, ?, ?)');
$insert->execute(array($order['user']['id'], 1, $order['currency'], $order['exchange_rate'],json_encode($data)));

$order['order']['id']=$db->lastInsertId();
//echo '<pre>'; print_r($_POST); exit;
$regions = $db->query('SELECT * FROM regions RIGHT JOIN region_entities ON region_id = regions.id')->fetchAll(PDO::FETCH_ASSOC);
$Max=0;
$RegionID=array();
if(is_array($_SESSION['mark_step2']['ClassToRegion'])){
while(list($key,$val)=each($_SESSION['mark_step2']['ClassToRegion']))
{
    if(count($val)>$Max)
    {
        $Max=count($val);
        $RegionID=$val;
        
    }
    
}
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
<pre>
<?
//print_r($_SESSION);
?>
</pre>		
	<?php include 'inc/header.php' ?>

	<section class="main-content process">
		<div class="wrapper">
			<?php include 'inc/checkout_mark_steps.php' ;
            $ClassToRegion=GetClassByRegion($_SESSION['mark_step2']['ClassToRegion'],$regions);
            ?>
			
			<h1>Tax invoice #: <?php echo $order['order']['id'] ?></h1>
			
			<div class="half-column left">
				<h5>Invoice to:</h5>
				<p>
					<?php echo $_SESSION['mark_step3']['AplicantName'] ?> <br>
                <?=$_SESSION['mark_step3']['BuildingNumber']?> <br /><?=$_SESSION['mark_step3']['StreetName']?><br /><?=$_SESSION['mark_step3']['City']?><br /><?=$_SESSION['mark_step3']['State']?><br /><?=$_SESSION['mark_step3']['Zip']	?>
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
			
			<table >
	                           
                
                <tr>
                    <th>Application Number</th>
                    <th>Region</th>
                    <th>Class</th>
                    <th>Cost</th>
                </tr>
                <?
                $total=0;
                for ($i=1;$i<=$Max;$i++):?>
                <tr>
                <td><?=$i?></td>
                <td><? 
                $id=array_shift($RegionID);
                $regionName='';
                foreach($regions as $val)
                {
                    if($val['id']==$id)
                    {
                        echo $regionName=$val['region'];
                        
                    }
                }
                ?></td>
                <td>
                <?
                // here I read the class of each region
                echo join(',',$ClassToRegion[$regionName]);
                ?>
                </td>
                <td><? 
               // print_r($ClassToRegion[$regionName]);
                reset($ClassToRegion[$regionName]);
                $tmp=array();
                foreach($ClassToRegion[$regionName] as $val3)
                {
                    $tmp[$val3]=$id;
                    
                }
                
                 $tmp= CalculateAmount($tmp,false);
                 echo number_format($tmp,2);
               
	$insert = $db->prepare('INSERT INTO order_entries (`order_id`, `region_entity_id`, `fee`) VALUES (?, ?, ?)');
	$insert->execute(array($order['order']['id'], $id, $tmp));

               
                $total+=$tmp;
                ?>	<small><?php echo $order['currency'] ?></small></td>
                
                </tr>
                <?endfor;?>
			<tr><td colspan="3"></td> <th>Total:<?=number_format($total,2)?><small><?php echo $order['currency'] ?></small></td></th></tr>
				
			</table>
			
			<div class="clearall"></div>
			
			
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
