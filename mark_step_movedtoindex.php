<?php
include 'inc/bootstrap.php';

if($_SESSION['LogoName'])
{
    
    
}
else
{
   $_SESSION['LogoName']=md5(time()); 
    
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
			<?php include 'inc/checkout_mark_steps.php' ?>
		
			<br/>
	                
			<form method="post" action="mark_step2.php" onsubmit="return checkOrder()" enctype="multipart/form-data">
            <br />
			<table >
	                           
                
                <tr>
                    <th>TradeMark Type</th>
                    <td><input  type="checkbox" name="TMType[]" value="WordMark" <?
                    if(is_array($_SESSION['mark_step1']['TMType'])){
                    reset($_SESSION['mark_step1']['TMType']);
                    foreach($_SESSION['mark_step1']['TMType'] as $val)
                    {
                      if($val=='WordMark') echo ' checked ';
                    }
                    }
                    ?>/> Work Mark</td>
                    <td><input  type="checkbox" name="TMType[]" value="Logo"
                    <?
                    if(is_array($_SESSION['mark_step1']['TMType'])){
                    reset($_SESSION['mark_step1']['TMType']);
                    foreach($_SESSION['mark_step1']['TMType'] as $val)
                    {
                      if($val=='Logo') echo ' checked ';
                    }
                    }
                    ?>                    
                    /> Logo</td>
                </tr>
                <tr>
                    <th>Word Mark</th>
                    <td><input  type="text" name="WordMark" value="<?=$_SESSION['mark_step1']['WordMark']?>"/> (brand name,product name,etc.)</td>
                    <td></td>
                </tr>

                <tr>
                    <th>Logo</th>
                    <td><input  type="file" name="MarkLogo" value=""/> <?if(is_file('tmp_img/'.$_SESSION['LogoName'].$_SESSION['LogoExtension'])):?> <img  src="tmp_img/<?echo $_SESSION['LogoName'].$_SESSION['LogoExtension']?>" class="small_logo"/><?endif?></td>
                    <td></td>
                </tr>
	
				
					
				
			</table>
			
			<input type="submit" class="btn right" value="Continue ›">
			</form>
			
		</div><!-- end wrapper -->				
	</section><!-- end main-content -->	
		
	<?php include 'inc/footer.php' ?>

</body>
</html>
