//
// CMS Uno
// Plugin Sidebar
//
	function f_sidebar(){
		jQuery(document).ready(function(){
			jQuery.post('uno/plugins/sidebar/sidebar.php',{
				'action':'save',
			//	'sidebar':document.getElementById('sidebar').value
				'sidebar':CKEDITOR.instances['sidebar'].getData()
				},function(r){f_alert(r);}
			);
		});
	}
	//
	function f_load_sidebar(){
		jQuery(document).ready(function(){
			jQuery.post('uno/plugins/sidebar/sidebar.php',{'action':'get'},function(r){
				CKEDITOR.instances['sidebar'].setData(r);
			});
		});
	}
//
CKEDITOR.replace('sidebar',{height:'300'});
f_load_sidebar();