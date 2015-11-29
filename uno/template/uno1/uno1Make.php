<?php
if (!isset($_SESSION['cmsuno'])) exit();
?>
<?php
if(file_exists('data/'.$Ubusy.'/uno1.json'))
	{
	$c = array();
	$o = '';
	$b = array(
		// ID => SELECTOR, CSS PROPERTY
		'bgp' => array('body', 'background-color'),
		'bgw' => array('#global', 'background-color'),
		'bgm' => array('#header,#nav,#nav .subMenu', 'background-color'),
		'tmc' => array('#header a:hover,#header a.active', 'color'),
		'tmo' => array('#header a', 'color'),
		'cot' => array('#header h1', 'color'),
		'coc' => array('#global .content h2.nav1 a', 'color')
		);
	$q1 = file_get_contents('data/'.$Ubusy.'/uno1.json');
	$a1 = json_decode($q1,true);
	foreach($a1 as $k=>$v)
		{
		if(isset($b[$k]) && $v)
			{
			if(!isset($c[$b[$k][0]])) $c[$b[$k][0]] = $b[$k][1].':'.$v.';';
			else $c[$b[$k][0]] .= $b[$k][1].':'.$v.';';
			}
		else if($k=='sub' && $v==1) $Ustyle .= 'li:hover ul.subMenu{display:block;}';
		}
	foreach($c as $k=>$v)
		{
		$o .= $k.'{'.$v.'}'."\r\n";
		}
	$Ustyle .= $o;
	}
?>
