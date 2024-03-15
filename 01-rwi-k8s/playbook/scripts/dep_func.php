<?PHP
    function _identify_playbook_home(){
	$dir=__dir__;
	$d=explode('/',$dir);
	$dc=count($d);
	    $lvl=0;
	    $path='';
	    while($lvl<(count($d) -1)){
	        //echo  $d[$lvl];
	        $path.=$d[$lvl].'/';
	       $lvl++;
	    }
	//echo "path=$path\n";
	return $path;	
    }

    function _identify_play_files(){   echo "Identify_play_files\n";
	$play_home=_identify_playbook_home();
	try{
	    if(!is_file($play_home.'deployment_install.yml')){
		echo $play_home." does not contain deployment_install.yml\n";
		throw new Exception();
			$pfs=false;
	    }else{
		$pfs[]=$play_home.'deployment_install.yml';
	    	$play=fopen($play_home.'deployment_install.yml','r');
		while(!feof($play)){
	 	    $line=fgets($play);
		    if(preg_match('/import_playbook: /',$line)){
			$p=explode(': ',$line);
			$plocal=trim($p[1]);
			if(is_file($play_home.$plocal)){
				$pfs[]=$play_home.$plocal;
			}
		    }
		}
		fclose($play);
	    }
        }
	catch(Exception $e){
		echo $e->getpfs();
		//$pfs=false;
	}
	finally{	
		return $pfs;
	}
    }
    
    function _identify_vars_files($dep_vars){
	if(!is_array($dep_vars)){
		$dep_vars=array();
	}
	$play_home=_identify_playbook_home();
	$pfs=_identify_play_files();
	$vfs=array();
	for($i=0;$i<count($pfs);$i++){
        	if(!is_file($pfs[$i])){
	                $message="no file found";
			echo $message."\n";
		}else{
		    echo "reading $pfs[$i]\n";
	            $play=fopen($pfs[$i],'r');
	            $add_line=false;
        	    while(!feof($play)){
                	$line=fgets($play);
	                if((!preg_match('/^- /',trim($line)))&&($add_line==false)){
        	            if(preg_match('/^vars_files:/',trim($line))){
                	        $add_line=true;
                    	    }else{
				$add_line=false;
			    }
                	}elseif((preg_match('/^- /',trim($line)))&&($add_line==true)){
			    $line=trim(str_replace('- ','',$line));
			    if(preg_match('/#/',$line)){
          			$l=explode('#',$line);
				$line = trim($l[0]);  
                    	    }
			    if(preg_match('/{{/', $line)){  ###>  This is a variable defining the variable file.
				$var=$line;
				$var=_replace_var($var,$dep_vars);
				
			    }
			    if(!is_file($line)){
				if(!is_file($play_home.$line)){
				    echo "File not found at [$line || $play_home$line].\n";
				}else{
				    if(!in_array($play_home.$line, $vfs)){
					$vfs[]=$play_home.$line;
					$dep_vars=_import_vars($play_home.$line,$dep_vars);
				    }
				}
			    }else{
			        if(!in_array($line, $vfs)){
                                    $vfs[]=$play_home.$line;
                                    $dep_vars=_import_vars($line,$dep_vars);

				}
					
			    }
                	}else{
 			    $add_line=false;
			}
            	    }
		    fclose($play);
        	}
	}
	return $dep_vars;
    }
    function _import_vars($file,$dep_vars){
	$vfs[]=$file;
	if(!is_array($dep_vars)){
		$dep_vars=array();
	}
	for($i=0;$i<count($vfs);$i++){
	    $vFile=fopen($vfs[$i],'r');
            while(!feof($vFile)){
                $line=trim(fgets($vFile));
                if(!preg_match('/^#/',$line)){
		    if(preg_match('/#/',$line)){
			$l=explode('#',$line);
			$line = trim($l[0]);
		    }
		    if(preg_match('/:/',$line)){
			$v=explode(': ',$line);
			if(preg_match('/{{/', $line)){
			    $value=_replace_var($v[1],$dep_vars);
			}else{
			    $value=$v[1];
			}
		 	$dep_vars[$v[0]]=trim($value);
		    }
		}
	    }
	}
	return $dep_vars;
    }
    function _replace_var($var,$dep_vars){
	$orig=$var;

	$rev=strrev($orig);
	$total=strlen($orig);
	$len1=strpos($orig,'{{');
	$len2=strpos($rev,'}}');
	$begin=substr($orig, $len1);
	$rev=strrev($begin);
	$end=substr($rev, $len2);
	$fin=strrev($end);
	$line=str_replace('{{','',$fin);
	$line=trim(str_replace('}}','',$line));
	//	$new_var=str_replace($fin,$a[$line],$orig);
	if(is_array($dep_vars)){
		//echo 'line='.$line."\n";
	    if($dep_vars[$line]!=''){		
	//	echo 'var='.$dep_vars[$line]."\n";
//		$value=(str_replace('{{ '.$var.' }}', $dep_vars[$var],$orig);
		$value=str_replace($fin,$dep_vars[$line],$orig);
	    }else{
		echo "Variable $fin is not defined.\n";
		$value=false;
    	    }
	}
	return $value;
    }
$dep_vars=array();
$dep_vars=_identify_vars_files($dep_vars);
foreach($dep_vars as $k => $v){
	echo $k.' = '.$v."\n";
}
?>
