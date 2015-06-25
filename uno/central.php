<?php
session_start(); 
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
if(!isset($_POST['unox']) || $_POST['unox']!=$_SESSION['unox']) {sleep(2);exit;} // appel depuis uno.php
//
$lazy = 1;
include('config.php');
include('includes/lang/lang.php');
//if (!is_dir('includes/js/ckeditor/')) $Udep = "https://cdn.rawgit.com/boiteasite/cmsuno/master/uno/"; else $Udep = "uno/"; // SEMI HOSTED VERSION
if (!is_dir('includes/js/ckeditor/')) $Udep = "https://rawgit.com/boiteasite/cmsuno/master/uno/"; else $Udep = "uno/"; // SEMI HOSTED VERSION
//
// ********************* functions ***********************************************************************
function f_lazy($f)
	{
	global $Udep;
	$out=''; $src=''; $alt=''; $b=0; $c=0; $v=4;
	do	{
		if ($b==0) do { ++$v; } while (substr($f,$v-4,4)!='<img' && $v<strlen($f));
		if (substr($f,$v-4,4)=='<img') { $out.=(substr($f,$c,$v-$c+1)); $b=1; }
		else if ($b==1 && (substr($f,$v-5,5)=='src="' || substr($f,$v-5,5)=="src='"))
			{
			do { $src.=substr($f,$v,1); ++$v; } while (substr($f,$v,1)!='"' && substr($f,$v,1)!="'" && $v<strlen($f));
			$out .= $Udep.'includes/css/a.png" data-echo="'.$src.'"'; // ECHO
			}
		else if ($b==1 && (substr($f,$v-5,5)=='alt="' || substr($f,$v-5,5)=="alt='") && substr($f,$v-5,6)!='alt=""' && substr($f,$v-5,6)!="alt=''")
			{
			do { $out.=substr($f,$v,1); $alt.=substr($f,$v,1); ++$v; } while (substr($f,$v,1)!='"' && substr($f,$v,1)!="'" && $v<strlen($f));
			$out.=substr($f,$v,1);
			}
		else if ($b==1)
			{
			$out.=substr($f,$v,1);
			if (substr($f,$v,1)=='>') {$out.='<noscript><img src="'.$src.'" style="display:inline;" alt="'.$alt.'"></noscript>'; $c=$v+1; $src=''; $alt=''; $b=0;}
			}
		++$v;
		} while ($v<strlen($f));
		$out.=(substr($f,$c,$v-$c));
	return $out;
	}
//
function f_zip($d,$n,$e=0)
	{
	// $e = 1 : no zip file in arch
	// zip un dossier $d (/aaaz/bbb/ccc/) avec le nom $n (nnn.zip)
	if (!extension_loaded('zip') || !file_exists($d)) return false;
	$zip = new ZipArchive(); if (!$zip->open($n, ZIPARCHIVE::CREATE)) return false;
	$d = str_replace('\\', '/', realpath($d));
	if (is_dir($d)===true)
		{
		$f = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d), RecursiveIteratorIterator::SELF_FIRST);
		foreach ($f as $r)
			{
			$r = str_replace('\\', '/', $r);
			if(in_array(substr($r, strrpos($r, '/')+1), array('.', '..')))
			continue;
			$r = realpath($r);
			if (is_dir($r)===true) $zip->addEmptyDir(str_replace($d . '/', '', $r . '/')); 
			else if (is_file($r)===true)
				{
				$ext=explode('.',$r);
				$ext=$ext[count($ext)-1];
				if($ext!='zip' || !$e) $zip->addFromString(str_replace($d. '/', '', $r), file_get_contents($r));
				}
			}
		}
	else if (is_file($d)===true)
		{
		$ext=explode('.',$r);
		$ext=$ext[count($ext)-1];
		if($ext!='zip' || !$e) $zip->addFromString(basename($d), file_get_contents($d));
		}
	return $zip->close();
	}
//
function f_copyDir($s,$d,$p=0755)
	{
	if (is_link($s)) return symlink(readlink($s), $d);
	if (is_file($s)) return copy($s, $d);
	if (!is_dir($d)) mkdir($d, $p);
	$dir = dir($s);
	while (false!==$e=$dir->read())
		{
		if($e=='.'||$e=='..') continue;
		f_copyDir($s.'/'.$e, $d.'/'.$e, $p);
		}
	$dir->close();
	return true;
	}
//
function f_rmdirR($dir)
	{
	$files = array_diff(scandir($dir), array('.','..'));
	foreach ($files as $file)
		{
		(is_dir("$dir/$file")) ? f_rmdirR("$dir/$file") : unlink("$dir/$file");
		}
	return rmdir($dir);
	}
//
// ********************* actions *************************************************************************
if (isset($_POST['action']))
	{
	$b = 0;
	if(file_exists('data/busy.json'))
		{
		$q = file_get_contents('data/busy.json');
		$a = json_decode($q,true);
		$Ubusy = $a['nom'];
		if(!is_dir('data/'.$Ubusy))
			{
			$h=opendir('data/');
			while(($d=readdir($h))!==false)
				{
				if(is_dir('data/'.$d) && file_exists('data/'.$d.'/site.json'))
					{
					file_put_contents('data/busy.json', '{"nom":"'.$d.'"}');
					$Ubusy = $d;
					$b = 1;
					}
				}
			closedir($h);
			}
		else $b = 1;
		}
	if(!$b)
		{
		if(!is_dir('data')) mkdir('data'); if(!is_dir('data/_sdata-'.$sdata)) mkdir('data/_sdata-'.$sdata,0711);
		if(!is_dir('data/index')) mkdir('data/index');
		if(!is_dir('data/_sdata-'.$sdata.'/index')) mkdir('data/_sdata-'.$sdata.'/index',0711);
		file_put_contents('data/busy.json', '{"nom":"index"}');
		$Ubusy = 'index';
		}
	if(!file_exists('data/'.$Ubusy.'/chap0.txt')) file_put_contents('data/'.$Ubusy.'/chap0.txt', 'blabla...');
	if(!file_exists('data/'.$Ubusy.'/site.json')) file_put_contents('data/'.$Ubusy.'/site.json', '{"chap":[{"d":"0","t":"'._("Welcome").'"}],"pub":0}');
	switch ($_POST['action'])
		{
		// ********************************************************************************************
		case 'getSite':
		if(file_exists(dirname(__FILE__).'/../files/archive.zip')) unlink(dirname(__FILE__).'/../files/archive.zip');
		$a = array();
		$q = @file_get_contents('data/'.$Ubusy.'/site.json');
		$q1 = @file_get_contents('data/_sdata-'.$sdata.'/ssite.json');
		if($q) $a = json_decode($q,true);
		if($q1)
			{
			$a1 = json_decode($q1,true);
			$a['mel'] = $a1['mel'];
			}
		if(!isset($a['tit'])) $a['tit'] = '';
		if(!isset($a['desc'])) $a['desc'] = '';
		$q = json_encode($a);
		echo $q; exit;
		break;
		// ********************************************************************************************
		case 'getChap':
		$q = file_get_contents('data/'.$Ubusy.'/chap'.((isset($_POST['data'])&&$_POST['data']!='')?$_POST['data']:'0').'.txt');
		$q1 = file_get_contents('data/'.$Ubusy.'/site.json'); $a = json_decode($q1,true); $b = 0; $c = 0;
		foreach ($a['chap'] as $k=>$v)
			{
			if($v['d']==$_POST['data']) { $c = $k; break; }
			}
		if(isset($a['chap'][$k]['ot']) && $a['chap'][$k]['ot']) $b += 1;
		if(isset($a['chap'][$k]['om']) && $a['chap'][$k]['om']) $b += 2;
		if(isset($a['chap'][$k]['od']) && $a['chap'][$k]['od']) $b += 4;
		echo strval($b).stripslashes($q); exit;
		break;
		// ********************************************************************************************
		case 'sauveChap':
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$a = json_decode($q,true);
		foreach ($a['chap'] as $k=>$v)
			{
			if ($k==$_POST['chap'])
				{
				$a['chap'][$k]['d'] = $_POST['data'];
				$a['chap'][$k]['t'] = $_POST['titre'];
				$a['chap'][$k]['ot'] = ($_POST['otit']=='true'?1:0);
				$a['chap'][$k]['om'] = ($_POST['omenu']=='true'?1:0);
				$a['chap'][$k]['od'] = ($_POST['odisp']=='true'?1:0);
				break;
				}
			}
		$a['pub'] = 1;
		if (!isset($a['lazy'])) $a['lazy'] = 1; // default
		if (!isset($a['sty'])) $a['sty'] = 0; // default
		if (!isset($a['edw'])) $a['edw'] = 960; // default
		if (!isset($a['jq'])) $a['jq'] = 0; // default
		$out = json_encode($a);
		if (file_put_contents('data/'.$Ubusy.'/site.json', $out) && file_put_contents('data/'.$Ubusy.'/chap'.$_POST['data'].'.txt', $_POST['content'])) echo _('Backup performed');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		case 'sauvePlace':
		if(isset($_POST['chap']) && isset($_POST['place']) && $_POST['chap']!=$_POST['place'])
			{
			$q = file_get_contents('data/'.$Ubusy.'/site.json');
			$a = json_decode($q,true);
			$b=0; $a1 = $a['chap'];
			foreach ($a1 as $k=>$v)
				{
				if($_POST['place']<$_POST['chap']) // le chapitre remonte
					{
					if ($k==$_POST['place'])
						{
						$a['chap'][$k] = $a1[$_POST['chap']];
						$b=1;
						}
					else if ($k==$_POST['chap']) $b=0;
					if ($b==1) $a['chap'][$k+1] = $v;
					}
				else
					{
					if ($k==$_POST['chap']) $b=1;
					else if ($k==$_POST['place'])
						{
						$a['chap'][$k] = $a1[$_POST['chap']];
						$b=0;
						}
					if ($b==1) $a['chap'][$k] = $a1[$k+1];
					}
				}
			$out = json_encode($a);
			if (file_put_contents('data/'.$Ubusy.'/site.json', $out)) echo _('Change made');
			else echo '!'._('Error');
			}
		else echo _('No change');
		break;
		// ********************************************************************************************
		case 'sauvePass':
		define('CMSUNO', 'cmsuno');
		include('password.php');
		$a = $_POST['user']; $b = $_POST['pass'];
		if ($_POST['user0']=='' || $_POST['pass0']=='') // only lang
			{
			$config = '<?php $lang = "'.$_POST['lang'].'"; $sdata = "'.$sdata.'"; ?>';
			if (file_put_contents('config.php', $config)) echo _('The language was changed');
			else echo '!'._('Impossible backup');
			}
		else if ($_POST['user0']!=$user || $_POST['pass0']!=$pass)
			{
			echo '!'._('Wrong current elements'); exit;
			}
		else
			{
			$password = '<?php if(!defined(\'CMSUNO\')) exit(); $user = "'.$a.'"; $pass = "'.$b.'"; ?>';
			$config = '<?php $lang = "'.$_POST['lang'].'"; $sdata = "'.$sdata.'"; ?>';
			if (file_put_contents('password.php', $password) && file_put_contents('config.php', $config)) echo _('The login / password were changed');
			else echo '!'._('Impossible backup');
			}
		break;
		// ********************************************************************************************
		case 'sauveConfig':
		$n = (($_POST['nom']!="")?preg_replace("/[^A-Za-z0-9-_]/",'',$_POST['nom']):'index');
		while(substr($n,0,1)=="_") $n = substr($n,1);
		if($Ubusy!=$n && $n!="")
			{
			if(!is_dir('data/'.$n) && is_dir('data/'.$Ubusy)) f_copyDir('data/'.$Ubusy, 'data/'.$n);
			else mkdir('data/'.$n, 0755, true);
			if(!is_dir('data/_sdata-'.$sdata.'/'.$n) && is_dir('data/_sdata-'.$sdata.'/'.$Ubusy)) f_copyDir('data/_sdata-'.$sdata.'/'.$Ubusy, 'data/_sdata-'.$sdata.'/'.$n, 0711);
			else mkdir('data/_sdata-'.$sdata.'/'.$n, 0711, true);
			f_rmdirR('data/'.$Ubusy);
			$Ubusy = $n;
			file_put_contents('data/busy.json', '{"nom":"'.$Ubusy.'"}');
			}
		$q=@file_get_contents('data/_sdata-'.$sdata.'/ssite.json');
		if($q) $a=json_decode($q,true);
		else $a = array();
		$a['mel']=$_POST['mel']; $out1=json_encode($a); file_put_contents('data/_sdata-'.$sdata.'/ssite.json',$out1);
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$a = json_decode($q,true);
		$a['tit'] = $_POST['tit'];
		$a['desc'] = $_POST['desc'];
		$a['url'] = $_POST['url'];
		if(substr($a['url'],-1)=='/') $a['url'] = substr($a['url'],0,-1);
		$a['tem'] = $_POST['tem'];
		$a['nom'] = $n;
		if ($_POST['edw']!='') $a['edw'] = $_POST['edw']; else $a['edw'] = 960;
		if ($_POST['lazy']=="true") $a['lazy']=1; else $a['lazy']=0;
		if ($_POST['jq']=="true") $a['jq']=1; else $a['jq']=0;
		if ($_POST['sty']=="true") $a['sty']=1; else $a['sty']=0;
		$out = json_encode($a);
		if (file_put_contents('data/'.$Ubusy.'/site.json', $out)) echo _('Backup performed');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		case 'nouvChap':
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$a= json_decode($q,true);
		$d = 0;
		while (file_exists('data/'.$Ubusy.'/chap'.$d.'.txt')) {++$d;} // numero de fichier libre
		$b=0; $a1 = $a['chap'];
		foreach ($a1 as $k=>$v)
			{
			if ($b==0 && $k==$_POST['chap'])
				{
				$a['chap'][$k+1] = array("d"=>$d,"t"=>_("new chapter"));
				$b=1;
				}
			else if ($b==1) $a['chap'][$k+1] = $v; // decallage de +1 des clefs
			}
		$out = json_encode($a);
		if (file_put_contents('data/'.$Ubusy.'/site.json', $out) && file_put_contents('data/'.$Ubusy.'/chap'.$d.'.txt',' ')) echo _('Chapter created');
		else echo '!'._('Failure');
		break;
		// ********************************************************************************************
		case 'suppChap':
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$a = json_decode($q,true);
		foreach ($a['chap'] as $k=>$v)
			{
			if ($k==$_POST['chap'])
				{
				unlink('data/'.$Ubusy.'/chap'.($a['chap'][$k]['d']).'.txt'); // supp fichier
				unset($a['chap'][$k]); // supp element tableau
				$a['chap'] = array_values($a['chap']); // renumerotation des clefs du tableau
				break;
				}
			}
		if(empty($a['chap'])) $a['chap'][0]=array("d"=>"0","t"=>"Welcome");
		$out = json_encode($a);
		if (file_put_contents('data/'.$Ubusy.'/site.json', $out)) echo _('Deletion complete');
		else echo '!'._('Failure');
		break;
		// ********************************************************************************************
		// SHORTCODE [[foo]] : title, description, template, head, foot, menu, jsmenu, content
		case 'publier':
		$Uhead = ''; $Ufoot = ''; $Uonload = ''; $Ucontent = ''; $Umenu = ''; $Ustyle = ''; $Uscript = ''; $Ujsmenu = '<script type="text/javascript" src="'.$Udep.'includes/js/uno_menu.js"></script>';
		$unoPop=0; // Include JS files
		$unoUbusy=0; // Include Ubusy in JS
		if(isset($_POST['Ubusy']) && $_POST['Ubusy'] && file_exists('data/'.$_POST['Ubusy'].'/site.json')) $Ubusy = $_POST['Ubusy'];
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$Ua = json_decode($q,true);
		if(!isset($Ua['tem']) || !isset($Ua['url']) || !isset($Ua['tit']) || !isset($Ua['desc']))
			{
			echo '!'._('Save Config First');
			exit;
			}
		$Uhtml = file_get_contents('template/'.$Ua['tem'].'/template.html');
		foreach ($Ua['chap'] as $k=>$v)
			{
			if(!isset($Ua['chap'][$k]['od']) || $Ua['chap'][$k]['od']==0)
				{
				$w = strtr(utf8_decode($v['t']),'¿¡¬√ƒ≈∆«»… ÀÃÕŒœ–—“”‘’÷ÿŸ⁄€‹›ﬁﬂ‡·‚„‰ÂÊÁËÈÍÎÏÌÓÔÒÚÛÙıˆ¯˘˙˚˝˝˛ˇ','aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyyby');
				$w = preg_replace('/[^a-zA-Z0-9%]/s','',$w);
				// menu
				if(!isset($Ua['chap'][$k]['om']) || $Ua['chap'][$k]['om']==0) $Umenu .= '<li><a href="#'.$w.'"'.($k==0?' class="active"':'').'>'.stripslashes($v['t']).'</a></li>';
				// titre + class pour menu scrollnav
				if(!isset($Ua['chap'][$k]['ot']) || $Ua['chap'][$k]['ot']==0)
					{
					if(!isset($Ua['chap'][$k]['om']) || $Ua['chap'][$k]['om']==0) $Ucontent .= '<h2 id="'.$w.'" class="nav1"><a name="'.$w.'">'.stripslashes($v['t']).'</a></h2>';
					else $Ucontent .= '<h2 id="'.$w.'" class="NAV1"><a name="'.$w.'">'.stripslashes($v['t']).'</a></h2>';
					}
				else if(!isset($Ua['chap'][$k]['om']) || $Ua['chap'][$k]['om']==0) $Ucontent .= '<h2 id="'.$w.'" class="nav1" style="height:0;padding:0;border:0;margin-bottom:5px;overflow:hidden;"><a name="'.$w.'">'.stripslashes($v['t']).'</a></h2>';
				$Ucontent .= file_get_contents('data/'.$Ubusy.'/chap'.$v['d'].'.txt');
				}
			}
		$Utitle = (isset($Ua['tit']))?stripslashes($Ua['tit']):"";
		$Udescription = (isset($Ua['desc']))?stripslashes($Ua['desc']):"";
		$Uname = (isset($Ua['nom']))?stripslashes($Ua['nom']):"";
		$Ucontent = str_replace('<h2>','<h2 class="nav2">',$Ucontent);
		$Ucontent = stripslashes($Ucontent);
		$u = dirname($_SERVER['PHP_SELF']).'/../';
		$Ucontent = str_replace($u,'',$Ucontent);
		if (isset($Ua['jq']) && $Ua['jq']==1)
			{
			$Uhead .= '<!--[if (!IE)|(gt IE 8)]><!--><script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script><!--<![endif]-->'."\r\n"
				.'<!--[if lte IE 8]><script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script><![endif]-->'."\r\n"
				.'<script type="text/javascript" src="'.$Udep.'includes/js/jquery-migrate-1.2.1.min.js"></script>'."\r\n";
			if($Udep=='uno/') $Uhead .= '<script type="text/javascript">window.jQuery || document.write(\'<script src="uno/includes/js/jquery-1.11.0.min.js">\x3C/script>\')</script>'."\r\n";
			}
		if (isset($Ua['lazy']) && $Ua['lazy']==1)
			{
			$Ustyle .= '.content img[data-echo]{display:none;background:#fff url('.$Udep.'includes/css/a.gif) no-repeat center center;}'."\r\n";
			$Ufoot .= '<script type="text/javascript" src="'.$Udep.'includes/js/echo.min.js"></script>'."\r\n".'<script type="text/javascript">var css=".content img[data-echo]{display:inline;}",head=document.head||document.getElementsByTagName("head")[0],style=document.createElement("style");style.type="text/css";if(style.styleSheet) style.styleSheet.cssText=css;else style.appendChild(document.createTextNode(css));head.appendChild(style);echo.init({offset:900,throttle:250});echo.render();</script>'."\r\n";
			$Ucontent = f_lazy($Ucontent);
			}
		if(file_exists('template/'.$Ua['tem'].'/0make.php')) include('template/'.$Ua['tem'].'/0make.php'); // template Make before plugin
		// *** Plugins ***
		if(isset($Ua['plug'])) for($Uv=1;$Uv<=5;++$Uv) // 1 first, 5 last, no number = 3 && alphabetic order
			{
			foreach($Ua['plug'] as $Uk=>$Ur)
				{
				if($Uv!=3 && file_exists('plugins/'.$Uk.'/'.$Uk.'Make'.$Uv.'.php')) include('plugins/'.$Uk.'/'.$Uk.'Make'.$Uv.'.php');
				else if($Uv==3 && file_exists('plugins/'.$Uk.'/'.$Uk.'Make.php')) include('plugins/'.$Uk.'/'.$Uk.'Make.php');
				else if($Uv==3 && file_exists('plugins/'.$Uk.'/'.$Uk.'Make3.php')) include('plugins/'.$Uk.'/'.$Uk.'Make3.php');
				}
			}
		// *** / ***
		if(file_exists('template/'.$Ua['tem'].'/make.php')) include('template/'.$Ua['tem'].'/make.php'); // template Make after plugin
		include('includes/lang/lang.php');
		if(strpos(strtolower($Uhtml),'charset="utf-8"')===false && strpos(strtolower($Uhtml),"charset='utf-8'")===false) $Uhead .= '<meta charset="utf-8">'."\r\n";
		$Uhead .= '<style type="text/css">'."\r\n".$Ustyle.'</style>'."\r\n";
		if($unoPop==1) $Uhead .= '<script type="text/javascript" src="'.$Udep.'includes/js/unoPop.js"></script><link rel="stylesheet" type="text/css" href="'.$Udep.'includes/css/unoPop.css" />'."\r\n";
		if($unoUbusy==1) $Uscript .= 'var Ubusy="'.$Ubusy.'";';
		if($Uscript) $Uhead .= '<script type="text/javascript">'.$Uscript.'</script>'."\r\n";
		$Ufoot .= $Ujsmenu;
		if($Uonload!='') $Ufoot .= '<script type="text/javascript">window.onload=function(){'.$Uonload.'}</script>'."\r\n";
		$Umenu = '<label for="navR" class="navR"></label><input type="checkbox" id="navR" />'."\r\n".'<ul id="nav">'.$Umenu.'</ul>';
		// HTML
		$Uhtml = str_replace('[[url]]',$Ua['url'],$Uhtml);
		$Uhtml = str_replace('[[head]]',$Uhead,$Uhtml);
		$Uhtml = str_replace('[[foot]]',$Ufoot,$Uhtml);
		$Uhtml = str_replace('[[menu]]',$Umenu,$Uhtml);
		$Uhtml = str_replace('[[content]]','<div id="pagesContent" class="pagesContent">'."\r\n".$Ucontent."\r\n".'</div>',$Uhtml);
		// HTML et CONTENT
		$Uhtml = str_replace('[[template]]','uno/template/'.$Ua['tem'].'/',$Uhtml);
		$Uhtml = str_replace('[[title]]',$Utitle,$Uhtml);
		$Uhtml = str_replace('[[description]]',$Udescription,$Uhtml);
		$Uhtml = str_replace('[[name]]',$Uname,$Uhtml);
		$Ua['pub'] = 0;
		if (!isset($Ua['nom'])) $Ua['nom']='index';
		$out = json_encode($Ua);
		if (file_put_contents('data/'.$Ubusy.'/site.json', $out) && file_put_contents('../'.$Ua['nom'].'.html', $Uhtml)) echo _('The site has been updated');
		else echo '!'._('Failure');
		break;
		// ********************************************************************************************
		case 'error':
		$Ua = array();
		if(file_exists('data/error.json'))
			{
			$q = file_get_contents('data/error.json');
			$Ua = json_decode($q,true);
			}
		$Ua[] = array('t'=>date("Y-m-d H:i:s"),'e'=>$_POST['e']);
		$out = json_encode($Ua);
		file_put_contents('data/error.json', $out);
		break;
		// ********************************************************************************************
		case 'suppPub':
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$Ua = json_decode($q,true);
		if(file_exists('../'.$Ua['nom'].'.html'))
			{
			if(unlink('../'.$Ua['nom'].'.html')) echo _('Publication deleted');
			else echo '!'._('Failure');
			}
		else echo '!'._('Missing file');
		break;
		// ********************************************************************************************
		case 'archivage':
		$d = 'data/_sdata-'.$sdata.'/_unosave';
		$n = $d.'/unosave-'.date('Ymd-Hi').'.zip';
		if (!file_exists($d)) mkdir($d, 0755, true);
		if (f_zip('data/',$n,1)) echo _('Archiving performed');
		else echo '!'._('Failure');
		break;
		// ********************************************************************************************
		case 'selectArchive':
		$d = 'data/_sdata-'.$sdata.'/_unosave/';
		$g=array();
		if ($h=opendir($d))
			{
			while (($file=readdir($h))!==false)
				{
				$ext=explode('.',$file);
				$ext=$ext[count($ext)-1];
				if ($ext=='zip' && $file!='.' && $file!='..') $g[]=$d.$file;
				}
			closedir($h);
			}
		usort($g,create_function('$a,$b','return filemtime($b)-filemtime($a);'));
		if ($g)
			{
			echo '<select id="archive">';
			foreach ($g as $r) {$r1=explode("/",$r);	echo '<option value="'.$r.'">'.$r1[count($r1)-1].'</option>'; }
			echo '</select>';
			}
		break;
		// ********************************************************************************************
		case 'restaure':
		$zip = new ZipArchive;
		$f = $zip->open($_POST['zip']);
		if ($f===true)
			{
			f_copyDir('data/_sdata-'.$sdata.'/_unosave', '_unosave', 0711);
			f_rmdirR('data');
			mkdir('data');
			$zip->extractTo('data/');
			$zip->close();
			mkdir('data/_sdata-'.$sdata.'/_unosave');
			f_copyDir('_unosave', 'data/_sdata-'.$sdata.'/_unosave', 0711);
			f_rmdirR('_unosave');
			echo _('Recovery performed');
			}
		else echo '!'._('Failure');
		break;
		// ********************************************************************************************
		case 'archDel':
		if(unlink($_POST['zip'])) echo _('Backup removed');
		else echo '!'._('Failure');
		break;
		// ********************************************************************************************
		case 'archDownload':
		if(file_exists('data/'.$Ubusy.'/site.json'))
			{
			$q = file_get_contents('data/'.$Ubusy.'/site.json');
			$a = json_decode($q,true);
			if(isset($a['url']) && copy($_POST['zip'], '../files/archive.zip')) echo $a['url'].'/files/archive.zip';
			exit;
			}
		echo '!'._('Failure');
		break;
		// ********************************************************************************************
		case 'filesDownload':
		if(file_exists(dirname(__FILE__).'/../files/archive.zip')) unlink(dirname(__FILE__).'/../files/archive.zip');
		if(file_exists('data/'.$Ubusy.'/site.json'))
			{
			$q = file_get_contents('data/'.$Ubusy.'/site.json');
			$a = json_decode($q,true);
			if(isset($a['url']) && f_zip('../files/', '../files/archive.zip',0)) echo $a['url'].'/files/archive.zip';
			exit;
			}
		echo '!'._('Failure');
		break;
		// ********************************************************************************************
		case 'plugins':
		$b = array();
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$a = json_decode($q,true);
		$d = array();
		if(is_dir('plugins') && $h=opendir('plugins'))
			{
			while(false!==($f=readdir($h)))
				{
				if($f!='.' && $f!='..' && is_dir('plugins/'.$f)) $d[]=$f;
				}
			closedir($h);
			}		
		sort($d);
		foreach($d as $r)
			{
			if (isset($a['plug'][basename($r)])) $b[]='1'.basename($r);
			else $b[]='0'.basename($r);
			}
		echo json_encode($b);
		break;
		// ********************************************************************************************
		case 'pluginsActifs':
		$b = array();
		if($Udep=='/uno/') $ck = '../../../';
		else $ck = substr($_SERVER['PHP_SELF'],0,-11); // 11 : central.php
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$a = json_decode($q,true);
		if(isset($a['plug'])) foreach($a['plug'] as $k=>$r)
				{
				if(file_exists('plugins/'.$k.'/'.$k.'.php'))
					{
					$b['pl'][]=$k;
					if(file_exists('plugins/'.$k.'/'.$k.'Ckeditor.js')) $b['ck'][]=$ck.'plugins/'.$k.'/'.$k.'Ckeditor.js';
					}
				}
			echo json_encode($b);
		break;
		// ********************************************************************************************
		case 'onPlug':
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$a = json_decode($q,true);
		if($_POST['c']=="true") $a['plug'][$_POST['n']]=1;
		else if(isset($a['plug'][$_POST['n']])) unset($a['plug'][$_POST['n']]);
		$b=$a['plug']; ksort($b); $a['plug']=$b;
		$out = json_encode($a);
		file_put_contents('data/'.$Ubusy.'/site.json', $out);
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>