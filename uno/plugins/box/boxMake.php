<?php
if (!isset($_SESSION['cmsuno'])) exit();
if(!file_exists('data/box.json'))
	{
	@unlink('plugins/box/on.txt');
	exit;
	}
?>
<?php
if (file_exists('data/box.json'))
	{
	$q1 = file_get_contents('data/box.json');
	$a1 = json_decode($q1,true);
	foreach($a1['box'] as $a2)
		{
		$html = str_replace('[[box-'.$a2['n'].']]',$a2['b'],$html);
		$content = str_replace('[[box-'.$a2['n'].']]',$a2['b'],$content);
		}
	}
?>
