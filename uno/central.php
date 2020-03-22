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
function f_lazy($f) {
	global $Udep;
	$out=''; $src=''; $alt=''; $b=0; $c=0; $v=4;
	do {
		if($b==0) do { ++$v; } while(substr($f,$v-4,4)!='<img' && $v<strlen($f));
		if(substr($f,$v-4,4)=='<img') { $out.=(substr($f,$c,$v-$c+1)); $b=1; }
		else if($b==1 && (substr($f,$v-5,5)=='src="' || substr($f,$v-5,5)=="src='")) {
			do { $src.=substr($f,$v,1); ++$v; }
			while(substr($f,$v,1)!='"' && substr($f,$v,1)!="'" && $v<strlen($f));
			$out .= $Udep.'includes/css/a.png" data-echo="'.$src.'"'; // ECHO
		}
		else if($b==1 && (substr($f,$v-5,5)=='alt="' || substr($f,$v-5,5)=="alt='") && substr($f,$v-5,6)!='alt=""' && substr($f,$v-5,6)!="alt=''") {
			do { $out.=substr($f,$v,1); $alt.=substr($f,$v,1); ++$v; }
			while(substr($f,$v,1)!='"' && substr($f,$v,1)!="'" && $v<strlen($f));
			$out.=substr($f,$v,1);
		}
		else if($b==1) {
			$out.=substr($f,$v,1);
			if(substr($f,$v,1)=='>') {$out.='<noscript><img src="'.$src.'" style="display:inline;" alt="'.$alt.'"></noscript>'; $c=$v+1; $src=''; $alt=''; $b=0;}
		}
		++$v;
	}
	while($v<strlen($f));
	$out.=(substr($f,$c,$v-$c));
	return $out;
}
//
function f_zip($d,$n,$e=0) {
	// $e = 1 : no zip file in arch
	// zip un dossier $d (/aaaz/bbb/ccc/) avec le nom $n (nnn.zip)
	if(!extension_loaded('zip') || !file_exists($d)) return false;
	$zip = new ZipArchive(); if(!$zip->open($n, ZIPARCHIVE::CREATE)) return false;
	$d = str_replace('\\', '/', realpath($d));
	if(is_dir($d)===true) {
		$f = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d), RecursiveIteratorIterator::SELF_FIRST);
		foreach($f as $r) {
			$r = str_replace('\\', '/', $r);
			if(in_array(substr($r, strrpos($r, '/')+1), array('.', '..')))
			continue;
			$r = realpath($r);
			if(is_dir($r)===true) $zip->addEmptyDir(str_replace($d . '/', '', $r . '/')); 
			else if(is_file($r)===true) {
				$ext=explode('.',$r);
				$ext=$ext[count($ext)-1];
				if($ext!='zip' || !$e) $zip->addFromString(str_replace($d. '/', '', $r), file_get_contents($r));
			}
		}
	}
	else if(is_file($d)===true) {
		$ext=explode('.',$r);
		$ext=$ext[count($ext)-1];
		if($ext!='zip' || !$e) $zip->addFromString(basename($d), file_get_contents($d));
	}
	return $zip->close();
}
//
function f_copyDir($s,$d,$p=0755) {
	if(is_link($s)) return symlink(readlink($s), $d);
	if(is_file($s)) return copy($s, $d);
	if(!is_dir($d)) mkdir($d, $p);
	$dir = dir($s);
	while(false!==$e=$dir->read()) {
		if($e=='.'||$e=='..') continue;
		f_copyDir($s.'/'.$e, $d.'/'.$e, $p);
	}
	$dir->close();
	return true;
}
//
function f_rmdirR($dir) {
	$files = array_diff(scandir($dir), array('.','..'));
	foreach($files as $file) {
		(is_dir("$dir/$file")) ? f_rmdirR("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}
//
function f_chmodR($path, $fr=0644, $dr=0755) {
	if(!file_exists($path)) return(false);
	if(is_file($path)) @chmod($path, $fr);
	else if(is_dir($path)) {
		$re = scandir($path);
		$q = array_slice($re, 2);
		foreach($q as $r) f_chmodR($path."/".$r, $fr, $dr);
		@chmod($path, $dr);
	}
	return(true);
}
//
function lastVersion($f,$g,$h) {
	// Version : $f : minimum 2 digit : 1.0, 2.4.5 maximum 3 digit
	// Version 2 => 2.0 (2 digit if 0)
	// Version 1.4.0 => 1.4 (not 3 digit if 0)
	$a = explode('.',$f);
	if(!isset($a[0])) $a[0] = 1;
	if(!isset($a[1])) $a[1] = 0;
	if(!isset($a[2])) $a[2] = 0;
	// next major version ? format : 2.0
	$b = ($a[0]+1);
	while(urlExists($g.$b.'.0/'.$h)) {
		$a[0] = ($a[0]+1);
		$a[1] = 0; $a[2] = 0;
		$b = ($a[0]+1);
	}
	// next version ? format : 1.7
	$b = ($a[1]+1);
	while(urlExists($g.$a[0].'.'.$b.'/'.$h)) {
		$a[1] = ($a[1]+1);
		$a[2] = 0;
		$b = ($a[1]+1);
	}
	// next minor version ? 1.7.3
	$b = ($a[2]+1);
	while(urlExists($g.$a[0].'.'.$a[1].'.'.$b.'/'.$h)) {
		$a[2] = ($a[2]+1);
		$b = ($a[2]+1);
	}
	return $a[0].'.'.$a[1].($a[2]?'.'.$a[2]:'');
}
//
function urlExists($u) {
	$head = get_headers($u);
	if($head && strpos($head[0],'404')===false) return true;
	// other try with curl
	if(!$head && function_exists('curl_version')) {
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
if(isset($_POST['action'])) {
	$b = 0;
	if(file_exists('data/busy.json')) {
		$q = file_get_contents('data/busy.json');
		$a = json_decode($q,true);
		$Ubusy = (!empty($a['nom'])?$a['nom']:'');
		$Umaster = (!empty($a['master'])?$a['master']:'');
		if(!$Ubusy || !is_dir('data/'.$Ubusy)) {
			$h=opendir('data/');
			while(($d=readdir($h))!==false) {
				if(is_dir('data/'.$d) && file_exists('data/'.$d.'/site.json')) {
					file_put_contents('data/busy.json', '{"nom":"'.$d.'","master":"'.$d.'"}');
					$Ubusy = $d;
					$Umaster = $d;
					$b = 1;
				}
			}
			closedir($h);
		}
		else $b = 1;
	}
	if(!$b) {
		if(!is_dir('data')) mkdir('data'); if(!is_dir('data/_sdata-'.$sdata)) mkdir('data/_sdata-'.$sdata,0711);
		if(!is_dir('data/index')) mkdir('data/index');
		if(!is_dir('data/_sdata-'.$sdata.'/index')) mkdir('data/_sdata-'.$sdata.'/index',0711);
		file_put_contents('data/busy.json', '{"nom":"index","nmaster":"index"}');
		$Ubusy = 'index';
		$Umaster = 'index';
	}
	if(!file_exists('data/'.$Ubusy.'/chap0.txt')) file_put_contents('data/'.$Ubusy.'/chap0.txt', 'blabla...');
	if(!file_exists('data/'.$Ubusy.'/site.json')) file_put_contents('data/'.$Ubusy.'/site.json', '{"chap":[{"d":"0","t":"'.T_("Welcome").'"}],"pub":0}');
	switch($_POST['action']) {
		// ********************************************************************************************
		case 'getSite':
		if(file_exists(dirname(__FILE__).'/../files/archive.zip')) unlink(dirname(__FILE__).'/../files/archive.zip');
		$a = array();
		$q = @file_get_contents('data/'.$Ubusy.'/site.json');
		$q1 = @file_get_contents('data/_sdata-'.$sdata.'/ssite.json');
		if($q) $a = json_decode($q,true);
		if($q1) {
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
		foreach($a['chap'] as $k=>$v) {
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
		foreach($a['chap'] as $k=>$v) {
			if($k==$_POST['chap']) {
				$a['chap'][$k]['d'] = strip_tags($_POST['data']);
				$a['chap'][$k]['t'] = strip_tags($_POST['titre']);
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
		if(!isset($a['w3'])) $a['w3'] = 0; // default
		$c = preg_replace_callback('/(<img[^>]*src=["\'])([^"\']*)/', function($m) { return ''.$m[1].substr($m[2],strpos($m[2],'files/'));}, $_POST['content']); // lien relatif
		$out = json_encode($a);
		if(file_put_contents('data/'.$Ubusy.'/site.json', $out) && file_put_contents('data/'.$Ubusy.'/chap'.$_POST['data'].'.txt', $c)) echo T_('Backup performed');
		else echo '!'.T_('Impossible backup');
		break;
		// ********************************************************************************************
		case 'sauvePlace':
		if(isset($_POST['chap']) && isset($_POST['place']) && $_POST['chap']!=$_POST['place']) {
			$q = file_get_contents('data/'.$Ubusy.'/site.json');
			$a = json_decode($q,true);
			$b=0; $a1 = $a['chap'];
			foreach($a1 as $k=>$v) {
				if($_POST['place']<$_POST['chap']) { // le chapitre remonte
					if($k==$_POST['place']) {
						$a['chap'][$k] = $a1[$_POST['chap']];
						$b=1;
					}
					else if($k==$_POST['chap']) $b=0;
					if($b==1) $a['chap'][$k+1] = $v;
				}
				else {
					if($k==$_POST['chap']) $b=1;
					else if($k==$_POST['place']) {
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
		if(!function_exists('password_hash')) include('uno/includes/password_hashing.php'); // php 5.5 missing => https://github.com/ircmaxell/password_compat (php>5.3)
		$a = $_POST['user']; $b = $_POST['pass'];
		if($_POST['user0']=='' || $_POST['pass0']=='') { // only lang
			$config = '<?php $lang = "'.$_POST['lang'].'"; $sdata = "'.$sdata.'"; $Uversion = "'.(isset($Uversion)?$Uversion:'1.0').'"; ?>';
			if(file_put_contents('config.php', $config)) echo T_('The language was changed');
			else echo '!'.T_('Impossible backup');
		}
		else if($_POST['user0']===$user && password_verify($_POST['pass0'],$pass)) {
			$password = '<?php if(!defined(\'CMSUNO\')) exit(); $user = "'.$a.'"; $pass = \''.password_hash($b, PASSWORD_BCRYPT).'\'; ?>';
			$config = '<?php $lang = "'.$_POST['lang'].'"; $sdata = "'.$sdata.'"; $Uversion = "'.(isset($Uversion)?$Uversion:'1.0').'"; ?>';
			if(file_put_contents('password.php', $password) && file_put_contents('config.php', $config)) echo T_('The login / password were changed');
			else echo '!'.T_('Impossible backup');
		}
		else {
			echo '!'.T_('Wrong current elements'); exit;
		}
		break;
		// ********************************************************************************************
		case 'sauveConfig':
		$n = (($_POST['nom']!="")?preg_replace("/[^A-Za-z0-9-_]/",'',$_POST['nom']):'index');
		while(substr($n,0,1)=="_") $n = substr($n,1);
		if($Ubusy!=$n && $n!="") {
			if(!is_dir('data/'.$n) && is_dir('data/'.$Ubusy)) f_copyDir('data/'.$Ubusy, 'data/'.$n);
			else mkdir('data/'.$n, 0755, true);
			if(!is_dir('data/_sdata-'.$sdata.'/'.$n) && is_dir('data/_sdata-'.$sdata.'/'.$Ubusy)) f_copyDir('data/_sdata-'.$sdata.'/'.$Ubusy, 'data/_sdata-'.$sdata.'/'.$n, 0711);
			else mkdir('data/_sdata-'.$sdata.'/'.$n, 0711, true);
			f_rmdirR('data/'.$Ubusy);
			$Ubusy = $n;
			file_put_contents('data/busy.json', '{"nom":"'.$Ubusy.'","master":"'.$Umaster.'"}');
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
		$a['tit'] = strip_tags($_POST['tit']);
		$a['desc'] = strip_tags($_POST['desc']);
		$a['url'] = strip_tags($_POST['url']);
		if(substr($a['url'],-1)=='/') $a['url'] = substr($a['url'],0,-1);
		$a['tem'] = $_POST['tem'];
		$a['nom'] = $n;
		if(intval($_POST['edw'])) $a['edw'] = intval($_POST['edw']); else $a['edw'] = 960;
		if(intval($_POST['ofs'])) $a['ofs'] = intval($_POST['ofs']); else $a['ofs'] = 0;
		if($_POST['lazy']=="true") $a['lazy']=1; else $a['lazy']=0;
		if($_POST['jq']=="true") $a['jq']=1; else $a['jq']=0;
		if($_POST['w3']=="true") $a['w3']=1; else $a['w3']=0;
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
		foreach($a1 as $k=>$v) {
			if($b==0 && $k==$_POST['chap']) {
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
		foreach($a['chap'] as $k=>$v) {
			if($k==$_POST['chap']) {
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
		$Uhead = ''; $Ufoot = ''; $Uonload = ''; $Ucontent = ''; $Ustyle = '.blocChap{clear:both}'."\r\n"; $UstyleSm = ''; $Uw3 = array();
		$Uscript = ''; $Umenu = ''; $Ujsmenu = '<script type="text/javascript" src="'.$Udep.'includes/js/uno_menu.js"></script>'."\r\n";
		$unoPop=0; // Include JS files
		$unoUbusy=0; // Include Ubusy in JS
		if(!empty($_POST['Ubusy']) && file_exists('data/'.$_POST['Ubusy'].'/site.json')) $Ubusy = $_POST['Ubusy'];
		$q = file_get_contents('data/'.$Ubusy.'/site.json');
		$Ua = json_decode($q,true);
		if(!isset($Ua['tem']) || !isset($Ua['url']) || !isset($Ua['tit']) || !isset($Ua['desc']) || !isset($Ua['ofs'])) {
			echo '!'.T_('Save Config First');
			exit;
		}
		if(!empty($Ua['w3'])) include('w3cssDefault.php');
		else $Ustyle .= '.w3-hide{display:none!important}.w3-right{float:right!important}.w3-button{cursor:pointer}.w3-container{padding:0 16px}.w3-section{margin:16px 0}'."\r\n";
		// *** Plugins & Theme make 0 ***
		if(isset($Ua['plug'])) foreach($Ua['plug'] as $Uk=>$Ur) {
			if(file_exists('plugins/'.$Uk.'/'.$Uk.'Make0.php')) include('plugins/'.$Uk.'/'.$Uk.'Make0.php');
		}
		if(file_exists('template/'.$Ua['tem'].'/'.$Ua['tem'].'Make0.php')) include('template/'.$Ua['tem'].'/'.$Ua['tem'].'Make0.php');
		// *** / ***
		$UmenuW3 = '<div id="nav">'."\r\n".'<div class="'.(isset($Uw3['UnoMenu']['large-bar'])?$Uw3['UnoMenu']['large-bar']:'w3-bar').'">'."\r\n";
		$UmenuW3S = '<div id="navSmall" class="'.(isset($Uw3['UnoMenu']['small-bar'])?$Uw3['UnoMenu']['small-bar']:'w3-bar-block').' '.(isset($Uw3['UnoMenu']['small-hide'])?$Uw3['UnoMenu']['small-hide']:'w3-hide-large w3-hide-medium w3-hide').'">'."\r\n";
		$Uscript .= 'var Umenuoffset='.intval($Ua['ofs']).';';
		$Uhtml = file_get_contents('template/'.$Ua['tem'].'/template.html');
		$Ustyle .= 'h2.nav1 a,h2.nav2 a,h2.nav1 a:hover,h2.nav2 a:hover{color:inherit;text-decoration:none;}'."\r\n";
		foreach($Ua['chap'] as $k=>$v) {
			if(empty($Ua['chap'][$k]['od'])) {
				$c = file_get_contents('data/'.$Ubusy.'/chap'.$v['d'].'.txt');
				$w = remove_accents($v['t']);
				$w = preg_replace('/[^a-zA-Z0-9%]/s','',$w);
				$Ucontent .= '<div id="'.$w.'BlocChap" class="blocChap '.(isset($Uw3['Uno']['w3-section'])?$Uw3['Uno']['w3-section']:'w3-section').'">'."\r\n";
				// menu
				if(empty($Ua['chap'][$k]['om'])) { // menu not hidden
					$d = array(); $m = '';
					$Umenu .= '<li><a href="#'.$w.'"'.($k==0?' class="active"':'').'>'.stripslashes($v['t']).'</a>';
					preg_match_all('/<h2[^>]*>([^<]*)/i', $c, $d); // submenu H2
					if(!empty($d[1][0])) {
						$m = "\r\n\t".'<ul class="subMenu">';
						$UmenuW3 .= '<div class="'.(isset($Uw3['UnoMenu']['w3-dropdown'])?$Uw3['UnoMenu']['w3-dropdown']:'w3-dropdown-hover').'"><a href="#'.$w.'" class="'.(isset($Uw3['UnoMenu']['large-bar-parent-item'])?$Uw3['UnoMenu']['large-bar-parent-item']:'w3-bar-item w3-button').' '.(isset($Uw3['UnoMenu']['large-hide'])?$Uw3['UnoMenu']['large-hide']:'w3-hide-small').'">'.stripslashes($v['t']).'</a>';
						$UmenuW3 .= '<div class="'.(isset($Uw3['UnoMenu']['w3-dropdown-content'])?$Uw3['UnoMenu']['w3-dropdown-content']:'w3-dropdown-content').' '.(isset($Uw3['UnoMenu']['w3-bar-block'])?$Uw3['UnoMenu']['w3-bar-block']:'w3-bar-block').'">'."\r\n";
						$e = 0; $m3 = '';
						foreach($d[1] as $r) {
							if($r) {
								++$e;
								$m .= '<li><a href="#'.$w.'-'.preg_replace('/[^a-zA-Z0-9%]/s','',$r).'-'.$e.'">'.$r.'</a></li>';
								$m3 .= '<a href="#'.$w.'-'.preg_replace('/[^a-zA-Z0-9%]/s','',$r).'-'.$e.'" class="'.(isset($Uw3['UnoMenu']['large-bar-sub-item'])?$Uw3['UnoMenu']['large-bar-sub-item']:'w3-bar-item w3-button').'">'.$r.'</a>';
							}
						}
						$m .= '</ul>'."\r\n";
						$UmenuW3 .= $m3."\r\n".'</div></div>'."\r\n";
					}
					else $UmenuW3 .= '<a href="#'.$w.'" class="'.(isset($Uw3['UnoMenu']['large-bar-item'])?$Uw3['UnoMenu']['large-bar-item']:'w3-bar-item w3-button').' '.(isset($Uw3['UnoMenu']['large-hide'])?$Uw3['UnoMenu']['large-hide']:'w3-hide-small').'">'.stripslashes($v['t']).'</a>'."\r\n";
					$UmenuW3S .= '<a href="#'.$w.'" class="'.(isset($Uw3['UnoMenu']['small-bar-item'])?$Uw3['UnoMenu']['small-bar-item']:'w3-bar-item w3-button').'">'.stripslashes($v['t']).'</a>'."\r\n";
					$Umenu .= $m.'</li>'."\r\n";
				}
				// titre + class pour menu & submenu
				$Ucontent .= '<h2 id="'.$w.'" class="nav1'.((!empty($Ua['chap'][$k]['om']))?' navOff':'').'" '.((!empty($Ua['chap'][$k]['ot']))?'style="height:0;padding:0;border:0;margin:0;overflow:hidden;"':'').'><a name="'.$w.'">'.stripslashes($v['t']).'</a></h2>'."\r\n";
				$e = 0;
				$c = preg_replace_callback('/(<h2[^>]*)(>)([^<]*)/i', function($m) {
					global $w; global $e;
					if($m[3]) {
						++$e;
						$t = $w.'-'.preg_replace('/[^a-zA-Z0-9%]/s','',$m[3]).'-'.$e;
						return $m[1].' id="'.$t.'" class="nav2"><a name="'.$t.'">'.$m[3].'</a>';
					}
					else return $m[1].$m[2];
				}, $c); // submenu
				$Ucontent .= $c.'</div><!-- #'.$w.'BlocChap -->'."\r\n"; // Comment = End bloc detection by plugins !
			}
		}
		$Utitle = (isset($Ua['tit']))?stripslashes($Ua['tit']):"";
		$Udescription = (isset($Ua['desc']))?stripslashes($Ua['desc']):"";
		$Uname = (isset($Ua['nom']))?stripslashes($Ua['nom']):"";
		$Ucontent = stripslashes($Ucontent);
		$u = dirname($_SERVER['PHP_SELF']).'/../';
		$Ucontent = str_replace($u,'',$Ucontent);
		if(!empty($Ua['jq'])) {
			$Uhead .= '<!--[if(!IE)|(gt IE 8)]><!--><script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script><!--<![endif]-->'."\r\n"
				.'<!--[if lte IE 8]><script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script><![endif]-->'."\r\n";
			if($Udep=='uno/') $Uhead .= '<script type="text/javascript">window.jQuery || document.write(\'<script src="uno/includes/js/jquery-3.4.1.min.js">\x3C/script>\')</script>'."\r\n";
			$Uhead .= '<script type="text/javascript" src="'.$Udep.'includes/js/jquery-migrate-1.4.1.min.js"></script>'."\r\n";
		}
		if(!empty($Ua['w3'])) $Uhead .= '<link rel="stylesheet" href="'.$Udep.'includes/css/w3.css"> '."\r\n";
		if(!empty($Ua['lazy'])) {
			$Ustyle .= '.pagesContent img[data-echo]{display:none;background:#fff url('.$Udep.'includes/css/a.gif) no-repeat center center;}'."\r\n";
			$Ufoot .= '<script type="text/javascript" src="'.$Udep.'includes/js/echo.min.js"></script>'."\r\n".'<script type="text/javascript">var css=".pagesContent img[data-echo]{display:inline;}",head=document.head||document.getElementsByTagName("head")[0],style=document.createElement("style");style.type="text/css";if(style.styleSheet) style.styleSheet.cssText=css;else style.appendChild(document.createTextNode(css));head.appendChild(style);echo.init({offset:900,throttle:250});echo.render();</script>'."\r\n";
			$Ucontent = f_lazy($Ucontent);
		}
		// *** Plugins make 1 -> 5 ***
		if(isset($Ua['plug'])) for($Uv=1;$Uv<=5;++$Uv) { // 1 first, 5 last, no number = 3 && alphabetic order
			foreach($Ua['plug'] as $Uk=>$Ur) {
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
		if($unoPop==1) {
			$Ufoot .= "<script type=\"text/javascript\">function unoPopFade(f,h){f-=.05;if(f>0)setTimeout(function(){h.style.opacity=f;unoPopFade(f,h);},30);else document.body.removeChild(h);};function unoGvu(p){var r=new RegExp('(?:[\?&]|&amp;)'+p+'=([^&]+)', 'i');var match=window.location.search.match(r);return(match&&match.length>1)?match[1]:'';};";
			if(!empty($Ua['w3'])) $Ufoot .= "function unoPop(i,t){if(document.getElementById('unoPop')==null){var h=document.createElement('div'),m,n,o;h.id='unoPop';h.className='".(isset($Uw3['modal']['w3-modal'])?$Uw3['modal']['w3-modal']:'w3-modal')." unoPop';h.style.display='block';h.style.zIndex='9999';m=document.createElement('div');m.className='".(isset($Uw3['modal']['w3-modal-content'])?$Uw3['modal']['w3-modal-content']:'w3-modal-content')."';n=document.createElement('header');n.className='".(isset($Uw3['modal']['header'])?$Uw3['modal']['header']:'')."';o=document.createElement('span');o.innerHTML='&nbsp;';n.appendChild(o);o=document.createElement('strong');o.className='".(isset($Uw3['modal']['xclose'])?$Uw3['modal']['xclose'].' ':'')."unoPopClose';o.innerHTML='&times;';o.onclick=function(){document.body.removeChild(document.getElementById('unoPop'))};n.appendChild(o);m.appendChild(n);n=document.createElement('div');n.className='".(isset($Uw3['modal']['content'])?$Uw3['modal']['content']:'')."';n.innerHTML=i;if(i.length<50)n.style.textAlign='center';m.appendChild(n);n=document.createElement('footer');n.className='".(isset($Uw3['modal']['footer'])?$Uw3['modal']['footer']:'')."';o=document.createElement('span');o.innerHTML='&nbsp;';n.appendChild(o);m.appendChild(n);h.appendChild(m);document.body.appendChild(h);if(t!=0)setTimeout(function(){unoPopFade(1,h);},t);}};";
			else {
				$Ufoot .= "function unoPop(i,t){if(document.getElementById('unoPop')==null){var h=document.createElement('div'),m,n;h.id='unoPop';h.className='unoPop';m=document.createElement('div');m.className='unoPopContent';n=document.createElement('a');n.className='unoPopClose';n.href='javascript:void(0)';n.onclick=function(){document.body.removeChild(document.getElementById('unoPop'))};m.innerHTML=i;h.appendChild(n);h.appendChild(m);document.body.appendChild(h);if(t!=0)setTimeout(function(){unoPopFade(1,h);},t);}};";
				$Uhead .= '<link rel="stylesheet" type="text/css" href="'.$Udep.'includes/css/unoPop.css" />'."\r\n";
			}
			$Ufoot .= "</script>\r\n";
		}
		if($unoUbusy==1) $Uscript .= 'var Ubusy="'.$Ubusy.'";';
		if(strpos($Uhtml,'[[menuW3]]')!==false) {
			$Uscript .= 'var UactiveMenuClass="'.(!empty($Uw3['UnoMenu']['activeMenuClass'])?$Uw3['UnoMenu']['activeMenuClass']:'active').'";';
			$UmenuW3 .= '<a href="javascript:void(0)" class="'.(isset($Uw3['UnoMenu']['small-bar-open'])?$Uw3['UnoMenu']['small-bar-open']:'w3-bar-item w3-button').'" onclick="sMenu()">&#9776;</a></div>'."\r\n";
			$UmenuW3 .= $UmenuW3S.'</div>'."\r\n".'</div><!-- #nav -->';
			$Uhtml = str_replace('[[menuW3]]',$UmenuW3,$Uhtml);
		}
		if(!empty($Uscript)) $Uhead .= '<script type="text/javascript">'.$Uscript.'</script>'."\r\n";
		$Ufoot .= $Ujsmenu;
		if($Uonload!='') $Ufoot .= '<script type="text/javascript">window.onload=function(){'.$Uonload.'}</script>'."\r\n";
		$Umenu = '<label for="navR" class="navR"></label><input type="checkbox" id="navR" />'."\r\n".'<ul id="nav">'."\r\n".$Umenu.'</ul><!-- #nav -->';
		// HTML
		$Uhtml = str_replace('[[url]]',$Ua['url'],$Uhtml);
		$Uhtml = str_replace('[[head]]',$Uhead,$Uhtml);
		$Uhtml = str_replace('[[foot]]',$Ufoot,$Uhtml);
		$Uhtml = str_replace('[[menu]]',$Umenu,$Uhtml);
		$Uhtml = str_replace('[[content]]','<div id="pagesContent" class="pagesContent '.(isset($Uw3['Uno']['div-pagesContent'])?$Uw3['Uno']['div-pagesContent']:'w3-container').'">'."\r\n".$Ucontent."\r\n".'</div><!-- #pageContent -->',$Uhtml);
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
		if(file_exists('data/error.json')) {
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
		if(file_exists('../'.$Ua['nom'].'.html')) {
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
		if($h=opendir($d)) {
			while(($file=readdir($h))!==false) {
				$ext=explode('.',$file);
				$ext=$ext[count($ext)-1];
				if($ext=='zip' && $file!='.' && $file!='..') $g[]=$d.$file;
			}
			closedir($h);
		}
		usort($g,create_function('$a,$b','return filemtime($b)-filemtime($a);'));
		if($g) {
			echo '<select id="archive">';
			foreach($g as $r) {$r1=explode("/",$r);	echo '<option value="'.$r.'">'.$r1[count($r1)-1].'</option>'; }
			echo '</select>';
		}
		break;
		// ********************************************************************************************
		case 'restaure':
		$zip = new ZipArchive;
		$f = $zip->open($_POST['zip']);
		if($f===true) {
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
		if(file_exists('data/'.$Ubusy.'/site.json')) {
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
		if(file_exists('data/'.$Ubusy.'/site.json')) {
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
		if(is_dir('plugins') && $h=opendir('plugins')) {
			while(false!==($f=readdir($h))) {
				if($f!='.' && $f!='..' && is_dir('plugins/'.$f)) $d[]=$f;
			}
			closedir($h);
		}		
		sort($d);
		foreach($d as $r) {
			if(isset($a['plug'][basename($r)])) {
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
		if(isset($a['plug'])) foreach($a['plug'] as $k=>$r) {
				if(file_exists('plugins/'.$k.'/'.$k.'.php')) {
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
		if(isset($a['plug'])) {
			foreach($a['plug'] as $k=>$r) {
				if(file_exists('plugins/'.$k.'/'.$k.'.php')) {
					$a['pl'][]=$k;
					if(file_exists('plugins/'.$k.'/'.$k.'Ckeditor.js.php')) $a['ck'][] = $ck.'plugins/'.$k.'/'.$k.'Ckeditor.js.php';
					else if(file_exists('plugins/'.$k.'/'.$k.'Ckeditor.js')) $a['ck'][] = $ck.'plugins/'.$k.'/'.$k.'Ckeditor.js';
					if(!file_exists('plugins/'.$k.'/'.$k.'.js')) file_put_contents('plugins/'.$k.'/'.$k.'.js', '');
				}
			}
		}
		// 2. plugins
		$d = array();
		if(is_dir('plugins') && $h=opendir('plugins')) {
			while(false!==($f=readdir($h))) {
				if($f!='.' && $f!='..' && is_dir('plugins/'.$f)) $d[] = $f;
			}
			closedir($h);
		}		
		sort($d);
		foreach($d as $r) {
			if(isset($a['plug'][basename($r)])) {
				if(file_exists('plugins/'.basename($r).'/'.basename($r).'Hook.js')) $a['plugins'][] = '2'.basename($r); // Hook JS
				else $a['plugins'][] = '1'.basename($r);
			}
			else $a['plugins'][] = '0'.basename($r);
		}
		// 3. theme
		if(isset($a['tem']) && file_exists('template/'.$a['tem'].'/'.$a['tem'].'.php')) {
			$a['pl'][] = '_';
			$a['plugins'][] = '9_';
			if(!file_exists('template/'.$a['tem'].'/'.$a['tem'].'.js')) file_put_contents('template/'.$a['tem'].'/'.$a['tem'].'.js', '');
		}
		// 4. getSite
		$q1 = @file_get_contents('data/_sdata-'.$sdata.'/ssite.json');
		if($q1) {
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
		if(file_exists('data/update.json')) {
			$q = file_get_contents('data/update.json');
			$a = json_decode($q,true);
		}
		if(!$u) { // CMSUno
			if(is_dir('includes/js/ckeditor/')) $c = 1;
			else $c = 0;
			$a['uno']['in'] = (isset($Uversion)?$Uversion:'0.9');
			if(!isset($a['uno']['ext'])) { $a['uno']['ext'] = '0.9'; $b = 1; }
			if(!isset($a['uno']['host'])) { $a['uno']['host'] = 'https://github.com/boiteasite/cmsuno'; $b = 1; }
			if(!file_exists('data/update.json') || filemtime('data/update.json')>time()-30 || filemtime('data/update.json')<time()-86400) { // Only once a day
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
			if($bi[0]<$be[0] || ($bi[0]==$be[0] && $bi[1]<$be[1]) || ($bi[0]==$be[0] && $bi[1]==$be[1] && $bi[2]<$be[2])) {
				echo '1|'.$c.'|'.$a['uno']['ext'];
			}
			else echo '0|'.$c.'|';
		}
		else { // Plugin
			if(!isset($a['plug'][$u]['in']) || !isset($a['plug'][$u]['host'])) {
				if(file_exists('plugins/'.$u.'/version.json')) {
					$q = file_get_contents('plugins/'.$u.'/version.json');
					$d = json_decode($q,true);
					$a['plug'][$u]['in'] = (isset($d['version'])?$d['version']:'0.9');
					$a['plug'][$u]['host'] = (isset($d['host'])?$d['host']:'0.9');
					$a['plug'][$u]['ext'] = (isset($d['version'])?$d['version']:'0.9');
				}
				else {
					$a['plug'][$u]['in'] = '0.9';
					$a['plug'][$u]['host'] = '';
					$a['plug'][$u]['ext'] = '0.9';
				}
				$b = 1;
			}
			if($a['plug'][$u]['host'] && (filemtime('data/update.json')>time()-30 || filemtime('data/update.json')<time()-86400) && strpos($a['plug'][$u]['host'],'github.com')!==false) { // Only once a day
				$last = lastVersion($a['plug'][$u]['ext'], $a['plug'][$u]['host'].'blob/', 'version.json');
				if($last!=$a['plug'][$u]['in']) {
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
			if($bi[0]<$be[0] || ($bi[0]==$be[0] && $bi[1]<$be[1]) || ($bi[0]==$be[0] && $bi[1]==$be[1] && $bi[2]<$be[2])) {
				echo '1|'.$a['plug'][$u]['in'].'|'.$a['plug'][$u]['ext'];
			}
			else echo '0|'.$a['plug'][$u]['in'].'|';
		}
		if($b) file_put_contents('data/update.json', json_encode($a));
		break;
		// ********************************************************************************************
		case 'update':
		if(file_exists('data/update.json')) {
			$u = $_POST['u']; $r = 0;
			$q = file_get_contents('data/update.json');
			$a = json_decode($q,true);
			if($u) { // plugin
				if(isset($a['plug'][$u]['ext']) && isset($a['plug'][$u]['host']) && strpos($a['plug'][$u]['host'],'github.com')!==false) {
					if(strpos($a['plug'][$u]['host'],'https://github.com/cmsunoPlugins/')!==false) {
						$z = 'https://codeload.github.com/cmsunoPlugins/'.substr($a['plug'][$u]['host'],33).'zip/'.$a['plug'][$u]['ext'];
					}
					else $z = $a['plug'][$u]['host'].'archive/'.$a['plug'][$u]['ext'].'.zip';
					$b = 0;
					if(function_exists('curl_version')) {
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
					if($f===true) {
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
			else {
				$base = dirname(dirname(__FILE__));
				$q1 = file_get_contents('data/'.$Ubusy.'/site.json');
				$a1 = json_decode($q,true);
				// 1. Get new version
				$z = 'https://codeload.github.com/boiteasite/cmsuno/zip/'.$a['uno']['ext'];
				$b = 0;
				if(function_exists('curl_version')) {
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
				if($f===true && filesize('../files/tmpuno.zip')>10000) {
					// 2. check free space : 4 x zip
					$sp = copy($base.'/files/tmpuno.zip', $base.'/files/tmpuno1.zip');
					if($sp) $sp = copy($base.'/files/tmpuno.zip', $base.'/files/tmpuno2.zip');
					if($sp) $sp = copy($base.'/files/tmpuno.zip', $base.'/files/tmpuno3.zip');
					if($sp) $sp = copy($base.'/files/tmpuno.zip', $base.'/files/tmpuno4.zip');
					if(!file_exists($base.'/files/tmpuno1.zip') || !file_exists($base.'/files/tmpuno2.zip') || !file_exists($base.'/files/tmpuno3.zip') || !file_exists($base.'/files/tmpuno4.zip')) $sp = false;
					if(!$sp) {
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
					if($q2) {
						$a2 = json_decode($q2,true);
						if(!empty($a2['git'])) { // light version
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
		if(!file_exists('data/plugin-list.json') || filemtime('data/plugin-list.json')<time()-604800) { // 7 days
			if(!is_dir('../files/tmp/')) mkdir('../files/tmp/');
			$z = 'https://codeload.github.com/cmsunoPlugins/plugin-list/zip/master';
			$b = @get_headers($z);
			if(!empty($b) && substr($b[0],9,3)==200) {
				$b = 0;
				if(function_exists('curl_version')) {
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
				if($f===true) {
					$zip->extractTo('../files/tmp/');
					$zip->close();
					if(file_exists('../files/tmp/plugin-list-master/plugin-list.json')) copy('../files/tmp/plugin-list-master/plugin-list.json', 'data/plugin-list.json');
					unlink('../files/plugin-list.zip');
					f_rmdirR('../files/tmp/plugin-list-master/');
				}
			}
		}
		if(file_exists('data/plugin-list.json')) {
			$q = file_get_contents('data/plugin-list.json');
			$a = json_decode($q,true);
			$o = '<div class="blocForm"><h2>'.T_('Add or remove plugins from the CMSUno GitHub list').'</h2>';
			$o .= '<table class="plugList">';
			foreach($a as $r) {
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
		if($q) {
			$a = json_decode($q,true);
			if(!empty($a['plugadd'])) {
				echo '!'.T_('Disabled by Admin');
				return;
			}
		}
		$p = strip_tags(trim($_POST['plug']));
		$z = 'https://codeload.github.com/cmsunoPlugins/'.$p.'/zip/master';
		$b = 0;
		if(function_exists('curl_version')) {
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
		if($f===true) {
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
		if(file_exists('plugins/'.$p.'/'.$p.'.php')) {
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
function remove_accents($s) {
    if(!preg_match('/[\x80-\xff]/',$s)) return $s;
    $c = array(
	chr(195).chr(128) => 'A', chr(195).chr(129) => 'A', chr(195).chr(130) => 'A', chr(195).chr(131) => 'A', chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
	chr(195).chr(135) => 'C', chr(195).chr(136) => 'E', chr(195).chr(137) => 'E', chr(195).chr(138) => 'E', chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
	chr(195).chr(141) => 'I', chr(195).chr(142) => 'I', chr(195).chr(143) => 'I', chr(195).chr(145) => 'N', chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
	chr(195).chr(148) => 'O', chr(195).chr(149) => 'O', chr(195).chr(150) => 'O', chr(195).chr(153) => 'U', chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
	chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y', chr(195).chr(159) => 's', chr(195).chr(160) => 'a', chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
	chr(195).chr(163) => 'a', chr(195).chr(164) => 'a', chr(195).chr(165) => 'a', chr(195).chr(167) => 'c', chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
	chr(195).chr(170) => 'e', chr(195).chr(171) => 'e', chr(195).chr(172) => 'i', chr(195).chr(173) => 'i', chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
	chr(195).chr(177) => 'n', chr(195).chr(178) => 'o', chr(195).chr(179) => 'o', chr(195).chr(180) => 'o', chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
	chr(195).chr(182) => 'o', chr(195).chr(185) => 'u', chr(195).chr(186) => 'u', chr(195).chr(187) => 'u', chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
	chr(195).chr(191) => 'y',
	chr(196).chr(128) => 'A', chr(196).chr(129) => 'a', chr(196).chr(130) => 'A', chr(196).chr(131) => 'a', chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
	chr(196).chr(134) => 'C', chr(196).chr(135) => 'c', chr(196).chr(136) => 'C', chr(196).chr(137) => 'c', chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
	chr(196).chr(140) => 'C', chr(196).chr(141) => 'c', chr(196).chr(142) => 'D', chr(196).chr(143) => 'd', chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
	chr(196).chr(146) => 'E', chr(196).chr(147) => 'e', chr(196).chr(148) => 'E', chr(196).chr(149) => 'e', chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
	chr(196).chr(152) => 'E', chr(196).chr(153) => 'e', chr(196).chr(154) => 'E', chr(196).chr(155) => 'e', chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
	chr(196).chr(158) => 'G', chr(196).chr(159) => 'g', chr(196).chr(160) => 'G', chr(196).chr(161) => 'g', chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
	chr(196).chr(164) => 'H', chr(196).chr(165) => 'h', chr(196).chr(166) => 'H', chr(196).chr(167) => 'h', chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
	chr(196).chr(170) => 'I', chr(196).chr(171) => 'i', chr(196).chr(172) => 'I', chr(196).chr(173) => 'i', chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
	chr(196).chr(176) => 'I', chr(196).chr(177) => 'i', chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
	chr(196).chr(182) => 'K', chr(196).chr(183) => 'k', chr(196).chr(184) => 'k', chr(196).chr(185) => 'L', chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
	chr(196).chr(188) => 'l', chr(196).chr(189) => 'L', chr(196).chr(190) => 'l', chr(196).chr(191) => 'L', chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
	chr(197).chr(130) => 'l', chr(197).chr(131) => 'N', chr(197).chr(132) => 'n', chr(197).chr(133) => 'N', chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
	chr(197).chr(136) => 'n', chr(197).chr(137) => 'N', chr(197).chr(138) => 'n', chr(197).chr(139) => 'N', chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
	chr(197).chr(142) => 'O', chr(197).chr(143) => 'o', chr(197).chr(144) => 'O', chr(197).chr(145) => 'o', chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
	chr(197).chr(148) => 'R', chr(197).chr(149) => 'r', chr(197).chr(150) => 'R', chr(197).chr(151) => 'r', chr(197).chr(152) => 'R', chr(197).chr(153) => 'r',
	chr(197).chr(154) => 'S', chr(197).chr(155) => 's', chr(197).chr(156) => 'S', chr(197).chr(157) => 's', chr(197).chr(158) => 'S', chr(197).chr(159) => 's',
	chr(197).chr(160) => 'S', chr(197).chr(161) => 's', chr(197).chr(162) => 'T', chr(197).chr(163) => 't', chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
	chr(197).chr(166) => 'T', chr(197).chr(167) => 't', chr(197).chr(168) => 'U', chr(197).chr(169) => 'u', chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
	chr(197).chr(172) => 'U', chr(197).chr(173) => 'u', chr(197).chr(174) => 'U', chr(197).chr(175) => 'u', chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
	chr(197).chr(178) => 'U', chr(197).chr(179) => 'u', chr(197).chr(180) => 'W', chr(197).chr(181) => 'w', chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
	chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z', chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z', chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
	chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
    );
    $s = strtr($s, $c);
    return $s;
}

?>
