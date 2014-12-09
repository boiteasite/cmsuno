//
// CMS Uno
// Plugin Carousel
//
	function f_save_carousel(){
		jQuery(document).ready(function(){
			var a=document.getElementById("carouselResult").getElementsByTagName("img");
			var b=document.getElementById("carouselResult").getElementsByTagName("input");
			var h=[];
			h.push({name:'action',value:'save'});
			var c=document.getElementById('carouselNum');
			h.push({name:'car',value:c.options[c.selectedIndex].value});
			c=document.getElementById('carouselTyp');
			h.push({name:'typ',value:c.options[c.selectedIndex].value});
			h.push({name:'wid',value:document.getElementById('carouselW').value});
			h.push({name:'hei',value:document.getElementById('carouselH').value});
			h.push({name:'pau',value:document.getElementById('carouselPause').value});
			h.push({name:'spe',value:document.getElementById('carouselSpeed').value});
			c=document.getElementById('carouselTransition');
			h.push({name:'tra',value:c.options[c.selectedIndex].value});
			h.push({name:'rst',value:document.getElementById('carouselRandStart').checked});
			h.push({name:'nb',value:a.length});
			for (v=0;v<a.length;v++){h.push({name:'img'+v,value:a[v].src});h.push({name:'text'+v,value:b[v].value});}
			jQuery.post('uno/plugins/carousel/carousel.php',h,function(r){
				c=document.getElementById('carouselNum');
				c.selectedIndex=0;
				f_load_carousel();
				f_alert(r);
			});
		});
	}
	//
	function f_load_carousel(){
		jQuery(document).ready(function(){
			jQuery.getJSON("uno/data/"+Ubusy+"/carousel.json?r="+Math.random(),function(data){
				var c=document.getElementById('carouselNum');
				var n=c.options[c.selectedIndex].value;
				var t=c.options[c.selectedIndex].text;
				if(n==0){
					document.getElementById('carouselW').value='';
					document.getElementById('carouselH').value='';
					document.getElementById('carouselPause').value='';
					document.getElementById('carouselSpeed').value='';
					document.getElementById('carouselRandStart').checked=false;
					document.getElementById('bSCarousel').style.visibility='hidden';
					jQuery("#carouselResult").empty();
					jQuery("#carouselNum").empty();
					jQuery('#carouselNum').append('<option value="0">'+t+'</option>');
					jQuery.each(data,function(k,da){
						jQuery('#carouselNum').append('<option value="'+k+'">carousel-'+k+'</option>');
					});
				}
				else jQuery.each(data,function(k,da){
					if(k==n){
						if(da.wid)document.getElementById('carouselW').value=da.wid;
						if(da.hei)document.getElementById('carouselH').value=da.hei;
						if(da.pau)document.getElementById('carouselPause').value=da.pau;
						if(da.spe)document.getElementById('carouselSpeed').value=da.spe;
						if(da.rst==1)document.getElementById('carouselRandStart').checked=true;else document.getElementById('carouselRandStart').checked=false;
						if(da.typ){
							t=document.getElementById("carouselTyp");
							to=t.options;
							for(v=0;v<to.length;v++){if(to[v].value==da.typ){to[v].selected=true;v=to.length;}}
						}
						if(da.tra){
							t=document.getElementById("carouselTransition");
							to=t.options;
							for(v=0;v<to.length;v++){if(to[v].value==da.tra){to[v].selected=true;v=to.length;}}
						}
						document.getElementById('bSCarousel').style.visibility='visible';
						jQuery("#carouselResult").empty();
						jQuery.each(da.img,function(k,v){f_carousel_add(v.s,v.t);});
						f_carousel_type(da.typ);
					}
				});
			});
		});
	}
	//
	function f_supp_carousel(){
		jQuery(document).ready(function(){
			var c=document.getElementById('carouselNum');
			jQuery.post('uno/plugins/carousel/carousel.php',{'action':'supp','s':c.options[c.selectedIndex].value},function(r){
				c.selectedIndex=0;
				f_load_carousel();
				f_alert(r);
			});
		});
	}
	//
	function f_carousel_add(f,g){
		var a=document.getElementById('carouselResult');
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
		document.getElementById('carouselImg').value='';
	}
	//
	function f_carousel_type(b){
		var a=document.getElementById('carouselTyp');
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
f_load_carousel();