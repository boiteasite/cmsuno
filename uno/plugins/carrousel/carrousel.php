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
			<h2>Carrousel</h2>
			<p><?php echo _("Add one or more images carousels.");?></p>
			<p><?php echo _("Just insert the code")." <code>[[carousel-3]]</code> "._("in the text of your page or directly into the template. This code will be replaced by the corresponding carousel.");?></p>
			<p><?php echo _("To operate, JQuery must be inserted. See settings (or in the template).");?></p>
			<h3><?php echo _("ID number :");?></h3>
			<table class="hForm">
				<tr>
					<td><label><?php echo _("Carousel Number");?></label></td>
					<td>
						<select name="carrouselNum" id="carrouselNum" onChange="f_load_carrousel(0);" />
							<option value="0"><?php echo _("New");?></option>
						</select>
						<div class="bouton" style="margin-left:30px;" id="bSCarrousel" onClick="f_supp_carrousel();" title="<?php echo _("Delete carousel");?>"><?php echo _("Delete");?></div>
					</td>
					<td><em><?php echo _("Ability to create severals carousels. They are identified by a number.");?></em></td>
				</tr>
			</table>
			<h3><?php echo _("Parameters :");?></h3>
			<table class="hForm">
				<tr>
					<td><label><?php echo _("Carousel type");?></label></td>
					<td>
						<select name="carrouselTyp" id="carrouselTyp" onChange="f_carrousel_type();" />
							<option value="nivo">Nivo Slider</option>
							<option value="fred">CarouFredSel</option>
							<option value="ken">KenBurning</option>
							<option value="feat">FeatureCarousel</option>
						</select>
					</td>
					<td id="tdCarTyp">
						<span style="font-size:90%;color:#666;" id="emCarNivo">
							<?php echo _("The 'frame by frame' Slider of reference. Many successful transitions.");?>
						</span>
						<span style="display:none;font-size:90%;color:#666;" id="emCarFred">
							<?php echo _("A multi-image carousel with many possibilities. Perfect for a logos parade.");?>
						</span>
						<span style="display:none;font-size:90%;color:#666;" id="emCarKen">
							<?php echo _("A simple slide show using the famous Ken Burns effect.");?>
						</span>
						<span style="display:none;font-size:90%;color:#666;" id="emCarFeat">
							<?php echo _("A simple and elegant carousel with great 3D rotating effect.");?>
						</span>
					</td>
				</tr>
				<tr id="trCarW">
					<td><label><?php echo _("Width");?></label></td>
					<td><input type="text" class="input" name="carrouselW" id="carrouselW" style="width:50px;" /></td>
					<td><em><?php echo _("Width of the carousel (px).");?></em></td>
				</tr><tr id="trCarH">
					<td><label><?php echo _("Height");?></label></td>
					<td><input type="text" class="input" name="carrouselH" id="carrouselH" style="width:50px;" /></td>
					<td><em><?php echo _("Carousel Height (px).");?></em></td>
				</tr><tr id="trCarPause">
					<td><label><?php echo _("Pause");?></label></td>
					<td><input type="text" class="input" name="carrouselPause" id="carrouselPause" style="width:50px;" /></td>
					<td><em><?php echo _("Duration of the break on each image (ms).");?></em></td>
				</tr><tr id="trCarSpeed">
					<td><label><?php echo _("Speed");?></label></td>
					<td><input type="text" class="input" name="carrouselSpeed" id="carrouselSpeed" style="width:50px;" /></td>
					<td><em><?php echo _("Duration of the transition between two images (ms).");?></em></td>
				</tr><tr id="trCarRandStart">
					<td><label><?php echo _("Random First");?></label></td>
					<td><input type="checkbox" class="input"  name="carrouselRandStart" id="carrouselRandStart" /></td>
					<td><em><?php echo _("The first image that appears is random.");?></em></td>
				</tr><tr id="trCarTransition">
					<td><label><?php echo _("Transition");?></label></td>
					<td>
						<select name="carrouselTransition" id="carrouselTransition" />
							<option value="sliceDown">sliceDown</option>
							<option value="sliceDownLeft">sliceDownLeft</option>
							<option value="sliceUp">sliceUp</option>
							<option value="sliceUpLeft">sliceUpLeft</option>
							<option value="sliceUpDown">sliceUpDown</option>
							<option value="sliceUpDownLeft">sliceUpDownLeft</option>
							<option value="fold">fold</option>
							<option value="fade">fade</option>
							<option value="random">random</option>
							<option value="slideInRight">slideInRight</option>
							<option value="slideInLeft">slideInLeft</option>
							<option value="boxRandom">boxRandom</option>
							<option value="boxRain">boxRain</option>
							<option value="boxRainReverse">boxRainReverse</option>
							<option value="boxRainGrow">boxRainGrow</option>
							<option value="boxRainGrowReverse">boxRainGrowReverse</option>
						</select>
					</td>
					<td><em><?php echo _("Type of transition between images.");?></em></td>
				</tr>
			</table>
			<h3><?php echo _("Add a picture :");?></h3>
			<table class="hForm">
				<tr>
					<td><label><?php echo _("Image");?></label></td>
					<td>
						<input type="text" class="input" name="carrouselImg" id="carrouselImg" value="" />
						<div class="bouton" style="margin-left:30px;" id="bFCarrousel" onClick="f_finder_select('carrouselImg')" title="<?php echo _("File manager");?>"><?php echo _("File Manager");?></div>
					</td>
					<td><div class="bouton fr" onClick="f_carrousel_add(document.getElementById('carrouselImg').value,'')" title="<?php echo _("Add this image");?>"><?php echo _("Add");?></div></td>
				</tr>
			</table>
			<h3><?php echo _("Selection :");?></h3>
			<form id="frmCarrousel">
				<table id="carrouselResult"></table>
			</form>
			<div class="bouton fr" onClick="f_save_carrousel();" title="<?php echo _("Save settings");?>"><?php echo _("Save");?></div>
			<div class="clear"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		case 'save':
		$q = file_get_contents('../../data/carrousel.json');
		$a = json_decode($q,true);
		$n = $_POST['car'];
		if($n==0) // nouveau carrousel
			{
			foreach($a as $k=>$v)
				{
				if($k>$n) $n = $k; // max
				}
			++$n; // valeur max +1
			for($v=1;$v<$n;++$v)
				{
				if(!isset($a[$v])) {$n = $v; break;} // valeur plus petite libre
				}
			}
		$a[$n]['typ'] = $_POST['typ'];
		$a[$n]['wid'] = $_POST['wid'];
		$a[$n]['hei'] = $_POST['hei'];
		$a[$n]['pau'] = $_POST['pau'];
		$a[$n]['spe'] = $_POST['spe'];
		$a[$n]['tra'] = $_POST['tra'];
		if ($_POST['rst']=="true") $a[$n]['rst']=1; else $a[$n]['rst']=0;
		for ($v=0;$v<$_POST['nb'];++$v)
			{
			$a[$n]['img'][$v]['s'] = $_POST['img'.$v];
			$a[$n]['img'][$v]['t'] = $_POST['text'.$v];
			}
		$out = json_encode($a);
		if (file_put_contents('../../data/carrousel.json', $out)) echo _('Backup performed : carrousel-').$n;
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		case 'supp':
		$q = file_get_contents('../../data/carrousel.json');
		$a = json_decode($q,true);
		if(isset($_POST['s']) && isset($a[$_POST['s']]))
			{
			unset($a[$_POST['s']]);
			$out = json_encode($a);
			if (file_put_contents('../../data/carrousel.json', $out)) echo _('Deletion made');
			else echo '!'._('Impossible deletion');
			}
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
	
// slidesjs : Adapté écran tactile (déplacement images)
// caroufredsel : défilé de marque
?>
