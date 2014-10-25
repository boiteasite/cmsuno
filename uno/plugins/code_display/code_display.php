<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
?>
<?php
include('../../password.php'); $user=0; $pass=0; // Lang
include('lang/lang.php');
// ********************* actions *************************************************************************
if (isset($_POST['action']))
	{
	switch ($_POST['action'])
		{
		// ********************************************************************************************
		case 'plugin': ?>
		<div class="blocForm">
			<h2><?php echo _("Code Display");?></h2>
			<p><?php echo _("This plugin allows you to insert code (PHP, HTML, JS, CSS) with syntax highlighting in the content of the page.");?></p>
			<p><?php echo _("It is used with the button") .'<img src="uno/plugins/code_display/pbckcode/icons/pbckcode.png" style="border:1px solid #aaa;padding:3px;margin:0 6px -5px;border-radius:2px;" />' . _("added to the text editor when the plugin is active.");?></p>
			<br />
			<p style="text-align:center;"><img src="uno/plugins/code_display/code_display_exemple.jpg" style="box-shadow:1px 1px 3px #999;" alt="<?php echo _("Code Display");?>"  title="<?php echo _("Code Display");?>"/></p>
			<div class="clear"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>
