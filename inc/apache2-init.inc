<?php
include_once 'class/apache2.php';
include_once 'inc/message.php';

echo('') . PHP_EOL;
message('Apache2 initialiseren !');
$apache2 = new apache2();
$vhosts = $apache2->getVirtualHostBySiteType('sites-enabled');