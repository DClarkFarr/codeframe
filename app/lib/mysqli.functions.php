<?php 

namespace Mysqli;

function query($query, $extra = null){
	//echo $query, "<br /><br />";
	if($query){
		$result = \mysqli_query(\App::mysqli(), $query);
	}else{
		$result = '';
	}
	return $result;
}

function insert_id(){
	return \mysqli_insert_id(\App::mysqli());
}
function fetch_assoc($res){
	if(!$res){
		return array();
	}
	return \mysqli_fetch_assoc($res);
}
function fetch_array($res){
	if(!$res){
		return array();
	}
	return \mysqli_fetch_array($res);
}
function num_rows($res){
	return mysqli_num_rows($res);
}
function connect_db($params){
  $mysqli = new \mysqli($params['host'], $params['username'], $params['password']);
  if(!$mysqli->connect_errno && isset($params['database'])) {
    \mysqli_select_db($mysqli, $params['database']);
  }
  return $mysqli;
}
function select_db($dbName){
	$res = \mysqli_select_db(\App::mysqli(), $dbName);
  return $res;
}
function error(){
	return \mysqli_error(\App::mysqli());
}
function real_escape_string($str){
	return \mysqli_real_escape_string(\App::mysqli(), $str);
}