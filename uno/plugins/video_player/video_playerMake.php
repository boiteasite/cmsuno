<?php
if (!isset($_SESSION['cmsuno'])) exit();
?>
<?php
	$tmp = $content;
	$b = 0; $out = ""; $wid = ""; $hei = ""; $src = '';
	$swf = 'uno/plugins/video_player/player_flv.swf';
	for($v=0; $v<strlen($tmp); ++$v)
		{
		if (substr($tmp,$v,6)=="<video") $b=1;
		if (substr($tmp,$v-1,8)=="><source" && $b>0) $out .= "\r\n"."\t";
		if (substr($tmp,$v,6)=="width=" && $b>0)
			{
			$w=7;
			do { $wid .= substr($tmp,$v+$w,1); ++$w; }
			while (substr($tmp,$v+$w,1)!='"' && substr($tmp,$v+$w,1)!=' ');
			}
		if (substr($tmp,$v,7)=="height=" && $b>0)
			{
			$w=8;
			do { $hei .= substr($tmp,$v+$w,1); ++$w; }
			while (substr($tmp,$v+$w,1)!='"' && substr($tmp,$v+$w,1)!=' ');
			}
		if (substr($tmp,$v,11)=="source src=" && $b>0 && $src=="")
			{
			$w=12;
			do { $src .= substr($tmp,$v+$w,1); ++$w; }
			while (substr($tmp,$v+$w,1)!='"' && substr($tmp,$v+$w,1)!=' ');
			if (strpos($src,".mp4")===false) $src="";
			}
		if (substr($tmp,$v,11)=="source src=" && $b>0) $b=4;
		if (substr($tmp,$v,8)=="</video>")
			{
			$b=0;
			if ($src!="")
				{
				$out .= "\r\n"."\t".'<object width="'.$wid.'" height="'.$hei.'" type="application/x-shockwave-flash" data="'.$swf.'">'."\r\n"."\t\t";
				$out .= '<param name="movie" value="'.$swf.'" />'."\r\n"."\t\t";
				$out .= '<param name="FlashVars" value="flv=../../../'.$src.'&amp;width='.$wid.'&amp;height='.$hei.'&amp;showvolume=1&amp;showtime=1&amp;showstop=1" />'."\r\n"."\t".'</object>';
				}
			$out .= "\r\n";
			}
		if ($b<5) $out .= substr($tmp,$v,1);
		}
	$content = $out;
?>
