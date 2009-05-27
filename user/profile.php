<?php
include("../common.php");
$html = new HTML;

checkUser();
$current_action = 'profile';

$category_id_list = getCategoryTree();

if(isset($_REQUEST['name'])) {
 	if($User->update($_SESSION['user_id'], $QUERY['password'], $QUERY['name'], $QUERY['email'], 
 			$QUERY['url'], $QUERY['anchor_text'], $QUERY['rss'], $QUERY['company'], $QUERY['description'], $QUERY['phone'], $QUERY['category_id'])) {
 		$QUERY['success'] = "Profile Updated";
 	}
}
$PARAM = $User->find($_SESSION['user_id']);
unset($PARAM['password']);

render();
