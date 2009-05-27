<?php
include("../common.php");

$User->logout();
showMessage("User logged out.", "user/login.php");
