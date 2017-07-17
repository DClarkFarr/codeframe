<?php 

function arrays_add(...$xs){
	$arr = [];
	foreach($xs as $x){
		if(!is_array($x)){
			continue;
		}
		foreach($x as $v){
			$arr[] = $v;
		}
	}
	return $arr;
}