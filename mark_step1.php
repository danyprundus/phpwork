<?php
include 'inc/bootstrap.php';


//echo '<pre>'; print_r($regions); exit;
//we need regions
$regions = $db->query('SELECT * FROM regions RIGHT JOIN region_entities ON region_id = regions.id')->fetchAll(PDO::FETCH_ASSOC);
//we need classes
$classes= $db->query('SELECT * FROM classes ')->fetchAll(PDO::FETCH_ASSOC);
//store all POST into Sessions
if($_POST){
$_SESSION['mark_step1']=$_POST;    
}
//here I generate random Image Name 

?>
<!DOCTYPE HTML>
<html>
<head>
	<?php include 'inc/head.php' ?>
	<title>Iptica</title>
	<meta name="description" lang="en-us" content="Iptica">
	<meta name="keywords" lang="en-us" content="Iptica">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
    
</head>
<body>
<pre>
<?
if($_FILES['MarkLogo']['name'])
{
$LogoExtension='.'.pathinfo($_FILES['MarkLogo']['name'], PATHINFO_EXTENSION);
$_SESSION['LogoExtension']=$LogoExtension;
    move_uploaded_file($_FILES['MarkLogo']['tmp_name'],'tmp_img/'.$_SESSION['LogoName'].$_SESSION['LogoExtension']);
}


?>
</pre>
	<?php include 'inc/header.php';
     ?>
	
	<section class="main-content process">
		<div class="wrapper">
			<?php include 'inc/checkout_mark_steps.php' ?>
		
			<h1>Exact match search <?=$_POST['WordMark']?></h1>
	                
			<form method="post" action="mark_step2.php" onsubmit="return checkOrder()">
            <br />
            <div style="float: left; width: 750px;;">
            			<table >
	                           
                
                <tr>
                    <th>Classes</th>
                    <th>Regions </th>
                    
                    <?foreach($regions  as $val):?>
                    <th><?=$val['region']?></th>
                    <?endforeach?>
                </tr>
				<? foreach($classes as $val):?>
                <tr>
                    <td><?=$val['ClassName']?></td>
                    <td></td>
                    
                    <?
                    reset($regions);
                    foreach($regions  as $val1):?>
                    <td><input type="checkbox" class="ClassToRegion" name="ClassToRegion[<?=$val['ID']?>][<?=$val1['id']?>]" value="<?=$val1['id']?>"
                     <?
                     if(is_array($_SESSION['mark_step2']['ClassToRegion'][$val[ID]])){
                     foreach($_SESSION['mark_step2']['ClassToRegion'][$val[ID]] as $val2){
                        if($val2==$val1['id']) echo ' checked ';    
                        
                     }
                     }
                    ?>
                    ></td>
                    <?endforeach?>
                </tr>
                
                <?endforeach;?>	
				
			</table>
            <input type="submit" class="btn right" value="Continue ›">
            </div>
			<div style="float: right; margin-top: 10px;;" id="Cost">123</div>
			
			</form>
			
		</div><!-- end wrapper -->				
	</section><!-- end main-content -->	
<div id="dialog" title="Error" >
    <p>You must select at least one class</p>
</div>

<script>
DoMath();
function checkOrder()
{
    //we must select at least ONE item
  var Found=false;
    $('.ClassToRegion').each(function(){
        
    //alert($(this).prop('checked'));
    //create a list of classes 
    
    if($(this).prop('checked')){
    Found=true;
        
    }

    
    }); 
    if(!Found){
        $( "#dialog" ).dialog();
        
    }   
    return Found;
}
$('.ClassToRegion').click(function(){
DoMath();
})
function DoMath(){
    
    var ClassesSelected='';
    $('.ClassToRegion').each(function(){
        
    //alert($(this).prop('checked'));
    //create a list of classes 
    
    if($(this).prop('checked')){
    ClassesSelected=ClassesSelected+','+$(this).attr('name');
        
    }

    
    });
//do ajax call
        $.post( "ajax/ajax.php", { action: "calculate", classes: ClassesSelected})
        .done(function( data ) {
            $("#Cost").html(data);    
        });

    
    

}
</script>		
	<?php include 'inc/footer.php' ?>

</body>
</html>
