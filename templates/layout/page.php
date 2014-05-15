<!DOCTYPE HTML>
<html lang="en"><head>
<title><?php echo $title?></title>
<link href="<?php echo $abs?>css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $abs?>images/silk_theme.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $abs?>js/library/calendar/calendar.css" rel="stylesheet" type="text/css" />
<?php echo $css_includes?>
</head>
<body>
<div id="loading">loading...</div>
<div id="header">
<h1 id="logo"><a href="<?php echo $abs?>"><?php echo $config['site_title'] ?></a></h1>

<div id="navigation">
<?php if(!isset($_SESSION['user_id'])) { ?>
<a href="<?php echo $abs?>user/register.php">Sign Up</a> &nbsp; &nbsp;
<a href="<?php echo $abs?>user/login.php">Login</a>
<?php } else { ?>
<a href="<?php echo $abs?>">Dashboard</a> &nbsp; &nbsp;
<a href="<?php echo $abs?>user/logout.php">Logout</a>
<?php } ?>
</div>

<?php if(isset($_SESSION['user_id'])) { ?>
<div id="search">
<form action="<?php echo $abs ?>reports/generator.php" method="post">
<label for="search">Task Name</label><input type="text" name="search" id="search" value="<?php echo i($_REQUEST, 'search') ?>" />
<input type="submit" value="Generate Report" name="action" />
</form>
</div>
<?php } ?>

</div>

<!-- Begin Content -->
<div id="error-message" <?php echo ($QUERY['error']) ? '':'style="display:none;"';?>><?php
	if(i($PARAM, 'error')) print strip_tags($PARAM['error']); //It comes from the URL
	else print $QUERY['error']; //Its set in the code(validation error or something.
?></div>
<div id="success-message" <?php echo ($QUERY['success']) ? '':'style="display:none;"';?>><?php echo strip_tags(stripslashes($QUERY['success']))?></div>

<?php 
/////////////////////////////////// The Template file will appear here ////////////////////////////

include($GLOBALS['template']->template); 

/////////////////////////////////// The Template file will appear here ////////////////////////////
?>
<!-- End Content -->

<script src="<?php echo $abs?>bower_components/jquery/dist/jquery.js" type="text/javascript"></script>
<script src="<?php echo $abs?>js/application.js" type="text/javascript"></script>
<script src="<?php echo $abs?>js/library/calendar/calendar.js" type="text/javascript"></script>
<script type="text/javascript">
site_url = "<?php echo $config['site_url']; ?>";
</script>
<?php echo $js_includes?>
</body>
</html>
