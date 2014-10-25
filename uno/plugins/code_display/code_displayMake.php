<?php
if (!isset($_SESSION['cmsuno'])) exit();
?>
<?php
	$head .= '<script src="uno/plugins/code_display/google-code-prettify/run_prettify.js"></script>'."\r\n";
	$content = str_replace('<pre','<div>'."\r\n".'<pre style="overflow:auto;"',$content);
	$content = str_replace('</pre>','</pre>'."\r\n".'</div>'."\r\n",$content);
?>
