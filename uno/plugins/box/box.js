//
// CMS Uno
// Plugin Box
//
	function f_save_box(){
		jQuery(document).ready(function(){
			h=jQuery('#frmBox').serializeArray();
			h.push({name:'action',value:'save'});
			jQuery.post('uno/plugins/box/box.php',h,function(r){f_alert(r);});
		});
	}
	//
	function f_add_box(f){
		a=document.getElementById('curBox');
		b=document.createElement('tr');
		c=document.createElement('td');
		f=f.replace(/[^\w]/gi, '');
		c.innerHTML=f;
		c.style.width='110px';
		c.style.verticalAlign='middle';
		c.style.paddingLeft='40px';
		b.appendChild(c);
		c=document.createElement('td');
		d=document.createElement('textarea');
		d.name=f;
		d.style.width='100%';
		c.appendChild(d);
		b.appendChild(c);
		c=document.createElement('td');
		c.style.backgroundImage='url(uno/includes/img/close.png)';
		c.style.backgroundPosition='center center';
		c.style.backgroundRepeat='no-repeat';
		c.style.cursor='pointer';
		c.width='30px';
		c.onclick=function(){this.parentNode.parentNode.removeChild(this.parentNode);}
		b.appendChild(c);
		a.appendChild(b);
		document.getElementById('boxName').value='';
	}
	//
	function f_load_box(){
		jQuery(document).ready(function(){
			jQuery.getJSON("uno/data/box.json?r="+Math.random(),function(data){
				jQuery.each(data.box,function(k,d){
					jQuery('#curBox').append('<tr><td style="width:100px;vertical-align:middle;padding-left:40px;">'+d.n+'</td><td><textarea style="width:100%" name="'+d.n+'">'+d.b+'</textarea></td><td width="30px" style="cursor:pointer;background:transparent url(\'uno/includes/img/close.png\') no-repeat scroll center center;" onClick="this.parentNode.parentNode.removeChild(this.parentNode);"></td></tr>');
				});
			});
		});
	}
	//
//
f_load_box();