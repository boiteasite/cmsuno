<?php
header('Content-Type: text/html; charset=utf-8');
	$langPlug = array(
		"fr" => "fr_FR.utf8",
		"en" => "en_US"
		);
	//	
	if ($langPlug[$lang])
		{
		putenv('LC_ALL='.$langPlug[$lang]);
		setlocale(LC_ALL, $langPlug[$lang]);
		bindtextdomain("scrollnav", dirname (__FILE__));
		textdomain("scrollnav");
		}
?>