CMSUno
======

An easy and clever content manager system to create one-page websites

<pre>
 uuuu      uuuu        nnnnnn           ooooooooo
u::::u    u::::u    nn::::::::nn     oo:::::::::::oo
u::::u    u::::u   nn::::::::::nn   o:::::::::::::::o
u::::u    u::::u  n::::::::::::::n  o:::::ooooo:::::o
u::::u    u::::u  n:::::nnnn:::::n  o::::o     o::::o
u::::u    u::::u  n::::n    n::::n  o::::o     o::::o
u::::u    u::::u  n::::n    n::::n  o::::o     o::::o
u:::::uuuu:::::u  n::::n    n::::n  o::::o     o::::o
u::::::::::::::u  n::::n    n::::n  o:::::ooooo:::::o
 u::::::::::::u   n::::n    n::::n  o:::::::::::::::o
  uu::::::::uu    n::::n    n::::n   oo:::::::::::oo
     uuuuuu        nnnn      nnnn       ooooooooo
        ___                                __
       / __\            /\/\              / _\
      / /              /    \             \ \
     / /___           / /\/\ \            _\ \
     \____/           \/    \/            \__/
</pre>

Presentation
------------

CMSUno is a free tool to create one-page responsive websites.
It was designed to be easy to use, comprehensive and particularly rapid in terms of navigation.
This strong CMS is a great tool to use jQuery plugins available on the web.

In a few words:

* No page creation in PHP. The site consists of an HTML page that is created when the redactor has finished its work.
The server has nothing to build, the page displays faster than any other CMS.
* No SQL. Data stored in JSON. Easier to install, faster to use, very suitable for Ajax transfer.
* Use of effective tools, tested and monitored as [CKEditor](http://ckeditor.com/) and [ELFinder](https://github.com/Studio-42/elFinder).
* Development of plugins easy and effective.
* Adaptation of open source CSS template fast and easy.
* Multilingual with Gettext.
* Less than 1MB. Centralized hosting of part of the code on Google servers.

More details in French [here](http://www.boiteasite.fr/fiches/cmsuno.html).

Installation
------------

1. Download ZIP CMSUno.
2. Unzip the file.
3. Upload the content (uno.php and uno/) to your website directory via FTP.
4. Chmod 0755 recursively the uno folder.
5. In your browser, open www.yoursite/uno.php.

Initial login & password : jack & 654321

Use
---

Connect to the dashboard.
There are only three tabs :

### Page ###

This first Tab is the main working tab. It is used to create some contents for the site.
It relies on a large window with CKEditor in a recent version.
At the top, a menu allows to pass from a chapter to another other one.
You can create as many chapters as you like.
A button allows access to the powerful and easy file manager ELFinder.

When your changes are complete, press the "publish" button to update the site.

### Config ###

Configuration is limited to the minimum useful.

* Title and Meta-Description
* Dynamic loading of images, JQuery...
* Backup, Restore
* Change Login & Password

### Plugins ###

Plugins can substantially improve the capabilities of CMSUno.
Plugin can especially add extra buttons to CKEditor and process the results before publication. In use, it's fantastic.

Plugins are available [here](https://github.com/boiteasite/cmsuno/tree/plugins)

Template Tags
-------------

* [[content]] : page content
* [[description]] : meta description
* [[head]] : head content (script, css link...)
* [[foot]] : foot content (script...)
* [[menu]] : menu
* [[name]] : page file name without .html
* [[template]] : template url
* [[title]] : page title
* [[url]] : base site url

Plugin development
------------------

Create a plugin is quite simple and fast. 
A basic structure is imposed with some mandatory files.
Your plugin should be in uno/plugins/foo/ (with a plugin called __foo__).

### foo.php ###

This file is __required__.

This file is called in AJAX.
It is used to display the PLUGIN tab in Dashboard. It's done with `$_POST["action"] = "plugin"`.

This file should look like this :

```
<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;}
include('../../password.php'); $user=0; $pass=0; // Lang
include('lang/lang.php');
if (isset($_POST['action']))
	{
	switch ($_POST['action'])
		{
		// ***********************
		case 'plugin': ?>
		<div class="blocForm">
			<h2>Foo</h2>
			<p><?php echo _("This plugin is used to...");?></p>
			<!--   CONFIG FORM IF NEEDED... -->
			<!-- ... -->
			?>
		</div>
		<?php break;
		// ***********************
		case 'save': // IF NEEDED FOR SAVING CONFIG IN JSON FILE
		$q = @file_get_contents('../../data/'.$Ubusy.'/foo.json');
		if($q) $a = json_decode($q,true);
		else $a = Array();
		$a['lol'] = $_POST['lol'];
		$a['yeswecan'] = $_POST['yeswecan'];
		$out = json_encode($a);
		if (file_put_contents('../../data/'.$Ubusy.'/foo.json', $out)) echo _('Backup performed');
		else echo '!'._('Impossible backup');
		break;
		// ***********************
		}
	clearstatcache();
	exit;
	}
?>
```
$Ubusy contains the name of the current page. It's because you can create multiple pages with CMSUno

### fooMake.php ###

This file is __not required__.

This file is called with an include statement when the user pushed "publish" in the Dashboard.
The goal is to complete the variables that replace Shortcodes.
If your plugin works with a Shortcode [[foo]] in the content of the page, there must be this :

```
<?php if (!isset($_SESSION['cmsuno'])) exit(); ?>
<?php
	$my_var = "<div>Hello. I'm the plugin</div>";
	$content = str_replace('[[foo]]',$my_var,$content);
?>
```

Variables usable all have almost the same name as the shortcodes. Here is a non-exhaustive list :

* __$html__ (no shortcode) : The template content. Used to replace a shortcode directly into the template.
* __$content__ : The content of the pages. Used to replace a shortcode added in the page with CKEditor.
* __$head__ : At the end of the block `<head>`. Used to add a script or a CSS file.
* __$style__ : CSS content added at the end of the block `<head>` in a `<style>` container.
* __$foot__ : At the end of the block `<body>`. Used to add a script.
* __$menu__ : Page list according to `<ul class="maClasse"><li><a href="#...">MyPage</a></li>...</ul>`.
* __$title__ : Website Title. (Used in template : `<title>[[title]]</title>`).
* __$description__ : Website Description (`<meta name="description" content="[[description]]">`).
* __$name__ : Published HTML file name. By default, it's "index".

### foo.js ###

This file is __not required__.

If it exists, this file is loaded at the same time as foo.php.
It is useful for example to load or save JSON data in AJAX :

```
function f_save_foo(){
	jQuery(document).ready(function(){
		var lol=document.getElementById("fooLol").value;
		var yeswecan=document.getElementById("fooYes").options[document.getElementById("fooYes").selectedIndex].value;
		jQuery.post('uno/plugins/foo/foo.php',{'action':'save','lol':lol,'yeswecan':yeswecan},function(r){
			f_alert(r);
		});
	});
}
function f_load_foo(){
	jQuery(document).ready(function(){
		jQuery.getJSON("uno/data/"+Ubusy+"/foo.json?r="+Math.random(),function(r){
			if(r.lol!=undefined)document.getElementById('fooLol').value=r.lol;
			if(r.yeswecan){
				t=document.getElementById("fooYes");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==r.yeswecan){to[v].selected=true;v=to.length;}}
			}
		});
	});
}
```

### fooCkeditor.js ###

This file is __not required__.

This file is used to customize CKeditor. It's very interesting.

In CKEditor, configuration files work in cascade as Matryoshka doll. Every configuration file calls the following one.
It is thus necessary to respect a specific format to not break the chain. Example with your CKEditor plugin ckfoo :

```
UconfigNum++;
CKEDITOR.plugins.addExternal('ckfoo', UconfigFile[UconfigNum-1]+'/../ckfoo/');
CKEDITOR.editorConfig = function(config)
	{
	config.extraPlugins += ',ckfoo';
	config.toolbarGroups.push('ckfoo');
	if(UconfigFile.length>UconfigNum)config.customConfig=UconfigFile[UconfigNum];   
	};
```

License 
-------

CMSUno is under MIT license.

<pre>
Copyright (c) <2014> <Jacques Malgrange contacter@boiteasite.fr>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
</pre>


Versions
--------

* V0.9.7 beta - 15/01/2015
* V0.9.6 beta - 13/01/2015
* V0.9.5 beta - 10/01/2015
* V0.9.4 beta - 06/01/2015
* V0.9.3 beta - 09/12/2014
* V0.9.2 beta - 29/11/2014
* V0.9.1 beta - 23/11/2014
* CMSUno Version 0.9 beta - 26/10/2014
