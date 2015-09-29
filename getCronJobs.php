<?php


function getCronKeys(){
  // Usage without mysql_list_dbs()
  $link = mysql_connect('localhost', 'bosqom', '$$Bosqom60$$');
  $res = mysql_query("SHOW DATABASES");
  
  $array = array();
  while ($rows = mysql_fetch_assoc($res)) {
    $query = "SELECT value FROM `" .$rows['Database']. "`.drupal_variable WHERE name = 'cron_key'";
    $result = mysql_query($query, $link);
    if ($result) {
      $row = mysql_fetch_assoc($result);
      $value = unserialize($row['value']);
            
      $array[$rows['Database']] = array('database' => $rows['Database'], 'cron_key' => $value);
    }

    $query = "SELECT value FROM `" .$rows['Database']. "`.variable WHERE name = 'cron_key'";
    $result = mysql_query($query, $link);
    if ($result) {
      $row = mysql_fetch_assoc($result);
      $value = unserialize($row['value']);
      
      $array[$rows['Database']] = array('database' => $rows['Database'], 'cron_key' => $value);
    }
  }
  
  return $array;
}

function getVirtualHost(){
  # Get Vhosts files
  $path = '/etc/apache2/sites-enabled'; # change to suit your needs
  $a_directory = scandir($path);
  $a_conf_files = array_diff($a_directory, array('..', '.'));
  $info = array(); $x=0;

  foreach($a_conf_files as $conf_file){
   $Thisfile   = fopen($path .'/'.$conf_file, 'r')or die('No open ups..');

      while(!feof($Thisfile)){
          $line = fgets($Thisfile);
          $line = trim($line);

         // CHECK IF STRING BEGINS WITH ServerAlias
          $tokens = explode(' ',$line);

          if(!empty($tokens)){
              $tokens[1] = str_replace('"', "", $tokens[1]);
              $tokens[1] = str_replace("'", "", $tokens[1]);
              $tokens[1] = trim($tokens[1]);
              
              if(strtolower($tokens[0]) == 'servername'){
                  $info[$x]['ServerName'] = $tokens[1];
              }
              if(strtolower($tokens[0]) == 'documentroot'){
                  $info[$x]['DocumentRoot'] = $tokens[1];
              }
              if(strtolower($tokens[0]) == 'errorlog'){
                  $info[$x]['ErrorLog'] = $tokens[1];
              }
              if(strtolower($tokens[0]) == 'serveralias'){
                  $info[$x]['ServerAlias'] = $tokens[1];
              }
              

          }else{
              echo "Puked...";
          }
      }

  fclose($Thisfile);
  $x++;
  }
  
  return $info;

}

function getSettingsAndCombine($virualhosts, $cronkeys){
  $array = array();
  
  foreach ($virualhosts as $key => $virualhost){
    
    if (file_exists($virualhost['DocumentRoot'] . '/sites/default/settings.php')) {
      include ($virualhost['DocumentRoot'] . '/sites/default/settings.php');
      
      /*echo('$databases:<pre>');
      print_r($databases);
      echo('</pre>');*/
      
      if(!empty($virualhost['ServerName'])){
        $array[$virualhost['ServerName']] = array(
          'database' => $databases['default']['default']['database'],
          'root' => $virualhost['DocumentRoot'],
          'error_log' => $virualhost['ErrorLog'],
          'server_name' => $virualhost['ServerName'],
          'cron_key' => $cronkeys[$databases['default']['default']['database']]['cron_key'],
        );
      }else {
        $array[] = array(
          'database' => $databases['default']['default']['database'],
          'root' => $virualhost['DocumentRoot'],
          'error_log' => $virualhost['ErrorLog'],
          'server_name' => $virualhost['ServerName'],
          'cron_key' => $cronkeys[$databases['default']['default']['database']]['cron_key'],
        );
      }
    }
  }
  
  ksort($array);
  return $array;
}

$cronkeys = getCronKeys();
echo('$cronkeys:<pre>');
print_r($cronkeys);
echo('</pre>');

$virualhosts = getVirtualHost();
echo('$virualhosts:<pre>');
print_r($virualhosts);
echo('</pre>');

$all = getSettingsAndCombine($virualhosts, $cronkeys);
echo('$all:<pre>');
print_r($all);
echo('</pre>');

echo('Aantal: ' . count($all)) . '<br/>' . PHP_EOL;

foreach($all as $key => $website){
  // /usr/bin/wget -O - -o /dev/null http://www.duqer.nl/cron.php?cron_key=ojLbiA2_sCpyGGD8WJ2OSWUOJ-_Sjz0kSoQYlF3s6jQ >/dev/null 2>&1
  echo('/usr/bin/wget -O - -o /dev/null ' . 'http://'. $website['server_name'] . '/cron.php?cron_key=' . $website['cron_key'] . ' >/dev/null 2>&1') . PHP_EOL;
}