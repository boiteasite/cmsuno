//
// CMS Uno
// Plugin Scrollnav
//
	function f_save_scrollnav(){
		jQuery(document).ready(function(){
			var topi=document.getElementById("scroTopi").value;
			var topf=document.getElementById("scroTopf").value;
			var tit=document.getElementById("scroTit").value;
			var sp=document.getElementById("scroSpeed").value;
			var ofs=document.getElementById("scroOfs").value;
			jQuery.post('uno/plugins/scrollnav/scrollnav.php',{'action':'save','topi':topi,'topf':topf,'tit':tit,'sp':sp,'ofs':ofs},function(r){
				f_alert(r);
			});
		});
	}
	//
	function f_load_scrollnav(){
		jQuery(document).ready(function(){
			jQuery.getJSON("uno/data/scrollnav.json?r="+Math.random(),function(r){
				if(r.topi!=undefined)document.getElementById('scroTopi').value=r.topi;
				if(r.topf!=undefined)document.getElementById('scroTopf').value=r.topf;
				if(r.tit)document.getElementById('scroTit').value=r.tit;
				if(r.sp)document.getElementById('scroSpeed').value=r.sp;
				if(r.ofs!=undefined)document.getElementById('scroOfs').value=r.ofs;
			});
		});
	}
	//
//
f_load_scrollnav();