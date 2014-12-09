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
		<style type="text/css">#contactResult td{padding:10px;vertical-align:middle;} #contactResult td:first-child{max-width:120px;}</style>
		<div class="blocForm">
			<h2>Contact</h2>
			<p><?php echo _("This plugin allows you to create a contact form that can be installed at any location of the site.");?></p>
			<p><?php echo _("Just insert the code")." <code>[[contact]]</code> "._("in the text of your page or directly into the template. This code will be replaced by the form.");?></p>
			<table class="hForm">
				<tr>
					<td><label><?php echo _("Admin email");?></label></td>
					<td><input type="text" class="input" name="contactAdmin" id="contactAdmin" /></td>
					<td><em><?php echo _("E-mail address of destination : Your email address or the contact site.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Send button");?></label></td>
					<td><input type="text" class="input" name="contactSend" id="contactSend" /></td>
					<td><em><?php echo _("The word that should appear on the 'Send' button.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Thank you");?></label></td>
					<td><input type="text" class="input" name="contactHappy" id="contactHappy" /></td>
					<td><em><?php echo _("The sentence of thanks that will be displayed on the screen after sending the message.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Enable Captcha");?></label></td>
					<td><input type="checkbox" class="input"  name="contactCaptcha" id="contactCaptcha" /></td>
					<td><em><?php echo _("Code image to enter to block the automatic sending of email by robots.");?></em></td>
				</tr>
			</table>
			<h3><?php echo _("Add a field :");?></h3>
			<table class="hForm">
				<tr>
					<td><label><?php echo _("Label");?></label></td>
					<td>
						<input type="text" class="input" name="contactLabel" id="contactLabel" value="" />
						<select name="contactType" id="contactType" />
							<option value="te"><?php echo _("Text");?></option>
							<option value="ta"><?php echo _("Textarea");?></option>
						</select>
					</td>
					<td><div class="bouton fr" onClick="f_contact_add(document.getElementById('contactLabel').value, document.getElementById('contactType').options[document.getElementById('contactType').selectedIndex].value);" title="<?php echo _("Saves settings");?>"><?php echo _("Add");?></div></td>
				</tr>
			</table>
			<h3><?php echo _("Result :");?></h3>
			<form id="frmContact">
				<table id="contactResult"></table>
			</form>
			<div class="bouton fr" onClick="f_contact();" title="<?php echo _("Saves settings");?>"><?php echo _("Save");?></div>
			<div class="clear"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		case 'save':
		$q = file_get_contents('../../data/busy.json'); $a = json_decode($q,true); $Ubusy = $a['nom'];
		$a = array(); $c=0;
		$a['mail'] = $_POST['contactAdmin'];
		$a['send'] = $_POST['contactSend'];
		$a['happy'] = $_POST['contactHappy'];
		if ($_POST['contactCaptcha']=="true") $a['captcha']=1; else $a['captcha']=0;
		foreach($_POST as $k=>$v)
			{
			if ($k!='action' && $k!='contactAdmin' && $k!='contactSend' && $k!='contactHappy' && $k!='contactCaptcha')
				{
				$a['frm'][$c]['t'] = substr($k,0,2);
				$a['frm'][$c]['l'] = substr($k,2);
				}
			++$c;
			}
		$out = json_encode($a);
		if (file_put_contents('../../data/sdata/'.$Ubusy.'/contact.json', $out)) echo _('Backup performed');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		case 'load':
		$q = file_get_contents('../../data/busy.json'); $a = json_decode($q,true); $Ubusy = $a['nom'];
		$q = file_get_contents('../../data/sdata/'.$Ubusy.'/contact.json');
		echo stripslashes($q); exit;
		break;
		// ********************************************************************************************
		case 'send':
		$q = file_get_contents('../../data/busy.json'); $a = json_decode($q,true); $Ubusy = $a['nom'];
		if (isset($_POST['contactCaptcha']))
			{
			session_start();
			if ($_POST['contactCaptcha']!=$_SESSION['captcha']['code'])
				{
				echo _('Captcha error');
				Exit;
				}
			}
		$msgT = "";
		$msgH = "<html><head></head><body><table>";
		$q = file_get_contents('../../data/sdata/'.$Ubusy.'/contact.json');
		$a = json_decode($q,true);
		$mail = $a['mail'];
		$happy = $a['happy'];
		$l = 0;
		if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) $rn = "\r\n";
		else $rn = "\n";
		foreach($_POST as $k=>$v)
			{
			if ($k!='action')
				{
				$msgT .= $k.' : '.$v.$rn;
				$msgH .= '<tr><td>'.$k.'</td><td> : '.$v.'</td></tr>';
				$l += strlen($v);
				}
			}
		$msgH .= "</table></body></html>";
		$boundary = "-----=".md5(rand());
		$q = file_get_contents('../../data/'.$Ubusy.'/site.json');
		$a = json_decode($q,true);

		$sujet = $a['tit'] . " - Contact";
		$header = "From: \"No reply\"<".$mail.">".$rn;
		$header.= "MIME-Version: 1.0".$rn;
		$header.= "Content-Type: multipart/alternative;".$rn." boundary=\"$boundary\"".$rn;
		$msg= $rn."--".$boundary.$rn;
		$msg.= "Content-Type: text/plain; charset=\"utf-8\"".$rn;
		$msg.= "Content-Transfer-Encoding: 8bit".$rn;
		$msg.= $rn.$msgT.$rn;
		$msg.= $rn."--".$boundary.$rn;
		$msg.= "Content-Type: text/html; charset=\"utf-8\"".$rn;
		$msg.= "Content-Transfer-Encoding: 8bit".$rn;
		$msg.= $rn.$msgH.$rn;
		$msg.= $rn."--".$boundary."--".$rn;
		$msg.= $rn."--".$boundary."--".$rn;
		if($l>10 && mail($mail, stripslashes($sujet), stripslashes($msg),$header)) echo _('OK')." ".$happy;
		else echo _('Failed to send');
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>
