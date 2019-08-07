<?php
#BULK MOZ CHECKER 
ob_start();
error_reporting(E_ALL ^ E_NOTICE);
set_time_limit(0);

if (!$_POST['check']) {

   echo "<h2>Mass Da & Checker</h2><form method='POST'><textarea name='check' cols=100 rows=30></textarea><br><input type=submit></form>";
   exit;

}

$domains = explode("\n",trim($_POST['check']));

echo "<br><a href='http://moz.com' rel='nofollow'><img src='http://d2eeipcrcdle6.cloudfront.net/brand-guide/logos/moz_blue.png'></a><br>";
ob_flush(); flush();

$totaldomain = count($domains);
for ($i=0;$i<$totaldomain;$i=$i+10){
  $collectivedomains = array();
 
  for ($j=0;$j<10;$j++){
    $cur = $i + $j;
    $domain =  trim($domains[$cur]);
    array_push($collectivedomains,$domain);
  }
  get_da($collectivedomains,$i,$filenameda);    ob_flush(); flush();
  sleep(20);
  //exit;
}


function get_da($collectivedomains,$i,$filenameda){

   $accessid = 'mozscape-xxxxxxxxx'; // moz.com acces id detail https://moz.com/products/api/keys
    $secretkey = 'xxxxxxxxxxxxxxxxxx'; // moz.com secret key  detail https://moz.com/products/api/keys
  

    $expires = time() + 300;

    $stringToSign = $accessid."\n".$expires;

    $binarySignature = hash_hmac('sha1', $stringToSign, $secretkey, true);

    $urlSafeSignature = urlencode(base64_encode($binarySignature));

    $cols = "103616137252"; // url flags

    $request = "http://lsapi.seomoz.com/linkscape/url-metrics/?Cols=".$cols."&accessid=".$accessid."&Expires=".$expires."&Signature=".$urlSafeSignature;

    $encodedDomains = json_encode($collectivedomains);

    $options = array(
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_POSTFIELDS     => $encodedDomains
       );

    $ch = curl_init($request);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    curl_close( $ch );

    $contents = json_decode($content);
   $counter =0;
    foreach ($contents as $obj){
        $domain = $obj->uu;
        $da = $obj->pda;    $da = round($da,2);
        $pa = $obj->upa;    $pa = round($pa,2);
        if ($domain == "") $domain = trim($collectivedomains[$counter]);
       if ($domain == "") exit;
        echo "$domain|$da|$pa<br>";
       ob_flush(); flush();
        $counter++;
    }

}

?>