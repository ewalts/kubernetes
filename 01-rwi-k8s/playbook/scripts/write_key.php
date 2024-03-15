#!/usr/bin/php
<?PHP
$key=$argv[2];
$h=fopen($argv[1],'w');
fwrite($h, $key);
fclose($h);
$cmd="cat $argv[1]";
exec($cmd,$out);
$cmd ="chmod 0600 $argv[1]";
exec($cmd,$out);

echo ($out[0]);
?>
