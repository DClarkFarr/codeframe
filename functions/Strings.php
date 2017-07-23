<?php

function segmentsToClass($segments){
	$classname = 'Controllers';
	if($segments){
		foreach($segments as $segment){
			if($segment->rule->type == 'static'){
				$classname .= '\\' . strToClass($segment->rule->value);
			}else if($segment->rule->type == 'variable'){
				$classname .= '\\' . strToClass( property_exists($segment, 'param') ?  $segment->param : $segment->rule->value);
			}

		}

		return $classname;
	}
	return false;
}
function strToClass($str){
	$str = str_replace(array('-', '_', '.'), ' ', $str);
	$str = preg_replace('@\s{2,}@', ' ', $str);
	$str = ucwords($str);
	$str = str_replace(' ', '', $str);
	return $str;
}
function strfix($str){
	$str = preg_replace('@[^a-z\-]@', '-', trim(strtolower($str)));
  return preg_replace('@\-{2,}@', '-', $str);
}
