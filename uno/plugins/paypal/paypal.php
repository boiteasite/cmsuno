<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
?>
<?php
include('../../password.php'); $user=0; $pass=0; // Lang
if (!is_dir('../../data/sdata/paypal/')) mkdir('../../data/sdata/paypal/');
if (!is_dir('../../data/sdata/paypal/tmp/')) mkdir('../../data/sdata/paypal/tmp/');
include('lang/lang.php');
// ********************* actions *************************************************************************
if (isset($_POST['action']))
	{
	switch ($_POST['action'])
		{
		// ********************************************************************************************
		case 'plugin': ?>
		<div class="blocForm">
			<div id="paypalC" class="bouton fr" onClick="f_paypalConfig();" title="<?php echo _("Configure Paypal plugin");?>"><?php echo _("Config");?></div>
			<div id="paypalV" class="bouton fr current" onClick="f_paypalVente();" title="<?php echo _("Sales list");?>"><?php echo _("Sales");?></div>
			<h2><?php echo _("Paypal");?></h2>
			<div id="paypalConfig" style="display:none;">
				<p><?php echo _("This plugin allows you to add different Paypal buttons in your website.");?></p>
				<p><?php echo _("It is used with the button") .'<img src="uno/plugins/paypal/ckpaypal/icons/ckpaypal.png" style="border:1px solid #aaa;padding:3px;margin:0 6px -5px;border-radius:2px;" />' . _("added to the text editor when the plugin is enable.");?></p>
				<h3><?php echo _("Default Settings :");?></h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo _("Email");?></label></td>
						<td><input type="text" class="input" name="payMail" id="payMail" style="width:150px;" /></td>
						<td><em><?php echo _("Email adress for the Paypal account.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Currency");?></label></td>
						<td>
							<select name="payCurr" id="payCurr">
								<option value="EUR"><?php echo _("Euro");?></option>
								<option value="USD"><?php echo _("US Dollar");?></option>
								<option value="CAD"><?php echo _("Canadian Dollar");?></option>
								<option value="GBP"><?php echo _("Pound Sterling");?></option>
								<option value="CHF"><?php echo _("Swiss Franc");?></option>
								<option value="DKK"><?php echo _("Danish Krone");?></option>
								<option value="NOK"><?php echo _("Norwegian Krone");?></option>
								<option value="SEK"><?php echo _("Swedish Krona");?></option>
								<option value="PLN"><?php echo _("Polish Zloty");?></option>
								<option value="RUB"><?php echo _("Russian Ruble");?></option>
							</select>
						</td>
						<td><em><?php echo _("What is the currency of payment.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Taxe rate (%)");?></label></td>
						<td><input type="text" class="input" name="payTax" id="payTax" style="width:100px;" /></td>
						<td><em><?php echo _("Taxe rate in your country for the payment (%). Will be added to prices.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Appearance");?></label></td>
						<td>
							<select name="payApp" id="payApp" onChange="f_btn_paypal();">
								<option value="CC_LG"><?php echo _("Standard with flags");?></option>
								<option value="_LG"><?php echo _("Standard");?></option>
								<option value="_SM"><?php echo _("Small");?></option>
							</select>
						</td>
						<td>
							<img id="payCC_LG" src="uno/plugins/paypal/images/btnCC_LG.gif" alt="<?php echo _("Standard with flags");?>" />
							<img id="pay_LG" style="display:none;" src="uno/plugins/paypal/images/btn_LG.gif" alt="<?php echo _("Standard with flags");?>" />
							<img id="pay_SM" style="display:none;" src="uno/plugins/paypal/images/btn_SM.gif" alt="<?php echo _("Standard with flags");?>" />
						</td>
					</tr>
					<tr>
						<td><label><?php echo _("Selling");?></label></td>
						<td>
							<select name="payAct" id="payAct">
								<option value="products"><?php echo _("products");?></option>
								<option value="services"><?php echo _("services");?></option>
							</select>
						</td>
						<td><em><?php echo _("Define what are you selling (only buy button).");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Donation value");?></label></td>
						<td>
							<select name="payDon" id="payDon">
								<option value="1"><?php echo _("fixed");?></option>
								<option value="0"><?php echo _("free");?></option>
							</select>
						</td>
						<td><em><?php echo _("Define a value for donation or let the client choose (only donate button).");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Notify URL");?></label></td>
						<td style="vertical-align:middle;padding:10px;"><?php echo substr($_SERVER['HTTP_REFERER'],0,-4).'/plugins/paypal/ipn.php';?></td>
						<td><em><?php echo _("Local File for Paypal Instant Payment Notification (IPN)"); ?></em></td>
					</tr>
				</table>
				<br />
				<h3><?php echo _("Publish Settings :");?></h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo _("Mode");?></label></td>
						<td>
							<select name="payMod" id="payMod">
								<option value="prod"><?php echo _("Production");?></option>
								<option value="test"><?php echo _("Test (sandbox)");?></option>
							</select>
						</td>
						<td><em><?php echo _("When publishing : Production = Real payment ; Test = Dummy payment to test account.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Popup");?></label></td>
						<td>
							<select name="payPop" id="payPop">
								<option value="0"><?php echo _("No");?></option>
								<option value="1"><?php echo _("Yes");?></option>
							</select>
						</td>
						<td><em><?php echo _("Open Paypal in a new small windows. Allow visitor to keep an eye on your website.");?></em></td>
					</tr>
				</table>
				<div class="bouton fr" onClick="f_save_paypal();" title="<?php echo _("Save settings");?>"><?php echo _("Save");?></div>
				<div class="clear"></div>
			</div>
			<div id="paypalVente">
				<h3><?php echo _("List of the Paypal payments");?> :</h3>
				<style>
					#paypalVente table tr{border-bottom:1px solid #888;}
					#paypalVente table th{text-align:center;padding:5px 2px;font-weight:700;}
					#paypalVente table td{text-align:left;padding:2px 6px;vertical-align:middle;color:#0b4a6a;}
					#paypalVente table tr.PayTreatedYes td{color:green;}
					#paypalVente table td.yesno{text-decoration:underline;cursor:pointer;}
				</style>
			<?php
			$tab=''; $d='../../data/sdata/paypal/';
			if ($dh=opendir($d))
				{
				while (($file = readdir($dh))!==false) { if ($file!='.' && $file!='..') $tab[]=$d.$file; }
				closedir($dh);
				}
			if(count($tab))
				{
				echo '<br /><table>';
				echo '<tr><th>'._("Date").'</th><th>'._("Type").'</th><th>'._("Name").'</th><th>'._("Adress").'</th><th>'._("Article").'</th><th>'._("Price").'</th><th>'._("Treated").'</th></tr>';
				$b = array();
				foreach($tab as $r)
					{
					$q=@file_get_contents($r);
					$a=json_decode($q,true);
					$b[]=$a;
					}
				function sortTime($u1,$u2) {return (isset($u2['time'])?$u2['time']:0) - (isset($u1['time'])?$u1['time']:0);}
				usort($b, 'sortTime');
				foreach($b as $r)
					{
					if($r)
						{
						$item=((isset($r['item_number'])&&$r['item_number'])?$r['item_number'].' : ':'').((isset($r['item_name']) && isset($r['quantity']))?$r['item_name'].(($r['quantity']!="0")?' ('.$r['quantity'].')':''):'');
						if(!$item)
							{
							$v=1;
							while(isset($r['item_name'.$v]))
								{
								$item.=($item?'<br />':'').((isset($r['item_number'.$v])&&$r['item_number'.$v])?$r['item_number'.$v].' : ':'').$r['item_name'.$v].' ('.$r['quantity'.$v].')';
								++$v;
								}
							}
						echo '<tr'.($r['treated']?' class="PayTreatedYes"':'').'>';
						echo '<td>'.(isset($r['time'])?date("dMy H:i", $r['time']):'').'</td>';
						echo '<td>'.(isset($r['subscr_id'])?'Sub':((isset($r['quantity'])&&$r['quantity']=="0")?'Don':'Pay')).'</td>';
						echo '<td>'.$r['first_name'].'&nbsp;'.$r['last_name'].'<br />'.$r['payer_email'].'</td>';
						echo '<td>'.$r['address_street'].'<br />'.$r['address_zip'].' - '.$r['address_city'].'<br />'.$r['address_state'].' - '.$r['address_country'].'</td>';
						echo '<td>'.$item.'</td>';
						echo '<td>'.$r['mc_gross'].' '.$r['mc_currency'].'</td>';
						echo '<td '.(!$r['treated']?'onClick="f_treated_paypal(this,\''.$r['txn_id'].'\',\''._("No").'\')"':'').($r['treated']?'>'._("No"):' class="yesno">'._("Yes")).'</td>';
					//	echo '<td>'.$a['custom'].'</td>';
						echo '</tr>';
						}
					}
				echo '</table>';
				}
			?>
			</div>
		</div>
		<?php break;
		// ********************************************************************************************
		case 'save':
		$q = @file_get_contents('../../data/paypal.json');
		$q1 = @file_get_contents('../../data/site.json');
		$home=0;
		if($q1)
			{
			$a1 = json_decode($q1,true);
			$home = $a1['nom'];
			}
		if($q) $a = json_decode($q,true);
		else $a = Array();
		$a['mail'] = $_POST['mail'];
		$a['curr'] = $_POST['curr'];
		$a['tax'] = ($_POST['tax']?$_POST['tax']:0);
		$a['app'] = $_POST['app'];
		$a['mod'] = $_POST['mod'];
		$a['pop'] = $_POST['pop'];
		$a['act'] = $_POST['act'];
		$a['don'] = ($_POST['don']?$_POST['don']:0);
		$a['url'] = substr($_SERVER['HTTP_REFERER'],0,-4).'/plugins/paypal/ipn.php';
		$a['home'] = substr($_SERVER['HTTP_REFERER'],0,-7).($home?$home:'index').'.html';
		$a['lang5'] = (isset($langPlug[$lang])?substr($langPlug[$lang],0,5):'en_US');
		$out = json_encode($a);
		if (file_put_contents('../../data/paypal.json', $out)) echo _('Backup performed');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		case 'treated':
		$q = @file_get_contents('../../data/sdata/paypal/'.$_POST['id'].'.json');
		if($q)
			{
			$a = json_decode($q,true);
			$a['treated'] = 1;
			}
		$out = json_encode($a);
		if (file_put_contents('../../data/sdata/paypal/'.$_POST['id'].'.json', $out)) echo _('Treated');
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>
