<?php

function coalesce(...$xs){
	foreach($xs as $x){
		if($x){
			return $x;
		}
	}
}
function ifGlobal($str){
	if(defined($str)){
		return constant($str);
	}
}