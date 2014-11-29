<?php
if (!isset($_SESSION['cmsuno'])) exit();
if(!file_exists('data/newsletter.txt'))
	{
	@unlink('plugins/newsletter/on.txt');
	exit;
	}
?>
<?php
include('plugins/newsletter/lang/lang.php');
	$a1 = '<form class="newsletterFrm" method="GET" action="uno/plugins/newsletter/newsletterSubscribe.php">'."\r\n".
		'<input type="hidden" name="a" value="new" />'."\r\n".
		'<input type="hidden" name="b" value="1" />'."\r\n".
		'<input type="hidden" name="c" value="1" />'."\r\n".
		'<label>'._("Email adress").' : </label>'."\r\n".
		'<input type="text" name="m" value="" />'."\r\n".
		'<input style="margin-left:10px;" type="submit" value="'._("Subscribe").'" />'."\r\n".
		'</form><div style="font-size:90%">'._("You will receive an email to confirm").'</div>';"\r\n".
	$html = str_replace('[[newsletter]]',"\r\n".$a1, $html); // template
	$content = str_replace('[[newsletter]]',"\r\n".$a1, $content); // editor

?>
