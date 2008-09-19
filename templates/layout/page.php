<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head>
<title><?=$title?></title>
<link href="<?=$abs?>css/style.css" rel="stylesheet" type="text/css" />
<script src="<?=$abs?>js/jsl.js" type="text/javascript"></script>
<script src="<?=$abs?>js/application.js" type="text/javascript"></script>
<?=$includes?>
</head>
<body>
<div id="header">
<h1 id="logo"><a href="<?=$abs?>"><?=$title?></a></h1>
</div>

<!-- Begin Content -->
<?php 
/////////////////////////////////// The Template file will appear here ////////////////////////////

include($GLOBALS['template']->template); 

/////////////////////////////////// The Template file will appear here ////////////////////////////
?>
<!-- End Content -->


</body>
</html>