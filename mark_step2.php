<?php
include 'inc/bootstrap.php';


//echo '<pre>'; print_r($regions); exit;
//store all POST into Sessions
if($_POST)
{
$_SESSION['mark_step2']=$_POST;    
}
$CurrentValues=$_SESSION['mark_step3'];

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
			<?php include 'inc/checkout_mark_steps.php' ?>
		
			<h1>Mark Info</h1>
	                
			<form method="post" action="mark_step3.php" onsubmit="return checkOrder()">
            <br />
			<table >
	                           
                
                <tr>
                    <th>Mark </th>
                    <th><?=$_SESSION['mark_step1']['WordMark']?></th>
                    <th><img  src="tmp_img/<?echo $_SESSION['LogoName'].$_SESSION['LogoExtension']?>" class="small_logo"/></th>
                    <th></th>
                </tr>
                <tr>
                    <th>Description of your Goods/Services</th>
                    <td colspan="3"><textarea name="GoodsDescription"><?=$CurrentValues['GoodsDescription']?></textarea></td>
                    
                </tr>

                <tr>
                    <th>Aplicant Name</th>
                    <td><input  type="text" name="AplicantName" value="<?=$CurrentValues['AplicantName']?>"/> </td>
                    <td></td><td></td>
                </tr>
	
                <tr>
                    <th>Building Number</th>
                    <td><input  type="text" name="BuildingNumber" value="<?=$CurrentValues['BuildingNumber']?>"/> </td>
                    <th>Street Name</th>
                    <td><input  type="text" name="StreetName" value="<?=$CurrentValues['StreetName']?>"/></td>
                </tr>
	
                <tr>
                    <th>City</th>
                    <td><input  type="text" name="City" value="<?=$CurrentValues['City']?>"/> </td>
                    <th>State</th>
                    <td><input  type="text" name="State" value="<?=$CurrentValues['State']?>"/></td>
                </tr>
                <tr>
                    <th>Country</th>
                    <td><input  type="text" name="Country" value="<?=$CurrentValues['Country']?>"/> </td>
                    <th>Zip/PostalCode</th>
                    <td><input  type="text" name="Zip" value="<?=$CurrentValues['Zip']?>"/></td>
                </tr>
	
	
				
					
				
			</table>
			
			<input type="submit" class="btn right" value="Continue ›">
			</form>
			
		</div><!-- end wrapper -->				
	</section><!-- end main-content -->	
		
	<?php include 'inc/footer.php' ?>

</body>
</html>
