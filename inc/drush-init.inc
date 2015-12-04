<?php
include_once 'class/drush.php';

echo('') . PHP_EOL;
message('Drush initialiseren !');

try {
  $drush = new drush($exec, $vhost['DocumentRoot']);
}catch (Exception $e) {
  message(sprintf('Bij initialiseren class drush, catch: %s !', $e->getMessage()), 'error');
  $error = true;
}