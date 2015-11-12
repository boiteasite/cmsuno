<?php
session_start(); 
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
if(!isset($_POST['unox']) || $_POST['unox']!=$_SESSION['unox']) {sleep(2);exit;} // appel depuis uno.php
?>
<?php
include('../../config.php');
include('lang/lang.php');
$q = file_get_contents('../../data/busy.json'); $a = json_decode($q,true); $Ubusy = $a['nom'];
$q = file_get_contents('../../data/'.$Ubusy.'/site.json'); $a = json_decode($q,true); $Utem = $a['tem'];
if(!file_exists('../../data/'.$Ubusy.'/'.$Utem.'.json')) file_put_contents('../../data/'.$Ubusy.'/'.$Utem.'.json', '[]');
// ********************* actions *************************************************************************
if (isset($_POST['action']))
	{
	switch ($_POST['action'])
		{
		// ********************************************************************************************
		case 'plugin': ?>
		<style>
		.del{background:transparent url(<?php echo $_POST['udep']; ?>includes/img/close.png) no-repeat center center;cursor:pointer;padding:0 20px;margin-left:10px}
		</style>
		<div class="blocForm">
			<h2><?php echo _("Options");?></h2>
			<form id="themeOption">
			<table class="hForm">
				<tr>
					<td><label><?php echo _("Page color");?></label></td>
					<td><input class="color" type="text" style="width:150px;" name="bgp" id="bgp" /><span class="del" onclick="f_del_uno1(this);"></span></td>
					<td><em><?php echo _("Background color for the border of your page");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Wrapper color");?></label></td>
					<td><input class="color" type="text" style="width:150px;" name="bgw" id="bgw" /><span class="del" onclick="f_del_uno1(this);"></span></td>
					<td><em><?php echo _("Background color for the content of your page");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Menu color");?></label></td>
					<td><input class="color" type="text" style="width:150px;" name="bgm" id="bgm" /><span class="del" onclick="f_del_uno1(this);"></span></td>
					<td><em><?php echo _("Background color of the menu");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Text menu color on");?></label></td>
					<td><input class="color" type="text" style="width:150px;" name="tmc" id="tmc" /><span class="del" onclick="f_del_uno1(this);"></span></td>
					<td><em><?php echo _("Color of the text for the current chapter in the menu");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Text menu color off");?></label></td>
					<td><input class="color" type="text" style="width:150px;" name="tmo" id="tmo" /><span class="del" onclick="f_del_uno1(this);"></span></td>
					<td><em><?php echo _("Color of the text for the other chapters in the menu");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Site Title color");?></label></td>
					<td><input class="color" type="text" style="width:150px;" name="cot" id="cot" /><span class="del" onclick="f_del_uno1(this);"></span></td>
					<td><em><?php echo _("Color of the text for the site title");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Chapter Title color");?></label></td>
					<td><input class="color" type="text" style="width:150px;" name="coc" id="coc" /><span class="del" onclick="f_del_uno1(this);"></span></td>
					<td><em><?php echo _("Color of the text for each chapter title");?></em></td>
				</tr>
			</table>
			</form>
			<div class="bouton fr" onClick="f_save_<?php echo $Utem;?>();" title="<?php echo _("Save settings");?>"><?php echo _("Save");?></div>
			<div class="clear"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		case 'save':
		$a = array();
		foreach($_POST as $k=>$v)
			{
			if($k!='action' && $k!='unox')
				{
				$a[$k]=$v;
				}
			}
		$out = json_encode($a);
		if (file_put_contents('../../data/'.$Ubusy.'/'.$Utem.'.json', $out)) echo _('Backup performed');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>
