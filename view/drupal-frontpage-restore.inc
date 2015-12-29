<?php
echo('') . PHP_EOL;
message('Zet de standaardvoorpagina terug !');
if(!$drupal->changeSiteFrontpageTemporary($drupal->getSiteFrontPage())){
  message('Bij het legen van standaardvoorpagina !', 'error');
  $error = true;
}else {
  message(sprintf('Standaardvoorpagina terug gezet naar: %s !', $drupal->getSiteFrontPage()), 'success');
}

