<?php
if (!isset($_SESSION['cmsuno'])) exit();
if(!file_exists('data/sdata/'.$Ubusy.'/contact.json'))
	{
	@unlink('plugins/contact/on.txt');
	exit;
	}
?>
<?php
	// {"t":"te","l":"Pr\u00e9nom"}
	$o1 = "\r\n".'<form id="contactfrm">'."\r\n\t".'<table>'."\r\n";
	$q1 = file_get_contents('data/sdata/'.$Ubusy.'/contact.json');
	$a1 = json_decode($q1,true);
	$s1 = '';
	$s2 = '';
	foreach ($a1['frm'] as $k1=>$v1)
		{
		$v2 = stripslashes(utf8_decode($v1['l']));
		$v2 = strtr($v2, 'באגהדוחיטךכםלמןסףעפצץתשûü‎ÿ -', 'aaaaaaceeeeiiiinooooouuuuyy__');
		$v2 = preg_replace("/[^a-zA-Z0-9\d_]+/","",$v2);
		$s1 .= '&'.$v2.'="+document.getElementById(\''.$v2.'\').value+"';
		$o1 .= "\t\t".'<tr><td><label>'.stripslashes(str_replace('_',' ',$v1['l'])).'</label></td>';
		if ($v1['t']=='te') $o1 .= '<td><input type="text" name="'.$v2.'" id="'.$v2.'" /></td>';
		else if ($v1['t']=='ta') $o1 .= '<td><textarea name="'.$v2.'" id="'.$v2.'"></textarea></td>';
		$o1 .= '</tr>'."\r\n";
		$s2 .= 'document.getElementById(\''.$v2.'\').value="";';
		}
	if ($a1['captcha']==1)
		{
		$s1 .= '&contactCaptcha="+document.getElementById(\'contactCaptcha\').value+"';
		$s2 .= 'document.getElementById(\'contactCaptcha\').value="";';
		$o1 .= "\t\t".'<tr><td>Captcha :<br /><img src="" title="Captcha" id="imageCaptcha" style="height:30px; width:72px" /></td><td><input type="text" name="contactCaptcha" id="contactCaptcha" /><br />'._('Copy this code').'</td></tr>'."\r\n";
		$foot .= '<script type="text/javascript">window.onload=function(){x=new XMLHttpRequest();x.open("POST","uno/plugins/contact/captcha.php",true);x.setRequestHeader("Content-type","application/x-www-form-urlencoded");x.onreadystatechange=function(){if (x.readyState==4 && x.status==200){document.getElementById("imageCaptcha").src=x.responseText;}};x.send();};</script>'."\r\n";
		}
	$o1 .= "\t\t".'<tr><td></td><td><button type="button" onClick="f_contact_send();">'.$a1['send'].'</button></td>'."\r\n";
	$o1 .= "\t".'</table>'."\r\n".'</form>'."\r\n";
	$content = str_replace('[[contact]]',$o1,$content);
	$html = str_replace('[[contact]]',$o1,$html);
	//
	$o2 = '<script type="text/javascript">'."\r\n";
	$o2 .= 'function f_contact_send(){x=new XMLHttpRequest();x.open("POST","uno/plugins/contact/contact.php",true);x.setRequestHeader("Content-type","application/x-www-form-urlencoded");x.setRequestHeader("X-Requested-With","XMLHttpRequest");x.onreadystatechange=function(){if (x.readyState==4 && x.status==200){f_info(x.responseText);}};x.send("action=send'.$s1.'");}'."\r\n";
	$o2 .= 'function f_info(i){if(document.getElementById("contactPop")==null){h=document.createElement("div");h.id="contactPop";s=h.style;s.textAlign="center";s.fontSize="110%";s.color="#000";s.backgroundColor="#999";s.border="2px outset #777";s.position="fixed";s.zIndex="99";s.top="40%";s.left="50%";s.margin="-100px 0 0 -150px";s.width="300px";s.padding="0 0 30px";s.opacity=1;m=document.createElement("div");t=m.style;t.width="260px";t.padding="20px";t.backgroundColor="#ddd";m.innerHTML=i;h.appendChild(m);document.body.appendChild(h);setTimeout(function(){f_fade(1);},5000);'.$s2.'}}'."\r\n";
	$o2 .= 'function f_fade(f){f-=.05;if(f>0)setTimeout(function(){h.style.opacity=f;f_fade(f);},30);else document.body.removeChild(h);}'."\r\n";
	$o2 .= '</script>'."\r\n";
	$head .= $o2;
?>
