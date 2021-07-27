<?php
if (!isset($_SESSION['cmsuno'])) exit();
?>
<?php
if(file_exists('data/'.$Ubusy.'/uno1.json'))
	{
	$c = array();
	$o = ''; $gof = 0;
	$b = array(
		// ID => SELECTOR, CSS PROPERTY
		'bgpcolor' => array('body', 'background-color:'),
		'bgpimg' => array('body', 'background-position:center center;background-attachment:fixed;background-size:cover;background-repeat:no-repeat;background-image:url'),
		'bgw' => array('#global', 'background-color:'),
		'wrw' => array('#global', 'width:px'),
		'tgo' => array('#toplogo', 'background-position:center center;background-repeat:no-repeat;background-image:url'),
		'tgh' => array('#toplogo', 'height:px'),
		'mpo' => array('#header', 'width:1024px;position:'),
		'mfo' => array('#nav,#nav .subMenu', 'font-family:'),
		'bgm' => array('#header,#nav,#nav .subMenu', 'background-color:'),
		'tit' => array('#nav', 'float:'),
		'tmc' => array('#header a:hover,#header a.active', 'color:'),
		'tmo' => array('#header a', 'color:'),
		'tfo' => array('#header h1', 'font-family:'),
		'tfs' => array('#header h1', 'font-size:px'),
		'tlh' => array('#header h1', 'line-height:em'),
		'cot' => array('#header h1', 'color:'),
		'coc' => array('#global .content h2.nav1 a', 'color:'),
		'cfo' => array('#global .content', 'font-family:'),
		'cfs' => array('#global .content', 'font-size:px'),
		'cfc' => array('#global .content', 'color:')
		);
	$q1 = file_get_contents('data/'.$Ubusy.'/uno1.json');
	$a1 = json_decode($q1,true);
	foreach($a1 as $k=>$v)
		{
		if(isset($a1['S'.$k])) $k1 = $k . $a1['S'.$k];
		else $k1 = $k;
		if(isset($b[$k1]) && $v)
			{
			$b1 = (isset($c[$b[$k1][0]])?$c[$b[$k1][0]]:'');
			if(substr($b[$k1][1],-1)==':') $c[$b[$k1][0]] = $b1 . $b[$k1][1].strip_tags($v).';';
			else if(substr($b[$k1][1],-3)=='url') $c[$b[$k1][0]] = $b1 . $b[$k1][1].'("'.strip_tags($v).'");';
			else if(substr($b[$k1][1],-2)=='px') $c[$b[$k1][0]] = $b1 . substr($b[$k1][1],0,-2).filter_var($v,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION).'px;';
			else if(substr($b[$k1][1],-2)=='em') $c[$b[$k1][0]] = $b1 . substr($b[$k1][1],0,-2).filter_var($v,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION).'em;';
			else if(substr($b[$k1][1],-1)==';') $c[$b[$k1][0]] = $b1 . $b[$k1][1];
			}
		else if($k=='sub' && $v==1) $Ustyle .= 'li:hover ul.subMenu{display:block;}'."\r\n";
		// Specific cases
		if($k=='mpo' && $v=='relative')
			{
			$Ustyle .= 'body{padding-top:0;}'."\r\n";
			if(isset($a1['wrw']) && $a1['wrw']) $c[$b['mpo'][0]] = str_replace('1024',$a1['wrw'],$c[$b['mpo'][0]]);
			}
		if($k=='tit' && $v=='none') $Uhtml = str_replace('<h1>[[title]]</h1>','',$Uhtml);
		if($k=='gof' && $v) $gof = $v;
		}
	foreach($c as $k=>$v)
		{
		$o .= $k.'{'.$v.'}'."\r\n";
		}
	if($gof)
		{
	//	$o ="@import url(https://fonts.googleapis.com/css?family=".str_replace(' ','+',$gof).");\r\n".$o;
		$Uhead .= "<link href='https://fonts.googleapis.com/css?family=".str_replace(' ','+',$gof)."' rel='stylesheet' type='text/css'>\r\n";
		$g = explode(':',$gof);
		$o = str_replace("googleFont","'".$g[0]."'",$o);
		}
	$Ustyle .= $o;
	}
?>
