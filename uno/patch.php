<?php
// **********************************
// CMSUno
// **********************************
// V1.4.2 : UKEY IN CONFIG
if(file_exists('uno/config.php')) include('uno/config.php');
if(empty($Ukey))
	{
	$ch = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789'; $Ukey = '';
	for($v=0;$v<63;++$v) $Ukey .= $ch[mt_rand(0, strlen($ch)-1)];
	$out = '<?php $lang = "'.$lang.'"; $sdata = "'.$sdata.'"; $Ukey = "'.$Ukey.'"; $Uversion = "'.$Uversion.'"; ?>';
	file_put_contents('uno/config.php', $out);
	}
// V1.6 : Umaster in busy
if(file_exists('uno/data/busy.json')) {
	$q = file_get_contents('uno/data/busy.json');
	$a = json_decode($q,true);
	if($a['nom'] && empty($a['master'])) file_put_contents('uno/data/busy.json', '{"nom":"'.$a['nom'].'","master":"'.$a['nom'].'"}');
}
// V1.7.2 JQuery previous files when update and old publish
if(file_exists('uno/includes/js/jquery.min.js')) {
	if(!file_exists('uno/includes/js/jquery-3.5.1.min.js')) link('uno/includes/js/jquery.min.js', 'uno/includes/js/jquery-3.5.1.min.js');
	if(!file_exists('uno/includes/js/jquery-3.6.0.min.js')) link('uno/includes/js/jquery.min.js', 'uno/includes/js/jquery-3.6.0.min.js');
}
//
// END PATCH - ONLY ONCE
@copy(dirname(__FILE__).'/patch.php', dirname(__FILE__).'/patch_off.php');
@unlink(dirname(__FILE__).'/patch.php');
?>
