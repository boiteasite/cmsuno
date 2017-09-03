<?php
session_start(); 
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
if(!isset($_POST['unox']) || $_POST['unox']!=$_SESSION['unox']) {sleep(2);exit;} // appel depuis uno.php
//
$lazy = 1;
include('config.php');
include('includes/lang/lang.php');
$Urawgit = "https://cdn.rawgit.com/boiteasite/cmsuno/";
if(!is_dir('includes/js/ckeditor/')) $Udep = $Urawgit.$Uversion."/uno/"; else $Udep = "uno/"; // LIGHT HOSTED VERSION
//
// ********************* functions ***********************************************************************
function f_lazy($f)
	{
	global $Udep;
	$out=''; $src=''; $alt=''; $b=0; $c=0; $v=4;
	do	{
		if($b==0) do { ++$v; } while(substr($f,$v-4,4)!='<img' && $v<strlen($f));
		if(substr($f,$v-4,4)=='<img') { $out.=(substr($f,$c,$v-$c+1)); $b=1; }
		else if($b==1 && (substr($f,$v-5,5)=='src="' || substr($f,$v-5,5)=="src='"))
			{
			do { $src.=substr($f,$v,1); ++$v; } while(substr($f,$v,1)!='"' && substr($f,$v,1)!="'" && $v<strlen($f));
			$out .= $Udep.'includes/css/a.png" data-echo="'.$src.'"'; // ECHO
			}
		else if($b==1 && (substr($f,$v-5,5)=='alt="' || substr($f,$v-5,5)=="alt='") && substr($f,$v-5,6)!='alt=""' && substr($f,$v-5,6)!="alt=''")
			{
			do { $out.=substr($f,$v,1); $alt.=substr($f,$v,1); ++$v; } while(substr($f,$v,1)!='"' && substr($f,$v,1)!="'" && $v<strlen($f));
			$out.=substr($f,$v,1);
			}
		else if($b==1)
			{
			$out.=substr($f,$v,1);
			if(substr($f,$v,1)=='>') {$out.='<noscript><img src="'.$src.'" style="display:inline;" alt="'.$alt.'"></noscript>'; $c=$v+1; $src=''; $alt=''; $b=0;}
			}
		++$v;
		} while($v<strlen($f));
		$out.=(substr($f,$c,$v-$c));
	return $out;
	}
//
function f_zip($d,$n,$e=0)
	{
	// $e = 1 : no zip file in arch
	// zip un dossier $d (/aaaz/bbb/ccc/) avec le nom $n (nnn.zip)
	if(!extension_loaded('zip') || !file_exists($d)) return false;
	$zip = new ZipArchive(); if(!$zip->open($n, ZIPARCHIVE::CREATE)) return false;
	$d = str_replace('\\', '/', realpath($d));
	if(is_dir($d)===true)
		{
		$f = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d), RecursiveIteratorIterator::SELF_FIRST);
		foreach($f as $r)
			{
			$r = str_replace('\\', '/', $r);
			if(in_array(substr($r, strrpos($r, '/')+1), array('.', '..')))
			continue;
			$r = realpath($r);
			if(is_dir($r)===true) $zip->addEmptyDir(str_replace($d . '/', '', $r . '/')); 
			else if(is_file($r)===true)
				{
				$ext=explode('.',$r);
				$ext=$ext[count($ext)-1];
				if($ext!='zip' || !$e) $zip->addFromString(str_replace($d. '/', '', $r), file_get_contents($r));
				}
			}
		}
	else if(is_file($d)===true)
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
	if(is_link($s)) return symlink(readlink($s), $d);
	if(is_file($s)) return copy($s, $d);
	if(!is_dir($d)) mkdir($d, $p);
	$dir = dir($s);
	while(false!==$e=$dir->read())
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
	foreach($files as $file)
		{
		(is_dir("$dir/$file")) ? f_rmdirR("$dir/$file") : unlink("$dir/$file");
		}
	return rmdir($dir);
	}
//
function f_chmodR($path, $fr=0644, $dr=0755)
	{
	if(!file_exists($path)) return(false);
	if(is_file($path)) @chmod($path, $fr);
	else if(is_dir($path))
		{
		$re = scandir($path);
		$q = array_slice($re, 2);
		foreach($q as $r) f_chmodR($path."/".$r, $fr, $dr);
		@chmod($path, $dr);
		}
	return(true);
	}
//
function lastVersion($f,$g,$h)
	{
	// Version : $f : minimum 2 digit : 1.0, 2.4.5 maximum 3 digit
	// Version 2 => 2.0 (2 digit if 0)
	// Version 1.4.0 => 1.4 (not 3 digit if 0)
	$a = explode('.',$f);
	if(!isset($a[0])) $a[0] = 1;
	if(!isset($a[1])) $a[1] = 0;
	if(!isset($a[2])) $a[2] = 0;
	// next major version ? format : 2.0
	$b = ($a[0]+1);
	while(urlExists($g.$b.'.0/'.$h))
		{
		$a[0] = ($a[0]+1);
		$a[1] = 0; $a[2] = 0;
		$b = ($a[0]+1);
		}
	// next version ? format : 1.7
	$b = ($a[1]+1);
	while(urlExists($g.$a[0].'.'.$b.'/'.$h))
		{
		$a[1] = ($a[1]+1);
		$a[2] = 0;
		$b = ($a[1]+1);
		}
	// next minor version ? 1.7.3
	$b = ($a[2]+1);
	while(urlExists($g.$a[0].'.'.$a[1].'.'.$b.'/'.$h))
		{
		$a[2] = ($a[2]+1);
		$b = ($a[2]+1);
		}
	return $a[0].'.'.$a[1].($a[2]?'.'.$a[2]:'');
	}
//
function urlExists($u)
	{
	$head = get_headers($u);
	if($head && strpos($head[0],'404')===false) return true;
	// other try with curl
	if(!$head && function_exists('curl_version'))
		{
		$h = curl_init($u);
		curl_setopt($h,  CURLOPT_RETURNTRANSFER, TRUE);
		$res = curl_exec($h);
		$cod = curl_getinfo($h, CURLINFO_HTTP_CODE);
		curl_close($h);		
		if($cod!=404) return true;
		}
	// not exists
	return false;
	}
// ********************* actions *************************************************************************
if(isset($_POST['action']))
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
	if(!file_exists('data/'.$Ubusy.'/site.json')) file_put_contents('data/'.$Ubusy.'/site.json', '{"chap":[{"d":"0","t":"'.T_("Welcome").'"}],"pub":0}');
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
		if(!isset($a['tem'])) $a['tem'] = false;
		if(!isset($a['ofs'])) $a['ofs'] = 0; // V1.1.4
		$q = json_encode($a);
		echo $q; exit;
		break;
		// ********************************************************************************************
		case 'getChap':
		$q = file_get_contents('data/'.$Ubusy.'/chap'.((isset($_POST['data'])&&$_POST['data']!='')?$_POST['data']:'0').'.txt');
		$q1 = file_get_contents('data/'.$Ubusy.'/site.json'); $a = json_decode($q1,true); $b = 0; $c = 0;
		foreach($a['chap'] as $k=>$v)
			{
			if($v['d']==$_POST['data']) { $c = $k; break; }
			}
		if(!empty($a['chap'][$k]['ot'])) $b += 1;
		if(!empty($a['chap'][$k]['om'])) $b += 2;
		if(!empty($a['chap'][$k]['od'])) $b += 4;
		echo strval($b).stripslashes($q); exit;
		break;
		// ********************************************************************************************
		case 'sauveChap':
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$a = json_decode($q,true);
		foreach($a['chap'] as $k=>$v)
			{
			if($k==$_POST['chap'])
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
		if(!isset($a['lazy'])) $a['lazy'] = 1; // default
		if(!isset($a['sty'])) $a['sty'] = 0; // default
		if(!isset($a['edw'])) $a['edw'] = 960; // default
		if(!isset($a['jq'])) $a['jq'] = 0; // default
		$c = preg_replace_callback('/(<img[^>]*src=["\'])([^"\']*)/', function($m) { return ''.$m[1].substr($m[2],strpos($m[2],'files/'));}, $_POST['content']); // lien relatif
		$out = json_encode($a);
		if(file_put_contents('data/'.$Ubusy.'/site.json', $out) && file_put_contents('data/'.$Ubusy.'/chap'.$_POST['data'].'.txt', $c)) echo T_('Backup performed');
		else echo '!'.T_('Impossible backup');
		break;
		// ********************************************************************************************
		case 'sauvePlace':
		if(isset($_POST['chap']) && isset($_POST['place']) && $_POST['chap']!=$_POST['place'])
			{
			$q = file_get_contents('data/'.$Ubusy.'/site.json');
			$a = json_decode($q,true);
			$b=0; $a1 = $a['chap'];
			foreach($a1 as $k=>$v)
				{
				if($_POST['place']<$_POST['chap']) // le chapitre remonte
					{
					if($k==$_POST['place'])
						{
						$a['chap'][$k] = $a1[$_POST['chap']];
						$b=1;
						}
					else if($k==$_POST['chap']) $b=0;
					if($b==1) $a['chap'][$k+1] = $v;
					}
				else
					{
					if($k==$_POST['chap']) $b=1;
					else if($k==$_POST['place'])
						{
						$a['chap'][$k] = $a1[$_POST['chap']];
						$b=0;
						}
					if($b==1) $a['chap'][$k] = $a1[$k+1];
					}
				}
			$out = json_encode($a);
			if(file_put_contents('data/'.$Ubusy.'/site.json', $out)) echo T_('Change made');
			else echo '!'.T_('Error');
			}
		else echo T_('No change');
		break;
		// ********************************************************************************************
		case 'sauvePass':
		define('CMSUNO', 'cmsuno');
		include('password.php');
		$a = $_POST['user']; $b = $_POST['pass'];
		if($_POST['user0']=='' || $_POST['pass0']=='') // only lang
			{
			$config = '<?php $lang = "'.$_POST['lang'].'"; $sdata = "'.$sdata.'"; $Uversion = "'.(isset($Uversion)?$Uversion:'1.0').'"; ?>';
			if(file_put_contents('config.php', $config)) echo T_('The language was changed');
			else echo '!'.T_('Impossible backup');
			}
		else if($_POST['user0']===$user && password_verify($_POST['pass0'],$pass))
			{
			$password = '<?php if(!defined(\'CMSUNO\')) exit(); $user = "'.$a.'"; $pass = \''.password_hash($b, PASSWORD_BCRYPT).'\'; ?>';
			$config = '<?php $lang = "'.$_POST['lang'].'"; $sdata = "'.$sdata.'"; $Uversion = "'.(isset($Uversion)?$Uversion:'1.0').'"; ?>';
			if(file_put_contents('password.php', $password) && file_put_contents('config.php', $config)) echo T_('The login / password were changed');
			else echo '!'.T_('Impossible backup');
			}
		else
			{
			echo '!'.T_('Wrong current elements'); exit;
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
		//
		$q = @file_get_contents('data/_sdata-'.$sdata.'/ssite.json');
		if($q) $a = json_decode($q,true);
		else $a = array();
		$a['mel'] = $_POST['mel'];
		if(!is_dir('includes/js/ckeditor/')) $a['git'] = 1; // here in case of multipage
		$out1 = json_encode($a);
		file_put_contents('data/_sdata-'.$sdata.'/ssite.json',$out1);
		//
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$a = json_decode($q,true);
		$a['tit'] = $_POST['tit'];
		$a['desc'] = $_POST['desc'];
		$a['url'] = $_POST['url'];
		if(substr($a['url'],-1)=='/') $a['url'] = substr($a['url'],0,-1);
		$a['tem'] = $_POST['tem'];
		$a['nom'] = $n;
		if($_POST['edw']!='') $a['edw'] = $_POST['edw']; else $a['edw'] = 960;
		if($_POST['ofs']!='') $a['ofs'] = $_POST['ofs']; else $a['ofs'] = 0;
		if($_POST['lazy']=="true") $a['lazy']=1; else $a['lazy']=0;
		if($_POST['jq']=="true") $a['jq']=1; else $a['jq']=0;
		if($_POST['sty']=="true") $a['sty']=1; else $a['sty']=0;
		$out = json_encode($a);
		if(file_put_contents('data/'.$Ubusy.'/site.json', $out)) echo T_('Backup performed');
		else echo '!'.T_('Impossible backup');
		break;
		// ********************************************************************************************
		case 'nouvChap':
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$a= json_decode($q,true);
		$d = 0;
		while(file_exists('data/'.$Ubusy.'/chap'.$d.'.txt')) {++$d;} // numero de fichier libre
		$b=0; $a1 = $a['chap'];
		foreach($a1 as $k=>$v)
			{
			if($b==0 && $k==$_POST['chap'])
				{
				$a['chap'][$k+1] = array("d"=>$d,"t"=>T_("new chapter"));
				$b=1;
				}
			else if($b==1) $a['chap'][$k+1] = $v; // decallage de +1 des clefs
			}
		$out = json_encode($a);
		if(file_put_contents('data/'.$Ubusy.'/site.json', $out) && file_put_contents('data/'.$Ubusy.'/chap'.$d.'.txt',' ')) echo T_('Chapter created');
		else echo '!'.T_('Failure');
		break;
		// ********************************************************************************************
		case 'suppChap':
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$a = json_decode($q,true);
		foreach($a['chap'] as $k=>$v)
			{
			if($k==$_POST['chap'])
				{
				unlink('data/'.$Ubusy.'/chap'.($a['chap'][$k]['d']).'.txt'); // supp fichier
				unset($a['chap'][$k]); // supp element tableau
				$a['chap'] = array_values($a['chap']); // renumerotation des clefs du tableau
				break;
				}
			}
		if(empty($a['chap'])) $a['chap'][0]=array("d"=>"0","t"=>"Welcome");
		$out = json_encode($a);
		if(file_put_contents('data/'.$Ubusy.'/site.json', $out)) echo T_('Deletion complete');
		else echo '!'.T_('Failure');
		break;
		// ********************************************************************************************
		// SHORTCODE [[foo]] : title, description, template, head, foot, menu, jsmenu, content
		case 'publier':
		$Uhead = ''; $Ufoot = ''; $Uonload = ''; $Ucontent = ''; $Umenu = ''; $Ustyle = '.blocChap{clear:both}'."\r\n"; $UstyleSm = '';
		$Uscript = ''; $Ujsmenu = '<script type="text/javascript" src="'.$Udep.'includes/js/uno_menu.js"></script>';
		$unoPop=0; // Include JS files
		$unoUbusy=0; // Include Ubusy in JS
		if(!empty($_POST['Ubusy']) && file_exists('data/'.$_POST['Ubusy'].'/site.json')) $Ubusy = $_POST['Ubusy'];
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$Ua = json_decode($q,true);
		if(!isset($Ua['tem']) || !isset($Ua['url']) || !isset($Ua['tit']) || !isset($Ua['desc']) || !isset($Ua['ofs']))
			{
			echo '!'.T_('Save Config First');
			exit;
			}
		$Uscript .= 'var Umenuoffset='.intval($Ua['ofs']).';';
		$Uhtml = file_get_contents('template/'.$Ua['tem'].'/template.html');
		$Ustyle .= 'h2.nav1 a,h2.nav2 a,h2.nav1 a:hover,h2.nav2 a:hover{color:inherit;text-decoration:none;}'."\r\n";
		foreach($Ua['chap'] as $k=>$v)
			{
			if(empty($Ua['chap'][$k]['od']))
				{
				$c = file_get_contents('data/'.$Ubusy.'/chap'.$v['d'].'.txt');
				$w = strtr(utf8_decode($v['t']),'¿¡¬√ƒ≈∆«»… ÀÃÕŒœ–—“”‘’÷ÿŸ⁄€‹›ﬁﬂ‡·‚„‰ÂÊÁËÈÍÎÏÌÓÔÒÚÛÙıˆ¯˘˙˚˝˝˛ˇ','aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyyby');
				$w = preg_replace('/[^a-zA-Z0-9%]/s','',$w);
				$Ucontent .= '<div id="'.$w.'BlocChap" class="blocChap">'."\r\n";
				// menu
				if(empty($Ua['chap'][$k]['om'])) // menu not hidden
					{
					$d = array(); $m = '';
					$Umenu .= '<li><a href="#'.$w.'"'.($k==0?' class="active"':'').'>'.stripslashes($v['t']).'</a>';
					preg_match_all('/<h2[^>]*>([^<]*)/i', $c, $d); // submenu H2
					if(!empty($d[1][0]))
						{
						$m = "\r\n\t".'<ul class="subMenu">';
						$e = 0;
						foreach($d[1] as $r)
							{
							if($r)
								{
								++$e;
								$m .= '<li><a href="#'.$w.'-'.preg_replace('/[^a-zA-Z0-9%]/s','',$r).'-'.$e.'">'.$r.'</a></li>';
								}
							}
						$m .= '</ul>'."\r\n";
						}
					$Umenu .= $m.'</li>'."\r\n";
					}
				// titre + class pour menu & submenu
				$Ucontent .= '<h2 id="'.$w.'" class="nav1'.((!empty($Ua['chap'][$k]['om']))?' navOff':'').'" '.((!empty($Ua['chap'][$k]['ot']))?'style="height:0;padding:0;border:0;margin:0;overflow:hidden;"':'').'><a name="'.$w.'">'.stripslashes($v['t']).'</a></h2>'."\r\n";
				$e = 0;
				$c = preg_replace_callback('/(<h2[^>]*)(>)([^<]*)/i', function($m)
					{
					global $w; global $e;
					if($m[3])
						{
						++$e;
						$t = $w.'-'.preg_replace('/[^a-zA-Z0-9%]/s','',$m[3]).'-'.$e;
						return $m[1].' id="'.$t.'" class="nav2"><a name="'.$t.'">'.$m[3].'</a>';
						}
					else return $m[1].$m[2];
					}, $c); // submenu
				$Ucontent .= $c.'</div><!-- .blocChap -->'."\r\n";
				}
			}
		$Utitle = (isset($Ua['tit']))?stripslashes($Ua['tit']):"";
		$Udescription = (isset($Ua['desc']))?stripslashes($Ua['desc']):"";
		$Uname = (isset($Ua['nom']))?stripslashes($Ua['nom']):"";
		$Ucontent = stripslashes($Ucontent);
		$u = dirname($_SERVER['PHP_SELF']).'/../';
		$Ucontent = str_replace($u,'',$Ucontent);
		if(!empty($Ua['jq']))
			{
			$Uhead .= '<!--[if(!IE)|(gt IE 8)]><!--><script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script><!--<![endif]-->'."\r\n"
				.'<!--[if lte IE 8]><script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script><![endif]-->'."\r\n"
				.'<script type="text/javascript" src="'.$Udep.'includes/js/jquery-migrate-1.4.1.min.js"></script>'."\r\n";
			if($Udep=='uno/') $Uhead .= '<script type="text/javascript">window.jQuery || document.write(\'<script src="uno/includes/js/jquery-3.2.1.min.js">\x3C/script>\')</script>'."\r\n";
			}
		if(!empty($Ua['lazy']))
			{
			$Ustyle .= '.content img[data-echo]{display:none;background:#fff url('.$Udep.'includes/css/a.gif) no-repeat center center;}'."\r\n";
			$Ufoot .= '<script type="text/javascript" src="'.$Udep.'includes/js/echo.min.js"></script>'."\r\n".'<script type="text/javascript">var css=".content img[data-echo]{display:inline;}",head=document.head||document.getElementsByTagName("head")[0],style=document.createElement("style");style.type="text/css";if(style.styleSheet) style.styleSheet.cssText=css;else style.appendChild(document.createTextNode(css));head.appendChild(style);echo.init({offset:900,throttle:250});echo.render();</script>'."\r\n";
			$Ucontent = f_lazy($Ucontent);
			}
		if(file_exists('template/'.$Ua['tem'].'/'.$Ua['tem'].'Make0.php')) include('template/'.$Ua['tem'].'/'.$Ua['tem'].'Make0.php'); // template Make before plugin
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
		if(file_exists('template/'.$Ua['tem'].'/'.$Ua['tem'].'Make.php')) include('template/'.$Ua['tem'].'/'.$Ua['tem'].'Make.php'); // template Make after plugin
		include('includes/lang/lang.php');
		if(strpos(strtolower($Uhtml),'charset="utf-8"')===false && strpos(strtolower($Uhtml),"charset='utf-8'")===false) $Uhead .= '<meta charset="utf-8">'."\r\n";
		$Uhead .= '<style type="text/css">'."\r\n".$Ustyle."\r\n".($UstyleSm?'@media screen and (max-width:480px){'."\r\n".$UstyleSm.'}'."\r\n":'').'</style>'."\r\n";
		if($unoPop==1) $Uhead .= '<script type="text/javascript" src="'.$Udep.'includes/js/unoPop.js"></script><link rel="stylesheet" type="text/css" href="'.$Udep.'includes/css/unoPop.css" />'."\r\n";
		if($unoUbusy==1) $Uscript .= 'var Ubusy="'.$Ubusy.'";';
		if($Uscript) $Uhead .= '<script type="text/javascript">'.$Uscript.'</script>'."\r\n";
		$Ufoot .= $Ujsmenu;
		if($Uonload!='') $Ufoot .= '<script type="text/javascript">window.onload=function(){'.$Uonload.'}</script>'."\r\n";
		$Umenu = '<label for="navR" class="navR"></label><input type="checkbox" id="navR" />'."\r\n".'<ul id="nav">'."\r\n".$Umenu.'</ul>';
		// HTML
		$Uhtml = str_replace('[[url]]',$Ua['url'],$Uhtml);
		$Uhtml = str_replace('[[head]]',$Uhead,$Uhtml);
		$Uhtml = str_replace('[[foot]]',$Ufoot,$Uhtml);
		$Uhtml = str_replace('[[menu]]',$Umenu,$Uhtml);
		$Uhtml = str_replace('[[content]]','<div id="pagesContent" class="pagesContent">'."\r\n".$Ucontent."\r\n".'</div><!-- #pageContent -->',$Uhtml);
		// HTML et CONTENT
		$Uhtml = str_replace('[[template]]','uno/template/'.$Ua['tem'].'/',$Uhtml);
		$Uhtml = str_replace('[[title]]',$Utitle,$Uhtml);
		$Uhtml = str_replace('[[description]]',$Udescription,$Uhtml);
		$Uhtml = str_replace('[[name]]',$Uname,$Uhtml);
		$Ua['pub'] = 0;
		if(!isset($Ua['nom'])) $Ua['nom']='index';
		$out = json_encode($Ua);
		if(file_put_contents('data/'.$Ubusy.'/site.json', $out) && file_put_contents('../'.$Ua['nom'].'.html', $Uhtml)) echo T_('The site has been updated');
		else echo '!'.T_('Failure');
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
			if(unlink('../'.$Ua['nom'].'.html')) echo T_('Publication deleted');
			else echo '!'.T_('Failure');
			}
		else echo '!'.T_('Missing file');
		break;
		// ********************************************************************************************
		case 'archivage':
		$d = 'data/_sdata-'.$sdata.'/_unosave';
		$n = $d.'/unosave-'.date('Ymd-Hi').'.zip';
		if(!file_exists($d)) mkdir($d, 0755, true);
		if(f_zip('data/',$n,1)) echo T_('Archiving performed');
		else echo '!'.T_('Failure');
		break;
		// ********************************************************************************************
		case 'selectArchive':
		$d = 'data/_sdata-'.$sdata.'/_unosave/';
		$g=array();
		if($h=opendir($d))
			{
			while(($file=readdir($h))!==false)
				{
				$ext=explode('.',$file);
				$ext=$ext[count($ext)-1];
				if($ext=='zip' && $file!='.' && $file!='..') $g[]=$d.$file;
				}
			closedir($h);
			}
		usort($g,create_function('$a,$b','return filemtime($b)-filemtime($a);'));
		if($g)
			{
			echo '<select id="archive">';
			foreach($g as $r) {$r1=explode("/",$r);	echo '<option value="'.$r.'">'.$r1[count($r1)-1].'</option>'; }
			echo '</select>';
			}
		break;
		// ********************************************************************************************
		case 'restaure':
		$zip = new ZipArchive;
		$f = $zip->open($_POST['zip']);
		if($f===true)
			{
			f_copyDir('data/_sdata-'.$sdata.'/_unosave', '_unosave', 0711);
			f_rmdirR('data');
			mkdir('data');
			$zip->extractTo('data/');
			$zip->close();
			if(!is_dir('data/_sdata-'.$sdata.'/_unosave')) mkdir('data/_sdata-'.$sdata.'/_unosave');
			f_copyDir('_unosave', 'data/_sdata-'.$sdata.'/_unosave', 0711);
			f_rmdirR('_unosave');
			echo T_('Recovery performed');
			}
		else echo '!'.T_('Failure');
		break;
		// ********************************************************************************************
		case 'archDel':
		if(unlink($_POST['zip'])) echo T_('Backup removed');
		else echo '!'.T_('Failure');
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
		echo '!'.T_('Failure');
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
		echo '!'.T_('Failure');
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
			if(isset($a['plug'][basename($r)]))
				{
				if(file_exists('plugins/'.basename($r).'/'.basename($r).'Hook.js')) $b[]='2'.basename($r); // Hook JS
				else $b[]='1'.basename($r);
				}
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
					if(file_exists('plugins/'.$k.'/'.$k.'Ckeditor.js.php')) $b['ck'][] = $ck.'plugins/'.$k.'/'.$k.'Ckeditor.js.php';
					else if(file_exists('plugins/'.$k.'/'.$k.'Ckeditor.js')) $b['ck'][] = $ck.'plugins/'.$k.'/'.$k.'Ckeditor.js';
					}
				}
			echo json_encode($b);
		break;
		// ********************************************************************************************
		case 'init':
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$a = json_decode($q,true);
		// 1. pluginsActifs
		if($Udep=='/uno/') $ck = '../../../';
		else $ck = substr($_SERVER['PHP_SELF'],0,-11); // 11 : central.php
		if(isset($a['plug']))
			{
			foreach($a['plug'] as $k=>$r)
				{
				if(file_exists('plugins/'.$k.'/'.$k.'.php'))
					{
					$a['pl'][]=$k;
					if(file_exists('plugins/'.$k.'/'.$k.'Ckeditor.js.php')) $a['ck'][] = $ck.'plugins/'.$k.'/'.$k.'Ckeditor.js.php';
					else if(file_exists('plugins/'.$k.'/'.$k.'Ckeditor.js')) $a['ck'][] = $ck.'plugins/'.$k.'/'.$k.'Ckeditor.js';
					if(!file_exists('plugins/'.$k.'/'.$k.'.js')) file_put_contents('plugins/'.$k.'/'.$k.'.js', '');
					}
				}
			}
		// 2. plugins
		$d = array();
		if(is_dir('plugins') && $h=opendir('plugins'))
			{
			while(false!==($f=readdir($h)))
				{
				if($f!='.' && $f!='..' && is_dir('plugins/'.$f)) $d[] = $f;
				}
			closedir($h);
			}		
		sort($d);
		foreach($d as $r)
			{
			if(isset($a['plug'][basename($r)]))
				{
				if(file_exists('plugins/'.basename($r).'/'.basename($r).'Hook.js')) $a['plugins'][] = '2'.basename($r); // Hook JS
				else $a['plugins'][] = '1'.basename($r);
				}
			else $a['plugins'][] = '0'.basename($r);
			}
		// 3. theme
		if(isset($a['tem']) && file_exists('template/'.$a['tem'].'/'.$a['tem'].'.php'))
			{
			$a['pl'][] = '_';
			$a['plugins'][] = '9_';
			if(!file_exists('template/'.$a['tem'].'/'.$a['tem'].'.js')) file_put_contents('template/'.$a['tem'].'/'.$a['tem'].'.js', '');
			}
		// 4. getSite
		$q1 = @file_get_contents('data/_sdata-'.$sdata.'/ssite.json');
		if($q1)
			{
			$a1 = json_decode($q1,true);
			$a['mel'] = $a1['mel'];
			}
		if(!isset($a['tit'])) $a['tit'] = '';
		if(!isset($a['desc'])) $a['desc'] = '';
		if(!isset($a['tem'])) $a['tem'] = false;
		echo json_encode($a);
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
		case 'checkUpdate':
		$u = $_POST['u']; $a = array(); $b = 0; $d = array();
		if(file_exists('data/update.json'))
			{
			$q = file_get_contents('data/update.json');
			$a = json_decode($q,true);
			}
		if(!$u) // CMSUno
			{
			if(is_dir('includes/js/ckeditor/')) $c = 1;
			else $c = 0;
			$a['uno']['in'] = (isset($Uversion)?$Uversion:'0.9');
			if(!isset($a['uno']['ext'])) { $a['uno']['ext'] = '0.9'; $b = 1; }
			if(!isset($a['uno']['host'])) { $a['uno']['host'] = 'https://github.com/boiteasite/cmsuno'; $b = 1; }
			if(!file_exists('data/update.json') || filemtime('data/update.json')>time()-30 || filemtime('data/update.json')<time()-86400) // Only once a day
				{
				$last = lastVersion($a['uno']['ext'], $Urawgit, 'uno/includes/css/uno.css');
				if($last!=$a['uno']['ext']) { $a['uno']['ext'] = $last; $b = 1; }
				}
			$bi = explode('.',$a['uno']['in']);
			$be = explode('.',$a['uno']['ext']);
			$bi[0] = intval($bi[0]);
			$be[0] = intval($be[0]);
			if(!isset($bi[1])) $bi[1] = 0; else $bi[1] = intval($bi[1]);
			if(!isset($be[1])) $be[1] = 0; else $be[1] = intval($be[1]);
			if(!isset($bi[2])) $bi[2] = 0; else $bi[2] = intval($bi[2]);
			if(!isset($be[2])) $be[2] = 0; else $be[2] = intval($be[2]);
			if($bi[0]<$be[0] || ($bi[0]==$be[0] && $bi[1]<$be[1]) || ($bi[0]==$be[0] && $bi[1]==$be[1] && $bi[2]<$be[2]))
				{
				echo '1|'.$c.'|'.$a['uno']['ext'];
				}
			else echo '0|'.$c.'|';
			}
		else // Plugin
			{
			if(!isset($a['plug'][$u]['in']) || !isset($a['plug'][$u]['host']))
				{
				if(file_exists('plugins/'.$u.'/version.json'))
					{
					$q = file_get_contents('plugins/'.$u.'/version.json');
					$d = json_decode($q,true);
					$a['plug'][$u]['in'] = (isset($d['version'])?$d['version']:'0.9');
					$a['plug'][$u]['host'] = (isset($d['host'])?$d['host']:'0.9');
					$a['plug'][$u]['ext'] = (isset($d['version'])?$d['version']:'0.9');
					}
				else
					{
					$a['plug'][$u]['in'] = '0.9';
					$a['plug'][$u]['host'] = '';
					$a['plug'][$u]['ext'] = '0.9';
					}
				$b = 1;
				}
			if($a['plug'][$u]['host'] && (filemtime('data/update.json')>time()-30 || filemtime('data/update.json')<time()-86400) && strpos($a['plug'][$u]['host'],'github.com')!==false) // Only once a day
				{
				$last = lastVersion($a['plug'][$u]['ext'], $a['plug'][$u]['host'].'blob/', 'version.json');
				if($last!=$a['plug'][$u]['in'])
					{
					$a['plug'][$u]['ext'] = $last;
					$b = 1;
					}
				}
			$bi = explode('.',$a['plug'][$u]['in']);
			$be = explode('.',$a['plug'][$u]['ext']);
			$bi[0] = intval($bi[0]);
			$be[0] = intval($be[0]);
			if(!isset($bi[1])) $bi[1] = 0; else $bi[1] = intval($bi[1]);
			if(!isset($be[1])) $be[1] = 0; else $be[1] = intval($be[1]);
			if(!isset($bi[2])) $bi[2] = 0; else $bi[2] = intval($bi[2]);
			if(!isset($be[2])) $be[2] = 0; else $be[2] = intval($be[2]);
			if($bi[0]<$be[0] || ($bi[0]==$be[0] && $bi[1]<$be[1]) || ($bi[0]==$be[0] && $bi[1]==$be[1] && $bi[2]<$be[2]))
				{
				echo '1|'.$a['plug'][$u]['in'].'|'.$a['plug'][$u]['ext'];
				}
			else echo '0|'.$a['plug'][$u]['in'].'|';
			}
		if($b) file_put_contents('data/update.json', json_encode($a));
		break;
		// ********************************************************************************************
		case 'update':
		if(file_exists('data/update.json'))
			{
			$u = $_POST['u']; $r = 0;
			$q = file_get_contents('data/update.json');
			$a = json_decode($q,true);
			if($u) // plugin
				{
				if(isset($a['plug'][$u]['ext']) && isset($a['plug'][$u]['host']) && strpos($a['plug'][$u]['host'],'github.com')!==false)
					{
					if(strpos($a['plug'][$u]['host'],'https://github.com/cmsunoPlugins/')!==false)
						{
						$z = 'https://codeload.github.com/cmsunoPlugins/'.substr($a['plug'][$u]['host'],33).'zip/'.$a['plug'][$u]['ext'];
						}
					else $z = $a['plug'][$u]['host'].'archive/'.$a['plug'][$u]['ext'].'.zip';
					$b = 0;
					if(function_exists('curl_version'))
						{
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $z);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						$data = curl_exec ($ch);
						curl_close ($ch);
						file_put_contents('../files/tmp'.$u.'.zip', $data);
						if(filesize('../files/tmp'.$u.'.zip')>0) $b = 1;
						}
					if(!$b) file_put_contents('../files/tmp'.$u.'.zip', fopen($z, 'r'));
					$zip = new ZipArchive;
					$f = $zip->open('../files/tmp'.$u.'.zip');
					if($f===true)
						{
						f_rmdirR('plugins/'.$u);
						$d = trim($zip->getNameIndex(0), '/');
						$zip->extractTo('plugins/');
						$zip->close();
						rename('plugins/'.$d, 'plugins/'.$u);
						unlink('../files/tmp'.$u.'.zip');
						$a['plug'][$u]['in'] = $a['plug'][$u]['ext'];
						file_put_contents('data/update.json', json_encode($a));
						echo T_('New Version Installed').'|'.$a['plug'][$u]['ext'];
						$r= 1;
						}
					}
				}
			else
				{
				$base = dirname(dirname(__FILE__));
				$q1 = file_get_contents('data/'.$Ubusy.'/site.json');
				$a1 = json_decode($q,true);
				// 1. Get new version
		//		$z = 'https://github.com/boiteasite/cmsuno/archive/'.$a['uno']['ext'].'.zip';
				$z = 'https://codeload.github.com/boiteasite/cmsuno/zip/'.$a['uno']['ext'];
				$b = 0;
				if(function_exists('curl_version'))
					{
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $z);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$data = curl_exec ($ch);
					curl_close ($ch);
					file_put_contents('../files/tmpuno.zip', $data);
					if(filesize('../files/tmpuno.zip')>0) $b = 1;
					}
				if(!$b) file_put_contents('../files/tmpuno.zip', fopen($z, 'r'));
				$zip = new ZipArchive;
				$f = $zip->open('../files/tmpuno.zip');
				if($f===true && filesize('../files/tmpuno.zip')>10000)
					{
					// 2. check free space : 4 x zip
					$sp = copy($base.'/files/tmpuno.zip', $base.'/files/tmpuno1.zip');
					if($sp) $sp = copy($base.'/files/tmpuno.zip', $base.'/files/tmpuno2.zip');
					if($sp) $sp = copy($base.'/files/tmpuno.zip', $base.'/files/tmpuno3.zip');
					if($sp) $sp = copy($base.'/files/tmpuno.zip', $base.'/files/tmpuno4.zip');
					if(!file_exists($base.'/files/tmpuno1.zip') || !file_exists($base.'/files/tmpuno2.zip') || !file_exists($base.'/files/tmpuno3.zip') || !file_exists($base.'/files/tmpuno4.zip')) $sp = false;
					if(!$sp)
						{
						echo '!'.T_('Not enough disk space');
						break;
						}
					unlink($base.'/files/tmpuno1.zip');
					unlink($base.'/files/tmpuno2.zip');
					unlink($base.'/files/tmpuno3.zip');
					unlink($base.'/files/tmpuno4.zip');
					// 3. Save current datas
					$q2 = @file_get_contents($base.'/uno/data/_sdata-'.$sdata.'/ssite.json');
					if(is_dir($base.'/files/.tmb')) f_rmdirR($base.'/files/.tmb'); // free space
					if(is_dir($base.'/uno/includes/elfinder/.tmb')) f_rmdirR($base.'/uno/includes/elfinder/.tmb');
					if(is_dir($base.'/uno/data')) { f_copyDir($base.'/uno/data', $base.'/files/tmpdata'); f_rmdirR($base.'/uno/data'); }
					if(is_dir($base.'/uno/plugins')) { f_copyDir($base.'/uno/plugins', $base.'/files/tmpplugins'); f_rmdirR($base.'/uno/plugins'); }
					if(is_dir($base.'/uno/template')) { f_copyDir($base.'/uno/template', $base.'/files/tmptemplate'); f_rmdirR($base.'/uno/template'); }
					if(file_exists($base.'/uno/password.php')) copy($base.'/uno/password.php', $base.'/files/tmppassword.php');
					// 4. Extract in Files
					$d = trim($zip->getNameIndex(0), '/');
					$zip->extractTo($base.'/files/');
					$zip->close();
					unlink($base.'/files/tmpuno.zip');
					// 5. Install new version
					f_rmdirR($base.'/uno');
					unlink($base.'/uno.php');
					unlink($base.'/README.md');
					f_copyDir($base.'/files/'.$d, $base);
					f_rmdirR($base.'/files/'.$d);
					if($q2)
						{
						$a2 = json_decode($q2,true);
						if(!empty($a2['git'])) // light version
							{
							if(is_dir($base.'/uno/includes/js')) f_rmdirR($base.'/uno/includes/js');
							if(is_dir($base.'/uno/includes/img')) f_rmdirR($base.'/uno/includes/img');
							if(is_dir($base.'/uno/includes/css')) f_rmdirR($base.'/uno/includes/css');
							}
						}
					f_rmdirR($base.'/uno/data');
					f_rmdirR($base.'/uno/plugins');
					f_rmdirR($base.'/uno/template');
					f_copyDir($base.'/files/tmpdata', $base.'/uno/data'); f_rmdirR($base.'/files/tmpdata');
					f_chmodR($base.'/uno/data/_sdata-'.$sdata,0600,0711);
					f_copyDir($base.'/files/tmpplugins', $base.'/uno/plugins'); f_rmdirR($base.'/files/tmpplugins');
					f_copyDir($base.'/files/tmptemplate', $base.'/uno/template'); f_rmdirR($base.'/files/tmptemplate');
					copy($base.'/files/tmppassword.php', $base.'/uno/password.php'); unlink($base.'/files/tmppassword.php');
					$config = '<?php $lang = "'.$lang.'"; $sdata = "'.$sdata.'"; $Uversion = "'.$a['uno']['ext'].'"; ?>';
					file_put_contents($base.'/uno/config.php', $config);
					echo T_('New Version Installed').'|'.$a['uno']['ext'];
					$r= 1;
					}
				}
			}
		if(!$r) echo '!'.T_('Failure');
		break;
		// ********************************************************************************************
		case 'lighter':
		if(is_dir('includes/js')) f_rmdirR('includes/js');
		if(is_dir('includes/img')) f_rmdirR('includes/img');
		if(is_dir('includes/css')) f_rmdirR('includes/css');
		//
		$q = @file_get_contents('data/_sdata-'.$sdata.'/ssite.json');
		if($q) $a = json_decode($q,true);
		else $a = array();
		$a['git'] = 1; // here in case of multipage
		$out = json_encode($a);
		file_put_contents('data/_sdata-'.$sdata.'/ssite.json',$out);
		echo T_('CMSUno is in Lightened Version');
		break;
		// ********************************************************************************************
		case 'pluglist':
		if(!file_exists('data/plugin-list.json') || filemtime('data/plugin-list.json')<time()-604800) // 7 days
			{
			if(!is_dir('../files/tmp/')) mkdir('../files/tmp/');
			$z = 'https://codeload.github.com/cmsunoPlugins/plugin-list/zip/master';
			$b = @get_headers($z);
			if(!empty($b) && substr($b[0],9,3)==200)
				{
				$b = 0;
				if(function_exists('curl_version'))
					{
					$ch = curl_init($z);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$data = curl_exec($ch);
					curl_close($ch);
					file_put_contents('../files/plugin-list.zip', $data);
					if(filesize('../files/plugin-list.zip')>0) $b = 1;
					}
				if(!$b) file_put_contents('../files/plugin-list.zip', fopen($z, 'r'));
				$zip = new ZipArchive;
				$f = $zip->open('../files/plugin-list.zip');
				if($f===true)
					{
					$zip->extractTo('../files/tmp/');
					$zip->close();
					if(file_exists('../files/tmp/plugin-list-master/plugin-list.json')) copy('../files/tmp/plugin-list-master/plugin-list.json', 'data/plugin-list.json');
					unlink('../files/plugin-list.zip');
					f_rmdirR('../files/tmp/plugin-list-master/');
					}
				}
			}
		if(file_exists('data/plugin-list.json'))
			{
			$q = file_get_contents('data/plugin-list.json');
			$a = json_decode($q,true);
			$o = '<div class="blocForm"><h2>'.T_('Add or remove plugins from the CMSUno GitHub list').'</h2>';
			$o .= '<table class="plugList">';
			foreach($a as $r)
				{
				$b = 0;
				if(file_exists('plugins/'.$r['p'].'/'.$r['p'].'.php')) $b = 1;
				$o .= '<tr><td>'.(!empty($r['b'])?'<div class="plugBest"></div>':'').'</td>';
				$o .= '<td>'.$r['p'].'</td><td>'.$r['d'].'</td>';
				if($b) $o .= '<td><div class="plugDel" title="'.T_('Remove').'" onClick="f_plugDel(\''.$r['p'].'\')"></div></td>';
				else $o .= '<td><div class="plugAdd" title="'.T_('Add').'" onClick="f_plugAdd(\''.$r['p'].'\')"></div></td>';
				$o .= '</tr>';
				}
			$o .= '</table></div>';
			echo $o;
			}
		else echo '!'.T_('Failure');
		break;
		// ********************************************************************************************
		case 'plugadd':
		$q = @file_get_contents('data/_sdata-'.$sdata.'/ssite.json');
		if($q)
			{
			$a = json_decode($q,true);
			if(!empty($a['plugadd']))
				{
				echo '!'.T_('Disabled by Admin');
				return;
				}
			}
		$p = strip_tags(trim($_POST['plug']));
		$z = 'https://codeload.github.com/cmsunoPlugins/'.$p.'/zip/master';
		$b = 0;
		if(function_exists('curl_version'))
			{
			$ch = curl_init($z);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec ($ch);
			curl_close ($ch);
			file_put_contents('../files/'.$p.'.zip', $data);
			if(filesize('../files/'.$p.'.zip')>0) $b = 1;
			}
		if(!$b) file_put_contents('../files/'.$p.'.zip', fopen($z, 'r'));
		$zip = new ZipArchive;
		$f = $zip->open('../files/'.$p.'.zip');
		if($f===true)
			{
			$zip->extractTo('plugins/');
			$zip->close();
			rename('plugins/'.$p.'-master', 'plugins/'.$p);
			unlink('../files/'.$p.'.zip');
			}
		echo T_('Plugin added');
		break;
		// ********************************************************************************************
		case 'plugdel':
		$p = strip_tags(trim($_POST['plug']));
		if(file_exists('plugins/'.$p.'/'.$p.'.php'))
			{
			f_rmdirR('plugins/'.$p.'/');
			echo T_('Plugin removed');
			}
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>
