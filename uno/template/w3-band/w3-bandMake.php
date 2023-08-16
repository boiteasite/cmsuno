<?php
if(!isset($_SESSION['cmsuno'])) exit();
?>
<?php
$Uhtml = str_replace('[[box-footer]]','',$Uhtml); // Box not exists => remove shortcode
?>
