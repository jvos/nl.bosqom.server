<?php
include_once 'class/drush.php';

echo('') . PHP_EOL;
display_message('Drush initialiseren !');

try {
  $drush = new drush($exec, $vhost['DocumentRoot']);
}catch (Exception $e) {
  display_message(sprintf('Bij initialiseren class drush, catch: %s !', $e->getMessage()), 'error');
  $error = true;
}