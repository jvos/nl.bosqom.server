<?php
include_once 'class/apache2.php';
include_once 'inc/display_message.php';

echo('') . PHP_EOL;
display_message('Apache2 initialiseren !');
$apache2 = new apache2();
$vhosts = $apache2->getVirtualHostBySiteType('sites-enabled');