<?php
	$langCode = array(
		"fr" => "fr_FR.utf8",
		"en" => "en_US",
		"es" => "es_ES.utf8"
		);
	//	
	if(isset($langCode[$lang]) && $langCode[$lang])
		{
		putenv('LC_ALL='.$langCode[$lang]);
		setlocale(LC_ALL, $langCode[$lang]);
		bindtextdomain("cmsuno", dirname(__FILE__));
		textdomain("cmsuno");
		}
	// if(file_exists(dirname(__FILE__).'/../extra/extra.php')) include(dirname(__FILE__).'/../extra/extra.php');
?>