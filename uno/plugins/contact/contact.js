//
// CMS Uno
// Plugin Contact
//
	function f_contact(){
		jQuery(document).ready(function(){
			h=jQuery('#frmContact').serializeArray();
			h.push({name:'action',value:'save'});
			h.push({name:'contactAdmin',value:document.getElementById('contactAdmin').value});
			h.push({name:'contactSend',value:document.getElementById('contactSend').value});
			h.push({name:'contactHappy',value:document.getElementById('contactHappy').value});
			h.push({name:'contactCaptcha',value:document.getElementById('contactCaptcha').checked});
			jQuery.post('uno/plugins/contact/contact.php',h,function(r){f_alert(r);});
		});
	}
	//
	function f_contact_add(f,g){
		a=document.getElementById('contactResult');
		b=document.createElement('tr');
		c=document.createElement('td');
		f=f.replace(/_/g,' ');f=f.replace(/\\/g,'');
		c.innerHTML=f;
		b.appendChild(c);
		c=document.createElement('td');
		if(g=='te'){d=document.createElement('input');d.type='text';}
		else if(g=='ta')d=document.createElement('textarea');
		d.name=g+f;
		d.style.width='100%';
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
		document.getElementById('contactLabel').value='';
	}
	//
	function f_sload_contact(){
		jQuery(document).ready(function(){
			jQuery.ajax({type:"POST",url:'uno/plugins/contact/contact.php',data:{'action':'load'},dataType:'json',async:true,success:function(r){
				if(r.mail)document.getElementById('contactAdmin').value=r.mail;
				if(r.send)document.getElementById('contactSend').value=r.send;
				if(r.happy)document.getElementById('contactHappy').value=r.happy;
				if(r.captcha==1)document.getElementById('contactCaptcha').checked=true;else document.getElementById('contactCaptcha').checked=false;
				jQuery("#contactResult").empty();
				jQuery.each(r.frm,function(k,v){f_contact_add(v.l,v.t);});
			}});
		});
	}
//
f_sload_contact();