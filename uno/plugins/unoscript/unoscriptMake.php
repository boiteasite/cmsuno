<?php
if (!isset($_SESSION['cmsuno'])) exit();
if(!file_exists('data/unoscript.json'))
	{
	@unlink('plugins/unoscript/on.txt');
	exit;
	}
?>
<?php
	if (file_exists('data/unoscript.json'))
		{
		$q1 = file_get_contents('data/unoscript.json');
		$a1 = json_decode($q1,true);
		$foot .= '<script type="text/javascript">'."\r\n".$a1['tex']."\r\n".'</script>'."\r\n";
		}
?>
