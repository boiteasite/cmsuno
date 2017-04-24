<?php
// **********************************
// CMSUno
// **********************************
// V1.4.2 : UKEY IN CONFIG
if(empty($Ukey))
	{
	$ch = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789'; $Ukey = '';
	for($v=0;$v<63;++$v) $Ukey .= $ch[mt_rand(0, strlen($ch)-1)];
	$out = '<?php $lang = "'.$lang.'"; $sdata = "'.$sdata.'"; $Ukey = "'.$Ukey.'"; $Uversion = "'.$Uversion.'"; ?>';
	file_put_contents('uno/config.php', $out);
	}
//
// END PATCH - ONLY ONCE
@copy(dirname(__FILE__).'/patch.php', dirname(__FILE__).'/patch_off.php');
@unlink(dirname(__FILE__).'/patch.php');
?>
