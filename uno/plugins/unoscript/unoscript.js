//
// CMS Uno
// Plugin Uno Script
//
	function f_google_analytics(){
		jQuery(document).ready(function(){
			jQuery.post('uno/plugins/unoscript/unoscript.php',{
				'action':'save',
				'ga':document.getElementById('codeGA').value
				},function(r){f_alert(r);}
			);
		});
	}
	//
	function f_load_google_analytics(){
		jQuery(document).ready(function(){
			jQuery.getJSON("uno/data/"+Ubusy+"/unoscript.json",function(data){
				x = data.tex.replace(/\\/g,'');
				document.getElementById('codeGA').value=x;
			});
		});
	}
//
f_load_google_analytics()