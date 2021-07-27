//
// CMSUno
// Theme Uno1
//
function f_save_uno1(){
	// generic name : f_save_[name of the theme]()
	var h=[],a=document.getElementById("themeOption").getElementsByTagName("INPUT");
	h.push({name:'action',value:'save'});
	h.push({name:'unox',value:Unox});
	for(v=0;v<a.length;v++){
		if(a[v].type=='text')h.push({name:a[v].name,value:a[v].value});
		else if(a[v].type=='checkbox')h.push({name:a[v].name,value:a[v].checked?1:0});
	}
	a=document.getElementById("themeOption").getElementsByTagName("SELECT");
	for(v=0;v<a.length;v++){
		h.push({name:a[v].name,value:a[v].options[a[v].selectedIndex].value});
	}
	jQuery.post('uno/template/uno1/uno1.php',h,function(r){
		f_alert(r);
	});
}
function f_load_uno1(){
	jQuery.getJSON("uno/data/"+Ubusy+"/uno1.json?r="+Math.random(),function(data){
		jQuery.each(data,function(k,v){
			var a=document.getElementById(k),i;
			if(a!==null){
				if(a.tagName=='INPUT'&&a.type=='text')a.value=v;
				else if(a.tagName=='INPUT'&&a.type=='checkbox')a.checked=(v==1?true:false);
				else if(a.tagName=='SELECT'){
					a.value=v;
					if(k.substr(0,1)=='S'&&document.getElementById(k.substr(1))){
						i=document.getElementById(k.substr(1));
						if(v=='color'){
							i.className='color';
							i.onclick=null;
						}
						else if(v=='img'){
							i.className='img';
							i.onclick=(function(f){return function(){f_finder_select(f.substr(1))}})(k);
						}
					}
				}
			}
		});
		jQuery('#themeOption .color').colorPicker();
	});
}
function f_del_uno1(f){
	var g=f.parentNode.firstChild,h=g.id;
	jQuery(g).parent().empty().append('<input type="text" class="color input" style="width:150px;" name="'+h+'" id="'+h+'" /><span class="del" onclick="f_del_uno1(this);"></span>');
}
function f_sel_uno1(f){
	var i=document.getElementById(f.id.substr(1));
	if(f.options[f.selectedIndex].value=='color'){
		i.className='color input';
		i.onclick=null;
		i.value='';
	}
	else{
		j=document.createElement('input');
		j.type='text';
		j.name=i.id;
		j.id=i.id;
		j.className='img input';
		j.style.width='150px';
		j.onclick=(function(f){return function(){f_finder_select(f)}})(i.id);
		i.parentNode.replaceChild(j,i);
	}
}
//
f_load_uno1();
