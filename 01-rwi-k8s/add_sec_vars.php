#!/usr/bin/php
<?PHP
###############################################################################################
###> New x php -> add_sec_vars.php  -> Initial creation user => eric => 2024-03-11_21:28:41 ###
###############################################################################################
#_#>

###> CLI colors
# $Red='\e[0;31m'; $BRed='\e[1;31m'; $BIRed='\e[1;91m'; $Gre='\e[0;32m'; $BGre='\e[1;32m'; $BBlu='\e[1;34m'; $BWhi='\e[1;37m'; $RCol='\e[0m';
$__dir__=__dir__;



function _linesinarray($fObj){
	while($line=fgets($fObj,4096)){
		$l[]=$line;
	}
	return $l;
}

function _insert_sec_vars($l,$file){ // $l numerical, single dimension array of lines
	$page='';
	for($i=0;$i<count($l);$i++){
		if(preg_match('/vars_files/',$l[$i])){
			$page.=$l[$i];
			$page.="    - vars/sec_vars.yml\n";
			echo "  Adding sec_vars.yml to to $file.\n";
		}else{
			$page.=$l[$i];
		}
	}
	return $page;
}
$parent_dir=$argv[1];
$p=opendir($parent_dir);
while($file=readdir($p)){
	if(preg_match('/.yml/',$file)){
		if(is_file($parent_dir.$file)){
			$yml_files[]=$parent_dir.$file;
		}
	}
}
closedir($p);
$yf=$yml_files;
for($i=0;$i<count($yf);$i++){
	$rf=fopen($yf[$i],'r');
	$l=_linesinarray($rf);
        fclose($rf);
	$page=_insert_sec_vars($l,$yf[$i]);

	$wf=fopen($yf[$i],'w');
	if(fwrite($wf,$page)===false) {
		echo "Cannot write to file $yf[$i]\n";
	}
	fclose($wf);

}

?>
