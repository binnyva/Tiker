<?php 
$extra = array();
if($current_action == 'profile') $extra = array('readonly'=>'readonly');
$html->buildInput("username", "Username", "text", i($PARAM,'username'), $extra); ?>

<?php $html->buildInput("password", "Password", "password", i($PARAM,'password')); ?>
<?php $html->buildInput("confirm_password", "Confirm Password", "password", i($PARAM,'confirm_password')); ?>

<h3>Optional</h3>
<?php $html->buildInput("name", "Name", "text", i($PARAM,'name')); ?>
<?php $html->buildInput("email", "Email", "text", i($PARAM,'email')); ?>
