<?php
if (!isset($_SESSION['cmsuno'])) exit();
if(!file_exists('data/'.$Ubusy.'/box.json')) exit;
?>
<?php
if (file_exists('data/'.$Ubusy.'/box.json'))
	{
	$q1 = file_get_contents('data/'.$Ubusy.'/box.json');
	$a1 = json_decode($q1,true);
	foreach($a1['box'] as $a2)
		{
		$html = str_replace('[[box-'.$a2['n'].']]',$a2['b'],$html);
		$content = str_replace('[[box-'.$a2['n'].']]',$a2['b'],$content);
		}
	}

?>
