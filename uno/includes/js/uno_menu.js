// CMSUno
// Smooth Menu in vanilla JS
//
var st=(typeof Umenuoffset!=='undefined'?Umenuoffset:0),
	u=document.getElementById('nav'),
	Umenu=u.getElementsByTagName('A'),
	Utt,
	Uv,
	u1=(window.attachEvent&&!window.addEventListener)?document.querySelectorAll('.nav1'):document.getElementsByClassName('nav1'),
	u2=u1[0].getBoundingClientRect(),
	Umg=-u2.top+100-(window.pageYOffset!=null?window.pageYOffset:window.document.documentElement.scrollTop)+st;
window.addEventListener('scroll',onScroll);
for(Uv=0;Uv<Umenu.length;++Uv){
	if(Umenu[Uv].addEventListener)Umenu[Uv].addEventListener('click',function(event){gTo(this);event.preventDefault();sMenu(1);},false);
	else Umenu[Uv].attachEvent('onclick',function(event){gTo(this);event.preventDefault();sMenu(1);});
}
function gTo(f,j){
	var g=(f+'').substring((f+'').search('#')+1),h=(window.pageYOffset!=null?window.pageYOffset:window.document.documentElement.scrollTop);
	if(typeof j=='undefined')g=Math.max(0,document.getElementById(g).offsetTop-Umg);else g=j;
	if(h<g-5){window.scrollBy(0,Math.max((g-h)/30,Math.min(80,(g-h)/4)));Utt=setTimeout(function(){gTo(f,g);},12);}
	else if(h>g+5){window.scrollBy(0,Math.min((g-h)/30,Math.max(-80,(g-h)/4)));Utt=setTimeout(function(){gTo(f,g);},12);}
	else{window.scrollTo(0,g+1);clearTimeout(Utt);}
}
function onScroll(){
	var s=(window.pageYOffset!=null?window.pageYOffset:window.document.documentElement.scrollTop),c=0,d=0,e,i,v,w,u=(typeof UactiveMenuClass!='undefined'?UactiveMenuClass:'active');
	for(v=Umenu.length-1;v>=0;--v){
		var b=Umenu[v];
		if(b.href.indexOf('#')!=-1){
			if(c)d=c;
			c=b.href.substring(b.href.search('#')+1);
			if((v==0||document.getElementById(c).offsetTop<=s+Umg)&&(!d||document.getElementById(d).offsetTop>s+Umg)){
				if(b.className.indexOf(u)==-1)b.className+=' '+u;
				if(b.parentNode.className.indexOf("w3-dropdown-content")!=-1&&b.parentNode.parentNode.firstChild.className.indexOf(u)==-1)b.parentNode.parentNode.firstChild.className+=u;
			}
			else{
				if(b.parentNode.className=='subMenu'){if(b.className.indexOf(u)!=-1)b.className=b.className.replace(u,'');}
				else for(w=Umenu.length-1;w>=0;--w){
					if(v!=w&&Umenu[w].className.indexOf(u)!=-1)Umenu[w].className=Umenu[w].className.replace(u,'');
				}
			}
		}
	}
}
function sMenu(f){var a=document.getElementById("navSmall");if(f!=1&&a.className.indexOf("w3-show")==-1)a.className+=" w3-show";else a.className=a.className.replace(" w3-show","");}
