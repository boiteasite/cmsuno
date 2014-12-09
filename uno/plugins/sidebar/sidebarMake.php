<?php
if (!isset($_SESSION['cmsuno'])) exit();
?>
<?php
if (file_exists('data/sidebar.txt'))
	{
	$q1 = file_get_contents('data/'.$Ubusy.'sidebar.txt');
	$html = str_replace('[[sidebar]]','<div class="sidebarContent">'."\r\n".$q1."\r\n".'</div>',$html);
	}
?>
