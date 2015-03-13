<?php
// EXTRA TO USE WITH OLD PHP
//
if(!function_exists('json_decode') || !function_exists('json_encode')) include(dirname(__FILE__).'/JSON.php');
if(!function_exists('json_encode'))
	{
	function json_encode($data)
		{
		$json = new Services_JSON();
		return($json->encode($data));
		}
	}
if(!function_exists('json_decode'))
	{
	function json_decode($data, $op)
		{
		$json = new Services_JSON();
		if($op) return object_to_array($json->decode($data));
		else return($json->decode($data));
		}
	}
function object_to_array($obj)
	{
	$_arr = is_object($obj) ? get_object_vars($obj) : $obj;
	foreach($_arr as $key=>$val)
		{
		$val = (is_array($val) || is_object($val)) ? object_to_array($val) : $val;
		$arr[$key] = $val;
		}
	return $arr;
	}
//
?>