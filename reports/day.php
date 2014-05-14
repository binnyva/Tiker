<?php
require('../common.php');
require('_day_report.php');

$title = 'Tiker : ' . date('dS F, Y', strtotime($day));
$template->setTitle($title);

render();
