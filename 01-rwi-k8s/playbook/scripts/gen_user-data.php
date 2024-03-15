#!/usr/bin/php
<?PHP
################################################################################################
###> New x php -> gen_user-data.php  -> Initial creation user => eric => 2024-02-26_05:10:30 ###
################################################################################################
###> This version reads an existing plaintext script in the same directory named user-data
###> it will replace the string S3_BUCKET with the actual bucket name passed in from the play
###> Then base64 encode the data into a file called user-data64
#_#>
$__dir__=__dir__;
#$constants='/home/eric/dep-1/constant/';

$s3_bucket=$argv[1]; ###> Passed in from play

$script=$__dir__.'/user-data.sh'; ###> Original script

$fh=fopen($script,'r'); ###> file handle
$orig_content=fread($fh,40000);  ###>  Read the stream
$orig_content=str_replace('S3_BUCKET',$s3_bucket,$orig_content); ###> define the current bucket

fclose($fh); ###> done reading closing file handle

#$fh=fopen($constants.'user-data.sh','r');
#$script_content=fread($fh,40000);

#fclose($fh);
#$script_content.="\n\n". str_replace('#!/bin/bash','##',$orig_content);

$script_content=$orig_content;

$encode64=base64_encode($script_content);

$fh64=fopen($__dir__.'/user-data.64','w'); ###> create or overwrite script called user-data64
try{
	if(!fwrite($fh64,$encode64)){
		throw new exception($e);
		$Message='Failed to write user-data.64';
	}
}
catch(Exception $e){
	echo $e->getMessage()."\n";
}
fclose($fh64);
?>
