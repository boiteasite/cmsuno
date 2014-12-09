<?php
if (!isset($_SESSION['cmsuno'])) exit();
if(!file_exists('data/'.$Ubusy.'/carousel.json'))
	{
	@unlink('plugins/carousel/on.txt');
	exit;
	}
?>
<?php
	$q1 = file_get_contents('data/'.$Ubusy.'/carousel.json');
	$a1 = json_decode($q1,true);
	$nivo=0; $fred=0; $ken=0; $feat=0;
	foreach($a1 as $n=>$a2)
		{
		// ******** NIVO **********************
		if($a2['typ']=='nivo' && strpos($content.$html,'[[carousel-'.$n.']]')!==false)
			{
			$o1 = "\r\n".'<div class="carouselWrap">'."\r\n\t".'<div id="carousel'.$n.'" class="nivoSlider" style="height:'.$a2['hei'].'px;width:'.$a2['wid'].'px;overflow:hidden;">'."\r\n";
			for($w=0;$w<count($a2['img']);++$w)
				{
				$o1 .= "\t\t".'<img src="'.$a2['img'][$w]['s'].'" data-thumb="'.$a2['img'][$w]['s'].'" alt="'.$a2['img'][$w]['t'].'" title="'.$a2['img'][$w]['t'].'" />'."\r\n";
				}
			$o1 .= "\t".'</div>'."\r\n".'</div>'."\r\n";
			$content = str_replace('[[carousel-'.$n.']]',$o1,$content); // editor
			$html = str_replace('[[carousel-'.$n.']]',$o1,$html); // template
			if(!$nivo)
				{
				$head .= '<link rel="stylesheet" href="uno/plugins/carousel/nivoSlider/nivo-slider.css" type="text/css" />'."\r\n";
				$foot .= '<script type="text/javascript" src="uno/plugins/carousel/nivoSlider/jquery.nivo.slider.pack.js"></script>'."\r\n";
				$nivo = 1;
				}
			$foot .= '<script type="text/javascript">jQuery(window).load(function(){$("#carousel'.$n.'").nivoSlider({';
			$foot .= 'pauseTime:'.(($a2['pau'])?$a2['pau']:3500);
			$foot .= ',animSpeed:'.(($a2['spe'])?$a2['spe']:700);
			$foot .= ',effect:"'.(($a2['tra'])?$a2['tra']:'fade').'"';
			$foot .= ',randomStart:"'.(($a2['rst'])?'true':'false').'"';
			$foot .= ',controlNav:false';
			$foot .= ',prevText:"<"';
			$foot .= ',nextText:">"';
			$foot .= '});});</script>'."\r\n";
			}
		// ******** CAROUFRED *****************
		else if($a2['typ']=='fred' && strpos($content.$html,'[[carousel-'.$n.']]')!==false)
			{
			$o1 = "\r\n".'<div class="carouselWrap">'."\r\n\t".'<div id="carousel'.$n.'" style="height:'.$a2['hei'].'px;width:'.$a2['wid'].'px;overflow:hidden;">'."\r\n";
			for($w=0;$w<count($a2['img']);++$w)
				{
				$o1 .= "\t\t".'<img src="'.$a2['img'][$w]['s'].'" style="height:'.$a2['hei'].'px;margin:0 7px;" alt="'.$a2['img'][$w]['t'].'" title="'.$a2['img'][$w]['t'].'" />'."\r\n";
				}
			$o1 .= "\t".'</div>'."\r\n".'</div>'."\r\n";
			$content = str_replace('[[carousel-'.$n.']]',$o1,$content); // editor
			$html = str_replace('[[carousel-'.$n.']]',$o1,$html); // template
			if(!$fred)
				{
				//$head .= '<link rel="stylesheet" href="uno/plugins/carousel/nivo-slider/nivoSlider.css" type="text/css" />'."\r\n";
				$foot .= '<script type="text/javascript" src="uno/plugins/carousel/carouFredSel/jquery.carouFredSel-6.2.1-packed.js"></script>'."\r\n";
				$fred = 1;
				}
			$foot .= '<script type="text/javascript">jQuery(window).load(function(){$("#carousel'.$n.'").carouFredSel({';
			$foot .= 'auto:'.(($a2['pau'])?$a2['pau']:3500);
			$foot .= ',width:'.(($a2['wid'])?$a2['wid']:'100%');
			$foot .= ',height:'.(($a2['hei'])?$a2['hei']:'auto');
			$foot .= ',padding:0';
			$foot .= ',scroll:{';
				$foot .= 'duration:'.(($a2['spe'])?$a2['spe']:700);
				$foot .= ',items:4,fx:"scroll",pauseOnHover:true';
			$foot .= '}';
			$foot .= ',items:{';
				$foot .= 'start:'.(($a2['rst'])?'"random"':0);
				$foot .= ',visible:"variable"';
				$foot .= ',width:"variable"';
			$foot .= '}';
			$foot .= '});});</script>'."\r\n";
			}
		// ******** KEN BURNING *****************
		else if($a2['typ']=='ken' && strpos($content.$html,'[[carousel-'.$n.']]')!==false)
			{
			$o1 = "\r\n".'<div class="carouselWrap">'."\r\n\t".'<div id="carousel'.$n.'" style="height:'.$a2['hei'].'px;width:'.$a2['wid'].'px;overflow:hidden;">'."\r\n";
			for($w=0;$w<count($a2['img']);++$w)
				{
				$o1 .= "\t\t".'<img src="'.$a2['img'][$w]['s'].'" alt="'.$a2['img'][$w]['t'].'" title="'.$a2['img'][$w]['t'].'" />'."\r\n";
				}
			$o1 .= "\t".'</div>'."\r\n".'</div>'."\r\n";
			$content = str_replace('[[carousel-'.$n.']]',$o1,$content); // editor
			$html = str_replace('[[carousel-'.$n.']]',$o1,$html); // template
			if(!$ken)
				{
				$head .= '<link rel="stylesheet" href="uno/plugins/carousel/kenburning/kenburning.css" type="text/css" />'."\r\n";
				$foot .= '<script type="text/javascript" src="uno/plugins/carousel/kenburning/kenburning.js"></script>'."\r\n";
				$ken= 1;
				}
			$foot .= '<script type="text/javascript">$("#carousel'.$n.'").kenBurning({';
			$foot .= 'zoom:1.25, ';
			$foot .= 'time:'.(($a2['pau'])?$a2['pau']:6000);
			$foot .= '});</script>'."\r\n";
			}
		// ******** FEATURE CAROUSEL *****************
		else if($a2['typ']=='feat' && strpos($content.$html,'[[carousel-'.$n.']]')!==false)
			{
			$o1 = "\r\n".'<div class="carouselWrap" >'."\r\n\t";
			$o1 .= '<div id="carousel'.$n.'" style="position:relative;height:'.(($a2['hei'])?$a2['hei'].'px':'auto').';margin:-10px 0 30px 0;">'."\r\n";
			for($w=0;$w<count($a2['img']);++$w)
				{
				$o1 .= "\t\t".'<div class="carousel-feature">'."\r\n";
				$o1 .= "\t\t\t".'<a href="#"><img class="carousel-image" src="'.$a2['img'][$w]['s'].'" alt="'.$a2['img'][$w]['t'].'" title="'.$a2['img'][$w]['t'].'" style="max-height:'.(($a2['hei'])?$a2['hei'].'px':'auto').';" /></a>'."\r\n";
				$o1 .= "\t\t\t".'<div class="carousel-caption"><p>'.$a2['img'][$w]['t'].'</p></div>'."\r\n";
				$o1 .= "\t\t".'</div>'."\r\n";
				}
			$o1 .= "\t".'</div>'."\r\n".'</div>'."\r\n";
			$content = str_replace('[[carousel-'.$n.']]',$o1,$content); // editor
			$html = str_replace('[[carousel-'.$n.']]',$o1,$html); // template
			if(!$feat)
				{
				$head .= '<link rel="stylesheet" href="uno/plugins/carousel/featureCarousel/feature-carousel.css" type="text/css" />'."\r\n";
				$foot .= '<script type="text/javascript" src="uno/plugins/carousel/featureCarousel/jquery.featureCarousel.min.js"></script>'."\r\n";
				$feat= 1;
				}
			$foot .= '<script type="text/javascript">jQuery(window).load(function(){$("#carousel'.$n.'").featureCarousel({';
			$foot .= 'carouselSpeed:'.(($a2['spe'])?$a2['spe']:1000).', ';
			$foot .= 'autoPlay:'.(($a2['pau'])?$a2['pau']:4000).', ';
			$foot .= 'pauseOnHover:true, ';
			$foot .= 'trackerIndividual:false, ';
			$foot .= 'trackerSummation:false});});';
			$foot .= '</script>'."\r\n";
			}
		}
?>
