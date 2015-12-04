<?php
require('../common.php');
require('_day_report.php');

$title = 'Tiker : ' . date('dS F, Y', strtotime($day));
$template->setTitle($title);

$template->addResource('http://localhost/Projects/Friendlee/bower_components/jquery-ui/ui/minified/jquery-ui.min.js','js',true);
render();
