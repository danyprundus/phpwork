<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="robots" content="all,index,follow">

<link rel="icon" href="favicon.ico" type="image/x-icon">

<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
<link rel="stylesheet" href="css/tooltip.css">
<link rel="stylesheet" media="print" href="css/print.css">
<link rel="stylesheet" href="css/styles.css" media="screen,projection">

<script src="js/jquery.min.js"></script>

<link rel="stylesheet" href="css/flexslider.css">
<script defer src="js/jquery.flexslider-min.js"></script> 

<link rel="stylesheet" href="css/vex.css">
<link rel="stylesheet" href="css/vex-theme-default.css">
<script src="js/vex.combined.min.js"></script>
<script> vex.defaultOptions.className = 'vex-theme-default'; </script>
<style> .vex-dialog-buttons input { background-color: rgb(51, 166, 111) !important; } </style>


<script src="js/modernizr.js"></script>

<script src="js/jquery.tinysort.min.js"></script> 

<script src="js/css3-mediaqueries.js"></script>

<script>
/*	Responsive Menu (http://www.hongkiat.com/blog/responsive-web-nav/)*/
$(function() {
	var pull 		= $('#pull');
		menu 		= $('nav ul');
		menuHeight	= menu.height();
	$(pull).on('click', function(e) {
		e.preventDefault();
		menu.slideToggle();
	});
	$(window).resize(function(){
		var w = $(window).width();
		if(w > 320 && menu.is(':hidden')) {
			menu.removeAttr('style');
		}
	});
});

</script>
