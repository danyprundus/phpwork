<?php
include("functions.php");
$nav = array(
	'index.php' => 'Home',
	'about.php' => 'About',
	'howto.php' => 'How To',
	'blog/index.php' => 'Blog',
	'contact.php' => 'Contact',
	);
?>
<header>
	<div class="wrapper">
		<a href="/" title="home"><img src="img/iptica.png" alt="Iptica" class="logo"/></a>
		<section class="main-nav">
			<nav>
				<ul class="clearfix">
					<?php foreach($nav as $href => $title): ?>
						<li<?php if(substr($_SERVER['SCRIPT_NAME'], 1) == $href): ?> class="current"<?php endif ?>>
							<a href="/<?php echo $href ?>"><?php echo $title ?></a>
						</li>
					<?php endforeach ?>
				</ul>
				<a href="#" id="pull">Menu</a>
			</nav>
		</section><!-- end main-nav -->
	</div><!-- end wrapper -->
</header><!-- end header -->
