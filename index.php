<?php
include 'inc/bootstrap.php';

chdir('blog');
@include 'blog/admin/boot/feed.bit';
chdir('..');

//echo '<pre>'; print_r($posts); exit;

$_SESSION['order'] = array();
if($_SESSION['LogoName'])
{
    
    
}
else
{
   $_SESSION['LogoName']=md5(time()); 
    
}

?>
<!DOCTYPE HTML>
<html>
<head>
	<?php include 'inc/head.php' ?>
	<title>Iptica</title>
	<meta name="description" lang="en-us" content="national phase patent registration and renewal in emerging markets">
	<meta name="keywords" lang="en-us" content="patent, national phase patent, pct">
</head>
<body class="loading">
	<div id="loading-layer"></div>
	<div id="body">
	<section class="slider">
		<div class="flexslider">
	     	<ul class="slides">
		        <li class="one">				
					<div class="copy">
						<div class="wrapper">										
							<h2><strong>Discount Patent Filing,<br>Registration &amp; Renewal</strong><br> in African Countries.</h2>						
	<h3>UNDER CONSTRUCTION! NOT OPERATIONAL</h3>	
						</div><!-- end wrapper -->	
					</div><!-- end copy -->
				</li>		    	
		    </ul>
		</div>
	</section>
<!--tabs here ?-->
		<link rel="stylesheet" href="css/tabs.css">
		<link rel="stylesheet" href="css/tabs_main.css">
<div class="pcss3t pcss3t-effect-scale pcss3t-theme-1">
				<input type="radio" name="pcss3t" checked  id="tab1"class="tab-content-first">
				<label for="tab1"><i class="icon-bolt"></i>Register Patent</label>
				
				<input type="radio" name="pcss3t" id="tab2" class="tab-content-last">
				<label for="tab2"><i class="icon-picture"></i>Register Trademark</label>
				
					
				<ul>
					<li class="tab-content tab-content-first typography">
    <div class="patent-number">
		<div class="wrapper">
			<form action="step1.php" onsubmit="return patentSearch()" >
				<label>File your patent</label>
				<input type="text" id="search" placeholder="Enter your PCT/WO No">	
				<input type="submit" value="Go" class="btn">
				<!--<a href="#" class="btn" onclick="patentSearch()">Go</a> -->
			</form>
		</div><!-- end wrapper -->
	</div>					
    </li>

					
					<li class="tab-content tab-content-last ">
     <div class="register-trademark">
     <form method="post" action="mark_step1.php" onsubmit="return checkOrder()" enctype="multipart/form-data">
			<table >
                <tr>
                    <th>TradeMark Type</th>
                    <td>
                    
                    <input  type="checkbox" name="TMType[]" value="WordMark" <?
                    if(is_array($_SESSION['mark_step1']['TMType'])){
                    reset($_SESSION['mark_step1']['TMType']);
                    foreach($_SESSION['mark_step1']['TMType'] as $val)
                    {
                      if($val=='WordMark') echo ' checked ';
                    }
                    }
                    ?>/> Work Mark<input  type="checkbox" name="TMType[]" value="Logo"
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
                   
                </tr>

                <tr>
                    <th>Logo</th>
                    <td><input  type="file" name="MarkLogo" value=""/> <?if(is_file('tmp_img/'.$_SESSION['LogoName'].$_SESSION['LogoExtension'])):?> <img  src="tmp_img/<?echo $_SESSION['LogoName'].$_SESSION['LogoExtension']?>" class="small_logo"/><?endif?>
                    <input type="submit" class="btn  trademark" value="Continue"></td>
                </tr>
			</table>
			
			
			</form>	     
     </div>
					
					</li>
				</ul>
			</div>	
	<!-- end patent-number -->
	
	<!-- end patent-number -->
	
	<?php include 'inc/header.php' ?>
	
	<section class="main-content">
		<div class="wrapper">
			<section class="news-feeds">
				<h6>From our blog</h6>
				<?php foreach($posts as $post): ?>
					<article>
						<h3><a href="<?php echo $post['permalink'] ?>"><?php echo $post['title'] ?></a></h3>
						<p><?php echo substr(strip_tags($post['content'][0]), 0, 200) ?>...</p>
						<p><a href="<?php echo $post['permalink'] ?>">Read more &#8250;</a></p>
					</article>
				<?php endforeach ?>
			</section><!-- end news-feeds -->
	<!--		<section class="testimonial">
				<h6>What our clients say</h6>
				<!--
				<div class="image"><img src="img/photo.png" alt="John Tulman"></div>
				<div class="quote">
					<blockquote>
					In a short time Iptica has become one of the most valuable services we use. It is how we share and discuss the content we find daily.</blockquote>
					<p>- John Tulman, CEO & Founder</p>				
				</div>
				-->
			</section>--><!-- end testimonial -->			
		</div><!-- end wrapper -->				
	</section><!-- end main-content -->	
		
	<?php include 'inc/footer.php' ?>
	</div>
<style>
	#loading-layer { position: absolute; width: 100%; height: 100%; left: 0; top: 0;
		background: url('img/loading.gif') center 300px no-repeat; transition: 0.4s; opacity: 1; display: none; z-index: 200; }
	#body { transition: 0.4s; }
</style>
<script>
	$(function(){
		$('#search').focus();
		$('.flexslider').flexslider({
			animation: "slide",
			mousewheel: true,
			start: function(slider){
				$('body').removeClass('loading');
				}
			});
		while($('.patent-number label').height() > $('.patent-number').height()){
			$('.patent-number label').css('font-size', (parseInt($('.patent-number label').css('font-size')) - 1) + "px");
			}
		});
		$.getJSON('http://ip-api.com/json/', function(data){
			$.get('index_ajax_geo.php?cc=' + data['countryCode']);
			});
	function patentSearch(){
		if(!$('#search').val()){
			vex.dialog.alert('Please enter a patent');
			return false;
			}
		$('#loading-layer').show();
		$('#body').css('opacity', '0.2');
		var success = false;
		$.ajax({
			url: 'index_ajax_patent.php?search=' + encodeURI($('#search').val()),
			success: function(data){
				if(data == 'ok'){
					success = true;
					}
				else{
					vex.dialog.alert(data);
					}
				},
			async: false
			});
		if(!success){
			$('#loading-layer').hide();
			$('#body').css('opacity', '1');
			}
		return success;
		}
</script>

</body>
</html>
