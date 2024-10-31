<?php

function nsx_referers_stat()
{
  global $wpdb;

  $hosts['google.']['q'] = 'q';
  $hosts['google.']['ek'] = 'ie';
  $hosts['google.']['ev'] = 'windows-1251';
  $hosts['altavista.com']['q'] = 'q';
  $hosts['yahoo.com']['q'] = 'p';
  $hosts['bing.com']['q'] = 'q';
  $hosts['redtram.com']['q'] = 'q';
  $hosts['yandex.']['q'] = 'text';
  $hosts['rambler.ru']['q'] = 'query';
  $hosts['webalta.ru']['q'] = 'q';
  $hosts['aport.ru']['q'] = 'r';
  $hosts['go.mail.ru']['q'] = 'q';
  $hosts['nigma.ru']['q'] = 's';

  $referer = $_SERVER['HTTP_REFERER'];
  $ref_arr = parse_url($referer);
  $ref_host = $ref_arr['host'];

  foreach($hosts as $host => $host_opt)
  {
    if (strpos($ref_host, $host) !== false)
    {
      $find_host = true;
      break;
    }
  }  

function isUTF8($s)
{
  if (preg_match('/([\xc0-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xf7][\x80-\xbf]{3})/x',$s))
  return true; else return false; 
}    

function isWIN1251($s)
{
  if (!isUTF8($s))
  if (preg_match('/[à-ÿ]+/i',$s))
  return true; else return false; 
}  

  $search = '';
  if ($find_host)
  {
    parse_str($ref_arr['query'], $query_arr);
    $search = $query_arr[$host_opt['q']];
    
    $need_conv = false;
    if ($host == 'go.mail.ru') {
       $need_conv = true;
       if (isUTF8($search)) $need_conv = false;
    }

    if ($host == 'yandex.') {
       if (isWIN1251($search)) $need_conv = true;
    }
    
    if ($host == 'google.') {
       if (isset($query_arr['ie']))
         if ($query_arr['ie'] == 'windows-1251') $need_conv = true;
    }

    if ($need_conv) $search= @iconv("windows-1251", "utf-8", $search);  
    $search = trim($wpdb->escape($search));  
  }
  
  $s = $_SERVER['HTTP_REFERER'];

  $url = $_SERVER['REQUEST_URI'];
   
  if (strlen($search) > 2)
  {

    $wpdb->query(
    "UPDATE ".REFTABLE." 
    SET hits = hits + 1
    WHERE url = '$url' and search = '$search'");
  
    if ($wpdb->rows_affected < 1)
    $wpdb->query(
    "INSERT INTO ".REFTABLE." 
    VALUES
    ('', '$url', 'NULL', '', '$search', 1)");
  }

}

?>