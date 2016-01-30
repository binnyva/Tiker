<!DOCTYPE HTML>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $title?></title>
<link href="<?php echo $abs?>css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $abs?>images/silk_theme.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $abs?>js/library/calendar/calendar.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $config['site_url']; ?>bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $config['site_url']; ?>bower_components/bootstrap/dist/css/bootstrap-theme.min.css" rel="stylesheet">
<?php echo $css_includes?>
</head>
<body>
<div id="loading">loading...</div>

<div id="header" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
<div id="nav" class="container">
	<div class="navbar-header">
	  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
	    <span class="sr-only">Toggle navigation</span>
	    <span class="icon-bar"></span>
	    <span class="icon-bar"></span>
	    <span class="icon-bar"></span>
	  </button>
	  <a class="navbar-brand" href="<?php echo $config['site_url'];  ?>"><?php echo $config['site_title'] ?></a>
	</div>
	<div class="collapse navbar-collapse">
		<ul class="nav navbar-nav pull-right">
		<?php if(!empty($_SESSION['user_id'])) { ?>
		<li><form action="<?php echo $config['site_url']; ?>reports/generator.php" method="post" id="search-area" class="input-group input-group-sm">
		<input type="text" name="search" id="search" placeholder="Search Tasks..." value="<?php if(isset($QUERY['search'])) echo $QUERY['search'] ?>" class="form-control" />
		<span class="input-group-btn"><button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button></span>
		</form></li>
		<li><a class="home with-icon" href="<?php echo $abs?>">Dashboard</a></li>
		<li><a class="logout with-icon" href="<?php echo $abs?>user/logout.php">Logout</a></li>
		<?php } else { ?>
		<li><a class="add with-icon" href="<?php echo $abs?>user/register.php">Sign Up</a></li>
		<li><a class="key with-icon" href="<?php echo $abs?>user/login.php">Login</a></li>
		<?php } ?>
		</ul>
	</div>
</div>
</div>

<div id="content" class="container">

<div class="message-area" id="error-message" <?php echo ($QUERY['error']) ? '':'style="display:none;"';?>><?php
	if(!empty($PARAM['error'])) print strip_tags($PARAM['error']); //It comes from the URL
	else print $QUERY['error']; //Its set in the code(validation error or something).
?></div>
<div class="message-area" id="success-message" <?php echo ($QUERY['success']) ? '':'style="display:none;"';?>><?php echo strip_tags(stripslashes($QUERY['success']))?></div>

<br /><br /><br />
<!-- Begin Content -->
<?php 
/////////////////////////////////// The Template file will appear here ////////////////////////////

include($GLOBALS['template']->template); 

/////////////////////////////////// The Template file will appear here ////////////////////////////
?>

<!-- End Content -->
</div>

<div id="footer"></div>

<script src="<?php echo $abs?>bower_components/jquery/dist/jquery.js" type="text/javascript"></script>
<script src="<?php echo $abs?>js/application.js" type="text/javascript"></script>
<script src="<?php echo $abs?>js/library/calendar/calendar.js" type="text/javascript"></script>
<script type="text/javascript">
site_url = "<?php echo $config['site_url']; ?>";
</script>
<?php echo $js_includes?>
</body>
</html>
