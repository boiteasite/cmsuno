//
// CMS Uno
// Plugin Carrousel
//
	function f_save_carrousel(){
		jQuery(document).ready(function(){
			var a=document.getElementById("carrouselResult").getElementsByTagName("img");
			var b=document.getElementById("carrouselResult").getElementsByTagName("input");
			var h=[];
			h.push({name:'action',value:'save'});
			var c=document.getElementById('carrouselNum');
			h.push({name:'car',value:c.options[c.selectedIndex].value});
			c=document.getElementById('carrouselTyp');
			h.push({name:'typ',value:c.options[c.selectedIndex].value});
			h.push({name:'wid',value:document.getElementById('carrouselW').value});
			h.push({name:'hei',value:document.getElementById('carrouselH').value});
			h.push({name:'pau',value:document.getElementById('carrouselPause').value});
			h.push({name:'spe',value:document.getElementById('carrouselSpeed').value});
			c=document.getElementById('carrouselTransition');
			h.push({name:'tra',value:c.options[c.selectedIndex].value});
			h.push({name:'rst',value:document.getElementById('carrouselRandStart').checked});
			h.push({name:'nb',value:a.length});
			for (v=0;v<a.length;v++){h.push({name:'img'+v,value:a[v].src});h.push({name:'text'+v,value:b[v].value});}
			jQuery.post('uno/plugins/carrousel/carrousel.php',h,function(r){
				c=document.getElementById('carrouselNum');
				c.selectedIndex=0;
				f_load_carrousel();
				f_alert(r);
			});
		});
	}
	//
	function f_load_carrousel(){
		jQuery(document).ready(function(){
			jQuery.getJSON("uno/data/carrousel.json?r="+Math.random(),function(data){
				var c=document.getElementById('carrouselNum');
				var n=c.options[c.selectedIndex].value;
				var t=c.options[c.selectedIndex].text;
				if(n==0){
					document.getElementById('carrouselW').value='';
					document.getElementById('carrouselH').value='';
					document.getElementById('carrouselPause').value='';
					document.getElementById('carrouselSpeed').value='';
					document.getElementById('carrouselRandStart').checked=false;
					document.getElementById('bSCarrousel').style.visibility='hidden';
					jQuery("#carrouselResult").empty();
					jQuery("#carrouselNum").empty();
					jQuery('#carrouselNum').append('<option value="0">'+t+'</option>');
					jQuery.each(data,function(k,da){
						jQuery('#carrouselNum').append('<option value="'+k+'">carrousel-'+k+'</option>');
					});
				}
				else jQuery.each(data,function(k,da){
					if(k==n){
						if(da.wid)document.getElementById('carrouselW').value=da.wid;
						if(da.hei)document.getElementById('carrouselH').value=da.hei;
						if(da.pau)document.getElementById('carrouselPause').value=da.pau;
						if(da.spe)document.getElementById('carrouselSpeed').value=da.spe;
						if(da.rst==1)document.getElementById('carrouselRandStart').checked=true;else document.getElementById('carrouselRandStart').checked=false;
						if(da.typ){
							t=document.getElementById("carrouselTyp");
							to=t.options;
							for(v=0;v<to.length;v++){if(to[v].value==da.typ){to[v].selected=true;v=to.length;}}
						}
						if(da.tra){
							t=document.getElementById("carrouselTransition");
							to=t.options;
							for(v=0;v<to.length;v++){if(to[v].value==da.tra){to[v].selected=true;v=to.length;}}
						}
						document.getElementById('bSCarrousel').style.visibility='visible';
						jQuery("#carrouselResult").empty();
						jQuery.each(da.img,function(k,v){f_carrousel_add(v.s,v.t);});
						f_carrousel_type(da.typ);
					}
				});
			});
		});
	}
	//
	function f_supp_carrousel(){
		jQuery(document).ready(function(){
			var c=document.getElementById('carrouselNum');
			jQuery.post('uno/plugins/carrousel/carrousel.php',{'action':'supp','s':c.options[c.selectedIndex].value},function(r){
				c.selectedIndex=0;
				f_load_carrousel();
				f_alert(r);
			});
		});
	}
	//
	function f_carrousel_add(f,g){
		var a=document.getElementById('carrouselResult');
		var b=document.createElement('tr');
		var c=document.createElement('td');
		var d=document.createElement('img');
		d.src=f;
		d.style.width='120px';
		d.style.height='80px';
		c.appendChild(d);
		b.appendChild(c);
		c=document.createElement('td');
		c.style.verticalAlign='middle';
		c.style.padding='0 20px';
		d=document.createElement('input');
		d.type='text';
		d.className='input';
		d.value=g;
		c.appendChild(d);
		b.appendChild(c);
		c=document.createElement('td');
		c.style.backgroundImage='url(uno/img/close.png)';
		c.style.backgroundPosition='center center';
		c.style.backgroundRepeat='no-repeat';
		c.style.cursor='pointer';
		c.width='30px';
		c.onclick=function(){this.parentNode.parentNode.removeChild(this.parentNode);}
		b.appendChild(c);
		a.appendChild(b);
		document.getElementById('carrouselImg').value='';
	}
	//
	function f_carrousel_type(b){
		var a=document.getElementById('carrouselTyp');
		if(!b)var b=a.options[a.selectedIndex].value;
		var aw=document.getElementById('trCarW');
		var ah=document.getElementById('trCarH');
		var ap=document.getElementById('trCarPause');
		var as=document.getElementById('trCarSpeed');
		var ar=document.getElementById('trCarRandStart');
		var at=document.getElementById('trCarTransition');
		var an=document.getElementById('emCarNivo');
		var af=document.getElementById('emCarFred');
		var ak=document.getElementById('emCarKen');
		var ae=document.getElementById('emCarFeat');
		if(b=='nivo'){
			aw.style.display="";ah.style.display="";ap.style.display="";as.style.display="";ar.style.display="";at.style.display="";
			an.style.display="inline";af.style.display="none";ak.style.display="none";ae.style.display="none";
		}
		else if(b=='fred'){
			aw.style.display="";ah.style.display="";ap.style.display="";as.style.display="";ar.style.display="";
			at.style.display="none";
			an.style.display="none";af.style.display="inline";ak.style.display="none";ae.style.display="none";
		}
		else if(b=='ken'){
			aw.style.display="";ah.style.display="";ap.style.display="";
			as.style.display="none";ar.style.display="none";at.style.display="none";
			an.style.display="none";af.style.display="none";ak.style.display="inline";ae.style.display="none";
		}
		else if(b=='feat'){
			ah.style.display="";ap.style.display="";as.style.display="";
			aw.style.display="none";ar.style.display="none";at.style.display="none";
			an.style.display="none";af.style.display="none";ak.style.display="none";ae.style.display="inline";
		}
	}
	//
//
f_load_carrousel();