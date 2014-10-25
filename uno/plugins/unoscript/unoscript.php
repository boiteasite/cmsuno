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
			<h2><?php echo _("Uno Script");?></h2>
			<p>
			<?php echo _("This plugin allows you to add a script on the site without modifying the template.");?>
			<?php echo _("You can use it to add the Google Analytics tracking code.");?>
			<?php echo _("You can also use this form to insert any other JavaScript code.");?>
			</p>
			<p><?php echo _("The code will be installed at the bottom of the page, in the end of /body/.");?></p>
			<table class="hForm">
				<tr>
					<td><label><?php echo _("Uno Script");?></label></td>
					<td><textarea name="codeGA" id="codeGA" style="width:100%;"></textarea></td>
					<td><em><?php echo _("Paste the tracking code");?>&nbsp;<span style='text-decoration:underline;'><?php echo _("without");?></span>&nbsp;<?php echo _("script tags.");?></em></td>
				</tr>
			</table>
			<div class="bouton fr" onClick="f_google_analytics();" title="<?php echo _("Saves settings");?>"><?php echo _("Save");?></div>
			<div class="clear"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		case 'save':
		$a = array();
		$a['tex']=$_POST['ga'];
		$out = json_encode($a);
		if (file_put_contents('../../data/unoscript.json', $out)) echo _('Backup performed');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>
