//
// CMS Uno
// Plugin Uno CSS
//
	function f_cssuno(){
		jQuery(document).ready(function(){
			jQuery.post('uno/plugins/unocss/unocss.php',{
				'action':'save',
				'css':document.getElementById('inputCSS').value
				},function(r){f_alert(r);}
			);
		});
	}
	//
	function f_load_cssuno(){
		jQuery(document).ready(function(){
			jQuery.getJSON("uno/data/unocss.json",function(data){
				x = data.tex.replace(/\\/g,'');
				document.getElementById('inputCSS').value=x;
			});
		});
	}
//
f_load_cssuno()