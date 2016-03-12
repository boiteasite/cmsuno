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
				<tr><th colspan=3>&nbsp;</th></tr>
				<tr>
					<td><label><?php echo _("Page background");?></label></td>
					<td>
						<input class="color input" type="text" style="width:150px;" name="bgp" id="bgp" />
						<select id="Sbgp" name="Sbgp" onChange="f_sel_uno1(this);" >
							<option value="color" selected>color</option>
							<option value="img">img</option>
						</select>
					</td>
					<td><em><?php echo _("Fixed background image or background color for the border of your page.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Wrapper color");?></label></td>
					<td><input class="color input" type="text" style="width:150px;" name="bgw" id="bgw" /><span class="del" onclick="f_del_uno1(this)"></span></td>
					<td><em><?php echo _("Background color for the content of your page.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Wrapper width");?></label></td>
					<td><input class="input" type="text" style="width:50px;" name="wrw" id="wrw" onkeyup="this.value=this.value.replace(/[^\d]+/,'')" /></td>
					<td><em><?php echo _("Width for the content of your page. Default : 1024px. Only number.");?></em></td>
				</tr>
				<tr><th colspan=3>&nbsp;</th></tr>
				<tr>
					<td><label><?php echo _("Logo");?></label></td>
					<td><input class="input" type="text" style="width:150px;" name="tgo" id="tgo" onclick="f_finder_select('tgo')" /><span class="del" onclick="document.getElementById('tgo').value=''"></span></td>
					<td><em><?php echo _("Image logo centered at the top of the page.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Logo height");?></label></td>
					<td><input class="input" type="text" style="width:50px;" name="tgh" id="tgh" onkeyup="this.value=this.value.replace(/[^\d]+/,'')" /></td>
					<td><em><?php echo _("Logo height in pixel. Only number.");?></em></td>
				</tr>
				<tr><th colspan=3>&nbsp;</th></tr>
				<tr>
					<td><label><?php echo _("Menu position");?></label></td>
					<td>
						<select id="mpo" name="mpo" >
							<option value="" selected><?php echo _("Top of the page");?></option>
							<option value="relative"><?php echo _("Top of the wrapper");?></option>
						</select>
					</td>
					<td><em><?php echo _("Position of the menu. No difference if you have no logo.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Menu font");?></label></td>
					<td>
						<select id="mfo" name="mfo" >
							<option value="" style="font-family:Georgia,serif">Serif : Georgia - <?php echo _("Default");?></option>
							<option value="'Palatino Linotype','Book Antiqua',Palatino,serif" style="font-family:'Palatino Linotype','Book Antiqua',Palatino,serif">Serif : Palatino</option>
							<option value="'Times New Roman',Times,serif" style="font-family:'Times New Roman',Times,serif">Serif : Times</option>
							<option value="Arial,Helvetica,sans-serif" style="font-family:Arial,Helvetica,sans-serif">Sans-Serif : Arial</option>
							<option value="'Arial Black',Gadget,sans-serif" style="font-family:'Arial Black',Gadget,sans-serif">Sans-Serif : Arial Black</option>
							<option value="'Comic Sans MS',cursive,sans-serif" style="font-family:'Comic Sans MS',cursive,sans-serif">Sans-Serif : Comic Sans MS</option>
							<option value="Impact,Charcoal,sans-serif" style="font-family:Impact,Charcoal,sans-serif">Sans-Serif : Impact</option>
							<option value="'Lucida Sans Unicode','Lucida Grande',sans-serif" style="font-family:'Lucida Sans Unicode','Lucida Grande',sans-serif">Sans-Serif : Lucida</option>
							<option value="Tahoma,Geneva,sans-serif" style="font-family:Tahoma,Geneva,sans-serif">Sans-Serif : Tahoma</option>
							<option value="'Trebuchet MS',Helvetica,sans-serif" style="font-family:'Trebuchet MS',Helvetica,sans-serif">Sans-Serif : Trebuchet</option>
							<option value="Verdana,Geneva,sans-serif" style="font-family:Verdana,Geneva,sans-serif">Sans-Serif : Verdana</option>
							<option value="'Courier New',Courier,monospace" style="font-family:'Courier New',Courier,monospace">Monospace : Courier</option>
							<option value="'Lucida Console',Monaco,monospace" style="font-family:'Lucida Console',Monaco,monospace">Monospace : Lucida</option>
							<option value="googleFont">Google font - <?php echo _("See below");?></option>
						</select>
					</td>
					<td><em><?php echo _("Font for the menu.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Menu color");?></label></td>
					<td><input class="color input" type="text" style="width:150px;" name="bgm" id="bgm" /><span class="del" onclick="f_del_uno1(this)"></span></td>
					<td><em><?php echo _("Background color of the menu.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Text menu color on");?></label></td>
					<td><input class="color input" type="text" style="width:150px;" name="tmc" id="tmc" /><span class="del" onclick="f_del_uno1(this)"></span></td>
					<td><em><?php echo _("Color of the text for the current chapter in the menu.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Text menu color off");?></label></td>
					<td><input class="color input" type="text" style="width:150px;" name="tmo" id="tmo" /><span class="del" onclick="f_del_uno1(this)"></span></td>
					<td><em><?php echo _("Color of the text for the other chapters in the menu.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Enable submenu");?></label></td>
					<td><input type="checkbox" class="input" name="sub" id="sub" /></td>
					<td><em><?php echo _("A submenu will be displayed for each chapter containing H2 tags.");?></em></td>
				</tr>
				<tr><th colspan=3>&nbsp;</th></tr>
				<tr>
					<td><label><?php echo _("Site title");?></label></td>
					<td>
						<select id="tit" name="tit" >
							<option value="" selected><?php echo _("Title in the menu bar");?></option>
							<option value="none"><?php echo _("No title");?></option>
						</select>
					</td>
					<td><em><?php echo _("Position for the site title.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Site title font");?></label></td>
					<td>
						<select id="tfo" name="tfo" >
							<option value="" style="font-family:Georgia,serif">Serif : Georgia - <?php echo _("Default");?></option>
							<option value="'Palatino Linotype','Book Antiqua',Palatino,serif" style="font-family:'Palatino Linotype','Book Antiqua',Palatino,serif">Serif : Palatino</option>
							<option value="'Times New Roman',Times,serif" style="font-family:'Times New Roman',Times,serif">Serif : Times</option>
							<option value="Arial,Helvetica,sans-serif" style="font-family:Arial,Helvetica,sans-serif">Sans-Serif : Arial</option>
							<option value="Arial Black',Gadget,sans-serif" style="font-family:'Arial Black',Gadget,sans-serif">Sans-Serif : Arial Black</option>
							<option value="'Comic Sans MS',cursive,sans-serif" style="font-family:'Comic Sans MS',cursive,sans-serif">Sans-Serif : Comic Sans MS</option>
							<option value="Impact,Charcoal,sans-serif" style="font-family:Impact,Charcoal,sans-serif">Sans-Serif : Impact</option>
							<option value="'Lucida Sans Unicode','Lucida Grande',sans-serif" style="font-family:'Lucida Sans Unicode','Lucida Grande',sans-serif">Sans-Serif : Lucida</option>
							<option value="Tahoma,Geneva,sans-serif" style="font-family:Tahoma,Geneva,sans-serif">Sans-Serif : Tahoma</option>
							<option value="'Trebuchet MS',Helvetica,sans-serif" style="font-family:'Trebuchet MS',Helvetica,sans-serif">Sans-Serif : Trebuchet</option>
							<option value="Verdana,Geneva,sans-serif" style="font-family:Verdana,Geneva,sans-serif">Sans-Serif : Verdana</option>
							<option value="'Courier New',Courier,monospace" style="font-family:'Courier New',Courier,monospace">Monospace : Courier</option>
							<option value="'Lucida Console',Monaco,monospace" style="font-family:'Lucida Console',Monaco,monospace">Monospace : Lucida</option>
							<option value="googleFont">Google font - <?php echo _("See below");?></option>
						</select>
					</td>
					<td><em><?php echo _("Font for the title.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Site title font size");?></label></td>
					<td><input class="input" type="text" style="width:50px;" name="tfs" id="tfs" onkeyup="this.value=this.value.replace(/[^\d]+/,'')" /></td>
					<td><em><?php echo _("Size of the font for the title. Default : 21px. Only number.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Site title line height");?></label></td>
					<td><input class="input" type="text" style="width:50px;" name="tlh" id="tlh" onkeyup="this.value=this.value.replace(/[^.\d]+/,'')" /></td>
					<td><em><?php echo _("Adjust the vertical position of the title. Default : 1.2em. Only number and dot.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Site Title color");?></label></td>
					<td><input class="color input" type="text" style="width:150px;" name="cot" id="cot" /><span class="del" onclick="f_del_uno1(this)"></span></td>
					<td><em><?php echo _("Color of the text for the site title.");?></em></td>
				</tr>
				<tr><th colspan=3>&nbsp;</th></tr>
				<tr>
					<td><label><?php echo _("Chapter Title color");?></label></td>
					<td><input class="color input" type="text" style="width:150px;" name="coc" id="coc" /><span class="del" onclick="f_del_uno1(this)"></span></td>
					<td><em><?php echo _("Color of the text for each chapter title.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Content font");?></label></td>
					<td>
						<select id="cfo" name="cfo" >
							<option value="" style="font-family:Georgia,serif">Serif : Georgia - <?php echo _("Default");?></option>
							<option value="'Palatino Linotype','Book Antiqua',Palatino,serif" style="font-family:'Palatino Linotype','Book Antiqua',Palatino,serif">Serif : Palatino</option>
							<option value="'Times New Roman',Times,serif" style="font-family:'Times New Roman',Times,serif">Serif : Times</option>
							<option value="Arial,Helvetica,sans-serif" style="font-family:Arial,Helvetica,sans-serif">Sans-Serif : Arial</option>
							<option value="'Arial Black',Gadget,sans-serif" style="font-family:'Arial Black',Gadget,sans-serif">Sans-Serif : Arial Black</option>
							<option value="'Comic Sans MS',cursive,sans-serif" style="font-family:'Comic Sans MS',cursive,sans-serif">Sans-Serif : Comic Sans MS</option>
							<option value="Impact,Charcoal,sans-serif" style="font-family:Impact,Charcoal,sans-serif">Sans-Serif : Impact</option>
							<option value="'Lucida Sans Unicode','Lucida Grande',sans-serif" style="font-family:'Lucida Sans Unicode','Lucida Grande',sans-serif">Sans-Serif : Lucida</option>
							<option value="Tahoma,Geneva,sans-serif" style="font-family:Tahoma,Geneva,sans-serif">Sans-Serif : Tahoma</option>
							<option value="'Trebuchet MS',Helvetica,sans-serif" style="font-family:'Trebuchet MS',Helvetica,sans-serif">Sans-Serif : Trebuchet</option>
							<option value="Verdana,Geneva,sans-serif" style="font-family:Verdana,Geneva,sans-serif">Sans-Serif : Verdana</option>
							<option value="'Courier New',Courier,monospace" style="font-family:'Courier New',Courier,monospace">Monospace : Courier</option>
							<option value="'Lucida Console',Monaco,monospace" style="font-family:'Lucida Console',Monaco,monospace">Monospace : Lucida</option>
							<option value="googleFont">Google font - <?php echo _("See below");?></option>
						</select>
					</td>
					<td><em><?php echo _("Font for the content of the page.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Content font size");?></label></td>
					<td><input class="input" type="text" style="width:50px;" name="cfs" id="cfs" onkeyup="this.value=this.value.replace(/[^\d]+/,'')" /></td>
					<td><em><?php echo _("Size of the font for the content of the page. Default : 14px. Only number.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Content font color");?></label></td>
					<td><input class="color input" type="text" style="width:150px;" name="cfc" id="cfc" /><span class="del" onclick="f_del_uno1(this)"></span></td>
					<td><em><?php echo _("Main font color for the content of your page.");?></em></td>
				</tr>
				<tr><th colspan=3>&nbsp;</th></tr>
				<tr>
					<td><label><?php echo _("Add a Google Font");?></label></td>
					<td><input class="input" type="text" style="width:200px;" name="gof" id="gof" /></td>
					<td>
						<em><?php echo _("Choose a font <a href='//www.google.com/fonts' target='_blank'>here</a> - Enter just the name in this form (ex : Lora).");?><br />
						<?php echo _("You can select one or several specific styles separated by a virgule (ex : Lora:700italic,400).");?></em>
					</td>
				</tr>
				<tr><th colspan=3>&nbsp;</th></tr>
			</table>
			</form>
			<div class="bouton fr" onClick="f_publier();" title="<?php echo _("Publish on the web all saved chapters");?>"><?php echo _("Publish");?></div>
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
