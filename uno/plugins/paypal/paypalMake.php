<?php
if (!isset($_SESSION['cmsuno'])) exit();
?>
<?php
	$q1 = file_get_contents('data/paypal.json');
	$a1 = json_decode($q1,true);
	$prod = 'https://www.paypal.com/';
	$sand = 'https://www.sandbox.paypal.com/';
	if($a1['mod']=='test')$content = str_replace($prod,$sand,$content); // mode SANDBOX
	else if($a1['mod']=='prod')$content = str_replace($sand,$prod,$content); // mode PRODUCTION
	if($a1['pop']=='1')$content = str_replace("appendChild(fm);fm.submit();","appendChild(fm);window.open('','paypop','width=960,height=600,resizeable,scrollbars');fm.target='paypop';fm.submit();",$content); // Paypal dans un popup
	else $content = str_replace("appendChild(fm);window.open('','paypop','width=960,height=600,resizeable,scrollbars');fm.target='paypop';fm.submit();","appendChild(fm);fm.submit();",$content);
?>
