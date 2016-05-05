CMSUno
======

An easy and clever content manager system to create one-page responsive websites

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

<p align="center">
==> <a href="http://www.cmsuno.boiteasite.fr"> DEMO </a> <==
</p>

In a few words:

* No page creation in PHP. The site consists of an HTML page that is created when the redactor has finished its work.
The server has nothing to build, the page displays faster than any other CMS.
* No SQL. Data stored in JSON. Easier to install, faster to use, very suitable for Ajax transfer.
* Use of effective tools, tested and monitored as [CKEditor](http://ckeditor.com/) and [ELFinder](https://github.com/Studio-42/elFinder).
* Development of plugins easy and effective.
* Adaptation of open source CSS template fast and easy.
* Multilingual with Gettext.
* Inline Update button for CMSUno and plugins.
* Less than 1MB. Centralized hosting of part of the code on GitHub servers.

More details in French [here](http://www.boiteasite.fr/fiches/cmsuno.html).

Installation
------------

1. Download ZIP CMSUno.
2. Unzip the file.
3. Upload the content (uno.php and uno/) to your website directory via FTP.
4. Chmod 0755 recursively the uno folder.
5. In your browser, open www.yoursite/uno.php.

Initial login & password : cmsuno & 654321

To reduce the size of CMSUno to less than 1 MB and benefit the speed of GitHub's servers, delete the following folders :

* includes/css
* includes/img
* includes/js

or do it with update button.

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
* Update

### Plugins ###

Plugins can substantially improve the capabilities of CMSUno.
Plugin can especially add extra buttons to CKEditor and process the results before publication. In use, it's fantastic.

Plugins are available [here](https://github.com/cmsunoPlugins)

#### Official Plugins List ####

* [__Box__](https://github.com/cmsunoPlugins/box) : Adds easily editable box with pieces of text or code or HTML that can be inserted into the template with a simple Shortcode. Exemple : address for footer, phone number, sidebar...
* [__Carousel__](https://github.com/cmsunoPlugins/carousel) : Allows you to add image slider.
Use [NivoSlider](https://github.com/gilbitron/Nivo-Slider),
[CarouFredSel](https://github.com/gilbitron/carouFredSel),
[Kenburning](https://github.com/simbirsk/kenburning-slider),
[FeatureCarousel](http://www.bkosborne.com/jquery-feature-carousel) and
[ZoomBox](http://grafikart.github.io/Zoombox) to have numerous possibilities.
* [__Code display__](https://github.com/cmsunoPlugins/code_display) : Adds a button in CKEditor to display code. Use [google-code-prettify](https://code.google.com/p/google-code-prettify/).
* [__Comment__](https://github.com/cmsunoPlugins/comment) : It allows visitors to add comments in the page. Added with a Shortcode in the content of the page or directly in the template.
* [__Contact__](https://github.com/cmsunoPlugins/contact) : It allows to create a custom-made contact form with Captcha. Added with a Shortcode in the content of the page or directly in the template.
* [__Fixed layout__](https://github.com/cmsunoPlugins/fixed_layout) : This plugin allows to create a page with a fixed background that changes with scrolling. Inspired by [Jquery Fixed Scroll Background](https://github.com/ebaumstarck/JqueryFixedScrollBackground).
* [__Googlemap__](https://github.com/cmsunoPlugins/googlemap) : Adds a button in CKEditor to insert one or more Google-Map in your page.
* [__Markdown__](https://github.com/cmsunoPlugins/markdown) : Allows you to display the formatted content of one or more MarkDown files in your page. It works with a shortcode and uses Parsedown.php to parse the markdown. Different CSS formats are availables. The wordpress format is also parsed. Allows to create a comprehensive system of paying plugin download with the appearance of wordpress.org.
* [__Multipage__](https://github.com/cmsunoPlugins/multipage) :  This plugin allows you to create and manage multiple pages in CMSUno. Drag and Drop Menu Manager. Simple and practical for a complete website.
* [__Model__](https://github.com/cmsunoPlugins/model) :  Create template for CKEditor. Very useful. Two default models exist : two columns and three columns. They are adjustable. Ability to create others sophisticated templates. As easy as the Lego game.
* [__Newsletter__](https://github.com/cmsunoPlugins/newsletter) : Great plugin to send a formated newsletter to a list of subscriber. Use PHP mail() or Gmail SMTP. Shortcode to add a subscribe form in the page. Link in the mail to unsubscribe.
* [__Payment__](https://github.com/cmsunoPlugins/payment) : Allows you to create a small e-commerce site from CKEditor.
It adds a "add to cart" button to the editor.
It adds a complete cart system with order registration, email sending, invoice in PDF, multi-tax, shipping cost, payment by cheque and by bank transfer.
It can work with other payment plugin (Paypal, Payplug).
Very usefull and powerfull.
* [__Paypal__](https://github.com/cmsunoPlugins/paypal) : Very powerful. Adds a button in CKEditor to create  as many Paypal buttons as you need. Pay, Add to Cart, View Cart, Donate and Subscribe are available. Sales are checked and recorded with IPN return. Works also with digital goods and cart plugin.
* [__Payplug__](https://github.com/cmsunoPlugins/payplug) : Like Paypal. Adds a button in CKEditor to create  as many Payplug buttons. More efficient and economical for sale by CB or Mastercard. Sales are checked and recorded with IPN return. Works also with digital goods and cart plugin.
* [__Pdf Creator__](https://github.com/cmsunoPlugins/pdf_creator) : Create PDF from CKEditor content. Custom Shortcode (name, phone...) to save time. Numerous options (page format, font, margin...). Very Usefull. Use [Mpdf](http://www.mpdf1.com/mpdf/index.php)
* [__Scrollnav__](https://github.com/cmsunoPlugins/scrollnav) : Replaces the menu with a drop down menu which scrolled with the page. Use [Scrollnav](http://scrollnav.com/)
* [__Sound player__](https://github.com/cmsunoPlugins/sound_player) : Adds a button in CKEditor to listen Self-Hosted musique on the site. Compatible with every browsers and supports.
* [__Support__](https://github.com/cmsunoPlugins/support) : Adds a real complete forum system very suitable for technical support. Coupled with 'Users', 'Markdown' and 'Paypal', you have a complete system to sell your premium plugin very easily with the same appearance and the same functionality as on wordpress.org.
* [__Tem2uno__](https://github.com/cmsunoPlugins/tem2uno) : Used to automatically transform a theme from another CMS (GetSimple & b2evolution) for a use in CMSUno.
* [__Unocss__](https://github.com/cmsunoPlugins/unocss) : Allows to add CSS in the site directly from the Dashboard.
* [__Unoscript__](https://github.com/cmsunoPlugins/unoscript) : Allows to add a script in the site directly from the Dashboard. Example : Google Analytics tracking code.
* [__Users__](https://github.com/cmsunoPlugins/users) : Create a members area with login / registration dropdown form. Integration with a shortcode or directly inside the menu. Login with Ajax and PHP session.
* [__Video player__](https://github.com/cmsunoPlugins/video_player) : Adds a button in CKEditor to watch Self-Hosted Videos on the site. Compatible with every browsers and supports. Adds also youtube button.


Template Development
--------------------

Create a theme is very easy. You just need a folder with the name of your theme and, inside, a file named 'template.html'. That's it.
The template file is a simple html file with some specifics tags. Example :

```
<!DOCTYPE html>
<html>
<head>
	<title>[[title]]</title>
	<meta charset="utf-8">
	<meta name="description" content="[[description]]" />
	<link rel="stylesheet" href="[[template]]style.css" />
	[[head]]
</head>
<body>
	<div id="header">
		<h1>[[title]]</h1>
		[[menu]]
	</div><!-- #header -->
	<div class="content">
		[[content]]
	</div><!-- .content -->
	[[foot]]
</body>
</html>
```

### Template Tags ###

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
include('../../config.php'); // Lang
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

In the same way, you can customize your theme by adding the file __name_of_your_theme.php__ in the theme folder.

### fooMake.php ###

This file is __not required__.

This file is called with an include statement when the user pushed "publish" in the Dashboard.
The goal is to complete the variables that replace Shortcodes.
If your plugin works with a Shortcode [[foo]] in the content of the page, the code should be like this :

```
<?php if (!isset($_SESSION['cmsuno'])) exit(); ?>
<?php
	$my_var = "<div>Hello. I'm the plugin</div>";
	$Ucontent = str_replace('[[foo]]',$my_var,$content);
	$Uhead .= '<link rel="stylesheet" type="text/css" href="uno/plugins/foo/my-plugin-styles.css" />'."\r\n";
	$Ufoot .= '<script type="text/javascript" src="uno/plugins/foo/myfoo-min.js"></script>'."\r\n";
	$unoUbusy = 1;
?>
```
The plugins are executed in alphabetical order. If a plugin must be executed before any others, you can add an order of precedence in the name :

* Exemple : fooMake2.php
* Number between 1 and 5. No number is equivalent to 3
* the series of 1 first (in alphabetic order) ... the series of 5 are the latest.

Variables usable all have almost the same name as the shortcodes. Here is a non-exhaustive list :

* __$Uhtml__ (no shortcode) : The template content. Used to replace a shortcode directly into the template.
* __$Ucontent__ : The content of the pages. Used to replace a shortcode added in the page with CKEditor.
* __$Uhead__ : At the end of the block `<head>`. Used to add a script or a CSS file.
* __$Ufoot__ : At the end of the block `<body>`. Used to add a script.
* __$Ustyle__ : CSS content added at the end of the block `<head>` in a `<style>` container.
* __$UstyleSm__ : Idem for small screens. Added at the end of Ustyle (max-device-width: 480px).
* __$Uscript__ : Short JS content added at the end of the head part in a `<script>` container. Use it, for example, to declare var.
* __$Uonload__ : Used to add a JS code to be run after the start. ex : $onload = 'alert("hello!");';
* __$Umenu__ : Page list according to `<ul class="maClasse"><li><a href="#...">MyPage</a></li>...</ul>`.
* __$Utitle__ : Website Title. (Used in template : `<title>[[title]]</title>`).
* __$Udescription__ : Website Description (`<meta name="description" content="[[description]]">`).
* __$Uname__ : Published HTML file name. By default, it's "index".
* __$unoPop__ : Set 1 to add unoPop JS and CSS. Small code in pure JS to use nodal window and to get value from url data.
* __$unoUbusy__ : Set 1 to add JS var Ubusy in the head.

As a plugin, you can add a makefile in your theme folder :

* __name_of_your_themeMake0.php__ executed before all plugins (if exists) when publishing.
* __name_of_your_themeMake.php__ executed after plugins (if exists) when publishing.

You can use both.

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
UconfigNum++; // needed
CKEDITOR.plugins.addExternal('ckfoo', UconfigFile[UconfigNum-1]+'/../ckfoo/'); // needed
CKEDITOR.editorConfig = function(config) {
	config.extraPlugins += ',ckfoo';
	config.toolbarGroups.push('ckfoo');
	if(UconfigFile.length>UconfigNum)config.customConfig=UconfigFile[UconfigNum]; // needed for next plugin
};
```

### version.json ###

This file is __required__. It allows to update the plugin online.

Example of content :

```
{"version":"1.0","host":"https://github.com/cmsunoPlugins/foo/"}
```

### Data ###

CMSUno don't use MySQL but flat files in json. These files are stored in uno/data/. You can have different files for the same plugin.

There are two options : "secret"/"not secret" and "available for all pages"/"only for a specific page" (see multipage plugin) :

* Not secret - available for all pages : uno/data/mydata.json
* Not secret - only for this page : uno/data/name-of-the-page/mydata.json (name of the page : $Ubusy in php or Ubusy in JS)
* Secret - available for all pages : uno/data/_sdata-xxxx/mydata.json
* Secret - only for this page : uno/data/_sdata-xxxx/name-of-the-page/mydata.json

Name of the page : $Ubusy in php or Ubusy in JS

xxxx : $sdata in config.php

The _sdata-xxxx folder is only readable/writable by owner (PHP). You cannot get access to a file from a javascript tag or from a browser.

License 
-------

CMSUno is under MIT license.

<pre>
Copyright (c) 2014-2016 Jacques Malgrange

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

* V1.1.9 - 18/04/2016 :
	* Cache in htaccess
	* Fix bugs
* V1.1.8 - 12/03/2016 :
	* CKEditor 4.5.7
	* ELFinder 2.0.6
	* Fix bugs
* V1.1.7 - 08/01/2016 :
	* CKEditor 4.5.6
	* Default theme uno1 customizable
	* Fix bugs
* V1.1.6 - 17/12/2015 :
	* Inline CSS in mail template
	* Add JS var $UstyleSm for small screens like smartphone (max-device-width: 480px)
	* Fix bug in menu with IE8
	* Add BR Clear button in CKEditor
* V1.1.5 - 29/11/2015 :
	* CKEditor 4.5.5
	* Add submenu
	* Fix bug with hourglass
	* Update in light version if already lightened
* V1.1.4 - 12/11/2015 :
	* Offset menu setting
	* Add theme customizing
	* Add tinyColorPicker
* V1.1.3 - 05/11/2015 :
	* Loading speed improvement
	* Add an icon on finder button
	* Warning when change chapter without saving
* V1.1.2 - 21/10/2015 : Offset in uno_menu : JS var Umenuoffset
* V1.1.1 - 13/10/2015 : Chmod secure recursively sdata on login
* V1.1 - 05/10/2015 :
      * CKEditor 4.5.3, ELFinder 2.0, JQuery 2.1.4, JQuery-UI 1.9.2.
      * Add Inline Update button for CMS and Plugins
* V1.0 - 11/08/2015 : First stable version. CKEditor 4.5.2.
* V0.9.27 beta - 09/08/2015 : Add Spanish.
* V0.9.26 beta - 25/06/2015 : Safety improvement - phase two.
* V0.9.25 beta - 23/06/2015 : Safety improvement - phase one. Add error registration system.
* V0.9.24 beta - 19/06/2015 : Improved speed of 40%.
* V0.9.23 beta - 15/06/2015 : Everywhere asynchronous ajax.
* V0.9.22 beta - 13/06/2015 : Add U in make variables. Add makefile for template.
* V0.9.21 beta - 12/06/2015 : Add a gif hourglass.
* V0.9.20 beta - 31/05/2015 : Fix some bugs.
* V0.9.19 beta - 22/05/2015 : CKEditor 4.4.7 with widget.
* V0.9.18 beta - 19/05/2015 : Responsive menu.
* V0.9.17 beta - 11/05/2015
* V0.9.16 beta - 29/03/2015
* V0.9.15 beta - 24/03/2015
* V0.9.14 beta - 21/03/2015
* V0.9.13 beta - 13/03/2015
* V0.9.12 beta - 10/03/2015
* V0.9.11 beta - 28/02/2015
* V0.9.10 beta - 22/02/2015
* V0.9.9 beta - 12/02/2015
* V0.9.8 beta - 02/02/2015
* V0.9.7 beta - 15/01/2015
* V0.9.6 beta - 13/01/2015
* V0.9.5 beta - 10/01/2015
* V0.9.4 beta - 06/01/2015
* V0.9.3 beta - 09/12/2014
* V0.9.2 beta - 29/11/2014
* V0.9.1 beta - 23/11/2014
* CMSUno Version 0.9 beta - 26/10/2014
