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
			<h2><?php echo _("Sidebar");?></h2>
			<p><?php echo _("This plugin allows you to add a block of HTML content. It can be used for a sidebar or somewhere else.");?></p>
			<p><?php echo _("Just insert the code");?>&nbsp;<code>[[sidebar]]</code>&nbsp;<?php echo _("in the template and add the sidebar in the CSS file.");?></p>
			<div>
				<div class="input" id="sidebarP">
					<textarea name="sidebar" id="sidebar"></textarea>
				</div>
			</div>
			<div class="blocBouton">
				<div class="bouton fr" onClick="f_sidebar();" title="<?php echo _("Saves the contents");?>"><?php echo _("Save");?></div>
			</div>
			<div class="clear"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		case 'save':
		if (file_put_contents('../../data/sidebar.txt', $_POST['sidebar'])) echo _('Backup performed');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		case 'get':
		if (file_exists('../../data/sidebar.txt'))
			{
			$q = file_get_contents('../../data/sidebar.txt');
			echo stripslashes($q);
			}
		exit;
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>
