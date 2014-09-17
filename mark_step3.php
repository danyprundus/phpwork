<?php
include 'inc/bootstrap.php';

$order['currency']='USD';  
//echo '<pre>'; print_r($regions); exit;
//we need regions
$regions = $db->query('SELECT * FROM regions RIGHT JOIN region_entities ON region_id = regions.id')->fetchAll(PDO::FETCH_ASSOC);
//we need classes
$classes= $db->query('SELECT * FROM classes ')->fetchAll(PDO::FETCH_ASSOC);
//store all POST into Sessions
if($_POST){
$_SESSION['mark_step3']=$_POST;    
$CurrentValues=$_SESSION['mark_step3'];
}

//detect max of regions selected
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

//echo $Max;
?>
<!DOCTYPE HTML>
<html>
<head>
	<?php include 'inc/head.php' ?>
	<title>Iptica</title>
	<meta name="description" lang="en-us" content="Iptica">
	<meta name="keywords" lang="en-us" content="Iptica">
</head>
<body><pre>
<?
//print_r($CurrentValues);
?>
</pre>
	<?php include 'inc/header.php';
     ?>
	
	<section class="main-content process">
		<div class="wrapper">
			<?php include 'inc/checkout_mark_steps.php';
            $ClassToRegion=GetClassByRegion($_SESSION['mark_step2']['ClassToRegion'],$regions);
             ?>
		
			<h1>Order Summary</h1>
	                
			<form method="post" action="mark_step4.php" onsubmit="return checkOrder()">
            <table>
            <tr><th>Mark</th><td><?=$_SESSION['mark_step1']['WordMark']?> </td><td><img  src="tmp_img/<?echo $_SESSION['LogoName'].$_SESSION['LogoExtension']?>" class="small_logo"/></td></tr>
            <tr><th>Applicant Name</th><td colspan="2"><?=$_SESSION['mark_step3']['AplicantName']?></td></tr>
            <tr><th>Address</th><td colspan="2"><?=$_SESSION['mark_step3']['BuildingNumber']?> <?=$_SESSION['mark_step3']['StreetName']?>,<?=$_SESSION['mark_step3']['City']?>,<?=$_SESSION['mark_step3']['State']?>,<?=$_SESSION['mark_step3']['Zip']?></td></tr>
            
            </table>
            
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
                
                $total=$total+CalculateAmount($tmp);
                ?>	<small><?php echo $order['currency'] ?></small></td>
                
                </tr>
                <?endfor;?>
			<tr><td colspan="3"></td> <th>Total:<?=number_format($total,2)?><small><?php echo $order['currency'] ?></small></td></th></tr>
				
			</table>
			
			<input type="submit" class="btn right" value="Confirm Order ›">
			</form>
			
		</div><!-- end wrapper -->				
	</section><!-- end main-content -->	
	<?
    ?>	
	<?php include 'inc/footer.php' ?>

</body>
</html>
