<?php
$layout = 'page';

if(i($QUERY,'layout') == 'cli') { //If the script is called from the command line
	$layout = 'cli';
	if(!isset($_SESSION['user_id'])) { //Single user
		if(isset($PARAM['auth_username'])) {
			$user_id = $User->login($PARAM['auth_username'], $PARAM['auth_password']);
			
			if(!$user_id) {
				print t('Invalid User/Password.')."\n";
				exit;
			}
		}
	}
}

checkUser();
