<?php
// **********************************
// CMSUno
$Uversion = '1.0';
// **********************************
ini_set('session.use_trans_sid', 0);
session_start();
if(file_exists('uno/config.php')) include('uno/config.php');
else
	{
	$ch = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789'; $sdata = '';
	for($v=0;$v<15;++$v) $sdata .= $ch[mt_rand(0, strlen($ch)-1)];
	$out = '<?php $lang = "en"; $sdata = "'.$sdata.'"; ?>';
	file_put_contents('uno/config.php', $out);
	$lang = 'en';
	}
include('uno/includes/lang/lang.php');
//if (!is_dir('uno/includes/js/ckeditor/')) $Udep = "https://cdn.rawgit.com/boiteasite/cmsuno/master/uno/"; else $Udep = "uno/"; // LIGHT HOSTED VERSION
if (!is_dir('uno/includes/js/ckeditor/')) $Udep = "https://rawgit.com/boiteasite/cmsuno/master/uno/"; else $Udep = "uno/"; // LIGHT HOSTED VERSION
if (isset($_POST['user']) && isset($_POST['pass']))
	{
	session_regenerate_id();
	define('CMSUNO', 'cmsuno');
	include('uno/password.php');
	if ($_POST['user']===utf8_encode($user) && $_POST['pass']===$pass && is_writable(dirname(__FILE__)))
		{
		$_SESSION['cmsuno']=true;
		if(!is_dir('uno/data')) mkdir('uno/data');
		if(!is_dir('uno/data/_sdata-'.$sdata)) mkdir('uno/data/_sdata-'.$sdata,0711);
		if(!is_dir('files')) mkdir('files');
		if(!is_dir('uno/data/_sdata-'.$sdata.'/_unosave')) mkdir('uno/data/_sdata-'.$sdata.'/_unosave',0711);
		if(!file_exists('uno/.htaccess')) file_put_contents('uno/.htaccess', 'Options -Indexes'."\r\n".'Allow from all');
		if(!file_exists('uno/data/.htaccess')) file_put_contents('uno/data/.htaccess', 'Options -Indexes'."\r\n".'Allow from all');
		if(!file_exists('uno/data/index.html')) file_put_contents('uno/data/index.html', '<html></html>');
		if(!file_exists('uno/data/_sdata-'.$sdata.'/.htaccess')) file_put_contents('uno/data/_sdata-'.$sdata.'/.htaccess', 'Order Allow,Deny'."\r\n".'Deny from all'); 
		if(substr(sprintf('%o', fileperms('uno/data/_sdata-'.$sdata)), -4)!="0711") @chmod("uno/data/_sdata-".$sdata, 0711);
		if(!file_exists('uno/data/busy.json')) file_put_contents('uno/data/busy.json', '{"nom":"index"}');
		}
	else sleep(2);
	if(is_dir('files') && is_writable(dirname(__FILE__)) && is_writable(dirname(__FILE__).'/uno')) echo '<script type="text/javascript">window.location=document.URL; </script>';
	else if(!is_writable(dirname(__FILE__))) echo '<div style="clear:both;text-align:center;color:red;font-weight:700;padding-top:20px;"><span  style="color:#000;">'.dirname(__FILE__).'</span>&nbsp'._("must writable recursively !").'</div>';
	else if(!is_writable(dirname(__FILE__).'/uno')) echo '<div style="clear:both;text-align:center;color:red;font-weight:700;padding-top:20px;"><span  style="color:#000;">'.dirname(__FILE__).'/uno</span>&nbsp'._("must writable recursively !").'</div>';
	}
//
else if (isset($_POST['logout']) && $_POST['logout']==1)
	{
	session_unset();
	session_destroy();
	echo '<script type="text/javascript">window.location=document.URL; </script>';
	}
//
else if (isset($_SESSION['cmsuno']))
	{
	$unox = md5(rand(1000,9999));
	$_SESSION['unox'] = $unox; // securisation des appels ajax
	include('uno/edition.php');
	}
//
else { ?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<meta name="robots" content="noindex" />
	<title>CMSUno - <?php echo _("Login");?></title>
	<link rel="icon" type="image/png" href="<?php echo $Udep; ?>includes/img/favicon.png" />
	<link rel="stylesheet" href="<?php echo $Udep; ?>includes/css/uno.css" />
	<script src="<?php if($Udep!='uno/') echo 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'; else echo 'uno/includes/js/jquery-1.7.2.min.js'; ?>"></script>
	<script type="text/javascript">$(document).ready(function(){$('.alert').delay(2000).fadeOut();});</script>
</head>
<body>
	<div class="blocTop bgNoir">
		<div class="container">
			<span class="titre" href="/">CMSUno<? if(isset($Uversion)) echo '&nbsp;<em>'.$Uversion.'</em>'; ?></span>
			<ul>
				<?php 
				if(file_exists('uno/data/busy.json')) { $q = file_get_contents('uno/data/busy.json'); $a = json_decode($q,true); $Ubusy = $a['nom']; }
				else $Ubusy = 'index';
				?>
				<li id="wait"><img style="margin:2px 6px 0 0;display:none;" src="<?php echo $Udep; ?>includes/img/wait.gif" /></li>
				<li><a href="<?php echo $Ubusy; ?>.html" target="_blank"><?php echo _("See the website");?></a></li>
			</ul>
		</div>
	</div><!-- .blocTop-->

	<div class="container">
		<form class="blocLogin" method="POST" action="">
			<img style="margin-bottom:20px;" src="<?php echo $Udep; ?>includes/img/logo-uno220.png" alt="cms uno" />
			<div class="clearfix">
				<label><?php echo _("Administrator");?></label>
				<div>
					<input type="text" class="input" name="user" id="username" />
				</div>
			</div>
			<div class="clearfix">
				<label><?php echo _("Password");?></label>
				<div>
					<input type="password" class="input" name="pass" id="password" />
				</div>
			</div>
			<div>
				<input type="submit" class="bouton fr" value="<?php echo _("Login");?>" />
			</div>
		</form>
		<div style="clear:both;text-align:center;color:red;font-weight:700;padding-top:20px;">
		<?php
			if(!is_writable(dirname(__FILE__))) echo '<span  style="color:#000;">'.dirname(__FILE__).'</span>&nbsp'._("must writable recursively !");
			else if(!is_writable(dirname(__FILE__).'/uno')) echo '<br /><span  style="color:#000;">'.dirname(__FILE__).'/uno</span>&nbsp'._("must writable recursively !");
		?>
		</div>
	</div><!-- .container -->
</body>
</html>
<?php } ?>