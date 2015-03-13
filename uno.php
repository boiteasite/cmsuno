<?php
ini_set('session.use_trans_sid', 0);
session_start();
include('uno/password.php');
include('uno/includes/lang/lang.php');
if (!is_dir('uno/includes/js/ckeditor/')) $dep = "http://cmsuno-dep.googlecode.com/git/"; else $dep = "uno/"; // LIGHT HOSTED VERSION
if (isset($_POST['user']) && isset($_POST['pass']))
	{
	if ($_POST['user']===utf8_encode($user) && $_POST['pass']===$pass && is_writable(dirname(__FILE__)))
		{
		$_SESSION['cmsuno']=true;
		if(!is_dir('uno/data')) mkdir('uno/data');
		if(!is_dir('uno/data/sdata')) mkdir('uno/data/sdata',0711);
		if(!is_dir('files')) mkdir('files');
		if(!is_dir('files/unosave')) mkdir('files/unosave',0711);
		if(!file_exists('uno/.htaccess')) file_put_contents('uno/.htaccess', 'Options -Indexes'."\r\n".'Allow from all');
		if(!file_exists('uno/data/.htaccess')) file_put_contents('uno/data/.htaccess', 'Options -Indexes'."\r\n".'Allow from all');
		if(!file_exists('uno/data/sdata/.htaccess')) file_put_contents('uno/data/sdata/.htaccess', 'Order Allow,Deny'."\r\n".'Deny from all'); 
		if(substr(sprintf('%o', fileperms('uno/data/sdata')), -4)!="0711") @chmod("uno/data/sdata", 0711);
		if(!file_exists('files/unosave/.htaccess')) file_put_contents('files/unosave/.htaccess', 'Order Allow,Deny'."\r\n".'Deny from all'); 
		if(substr(sprintf('%o', fileperms('files/unosave')), -4)!="0711") @chmod("files/unosave", 0711);
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
else if (!isset($_SESSION['cmsuno'])) { ?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<meta name="robots" content="noindex" />
	<title>CMSUno - <?php echo _("Login");?></title>
	<link rel="icon" type="image/png" href="<?php echo $dep; ?>includes/img/favicon.png" />
	<link rel="stylesheet" href="<?php echo $dep; ?>includes/css/uno.css" />
	<script src="<?php if($dep!='uno/') echo 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'; else echo 'uno/includes/js/jquery-1.7.2.min.js'; ?>"></script>
	<script type="text/javascript">$(document).ready(function(){$('.alert').delay(2000).fadeOut();});</script>
</head>
<body>
	<div class="blocTop bgNoir">
		<div class="container">
			<span class="titre" href="/">CMSUno</span>
			<ul>
				<?php 
				if(file_exists('uno/data/busy.json')) { $q = file_get_contents('uno/data/busy.json'); $a = json_decode($q,true); $Ubusy = $a['nom']; }
				else $Ubusy = 'index';
				?>
				<li><a href="<?php echo $Ubusy; ?>.html" target="_blank"><?php echo _("See the website");?></a></li>
			</ul>
		</div>
	</div><!-- .blocTop-->

	<div class="container">
		<form class="blocLogin" method="POST" action="">
			<img style="margin-bottom:20px;" src="<?php echo $dep; ?>includes/img/logo-uno220.png" alt="cms uno" />
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
<?php }
else
	{
	$unox = md5(rand(1000,9999));
	$_SESSION['unox'] = $unox; // securisation des appels ajax
	include('uno/edition.php');
	}
?>