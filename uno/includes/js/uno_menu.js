// CMSUno
// Smooth Menu in vanilla JS
//
var st=(typeof Umenuoffset!=='undefined'?Umenuoffset:0),
	u=document.getElementById('nav'),
	Umenu=u.getElementsByTagName('LI'),
	Utt,
	Ubot,
	Uv,
	u1=(window.attachEvent&&!window.addEventListener)?document.querySelectorAll('.nav1'):document.getElementsByClassName('nav1'),
	u2=u1[0].getBoundingClientRect(),
	Umg=-u2.top+100-(window.pageYOffset!=null?window.pageYOffset:window.document.documentElement.scrollTop)+st;
window.addEventListener('scroll',onScroll);
document.getElementById('navR').checked=false;
document.getElementById('navR').style.display='none';
for(Uv=0;Uv<Umenu.length;Uv++){
	if(Umenu[Uv].nodeName.toUpperCase()=='LI'&&Umenu[Uv].firstChild.nodeName.toUpperCase()=='A'){
		if(Umenu[Uv].firstChild.addEventListener)Umenu[Uv].firstChild.addEventListener('click',function(event){gTo(this);event.preventDefault();},false);
		else Umenu[Uv].firstChild.attachEvent('onclick',function(event){gTo(this);event.preventDefault();});
	}
}
function gTo(f,j){
	var g=(f+'').substring((f+'').search('#')+1),h=(window.pageYOffset!=null?window.pageYOffset:window.document.documentElement.scrollTop);
	if(typeof j=='undefined')g=Math.max(0,document.getElementById(g).parentElement.offsetTop-Umg);else g=j;
	if(h<g-5&&h!=Ubot){window.scrollBy(0,Math.max((g-h)/30,Math.min(80,(g-h)/4)));Utt=setTimeout(function(){gTo(f,g);},12);}
	else if(h>g+5){window.scrollBy(0,Math.min((g-h)/30,Math.max(-80,(g-h)/4)));Utt=setTimeout(function(){gTo(f,g);},12);}
	else{window.scrollTo(0,g+1);clearTimeout(Utt);document.getElementById('navR').checked=false;}
	Ubot=h;
}
function onScroll(){
	var s=(window.pageYOffset!=null?window.pageYOffset:window.document.documentElement.scrollTop),c=0,d=0,e,i,v,w;
	for(v=Umenu.length-1;v>=0;v--){
		var b=Umenu[v].firstChild;
		if(b.nodeName.toUpperCase()=='A'&&b.href.search('#')!=-1){
			if(c)d=c;
			c=b.href.substring(b.href.search('#')+1);
			if((v==0||document.getElementById(c).parentElement.offsetTop<=s+Umg)&&(!d||document.getElementById(d).parentElement.offsetTop>s+Umg)){
				b.className='active';
				if(Umenu[v].parentNode.className=='subMenu')Umenu[v].parentNode.parentNode.firstChild.className='active parent';
			}
			else{
				if(Umenu[v].parentNode.className=='subMenu')b.className='';
				else{
					e=Umenu[v].getElementsByTagName('A');
					i=0;
					if(e.length)for(w=1;w<e.length;++w){
						if(e[w].className=='active')i=1;
					}
					if(!i)b.className='';
				}
			}
		}
	}
}
