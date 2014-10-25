<?php
session_start(); 
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
if(!isset($_POST['unox']) || $_POST['unox']!=$_SESSION['unox']) {sleep(2);exit;} // appel depuis uno.php
//
$lazy = 1;
include('password.php'); $user=0; $pass=0; // reset
include('lang/lang.php');
//
// ********************* functions ***********************************************************************
function f_lazy($f)
	{
	$out=''; $src=''; $alt=''; $b=0; $c=0; $v=4;
	do	{
		if ($b==0) do { ++$v; } while (substr($f,$v-4,4)!='<img' && $v<strlen($f));
		if (substr($f,$v-4,4)=='<img') { $out.=(substr($f,$c,$v-$c+1)); $b=1; }
		else if ($b==1 && (substr($f,$v-5,5)=='src="' || substr($f,$v-5,5)=="src='"))
			{
			do { $src.=substr($f,$v,1); ++$v; } while (substr($f,$v,1)!='"' && substr($f,$v,1)!="'" && $v<strlen($f));
			$out .= 'uno/css/a.png" data-echo="'.$src.'"'; // ECHO
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
function f_zip($d,$n)
	{
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
			else if (is_file($r)===true) $zip->addFromString(str_replace($d. '/', '', $r), file_get_contents($r));
			}
		}
	else if (is_file($d)===true) $zip->addFromString(basename($d), file_get_contents($d));
	return $zip->close();
	}
//
// ********************* actions *************************************************************************
if (isset($_POST['action']))
	{
	if(!file_exists('data/page0.txt')) file_put_contents('data/page0.txt', 'blabla...');
	if(!file_exists('data/site.json')) file_put_contents('data/site.json', '{"pages":[{"d":"0","t":"Welcome"}],"pub":0}');
	switch ($_POST['action'])
		{
		// ********************************************************************************************
		case 'getSite':
		$q = file_get_contents('data/site.json');
		echo $q; exit;
		break;
		// ********************************************************************************************
		case 'getPage':
		$q = file_get_contents('data/page'.(($_POST['data']!='')?$_POST['data']:'0').'.txt');
		echo stripslashes($q); exit;
		break;
		// ********************************************************************************************
		case 'sauvePage':
		$q = file_get_contents('data/site.json');
		$a = json_decode($q,true);
		foreach ($a['pages'] as $k=>$v)
			{
			if ($k==$_POST['page'])
				{
				$a['pages'][$k]['d'] = $_POST['data'];
				$a['pages'][$k]['t'] = $_POST['titre'];
				break;
				}
			}
		$a['pub'] = 1;
		if (!isset($a['lazy'])) $a['lazy'] = 1; // default
		if (!isset($a['sty'])) $a['sty'] = 0; // default
		if (!isset($a['edw'])) $a['edw'] = 960; // default
		if (!isset($a['jq'])) $a['jq'] = 0; // default
		$out = json_encode($a);
		if (file_put_contents('data/site.json', $out) && file_put_contents('data/page'.$_POST['data'].'.txt', $_POST['content'])) echo _('Backup performed');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		case 'sauvePlace':
		$q = file_get_contents('data/site.json');
		$a = json_decode($q,true);
		$b=0; $a1 = $a['pages'];
		foreach ($a1 as $k=>$v)
			{
			if($_POST['place']<$_POST['page']) // la page remonte
				{
				if ($k==$_POST['place'])
					{
					$a['pages'][$k] = $a1[$_POST['page']];
					$b=1;
					}
				else if ($k==$_POST['page']) $b=0;
				if ($b==1) $a['pages'][$k+1] = $v;
				}
			else
				{
				if ($k==$_POST['page']) $b=1;
				else if ($k==$_POST['place'])
					{
					$a['pages'][$k] = $a1[$_POST['page']];
					$b=0;
					}
				if ($b==1) $a['pages'][$k] = $a1[$k+1];
				}
			}
		$out = json_encode($a);
		if (file_put_contents('data/site.json', $out)) echo _('Change made');
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'sauvePass':
		include('password.php');
		$a = $_POST['user']; $b = $_POST['pass'];
		if ($_POST['user0']=='' && $_POST['pass0']=='') {$a = $user; $b = $pass;}
		else if ($_POST['user0']!=$user || $_POST['pass0']!=$pass) {echo '!'._('Wrong current elements'); exit;}
		$out = '<?php $user = "'.$a.'"; $pass = "'.$b.'"; $lang = "'.$_POST['lang'].'"; ?>';
		if (file_put_contents('password.php', $out)) echo ($_POST['user0']!='')?_('The login / password were changed'):_('The language was changed');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		case 'sauveConfig':
		$q = file_get_contents('data/site.json');
		$a = json_decode($q,true);
		$a['tit'] = $_POST['tit'];
		$a['desc'] = $_POST['desc'];
		$a['nom'] = (($_POST['nom']!="")?preg_replace("/[^A-Za-z0-9-]/",'',$_POST['nom']):'index');
		if ($_POST['edw']!='') $a['edw'] = $_POST['edw']; else $a['edw'] = 960;
		if ($_POST['lazy']=="true") $a['lazy']=1; else $a['lazy']=0;
		if ($_POST['jq']=="true") $a['jq']=1; else $a['jq']=0;
		if ($_POST['sty']=="true") $a['sty']=1; else $a['sty']=0;
		$out = json_encode($a);
		if (file_put_contents('data/site.json', $out)) echo _('Backup performed');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		case 'nouvPage':
		$q = file_get_contents('data/site.json');
		$a= json_decode($q,true);
		$d = 0;
		while (file_exists('data/page'.$d.'.txt')) {++$d;} // numero de fichier libre
		$b=0; $a1 = $a['pages'];
		foreach ($a1 as $k=>$v)
			{
			if ($b==0 && $k==$_POST['page'])
				{
				$a['pages'][$k+1] = array("d"=>$d,"t"=>_("new page"));
				$b=1;
				}
			else if ($b==1) $a['pages'][$k+1] = $v; // decallage de +1 des clefs
			}
		$out = json_encode($a);
		if (file_put_contents('data/site.json', $out) && file_put_contents('data/page'.$d.'.txt',' ')) echo _('Page created');
		else echo '!'._('Failure');
		break;
		// ********************************************************************************************
		case 'suppPage':
		$q = file_get_contents('data/site.json');
		$a = json_decode($q,true);
		foreach ($a['pages'] as $k=>$v)
			{
			if ($k==$_POST['page'])
				{
				unlink('data/page'.($a['pages'][$k]['d']).'.txt'); // supp fichier
				unset($a['pages'][$k]); // supp element tableau
				$a['pages'] = array_values($a['pages']); // renumerotation des clefs du tableau
				break;
				}
			}
		$out = json_encode($a);
		if (file_put_contents('data/site.json', $out)) echo _('Deletion complete');
		else echo '!'._('Failure');
		break;
		// ********************************************************************************************
		// SHORTCODE [[foo]] : title, description, template, head, foot, menu, jsmenu, content
		case 'publier':
		$html = file_get_contents('template/template.html');
		$head = ''; $foot = ''; $content = ''; $menu = '<ul id="nav">'; $style = '';
		$jsmenu = '<script src="uno/js/uno_menu.js"></script>';
		$q = file_get_contents('data/site.json');
		$a = json_decode($q,true);
		foreach ($a['pages'] as $k=>$v)
			{
			$w = strtr(utf8_decode($v['t']),'¿¡¬√ƒ≈∆«»… ÀÃÕŒœ–—“”‘’÷ÿŸ⁄€‹›ﬁﬂ‡·‚„‰ÂÊÁËÈÍÎÏÌÓÔÒÚÛÙıˆ¯˘˙˚˝˝˛ˇ','aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyyby');
			$w = preg_replace('/[^a-zA-Z0-9%]/s','',$w);
			$menu .= '<li><a href="#'.$w.'"'.($k==0?' class="active"':'').'>'.$v['t'].'</a></li>';
			$content .= '<h2 id="'.$w.'" class="nav1"><a name="'.$w.'">'.$v['t'].'</a></h2>';
			$content .= file_get_contents('data/page'.$v['d'].'.txt');
			}
		$menu .= '</ul>'; 
		$title = (isset($a['tit']))?$a['tit']:"";
		$description = (isset($a['desc']))?$a['desc']:"";
		$name = (isset($a['nom']))?$a['nom']:"";
		$content = str_replace('<h2>','<h2 class="nav2">',$content);
		$content = stripslashes($content);
		$u = dirname($_SERVER['PHP_SELF']).'/../';
		$content = str_replace($u,'',$content);
		if (isset($a['jq']) && $a['jq']==1)
			{
			$head .= '<!--[if (!IE)|(gt IE 8)]><!--><script src="//code.jquery.com/jquery-2.1.0.min.js"></script><!--<![endif]-->'."\r\n"
				.'<!--[if lte IE 8]><script src="//code.jquery.com/jquery-1.11.0.min.js"></script><![endif]-->'."\r\n"
				.'<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>'."\r\n"
				.'<script>window.jQuery || document.write(\'<script src="uno/js/jquery-1.11.0.min.js">\x3C/script><script src="uno/js/jquery-migrate-1.2.1.min.js">\x3C/script>\')</script>'."\r\n";
			}
		if (isset($a['lazy']) && $a['lazy']==1)
			{
			$style .= '.content img[data-echo]{display:none;background:#fff url(uno/css/a.gif) no-repeat center center;}'."\r\n";
			$foot .= '<script src="uno/js/echo.min.js"></script>'."\r\n".'<script type="text/javascript">var css=".content img[data-echo]{display:inline;}",head=document.head||document.getElementsByTagName("head")[0],style=document.createElement("style");style.type="text/css";if(style.styleSheet) style.styleSheet.cssText=css;else style.appendChild(document.createTextNode(css));head.appendChild(style);echo.init({offset:900,throttle:250});echo.render();</script>'."\r\n";
			$content = f_lazy($content);
			}
		// *** Plugins ***
		$d = glob('plugins/*',GLOB_ONLYDIR);
		foreach($d as $r)
			{
			if (file_exists('plugins/'.basename($r).'/on.txt')) include('plugins/'.basename($r).'/'.basename($r).'Make.php');
			}
		// *** / ***
		$head .= '<style type="text/css">'."\r\n".$style.'</style>';
		$foot .= $jsmenu;
		// HTML
		$html = str_replace('[[head]]',$head,$html);
		$html = str_replace('[[foot]]',$foot,$html);
		$html = str_replace('[[menu]]',$menu,$html);
		$html = str_replace('[[content]]','<div class="pagesContent">'."\r\n".$content."\r\n".'</div>',$html);
		// HTML et CONTENT
		$html = str_replace('[[template]]','uno/template/',$html);
		$html = str_replace('[[title]]',$title,$html);
		$html = str_replace('[[description]]',$description,$html);
		$html = str_replace('[[name]]',$name,$html);
		$a['pub'] = 0;
		$out = json_encode($a);
		if (!isset($a['nom'])) $a['nom']='index';
		if (file_put_contents('data/site.json', $out) && file_put_contents('../'.$a['nom'].'.html', $html)) echo _('The site has been updated');
		else echo '!'._('Failure');
		break;
		// ********************************************************************************************
		case 'archivage':
		$d = '../files/unosave';
		$nom = preg_replace("/[^A-Za-z0-9-]/",'',$_POST['nom']);
		$n = $d.'/'.$nom.'-'.date('Ymd-Hi').'.zip';
		if (!file_exists($d)) mkdir($d, 0755, true);
		if (f_zip('data/',$n)) echo _('Archiving performed');
		else echo '!'._('Failure');
		break;
		// ********************************************************************************************
		case 'selectArchive':
		$d = '../files/unosave/';
	//	$g = glob($d."*.zip"); // fonctionne mal
		// alternative a glob ******
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
		// **************************
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
			array_map('unlink', glob('data/*'));
			$zip->extractTo('data/');
			$zip->close();
			echo _('Recovery performed');
			}
		else echo '!'._('Failure');
		break;
		// ********************************************************************************************
		case 'plugins':
		$a = array();
		$d = glob('plugins/*',GLOB_ONLYDIR);
		sort($d);
		foreach($d as $r)
			{
			if (file_exists('plugins/'.basename($r).'/on.txt')) $a[]='1'.basename($r);
			else $a[]='0'.basename($r);
			}
		echo json_encode($a);
		break;
		// ********************************************************************************************
		case 'pluginsActifs':
		$a = array();
		$d = glob('plugins/*',GLOB_ONLYDIR);
		if($d)
			{
			sort($d);
			foreach($d as $r) 
				{
				if (file_exists('plugins/'.basename($r).'/on.txt'))
					{
					$a['pl'][]=basename($r);
					if (file_exists('plugins/'.basename($r).'/'.basename($r).'Ckeditor.js')) $a['ck'][]='../../plugins/'.basename($r).'/'.basename($r).'Ckeditor.js';
					}
				}
			echo json_encode($a);
			}
		else echo null;
		break;
		// ********************************************************************************************
		case 'onPlug':
		if ($_POST['c']=="true" && file_exists('plugins/'.$_POST['n'].'/'.$_POST['n'].'Make.php')) file_put_contents('plugins/'.$_POST['n'].'/on.txt','');
		else @unlink('plugins/'.$_POST['n'].'/on.txt');
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>