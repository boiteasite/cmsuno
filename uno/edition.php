<!DOCTYPE html>
<?php
if(!isset($_SESSION['cmsuno']) || !isset($_SESSION['unox']) || !isset($unox) || $_SESSION['cmsuno']!=$unox) exit();
?>
<?php
$user=0; $pass=0; // reset
if(file_exists(dirname(__FILE__).'/../files/archive.zip')) unlink(dirname(__FILE__).'/../files/archive.zip');
function f_theme() {
	// liste des themes dans un select
	$t = "uno/template/";
	$d = opendir($t);
	while(($f = readdir($d))!==false) { if(is_dir($t.$f) && file_exists($t.$f.'/template.html') && $f!="." && $f!="..") echo '<option value="'.$f.'">'.$f.'</option>'; }
	closedir($d);
}
?>

<html lang="<?php echo $lang; ?>">
<head>
<meta charset="utf-8" />
<meta name="robots" content="noindex" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" /> 
<title>CMSUno</title>
<script type="text/javascript" src="<?php echo $Udep; ?>includes/js/ckeditor/ckeditor.js"></script>
<link rel="icon" type="image/png" href="<?php echo $Udep; ?>includes/img/favicon.png" />
<link rel="stylesheet" href="<?php echo $Udep; ?>includes/css/uno.css" />
<style type="text/css">
.onoff{background-image:url("<?php echo $Udep; ?>includes/img/onoff16.png")}
.plugAdd,.plugDel,.plugBest{background-image:url("<?php echo $Udep; ?>includes/img/ui-icons_444444_256x240.png")}
</style>
</head>
<body>
	<div class="blocTop bgNoir">
		<div class="container">
			<span class="titre"><a href="https://github.com/boiteasite/cmsuno" title="<?php echo T_("CMSUno on GitHub");?>" target="_blank">CMSUno<?php if(isset($Uversion)) echo '&nbsp;<em>'.$Uversion.'</em>'; ?></a></span>
			<div id="info" class="navitem"></div>
			<label id="hamburgerLabel" for="hamburgerInput">&#9776;</label>
			<input type="checkbox" id="hamburgerInput" />
			<ul id="topMenu" class="topMenu topMenuEdition">
				<li><a id="apage" style="text-decoration:underline" href=""><?php echo T_("Page");?></a></li>
				<li><a id="aconfig" style="display:none;" onClick="f_config();document.getElementById('hamburgerInput').checked=false;" href="javascript:void(0)"><?php echo T_("Settings");?></a></li>
				<li><a id="aplugin" style="display:none;" onClick="f_plugin(0);f_plugAll(document.getElementById('plugOnOff'),1);document.getElementById('hamburgerInput').checked=false;" href="javascript:void(0)"><?php echo T_("Plugins");?></a></li>
				<li><a id="avoir" href="index.html" onClick="document.getElementById('hamburgerInput').checked=false;" target="_blank"><?php echo T_("See the website");?></a></li>
				<li><a id="alogout" onClick="f_logout();" href="javascript:void(0)"><?php echo T_("Log out");?></a></li>
			</ul>
			<div id="wait" class="navitem wait"><img style="margin:2px 6px 0 0;" src="<?php echo $Udep; ?>includes/img/wait.gif" /></div>
		</div>
	</div><!-- .blocTop-->
	<div id="chaps" class="container">
		<div class="blocBouton">
			<div class="bouton finder fr" id="boutonFinder0" onClick="f_elfinder(0)" title="<?php echo T_("File manager");?>"><img src="<?php echo $Udep; ?>includes/img/finder.png" /></div>
			<div class="bouton fr" onClick="f_publier();" title="<?php echo T_("Publish on the web all saved chapters");?>"><?php echo T_("Publish");?></div>
			<div id="menu"></div>
		</div>
		<div id="finderDiv"></div>
		<div>
			<div class="input" id="contentP">
				<textarea name="content" id="content"></textarea>
			</div>
		</div>
		<div class="blocBouton" style="text-align:right;">
			<div id="optOnOff" class="onoff" onClick="f_chapOption(this);"></div>
			<div class="bouton fl" onClick="f_supp_chap();" title="<?php echo T_("Remove this chapter and title");?>"><?php echo T_("Delete Chapter");?></div>
			<span class="blocInput fl">
				<label class="label"><?php echo T_("Chapter title");?>&nbsp;:</label>
				<input type="text" id="titreChap" name="titre" class="input" style="" />
			</span>
			<div class="bouton" onClick="f_nouv_chap();" title="<?php echo T_("Inserts a chapter after this one. Have you saved ?");?>"><?php echo T_("New chapter");?></div>
			<div class="bouton" id="boutonSauv" onClick="f_sauve_chap();" title="<?php echo T_("Save this chapter and title");?>"><?php echo T_("Save Chapter");?></div>
			<div class="bouton" id="boutonPub" onClick="f_publier();" title="<?php echo T_("Publish on the web all saved chapters");?>"><?php echo T_("Publish");?></div>
			<div id="chapOpt" class="chapOpt" style="position:relative;display:none;text-align:left;clear:both;">
				<div class="bouton fr" onClick="f_suppPub();" title="<?php echo T_("Destroy the HTML file of this page (not the data)");?>"><?php echo T_("Delete Publication");?></div>
				<div style="padding-top:12px;">
					<label><?php echo T_("No Title");?></label><input type="checkbox" class="input" name="optTit" id="optTit" />
					<label><?php echo T_("Not in menu");?></label><input type="checkbox" class="input" name="optMenu" id="optMenu" />
					<label><?php echo T_("Hidden");?></label><input type="checkbox" class="input" name="optDisp" id="optDisp" />
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div><!-- .container -->
	<div id="config" class="container" style="display:none;">
		<div class="blocForm">
			<div class="bouton fr" onClick="f_publier();" title="<?php echo T_("Publish on the web all saved chapters");?>"><?php echo T_("Publish");?></div>
			<h2><?php 
			echo T_("My Card"); ?></h2>
			<table class="hForm">
				<tr>
					<td><label><?php echo T_("Page Title");?></label></td>
					<td><input type="text" class="input" name="tit" id="tit" onkeyup="f_ctit(this);" /><span id="ctit"></span></td>
					<td><em><?php echo T_("Very important. The most important words at the beginning. 60 characters max.");?></em></td>
				</tr><tr>
					<td><label><?php echo T_("Page Description");?></label></td>
					<td><input type="text" class="input" name="desc" id="desc" onkeyup="f_cdesc(this);" /><span id="cdesc"></span></td>
					<td><em><?php echo T_("Important for attracting visitors. 160 characters max.");?></em></td>
				</tr><tr>
					<td><label><?php echo T_("Base URL");?></label></td>
					<td><input type="text" class="input" name="url" id="url" /></td>
					<td><em><?php echo T_("Base URL for this site (URL displayed by the browser without uno.php).");?></em></td>
				</tr><tr>
					<td><label><?php echo T_("Filename");?></label></td>
					<td><input type="text" class="input" style="text-align:right;max-width:250px;" name="nom" id="nom" />.html</td>
					<td><em><?php echo T_("Created file will be index.html by default.");?></em></td>
				</tr><tr>
					<td><label><?php echo T_("E-mail");?></label></td>
					<td><input type="text" class="input" name="mel" id="mel" /></td>
					<td><em><?php echo T_("Email address of the site administrator.");?></em></td>
				</tr><tr>
					<td><label><?php echo T_("Theme");?></label></td>
					<td>
						<select name="tem" id="tem">
						<?php f_theme(); ?>
						
						</select>
					</td>
					<td><em><?php echo T_("Theme to use for this page.");?></em></td>
				</tr>
			</table>
			<h2><?php echo T_("Options");?></h2>
			<table class="hForm">
				<tr>
					<td><label><?php echo T_("LazyLoad");?></label></td>
					<td><input type="checkbox" class="input" name="lazy" id="lazy" /></td>
					<td><em><?php echo T_("Dynamic images loading. (recommended)");?></em></td>
				</tr><tr>
					<td><label><?php echo T_("Load w3.css");?></label></td>
					<td><input type="checkbox" class="input" name="w3" id="w3" /></td>
					<td><em><?php echo T_("W3.css is a modern CSS framework with built-in responsiveness. (Used by some themes and plugins)");?></em></td>
				</tr><tr>
					<td><label><?php echo T_("Load JQuery");?></label></td>
					<td><input type="checkbox" class="input" name="jq" id="jq" /></td>
					<td><em><?php echo T_("Javascript library useful for some plugins. (not recommended if not required)");?></em></td>
				</tr><tr>
					<td><label><?php echo T_("CSS template");?></label></td>
					<td><input type="checkbox" class="input" name="sty" id="sty" /></td>
					<td><em><?php echo T_("Same styles in the editor and page. Ref");?> : <span style='font-weight:700'>style.css</span> <?php echo T_("or");?> <span style='font-weight:700'>styles.css</span> <?php echo T_("in");?> <span style='font-weight:700'>template/</span> <?php echo T_("or");?> <span style='font-weight:700'>template/css/</span>.</em></td>
				</tr><tr>
					<td><label><?php echo T_("Width page");?> (px)</label></td>
					<td><input type="text" class="input" name="edw" id="edw" style="width:50px;" maxlength="4" onkeypress="return f_nombre(event)"/></td>
					<td><em><?php echo T_("Adapt the editor width with the observed width of the HTML page. (960 by default)");?></em></td>
				</tr><tr>
					<td><label><?php echo T_("Menu offset");?> (px)</label></td>
					<td><input type="text" class="input" name="ofs" id="ofs" style="width:50px;" maxlength="4" onkeypress="return f_nombre(event)"/></td>
					<td><em><?php echo T_("Margin height upon arrival on a chapter after clicking on the menu (0 by default)");?></em></td>
				</tr>
			</table>
			<div class="bouton fr" id="boutonConfig" onClick="f_sauve_config();" title="<?php echo T_("Saves settings");?>"><?php echo T_("Save");?></div>
			<div class="clear"></div>
		</div>
		<div class="blocBouton">
			<div id="archOnOff" class="onoff" onClick="f_archOption(this);"></div>
			<div class="bouton fr" onClick="f_archivage();" title="<?php echo T_("Save all the website");?>"><?php echo T_("Make a backup");?></div>
			<div id="boutonRestaure" class="bouton fl" onClick="f_restaure(document.getElementById('archive').options[document.getElementById('archive').selectedIndex].value);" title="<?php echo T_("Restore a backup (delete the current site)");?>"><?php echo T_("Restore a backup");?></div>
			<div id="blocArchive"></div>
			<div id="archOpt" class="archOpt" style="position:relative;display:none;text-align:left;clear:both;">
				<div class="bouton" onClick="f_archDel(document.getElementById('archive').options[document.getElementById('archive').selectedIndex].value);" title="<?php echo T_("Remove this backup");?>"><?php echo T_("Remove this backup");?></div>
				<div class="bouton" onClick="f_archDownload(document.getElementById('archive').options[document.getElementById('archive').selectedIndex].value);" title="<?php echo T_("Download this backup");?>"><?php echo T_("Download this backup");?></div>
				<div class="bouton fr" onClick="f_fileDownload();" title="<?php echo T_("Download all Finder files");?>"><?php echo T_("Download all Finder files");?></div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="blocForm">
			<h2><?php echo T_("Change Language");?></h2>
			<table class="hForm">
				<tr>
					<td><label><?php echo T_("Language");?></label></td>
					<td>
						<select name="lang" id="lang" onchange="document.getElementById('user0').value='';document.getElementById('pass0').value='';">
						<?php foreach($langCode as $k=>$r) { echo "<option value='".$k."' ".(($lang==$k)?'selected':'').">".$k."</option>"; } ?>
						</select>
					</td>
					<td>
						<em><?php echo T_("Language for the admin side of the site.");?></em>
					</td>
				</tr><tr>
					<td><label><?php echo T_("Embedded Gettext");?></label></td>
					<td><input type="checkbox" class="input" name="gt" id="gt" <?php if(!empty($forceGettext)) echo 'checked '; ?>/></td>
					<td><em><?php echo T_("Force the use of the GETTEXT module of CMSUNO (not recommended if PHP-Gettext works)");?></em></td>
				</tr>
			</table>
			<div class="bouton fr" id="boutonPass" onClick="f_sauve_pass();" title="<?php echo T_("Save");?>"><?php echo T_("Save");?></div>
			<div class="clear"></div>
			<h2><?php echo T_("Change User / Password");?></h2>
			<table class="hForm">
				<tr>
					<td><label><?php echo T_("Current user");?></label></td>
					<td><input type="text" class="input" name="user0" id="user0" autocomplete="off" /></td>
					<td></td>
				</tr>
				<tr>
					<td><label><?php echo T_("Current password");?></label></td>
					<td><input type="password" class="input" name="pass0" id="pass0" autocomplete="off" /></td>
					<td></td>
				</tr>
				<tr>
					<td><label><?php echo T_("User");?></label></td>
					<td><input type="text" class="input" name="user" id="user" /></td>
					<td><em><?php echo T_("Enter a nickname. Avoid words that are too simple (admin, user ...)");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo T_("Password");?></label></td>
					<td><input type="password" class="input" name="pass" id="pass" /></td>
					<td><em><?php echo T_("Very important for the safety of the site. Use lowercase, uppercase and digit.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo T_("Password");?></label></td>
					<td><input type="password" class="input" name="pass1" id="pass1" /></td>
					<td><em><?php echo T_("Check. Re-enter the password.");?></em></td>
				</tr>
			</table>
			<div class="bouton fr" id="boutonPass" onClick="f_sauve_pass();" title="<?php echo T_("Save password");?>"><?php echo T_("Save");?></div>
			<div class="clear"></div>
		</div>
		<div class="blocForm">
			<div class="bouton fr" id="checkUpdate" onClick="f_checkUpdate();"><?php echo T_("Check for update"); ?></div>
			<h2><?php echo T_("Update");?></h2>
			<div id="updateDiv"></div>
		</div>
	</div><!-- .container -->
	<div id="plugins" class="container" style="display:none;">
		<div class="blocBouton">
			<div id="plugOnOff" class="onoff" onClick="f_plugAll(this);"></div>
			<div class="bouton finder fr" id="boutonFinder1" onClick="f_elfinder(1)" title="<?php echo T_("File manager");?>"><img src="<?php echo $Udep; ?>includes/img/finder.png" /></div>
			<div class="bouton managePlug fr" id="boutonManagePlug" onClick="f_managePlug(0)" title="<?php echo T_("Add or remove plugins");?>"><img src="<?php echo $Udep; ?>includes/img/plugin.png" /></div>
			<div class="bouton fr" onClick="f_publier();" title="<?php echo T_("Publish on the web all saved chapters");?>"><?php echo T_("Publish");?></div>
			<div id="listPlugins"></div>
		</div>
		<div id="finder1"></div>
		<div id="actiBarPlugin" class="blocBouton">
			<div id="prePlugin" style="display:none;">
				<h1 id="nomPlug"></h1>
				<div>
					<input type="checkbox" class="input" onchange="f_onPlug(this)" id="onPlug" /><label></label>
				</div>
			</div>
		</div>
		<div id="plugin"></div>
		<div id="managePlug" style="display:none;"></div>
	</div><!-- .container -->
	
<script type="text/javascript">
var Up=0,Udg=0,Usty=0,Uini=0,Utem=false,Uplug='',Uplugon=0,Unox='<?php echo $unox; ?>',Udep='<?php echo $Udep; ?>',Upt=[],Upd=[],Uplugact=[],Upluglist=[],UconfigFile=[],Ulang='<?php echo $lang; ?>',UconfigNum=0,Ubusy='',Uch=0;
function f_init(){
	let x=new FormData();
	x.set('action','init');
	x.set('unox',Unox);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.json())
	.then(function(r){
		let k,j;
		Ubusy=r['nom'];
		Usty=r['sty'];
		Utem=r['tem'];
		if(Usty==0||!Utem)CKEDITOR.replace('content');
		else CKEDITOR.replace('content',{contentsCss:['uno/template/'+Utem+'/style.css','uno/template/'+Utem+'/styles.css','uno/template/'+Utem+'/css/style.css','uno/template/'+Utem+'/css/style.css']});
	//	CKEDITOR.on('instanceReady',function(e){document.getElementById('wait').style.display='none';});
		if(r['pl'])for(k in r['pl']){
			Uplugact[k]=r['pl'][k];
		}
		if(r['ck'])for(k in r['ck']){
			UconfigFile[k]=r['ck'][k];
		}
		if(r['plugins'])f_load_plugin_hook(r['plugins']);
		else document.getElementById('actiBarPlugin').style.display=(Upluglist.length==0?'none':'block');
		let b=document.createElement('ul');
		b.id='menuSort';
		b.className='ui-sortable';
		for(k in r['chap']){
			Upt[k]=r['chap'][k]['t'];
			Upd[k]=r['chap'][k]['d'];
			let c=document.createElement('li');
			if(k==Up)c.className='bouton current off';
			else{
				c.className='bouton unsort';
				function f_clic(k,c){
					c.onclick=function(){
						if(Uch==1){
							f_alert("!<?php echo T_("do not save ?");?>");
							Uch=0;
						}
						else{
							Up=k;
							f_get_site(1);
						}
					}
				}
				f_clic(k,c);
			}
			if(r['chap'][k]['od']==1)c.className+=' chapoff';
			c.innerHTML=r['chap'][k]['t'].replace(/\\/,"");
			b.appendChild(c);
		}
		let x=new FormData();
		x.set('action','getChap');
		x.set('unox',Unox);
		x.set('data',Upd[Up]);
		fetch('uno/central.php',{method:'post',body:x})
		.then(r=>r.text())
		.then(function(s){
			if(s.length<3)s+='-';
			let a=s.substr(0,1);
		//	document.getElementById('wait').style.display='block';
			CKEDITOR.instances['content'].setData(s.substr(1),function(){document.getElementById('wait').style.display='none';});
			CKEDITOR.instances['content'].on('change',function(){
				if(Udg==4){
					document.getElementById('boutonSauv').className='bouton danger';
					Udg=5;
					Uch=1;
				}
				else if(Udg<4)Udg++;
			});
			document.getElementById('aplugin').style.display='inline';
			document.getElementById('titreChap').onkeypress=function(){
				document.getElementById('boutonSauv').className='bouton danger';
				Udg=1;
				Uch=1;
			}
			document.getElementById('aconfig').style.display='inline';
			document.getElementById('menu').appendChild(b);
			document.getElementById('optOnOff').className='onoff';
			if(r.pub)document.getElementById('boutonPub').style.display='inline';
			if(r.edw)document.getElementById('contentP').style.width=r.edw+'px';
			document.getElementById('titreChap').value=Upt[Up].replace(/\\/,'');
			document.getElementById('optTit').checked=((a==1||a==3||a==5||a==7)?true:false);
			document.getElementById('optMenu').checked=((a==2||a==3||a==6||a==7)?true:false);
			document.getElementById('optDisp').checked=((a==4||a==5||a==6||a==7)?true:false);
			document.getElementById('chapOpt').style.display='none';
			Uini=1;
			Udg=0;
			document.getElementById('boutonSauv').className='bouton';
			f_menuSort()
			if(r.nom)document.getElementById('avoir').href=r.nom+'.html';
		});
	});
}
function f_menuSort(){
	let ul=document.getElementById('menuSort'),li=Array.from(ul.children),id,io,v;
	const dragOn=(e)=>id=li.indexOf(e.target);
	const dragEnd=(e)=>margin(-1);
	const dragOver=(e)=>{
		io=li.indexOf(e.target);
		margin(io);
		e.preventDefault();
	}
	const sauvePlace=(e)=>{
		margin(-1);
		let x=new FormData();
		x.set('action','sauvePlace');
		x.set('unox',Unox);
		x.set('chap',Up);
		x.set('place',io);
		fetch('uno/central.php',{method:'post',body:x})
		.then(r=>r.text())
		.then(function(r){
			f_alert(r);
			Up=io;
			f_get_site(0);
		});
	}
	const margin=(i)=>{
		for(v=0;v<li.length;v++){
			if(v==i)li[v].style=(i>id?"margin-right":"margin-left")+":20px";
			else li[v].removeAttribute('style');
		}
	}
	for(v=0;v<li.length;v++){
		c=li[v];
		if(c.tagName!='LI')continue;
		if(c.className.indexOf('bouton current off')!=-1){
			c.draggable=true;
			c.addEventListener('drag',dragOn);
			c.addEventListener('dragend',dragEnd);
		}
		else{
			c.addEventListener('dragover',dragOver)
			c.addEventListener('drop',sauvePlace) 
		}
	}
}
function f_load_plugin_hook(f){
	if(window.jQuery){
		for(k in f){
			Upluglist[k]=f[k];
			if(f[k].substr(0,1)=='2'){
				j=document.createElement('script');
				j.type='text/javascript';
				j.src='uno/plugins/'+f[k].substr(1)+'/'+f[k].substr(1)+'Hook.js';
				document.body.appendChild(j);
			}
		}
		document.getElementById('actiBarPlugin').style.display=(Upluglist.length==0?'none':'block');
	}
	else setTimeout(function(){f_load_plugin_hook(f)},50);
}
function f_get_site(f){
	let x=new FormData();
	x.set('action','getSite');
	x.set('unox',Unox);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.json())
	.then(function(r){
		var a=document.getElementById('menu'),b;
		Ubusy=r.nom;
		Usty=r.sty;
		Utem=r.tem;
		if(Up!=-1){
			document.getElementById('menu').replaceChildren();
			b=document.createElement('ul');
			b.id='menuSort';
			b.className='ui-sortable';
			for(let k in r.chap)(function(k){ 
				let v=r.chap[k];
				Upt[k]=v.t;
				Upd[k]=v.d;
				var c=document.createElement('li');
				if(k==Up)c.className='bouton current off';
				else{
					c.className='bouton unsort';
					c.onclick=function(){
						if(Uch==1){
							f_alert("!<?php echo T_("do not save ?");?>");
							Uch=0;
						}
						else{
							Up=k;
							f_get_site(1);
						}
					};
				}
				if(v.od==1)c.className+=' chapoff';
				c.innerHTML=v.t.replace(/\\/,"");
				b.appendChild(c);
			})(k);
			a.appendChild(b);
			f_menuSort();
			document.getElementById('titreChap').value=Upt[Up].replace(/\\/,'');
			document.getElementById('optOnOff').className='onoff';
			if(r.pub)document.getElementById('boutonPub').style.display='inline';
			if(r.edw)document.getElementById('contentP').style.width=r.edw+'px';
			if(f!=0)f_get_chap(Up);
		}else{
			document.getElementById('tit').value=r.tit.replace(/\\/, '')||'';
			document.getElementById('desc').value=r.desc.replace(/\\/, '')||'';
			document.getElementById('nom').value=r.nom||'';
			let h=window.location.href,i=h.indexOf('/uno.php');
			h=(i!=-1?h.substr(0,i):'');
			document.getElementById('url').value=r.url||h;
			let t=document.getElementById('tem'),to=t.options,v;
			for(v=0;v<to.length;v++){
				if(to[v].value==r.tem){
					to[v].selected=true;v=to.length;
				}
			};
			document.getElementById('edw').value=r.edw||'';
			document.getElementById('ofs').value=r.ofs||'';
			document.getElementById('lazy').checked=(r.lazy==1?true:false);
			document.getElementById('jq').checked=(r.jq==1?true:false);
			document.getElementById('w3').checked=(r.w3==1?true:false);
			document.getElementById('sty').checked=(r.sty==1?true:false);
			document.getElementById('mel').value=r.mel||'';
		}
		if(r.nom)document.getElementById('avoir').href=r.nom+'.html';
	});
}
function f_get_chap(f){
	Up=f;
	let x=new FormData();
	x.set('action','getChap');
	x.set('unox',Unox);
	x.set('data',Upd[Up]);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		if(r.length<3)r+='-';
		CKEDITOR.instances['content'].setData(r.substr(1));
		let a=r.substr(0,1);
		document.getElementById('optTit').checked=((a==1||a==3||a==5||a==7)?true:false);
		document.getElementById('optMenu').checked=((a==2||a==3||a==6||a==7)?true:false);
		document.getElementById('optDisp').checked=((a==4||a==5||a==6||a==7)?true:false);
		document.getElementById('chapOpt').style.display='none';
		Uini=1;
		Udg=0;
		document.getElementById('wait').style.display='none';
		document.getElementById('boutonSauv').className='bouton';
	});
}
function f_sauve_chap(){
	let x=new FormData();
	x.set('action','sauveChap');
	x.set('unox',Unox);
	x.set('chap',Up);
	x.set('data',Upd[Up]);
	x.set('content',CKEDITOR.instances['content'].getData());
	x.set('titre',document.getElementsByName('titre')[0].value);
	x.set('otit',document.getElementById('optTit').checked);
	x.set('omenu',document.getElementById('optMenu').checked);
	x.set('odisp',document.getElementById('optDisp').checked);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_alert(r);
		f_get_site(0);
		Udg=0;
		document.getElementById('boutonSauv').className='bouton';
		Uch=0;
	});
}
function f_sauve_config(){
	let nom=document.getElementById('nom').value,x=new FormData();
	x.set('action','sauveConfig');
	x.set('unox',Unox);
	x.set('tit',document.getElementById('tit').value);
	x.set('desc',document.getElementById('desc').value);
	x.set('nom',nom);
	x.set('mel',document.getElementById('mel').value);
	x.set('tem',document.getElementById('tem').options[document.getElementById('tem').selectedIndex].value);
	x.set('url',document.getElementById('url').value);
	x.set('lazy',document.getElementById('lazy').checked);
	x.set('jq',document.getElementById('jq').checked);
	x.set('w3',document.getElementById('w3').checked);
	x.set('sty',document.getElementById('sty').checked);
	x.set('edw',document.getElementById('edw').value);
	x.set('ofs',document.getElementById('ofs').value);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_alert(r);
		if(nom.length>0)document.getElementById('avoir').href=nom+'.html';
	});
}
function f_sauve_pass(){
	if(document.getElementById('user0').value.length>0||document.getElementById('pass0').value.length>0||document.getElementById('user').value.length>0||document.getElementById('pass').value.length>0){
		if(document.getElementById('user0').value.length<1||document.getElementById('pass0').value.length<1){
			f_alert('!<?php echo T_("Current elements are missing");?>');
			return;
		}
		if(document.getElementById('user').value.length<4){
			f_alert('!<?php echo T_("Too short name");?>');
			return;
		}
		if(document.getElementById('pass').value!=document.getElementById('pass1').value){
			f_alert('!<?php echo T_("Passwords are different");?>');
			return;
		}
		if(document.getElementById('pass').value.length<6){
			f_alert('!<?php echo T_("Too short password");?>');
			return;
		}
	}
	let x=new FormData();
	x.set('action','sauvePass');
	x.set('unox',Unox);
	x.set('user0',document.getElementById('user0').value);
	x.set('pass0',document.getElementById('pass0').value);
	x.set('user',document.getElementById('user').value);
	x.set('pass',document.getElementById('pass').value);
	x.set('lang',document.getElementById('lang').options[document.getElementById('lang').selectedIndex].value);
	x.set('gt',document.getElementById('gt').checked);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_alert(r);
		if(r.substr(0,1)!="!")setTimeout(function(){
			location.reload();
		},1000);
	});
}
function f_nouv_chap(){
	let x=new FormData();
	x.set('action','nouvChap');
	x.set('unox',Unox);
	x.set('chap',Up);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		Up++;
		f_alert(r);
		f_get_site(1);
	});
}
function f_supp_chap(){
	if(confirm("<?php echo T_("Delete Chapter"); ?> ?")){
		let x=new FormData();
		x.set('action','suppChap');
		x.set('unox',Unox);
		x.set('chap',Up);
		fetch('uno/central.php',{method:'post',body:x})
		.then(r=>r.text())
		.then(function(r){
			if(Up>0)Up--;
			else Up=0;
			f_alert(r);
			f_get_site(1);
		});
	}
}
function f_publier(){
	document.getElementById('wait').style.display='block';
	let x=new FormData();
	x.set('action','publier');
	x.set('unox',Unox);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		document.getElementById('boutonPub').style.display='none';
		f_alert(r);
	});
}
function f_suppPub(){
	let x=new FormData();
	x.set('action','suppPub');
	x.set('unox',Unox);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(r=>f_alert(r));
}
function f_archivage(){
	document.getElementById('wait').style.display='block';
	let x=new FormData();
	x.set('action','archivage');
	x.set('unox',Unox);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_selectArchive();
		f_alert(r);
	});
}
function f_restaure(f){
	document.getElementById('wait').style.display='block';
	let x=new FormData();
	x.set('action','restaure');
	x.set('unox',Unox);
	x.set('zip',f);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(r=>f_alert(r));
}
function f_archDel(f){
	document.getElementById('wait').style.display='block';
	let x=new FormData();
	x.set('action','archDel');
	x.set('unox',Unox);
	x.set('zip',f);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		let t=document.getElementById('archive'),to=t.options,v;
		for(v=0;v<to.length;v++){
			if(to[v].value==f){
				t.removeChild(to[v]);
			}
		}
		f_alert(r);
	});
}
function f_archDownload(f){
	document.getElementById('wait').style.display='block';
	let x=new FormData();
	x.set('action','archDownload');
	x.set('unox',Unox);
	x.set('zip',f);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		document.getElementById('wait').style.display='none';
		if(r.substr(0,1)=='!')f_alert(r);
		else window.location=r;
	});
}
function f_fileDownload(){
	document.getElementById('wait').style.display='block';
	let x=new FormData();
	x.set('action','filesDownload');
	x.set('unox',Unox);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		document.getElementById('wait').style.display='none';
		if(r.substr(0,1)=='!')f_alert(r);
		else window.location=r;
	});
}
function f_selectArchive(){
	let x=new FormData();
	x.set('action','selectArchive');
	x.set('unox',Unox);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		if(r){
			document.getElementById('boutonRestaure').style.display='inline';
			document.getElementById('blocArchive').innerHTML=r;
		}
		else{
			document.getElementById('boutonRestaure').style.display='none';
			document.getElementById('blocArchive').innerHTML=''
		}
	});
}
function f_logout(){
	var a=document.getElementById('info'),b=document.createElement('form'),c=document.createElement('input');
	b.method='POST';
	b.action='';
	c.name='logout';
	c.type='hidden';
	c.value=1;
	b.appendChild(c);
	a.appendChild(b);
	b.submit();
}
function f_alert(f){
	if(f.search('<br />')!=-1){
		alert(f);
		let x=new FormData();
		x.set('action','error');
		x.set('unox',Unox);
		x.set('e',f);
		fetch('uno/central.php',{method:'post',body:x});
	}
	else{
		let a=document.getElementById('info'),b=document.createElement('span'),t,i;
		b.id='alert';
		if(f.substr(0,1)=='!'){
			b.style.color='red';
			f=f.substr(1);
		}
		b.innerHTML=f;
		a.appendChild(b);
		setTimeout(function(){
			document.getElementById('info').innerHTML='';
		},2000);
	}
	document.getElementById('wait').style.display='none';
}
function f_config(){
	document.getElementById('wait').style.display='block';
	document.getElementById('plugins').style.display='none';
	document.getElementById('apage').style.textDecoration='none';
	document.getElementById('aplugin').style.textDecoration='none';
	document.getElementById('aconfig').style.textDecoration='underline';
	Up=-1;
	f_get_site(0);
	document.getElementById('chaps').style.display='none';
	document.getElementById('config').style.display='block';
	f_selectArchive();
	document.getElementById('wait').style.display='none';
}
function f_chapOption(f){
	var a=document.getElementById('chapOpt'),b;
	if(a.style.display=='none'){
		b='block';
		f.className='onoff all';
	}
	else{
		b='none';
		f.className='onoff';
	}
	a.style.display=b;
	window.scrollTo(0,document.body.scrollHeight);
}
function f_archOption(f){
	var a=document.getElementById('archOpt'),b;
	if(a.style.display=='none'){
		b='block';
		f.className='onoff all';
	}
	else{
		b='none';
		f.className='onoff';
	}
	a.style.display=b;
}
function f_plugins(){
	Up=-1;
	var a=document.getElementById('listPlugins');
	document.getElementById('config').style.display='none';
	document.getElementById('chaps').style.display='none';
	document.getElementById('plugins').style.display='block';
	document.getElementById('prePlugin').style.display='block';
	f_managePlug(2);
	a.innerHTML='';
	for(let k in Upluglist)(function(k){
		let v=Upluglist[k];
		var b=document.createElement('span');
		b.className='bouton';
		b.id='p'+v.substr(1);
		b.onclick=function(){
			f_plugin(v);
		};
		if(v=='9_')b.innerHTML="<?php echo T_("My theme");?>";
		else b.innerHTML=v.charAt(1).toUpperCase()+v.substr(2);
		if(k==0){
			b.className='bouton current off';
			Uplugon=v.substr(0,1);
		}
		a.appendChild(b);
	})(k);
}
function f_plugin(f){
	document.getElementById('wait').style.display='block';
	var a=document.getElementById('listPlugins'),d,v,c;
	if(f==0){
		f_plugins();
		document.getElementById('apage').style.textDecoration='none';
		document.getElementById('aplugin').style.textDecoration='underline';
		document.getElementById('aconfig').style.textDecoration='none';
		jQuery('#finderDiv').elfinder('close');
		document.getElementById('boutonFinder1').className='bouton finder fr';
		if(Uplugact[0]&&Uplugact[0]!='_')f='1'+Uplugact[0];
		else if(Uplugact[0]=='_')f='9_';
		else f=Uplugon+a.firstChild.id.substr(1);
	}
	d=a.childNodes;
	for(v=0;v<d.length;v++)d[v].className=((d[v].id=='p'+f.substr(1))?'bouton current off':'bouton');
	d=document.getElementById('onPlug');
	d.name=f.substr(1);
	d.style.display='inline-block';
	c=f.substr(0,1);
	if(c=='1'||c=='2'){
		d.checked=true;
		d.nextSibling.innerHTML="<?php echo T_("Enable");?>";
		d.nextSibling.style.color='green';
	}
	else if(c=='9'){
		d.checked=true;
		d.style.display='none';
		d.nextSibling.innerHTML='';
	}
	else{
		d.checked=false;
		d.nextSibling.innerHTML="<?php echo T_("Disable");?>";
		d.nextSibling.style.color='#f79f81';
	}
	document.getElementById('nomPlug').innerHTML=(c=='9'?'<?php echo T_("My theme");?> : '+Utem:'Plugin : '+f.substr(1));
	document.getElementById('plugin').innerHTML='';
	let x=new FormData(),urp='uno/plugins/'+f.substr(1)+'/'+f.substr(1)+'.php',urj='uno/plugins/'+f.substr(1)+'/'+f.substr(1)+'.js';
	if(c==9){
		urp='uno/template/'+Utem+'/'+Utem+'.php';
		urj='uno/template/'+Utem+'/'+Utem+'.js';
	}
	x.set('action','plugin');
	x.set('unox',Unox);
	x.set('udep',Udep);
	x.set('ubusy',Ubusy);
	fetch(urp,{method:'post',body:x,headers:{'X-Requested-With':'XMLHttpRequest'}})
	.then(r=>r.text())
	.then(function(r){
		document.getElementById('plugin').innerHTML='';
		document.getElementById('plugin').insertAdjacentHTML('beforeend',r);
		fetch(urj)
		.then(r=>r.text())
		.then(t=>{
			let i='sc'+(c==9?Utem:f.substr(1)),js;
			if(document.getElementById(i)!=null)document.getElementById(i).remove();
			js=document.createElement('script');
			js.id=i;
			js.textContent=t;
			document.head.appendChild(js);
		})
		.then(document.getElementById('wait').style.display='none');
	});
}
function f_onPlug(f){
	if(f.checked){
		f.nextSibling.innerHTML="<?php echo T_("Enable");?>";
		f.nextSibling.style.color='green';
	}
	else{
		f.nextSibling.innerHTML="<?php echo T_("Disable");?>";
		f.nextSibling.style.color='#f79f81';
	}
	let x=new FormData();
	x.set('action','onPlug');
	x.set('unox',Unox);
	x.set('n',f.name);
	x.set('c',f.checked);
	fetch('uno/central.php',{method:'post',body:x})
	.then(function(r){
		f_plugin_hook();
	});
	var t=((f.checked)?'1':'0')+f.name;
	document.getElementById('p'+f.name).onclick=function(){
		f_plugin(t);
	};
	if(document.getElementById('plugOnOff').className.search('all')==-1)document.getElementById('p'+f.name).style.display=((f.checked)?'inline-block':'none');
}
function f_plugin_hook(){
	let x=new FormData();
	x.set('action','pluginsActifs');
	x.set('unox',Unox);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.json())
	.then(function(r){
		if(r.pl)for(let k in r.pl)Uplugact[k]=r.pl[k];
		if(r.ck)for(let k in r.ck)UconfigFile[k]=r.ck[k];
	});
}
function f_plugAll(f,g){
	let b=0,v,s;
	if((f.className.search('all')!=-1)||g){
		f.className='onoff';
		s=document.querySelectorAll('#listPlugins>span');
		for(v=0;v<s.length;v++)s[v].style.display='none';
		for(v=0;v<Uplugact.length;v++){
			if(Uplugact[v])document.getElementById('p'+Uplugact[v]).style.display='inline-block';
			b=1;
		};
	}
	if(b==0){
		f.className='onoff all';
		s=document.querySelectorAll('#listPlugins>span');
		for(v=0;v<s.length;v++)s[v].style.display='inline-block';
	}
}
function f_ctit(f){
	var a=document.getElementById('ctit');
	a.style.color=(f.value.length>60?'red':'green');
	a.innerHTML=f.value.length;
}
function f_cdesc(f){
	var a=document.getElementById('cdesc');
	a.style.color=(f.value.length>160?'red':'green');
	a.innerHTML=f.value.length;
}
function f_elfinder(f){
	var a=document.getElementById('finderDiv');
	if(f==1)jQuery('#finderDiv').appendTo(jQuery('#finder1'));
	if(a.style.display=='none'){
		jQuery('#finderDiv').elfinder('open');
		document.getElementById('boutonFinder'+f).className='bouton finder fr current';
		return
	};
	jQuery('#finderDiv').elfinder('close');
	document.getElementById('boutonFinder'+f).className='bouton finder fr';
}
function f_finder_select(f){
	jQuery('<div \>').dialog({modal:true,width:'940px',title:"<?php echo T_("Select a file");?>",zIndex: 9999,create:function(e,u){
		jQuery(this).elfinder({
			resizable:false,
			url:'uno/includes/elfinder/php/connector.php',
			useBrowserHistory:false,
			commandsOptions:{getfile:{oncomplete:'destroy'}},
			getFileCallback:function(file){
				document.getElementById(f).value=file.url;
				jQuery('button.ui-dialog-titlebar-close').click();
			}
		}).elfinder('instance')
	}});
}
function f_managePlug(f){
	var a=document.getElementById('managePlug'),b=document.getElementById('prePlugin'),c=document.getElementById('plugin');
	if(f!=2&&(a.style.display=='none'||f==1)){
		let x=new FormData();
		x.set('action','pluglist');
		x.set('unox',Unox);
		fetch('uno/central.php',{method:'post',body:x})
		.then(r=>r.text())
		.then(function(r){
			if(r.substr(0,1)!='!')a.innerHTML=r;
			else f_alert(r);
		});
		a.style.display='block';
		b.style.display='none';
		c.style.display='none';
		document.getElementById('boutonManagePlug').className='bouton managePlug fr current';
		return
	};
	a.style.display='none';
	b.style.display='block';
	c.style.display='block';
	document.getElementById('boutonManagePlug').className='bouton managePlug fr';
}
function f_plugAdd(f){
	document.getElementById('wait').style.display='block';
	let x=new FormData();
	x.set('action','plugadd');
	x.set('unox',Unox);
	x.set('plug',f);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_alert(r);
		setTimeout(function(){
			f_managePlug(1);
		},1000);
	});
}
function f_plugDel(f){
	if(confirm("<?php echo T_("Remove"); ?> "+f+" ?")){
		let x=new FormData();
		x.set('action','plugdel');
		x.set('unox',Unox);
		x.set('plug',f);
		fetch('uno/central.php',{method:'post',body:x})
		.then(r=>r.text())
		.then(function(r){
			f_alert(r);
			setTimeout(function(){
				f_managePlug(1);
			},1000);
		});
	}
}
function f_nombre(e){
	var c=(e.which)?e.which:event.keyCode;
	if(c>31&&(c<48||c>57))return false;
	return true;
}
function f_checkUpdate(){
	document.getElementById('wait').style.display='block';
	document.getElementById('checkUpdate').style.display='none';
	let a=document.getElementById('updateDiv'),b=0,c=document.createElement('table'),d='',x=new FormData();
	x.set('action','checkUpdate');
	x.set('unox',Unox);
	x.set('u',0);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		r=r.split('|');
		if(r[1]=='1')d+='<div id="lighter" style="float:right"><?php echo T_("Remove what is available online ? (recommended)"); ?> <span class="bouton" onClick="f_lighter();"><?php echo T_("Lighten"); ?></span></div>';
		d+='<table><tr><td>CMSUno</td>';
		if(r[0]=='0')d+='<td><?php echo T_("Up to date"); ?></td>';
		else d+='<td><span class="bouton danger" onClick="f_update(0);"><?php echo T_("Update"); ?> '+r[2]+'</span></td></tr>';
		d+='<tr><td colspan="2"><div class="bouton" style="text-transform:none" id="backUpdate" onClick="f_update(\'##<?php echo $previousVersion; ?>\');"><?php echo T_("Back to previous version"); ?></div></td></tr>';
		d+='</table>';
		d+='<hr />';
		a.innerHTML=d;
		a.appendChild(c);
		window.scrollTo(0,document.body.scrollHeight);
		for(let k in Upluglist)(function(k){
			let v=Upluglist[k];
			if(v.substr(0,1)!='9'){
				++b;
				let x=new FormData();
				x.set('action','checkUpdate');
				x.set('unox',Unox);
				x.set('u',v.substr(1));
				fetch('uno/central.php',{method:'post',body:x})
				.then(r=>r.text())
				.then(function(r){
					if(r.search('|')!=-1){
						r=r.split('|');
					//	if(r[0]!='1')c.innerHTML+='<tr><td>'+v.substr(1)+' : '+r[1]+'</td><td><?php echo T_("Up to date"); ?></td></tr>';
						if(r[0]!='1')c.insertAdjacentHTML('beforeend','<tr><td>'+v.substr(1)+' : '+r[1]+'</td><td><?php echo T_("Up to date"); ?></td></tr>');
					//	else c.innerHTML+='<tr id="T'+v.substr(1)+'"><td>'+v.substr(1)+' : '+r[1]+'</td><td><span class="bouton danger" onClick="f_update(\''+v.substr(1)+'\');"><?php echo T_("Update"); ?> '+r[2]+'</span></td></tr>';
						else c.insertAdjacentHTML('beforeend','<tr id="T'+v.substr(1)+'"><td>'+v.substr(1)+' : '+r[1]+'</td><td><span class="bouton danger" onClick="f_update(\''+v.substr(1)+'\');"><?php echo T_("Update"); ?> '+r[2]+'</span></td></tr>');
						window.scrollTo(0,document.body.scrollHeight);
					}
					--b;
					if(b==0)document.getElementById('wait').style.display='none';
				});
			}
		})(k);
		if(b==0)document.getElementById('wait').style.display='none';
	});
}
function f_update(f){
	document.getElementById('wait').style.display='block';
	let x=new FormData();
	x.set('action','update');
	x.set('unox',Unox);
	x.set('u',f);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		if(r.search('|')!=-1){
			r=r.split('|');
			f_alert(r[0]);
			document.getElementById('wait').style.display='none';
			if(f!=0&&f.substr(0,2)!='##')document.getElementById('T'+f).innerHTML='<td>'+f+' : '+r[1]+'</td><td><div><?php echo T_("Up to date"); ?></div></td>';
			else location.reload(true);
		}
	});
}
function f_lighter(){
	let x=new FormData();
	x.set('action','lighter');
	x.set('unox',Unox);
	fetch('uno/central.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		document.getElementById('lighter').replaceChildren();
		f_alert(r);
	});
}
function colorPick(f){ // Pure JS colorPicker
	if(f.length>0){let a=document.querySelectorAll(f),i;
		for(i=0;i<a.length;i++){
			(function(i){
				a[i].addEventListener('click',function(){
					if(a[i].nextElementSibling.classList.contains('cp-small')==false){
						let c=document.createElement('div');c.className='cp cp-small';a[i].insertAdjacentElement('afterend',c);
						ColorPicker(a[i].nextElementSibling,function(hex,hsv,rgb){a[i].style.background=hex;a[i].value=hex;});
					}else a[i].nextElementSibling.remove();
				});
				if(a[i].value.substr(0,1)=='#')a[i].style.background=a[i].value;
			})(i);
		}
	} // emulate old jqColorPicker.min.js
	else{(function(jQuery){jQuery.fn.colorPicker=function(){
			jQuery(this).each(function(i){let b = jQuery(this)[0].value;if(b.substr(0,1)=='#'||b.substr(0,3)=='rgb')jQuery(this).css('background',b);});
			jQuery(this).on('click',function(){
				let a=jQuery(this)[0];
				if(a.nextElementSibling.classList.contains('cp-small')==false){
					let c=document.createElement('div');c.className='cp cp-small';a.insertAdjacentElement('afterend',c);
					ColorPicker(a.nextElementSibling,function(hex,hsv,rgb){a.style.background=hex;a.value=hex;});
				}else a.nextElementSibling.remove();
			});
		}})(jQuery);
	}
}
//
f_init();
</script>
<style>.picker-wrapper,.slide-wrapper{position:relative;float:left;}.picker-indicator,.slide-indicator{position:absolute;left:0;top:0;pointer-events:none;}.picker,.slide{cursor:crosshair;float:left;}.cp-small{padding:5px;background-color:white;float:left;border-radius:5px;}.cp-small .picker{width:100px;height:100px;}.cp-small .slide{width:15px;height:100px;}.cp-small .slide-wrapper{margin-left:5px;}.cp-small .picker-indicator{width:1px;height:1px;border:1px solid black;background-color:white;}.cp-small .slide-indicator{width:100%;height:2px;left:0px;background-color:black;}</style>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $Udep; ?>includes/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" media="screen" href="uno/includes/elfinder/css/elfinder.min.css" />
<script type="text/javascript" src="<?php echo ($Udep=='uno/'?'uno/includes/js/jquery.min.js':'//ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ($Udep=='uno/'?'uno/includes/js/jquery-ui.min.js':'//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js'); ?>"></script>
<script type="text/javascript" src="uno/includes/elfinder/js/elfinder.min.js"></script>
<?php if($lang!='en' && $lang!='') echo '<script type="text/javascript" src="uno/includes/elfinder/js/i18n/elfinder.'.$lang.'.js"></script>'; ?>
<script type="text/javascript" src="<?php echo $Udep; ?>includes/js/colorpicker.min.js"></script>
<script type="text/javascript">
window.scrollTo(0,0);
jQuery(document).ready(function(){
	colorPick('');
	jQuery('#finderDiv').elfinder({
		lang:'<?php echo $lang;?>',
		url:'uno/includes/elfinder/php/connector.php',
		useBrowserHistory:false,
		cssAutoLoad:false
	}).elfinder('instance');
	jQuery('#finderDiv').elfinder('close').css('zIndex',99);
});
</script>
</body>
</html>
