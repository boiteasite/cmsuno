<?php
if (!isset($_SESSION['cmsuno'])) exit();
if(!file_exists('data/'.$Ubusy.'/unocss.json'))
	{
	@unlink('plugins/unocss/on.txt');
	exit;
	}
?>
<?php
	if (file_exists('data/'.$Ubusy.'/unocss.json'))
		{
		$q1 = file_get_contents('data/'.$Ubusy.'/unocss.json');
		$a1 = json_decode($q1,true);
		$style .= $a1['tex']."\r\n";
		}
?>
