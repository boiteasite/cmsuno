// CMSUno
function unoPop(i,t){
if(document.getElementById('unoPop')==null){
	var h=document.createElement('div'),m,n;h.id='unoPop';h.className='unoPop';m=document.createElement('div');m.className='unoPopContent';
	n=document.createElement('a');n.className='unoPopClose';n.href='javascript:void(0)';n.onclick=function(){document.body.removeChild(document.getElementById('unoPop'))};
	m.innerHTML=i;h.appendChild(n);h.appendChild(m);document.body.appendChild(h);
	if(t!=0)setTimeout(function(){unoPopFade(1,h);},t);
}};
function unoPopFade(f,h){f-=.05;if(f>0)setTimeout(function(){h.style.opacity=f;unoPopFade(f,h);},30);else document.body.removeChild(h);};
function unoGvu(p){var r=new RegExp('(?:[\?&]|&amp;)'+p+'=([^&]+)', 'i');var match=window.location.search.match(r);return(match&&match.length>1)?match[1]:'';};
