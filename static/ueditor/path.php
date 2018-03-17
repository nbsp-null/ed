<?php
	$a=$_SERVER['SCRIPT_NAME'];
	$arr=explode('/',$a);
	foreach  ($arr as $k=>$v){
			if($arr[$k]=='path.php'){
				$a=$k;
			}
	}
	$b='';
	for($i=0;$i<=$a-1;$i++){
		$b.=$arr[$i].'/';
	}
	if($b==null)$b='/';
	echo $b;
die;
