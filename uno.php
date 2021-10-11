<?php
// **********************************
// CMSUno
$version = '1.7.3';
// **********************************
// *** DEBUG MODE ***
	//error_reporting(E_ALL); ini_set('display_errors',1);
// ******************
$lang = 'en';
ini_set('session.use_trans_sid', 0);
session_start();
if(is_writable(dirname(__FILE__).'/uno')) {
	if(file_exists('uno/config.php')) include('uno/config.php');
	else {
		$Sdata = f_setKey('Sdata');
		$Ukey = f_setKey('Ukey');
		$out = '<?php $lang = "en"; $sdata = "'.$sdata.'"; $Ukey = "'.$Ukey.'"; $Uversion = "'.(isset($version)?$version:'1.0').'"; ?>';
		file_put_contents('uno/config.php', $out);
	}
	if(file_exists('uno/patch.php')) include('uno/patch.php');
	if(empty($Uversion) || $Uversion!=$version || empty($Sdata) || empty($Ukey)) {
		if(empty($Sdata)) $Sdata = f_setKey('Sdata');
		if(empty($Ukey)) $Ukey = f_setKey('Ukey');
		$out = '<?php $lang = "'.$lang.'"; $sdata = "'.$sdata.'"; $Ukey = "'.$Ukey.'"; $Uversion = "'.(isset($version)?$version:'1.0').'"; ?>';
		file_put_contents('uno/config.php', $out);
		$Uversion = (isset($version)?$version:'1.0');
	}
}
include('uno/includes/lang/lang.php');
//$Urawgit = '//cdn.rawgit.com/boiteasite/cmsuno/';
$Urawgit = 'https://cdn.jsdelivr.net/gh/boiteasite/cmsuno@';
$Udep = 'uno/';
if(!is_dir('uno/includes/js/ckeditor/')) $Udep = $Urawgit.$Uversion."/uno/"; // LIGHT HOSTED VERSION
if(!empty($_POST['user']) && !empty($_POST['pass']) && !empty($_POST['unox']) && !empty($_SESSION)) {
	session_regenerate_id();
	define('CMSUNO', 'cmsuno');
	include('uno/password.php');
	if(is_writable(dirname(__FILE__)) && $_POST['user']===$user && f_check_pass($_POST['pass'],$pass,$user) && $_SESSION['unox']===$_POST['unox']) {
		$hta = '# CMSUno - HTACCESS auto'."\r\n".
			'Options -Indexes'."\r\n".
			'Allow from all'."\r\n\r\n".
			'<IfModule mod_deflate.c>'."\r\n".
			"\t".'SetOutputFilter DEFLATE'."\r\n".
			"\t".'SetEnvIfNoCase Request_URI \.(?:gif|jpe?g|png)$ no-gzip'."\r\n".
			'</IfModule>'."\r\n\r\n".
			'<IfModule mod_expires.c>'."\r\n".
			"\t".'ExpiresActive On'."\r\n".
			"\t".'ExpiresDefault "access plus 7200 seconds"'."\r\n".
			"\t".'ExpiresByType image/jpg "access plus 1 week"'."\r\n".
			"\t".'ExpiresByType image/jpeg "access plus 1 week"'."\r\n".
			"\t".'ExpiresByType image/png "access plus 1 week"'."\r\n".
			"\t".'ExpiresByType text/javascript "access plus 7200 seconds"'."\r\n".
			"\t".'ExpiresByType application/x-javascript "access plus 7200 seconds"'."\r\n".
			"\t".'ExpiresByType application/javascript "access plus 7200 seconds"'."\r\n".
			'</IfModule>'."\r\n";
		$_SESSION['cmsuno']=true;
		if(!is_dir('uno/data')) mkdir('uno/data');
		if(!is_dir('uno/data/_sdata-'.$sdata)) mkdir('uno/data/_sdata-'.$sdata,0711);
		if(!is_dir('files')) mkdir('files');
		if(!is_dir('uno/data/_sdata-'.$sdata.'/_unosave')) mkdir('uno/data/_sdata-'.$sdata.'/_unosave',0711);
		if(!file_exists('.htaccess')) file_put_contents('.htaccess', $hta);
		if(!file_exists('uno/.htaccess')) file_put_contents('uno/.htaccess', $hta);
		if(!file_exists('uno/data/.htaccess')) file_put_contents('uno/data/.htaccess', 'Options -Indexes'."\r\n".'Allow from all');
		if(!file_exists('uno/data/index.html')) file_put_contents('uno/data/index.html', '<html></html>');
		if(!file_exists('uno/data/_sdata-'.$sdata.'/.htaccess')) file_put_contents('uno/data/_sdata-'.$sdata.'/.htaccess', 'Order Allow,Deny'."\r\n".'Deny from all'); 
		f_chmodR('uno/data/_sdata-'.$sdata,0600,0711);
		if(!is_readable('files') || !is_writable('files')) f_chmodR('files',0644,0755);
		if(!file_exists('uno/data/busy.json')) file_put_contents('uno/data/busy.json', '{"nom":"index"}');
		if(is_dir('files/.tmb')) { // clean up - free space
			if($h=opendir('files/.tmb')) {
				while(false!==($f=readdir($h))) if(is_file('files/.tmb/'.$f)) unlink('files/.tmb/'.$f);
				closedir($h);
			}
		}
		if(is_dir('uno/includes/elfinder/.tmb')) { // clean up - free space
			if($h=opendir('uno/includes/elfinder/.tmb')) {
				while(false!==($f=readdir($h))) if(is_file('uno/includes/elfinder/.tmb/'.$f)) unlink('uno/includes/elfinder/.tmb/'.$f);
				closedir($h);
			}
		}
	}
	else sleep(2);
	if(!is_writable(dirname(__FILE__))) echo '<div style="clear:both;text-align:center;color:red;font-weight:700;padding-top:20px;"><span  style="color:#000;">'.dirname(__FILE__).'</span>&nbsp'.T_("must writable recursively !").'</div>';
	else if(!is_writable(dirname(__FILE__).'/uno')) echo '<div style="clear:both;text-align:center;color:red;font-weight:700;padding-top:20px;"><span  style="color:#000;">'.dirname(__FILE__).'/uno</span>&nbsp'.T_("must writable recursively !").'</div>';
	else echo '<script type="text/javascript">window.location=document.URL; </script>';
}
//
else if(isset($_POST['logout']) && $_POST['logout']==1) {
	session_unset();
	session_destroy();
	echo '<script type="text/javascript">window.location=document.URL; </script>';
}
//
else if(isset($_SESSION['cmsuno'])) {
	$unox = md5(mt_rand().mt_rand());
	$_SESSION['unox'] = $unox; // securisation des appels ajax
	include('uno/edition.php');
}
//
else { 
	$unox = md5(mt_rand().mt_rand());
	$_SESSION['unox'] = $unox; // securisation connexion (CSRF)

?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<meta name="robots" content="noindex" />
	<title>CMSUno - <?php echo T_("Login");?></title>
	<link rel="icon" type="image/png" href="<?php echo $Udep; ?>includes/img/favicon.png" />
	<link rel="stylesheet" href="<?php echo $Udep; ?>includes/css/uno.css" />
	<script src="<?php if($Udep!='uno/') echo '//ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js'; else echo 'uno/includes/js/jquery.min.js'; ?>"></script>
</head>
<body>
	<div class="blocTop bgNoir">
		<div class="container">
			<span class="titre"><a href="https://github.com/boiteasite/cmsuno" title="<?php echo T_("CMSUno on GitHub");?>" target="_blank">CMSUno<?php if(isset($Uversion)) echo '&nbsp;<em>'.$Uversion.'</em>'; ?></a></span>
			<ul id="topMenu" class="topMenu">
				<?php 
				if(file_exists('uno/data/busy.json')) { $q = file_get_contents('uno/data/busy.json'); $a = json_decode($q,true); $Ubusy = $a['nom']; }
				else $Ubusy = 'index';
				?>
				<li id="wait"><img style="margin:2px 6px 0 0;display:none;" src="<?php echo $Udep; ?>includes/img/wait.gif" /></li>
				<li><a href="<?php echo $Ubusy; ?>.html" target="_blank"><?php echo T_("See the website");?></a></li>
			</ul>
		</div>
	</div><!-- .blocTop-->

	<div class="container">
		<form class="blocLogin" method="POST" action="">
			<input type="hidden" name="unox" value="<?php echo $unox; ?>" \>
			<img style="margin-bottom:20px;" src="<?php echo $Udep; ?>includes/img/logo-uno220.png" alt="cms uno" />
			<div class="clearfix">
				<label><?php echo T_("Administrator");?></label>
				<div>
					<input type="text" class="input" name="user" id="username" />
				</div>
			</div>
			<div class="clearfix">
				<label><?php echo T_("Password");?></label>
				<div>
					<input type="password" class="input" name="pass" id="password" />
				</div>
			</div>
			<div>
				<input type="submit" class="bouton fr" value="<?php echo T_("Login");?>" />
			</div>
		</form>
		<div style="clear:both;text-align:center;color:red;font-weight:700;padding-top:20px;">
		<?php
			if(!is_writable(dirname(__FILE__))) echo '<span  style="color:#000;">'.dirname(__FILE__).'</span>&nbsp'.T_("must writable recursively !");
			else if(!is_writable(dirname(__FILE__).'/uno')) echo '<br /><span  style="color:#000;">'.dirname(__FILE__).'/uno</span>&nbsp'.T_("must writable recursively !");
		?>
		</div>
	</div><!-- .container -->
	<script type="text/javascript" src="<?php echo $Udep; ?>includes/js/ckeditor/ckeditor.js"></script>
</body>
</html>
<?php }
//
function f_chmodR($path, $fr=0644, $dr=0755) {
	if(!file_exists($path)) return(false);
	if(is_file($path)) @chmod($path, $fr);
	else if(is_dir($path)) {
		$re = scandir($path);
		$q = array_slice($re, 2);
		foreach($q as $r) f_chmodR($path.'/'.$r, $fr, $dr);
		@chmod($path, $dr);
	}
	return(true);
}
function f_check_pass($a,$b,$user) {
	if(!function_exists('password_hash')) include('uno/includes/password_hashing.php'); // php 5.5 missing => https://github.com/ircmaxell/password_compat (php>5.3)
	if(substr($b,0,1)=='$' && strlen($b)==60 && password_verify($a,$b)) return true;
	else if($b===$a) {
		$pass = password_hash($b, PASSWORD_BCRYPT);
		file_put_contents('uno/password.php', '<?php if(!defined(\'CMSUNO\')) exit(); $user = "'.$user.'"; $pass = \''.$pass.'\';'."\r\n".'// Lost password? Enter the new one in clear text here in $pass and log in again. ?>');
		return true;
	}
	return false;
}
function f_setKey($f) {
	$b = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789';$a = '';
	if($f=='Sdata') for($v=0;$v<15;++$v) $a .= $b[mt_rand(0, strlen($b)-1)];
	else for($v=0;$v<63;++$v) $a .= $b[mt_rand(0, strlen($b)-1)];
	return $a;
}
?>
