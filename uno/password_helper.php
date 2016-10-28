<?php

include('password.php');

function timing_safe_equals($a, $b)
{
	$i = 0;
	$eq = true;
	$mx = max(strlen($a),strlen($b));
	$mn = min(strlen($a),strlen($b));
	for($i = 0; $i < $mx; $i++){
		if($i < $mn && $a[$i] != $b[$i])
			$eq = false;
	}
	return ($eq && $mx === $mn);
}
function timing_safe_and($a, $b)
{
	$a = !!$a;
	$b = !!$b;
	return $a && $b;
}
function timing_safe_or($a, $b)
{
	$a = !!$a;
	$b = !!$b;
	return $a || $b;
}

function password_save($user, $pass, $pwfile)
{
	$pass = password_hash($pass, PASSWORD_BCRYPT);
	$password = '<?php if(!defined(\'CMSUNO\')) exit(); $user = "'.$user.'"; $pass = \''.$pass.'\'; ?>';
	if(file_put_contents($pwfile, $password)){
		return true;
	}
	return false;
}

function password_check($user,$pass,$hash,$pwfile)
{
	$pass_ok = false;
	if($hash[0] != '$'){
		$pass_ok = timing_safe_equals($pass, $hash);
		if($pass_ok === true){
			// replace plain text pw with hashed one
			password_save($user, $pass,$pwfile);
		}
	}else{
		$pass_ok = password_verify($pass, $hash);
	}
	return $pass_ok;
}

?>
