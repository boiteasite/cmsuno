<?php
$langCode = array(
	"fr" => "fr_FR.utf8",
	"en" => "en_US",
	"es" => "es_ES.utf8"
	);
if(empty($forceGettext)) $forceGettext = 0;
//	
if(!empty($langCode[$lang])) {
	require_once(dirname(__FILE__).'/php-gettext/gettext.inc');
	T_setlocale(LC_MESSAGES, $langCode[$lang]);
	T_bindtextdomain("cmsuno", dirname(__FILE__));
	T_bind_textdomain_codeset("cmsuno", "UTF-8");
	T_textdomain("cmsuno");
}
else if(!function_exists('T_')) {
	function T_($f) { return $f; }
}
?>
