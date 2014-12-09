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
			<h2>ScrollNav</h2>
			<p><?php echo _("Add a lateral sliding menu. (JQuery Required)");?></p>
			<p><?php echo _("The Format -Title 2- (H2 HTML tags) inside pages will be submenu.");?></p>
			<h3><?php echo _("Parameters :");?></h3>
			<table class="hForm">
				<tr>
					<td><label><?php echo _("Top Start");?></label></td>
					<td><input type="text" class="input" name="scroTopi" id="scroTopi" style="width:50px;" /></td>
					<td><em><?php echo _("Margin up when the page is at the top (px).");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Top Fixed");?></label></td>
					<td><input type="text" class="input" name="scroTopf" id="scroTopf" style="width:50px;" /></td>
					<td><em><?php echo _("Margin up when the menu becomes fixed (px).");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Menu Title");?></label></td>
					<td><input type="text" class="input" name="scroTit" id="scroTit" style="width:100px;" /></td>
					<td><em><?php echo _("If you want a title at the top of the menu, write it. Otherwise, empty it.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Speed");?></label></td>
					<td><input type="text" class="input" name="scroSpeed" id="scroSpeed" style="width:100px;" /></td>
					<td><em><?php echo _("Set the animated page scroll speed (ms). Empty it for no animation.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Top Offset");?></label></td>
					<td><input type="text" class="input" name="scroOfs" id="scroOfs" style="width:50px;" /></td>
					<td><em><?php echo _("When click, position of the window relative to the beginning of the section (px).");?></em></td>
				</tr>
			</table>
			<div class="bouton fr" onClick="f_save_scrollnav();" title="<?php echo _("Save settings");?>"><?php echo _("Save");?></div>
			<div class="clear"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		case 'save':
		$q = file_get_contents('../../data/busy.json'); $a = json_decode($q,true); $Ubusy = $a['nom'];
		$q = @file_get_contents('../../data/'.$Ubusy.'/scrollnav.json');
		if($q) $a = json_decode($q,true);
		else $a = Array();
		$a['topi'] = ($_POST['topi']?$_POST['topi']:0);
		$a['topf'] = ($_POST['topf']?$_POST['topf']:0);
		$a['tit'] = $_POST['tit'];
		$a['sp'] = ($_POST['sp']?$_POST['sp']:false);
		$a['ofs'] = ($_POST['ofs']?$_POST['ofs']:0);
		$out = json_encode($a);
		if (file_put_contents('../../data/'.$Ubusy.'/scrollnav.json', $out)) echo _('Backup performed');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}

?>
