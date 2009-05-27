<?php
include("../common.php");
$html = new HTML;

$current_action = 'register';

if(isset($QUERY['username'])) {
	if($User->register($QUERY['username'], $QUERY['password'], $QUERY['name'], $QUERY['email'])) {
		showMessage("Welcome to $config[site_title], $_SESSION[user_name]!", "index.php");
	}
}

render();
