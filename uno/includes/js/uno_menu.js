// CMSUno
// Smooth Menu in vanilla JS
//
var st=(typeof Umenuoffset!=='undefined'?Umenuoffset:0),u=document.getElementById('nav'),Umenu=u.childNodes,Utt,Ubot,Uv,u1=document.getElementsByClassName("nav1"),u2=u1[0].getBoundingClientRect(),Umg=-u2.top+100-window.pageYOffset+st;
window.onscroll=function(){onScroll()};document.getElementById('navR').checked=false;document.getElementById('navR').style.display="none";
for(Uv=0;Uv<Umenu.length;Uv++){
	if(Umenu[Uv].nodeName.toUpperCase()=="LI"&&Umenu[Uv].firstChild.nodeName.toUpperCase()=="A"){
		if(Umenu[Uv].firstChild.addEventListener)Umenu[Uv].firstChild.addEventListener('click',function(event){gTo(this);event.preventDefault();},false);
		else Umenu[Uv].firstChild.attachEvent('onclick',function(event){gTo(this);event.preventDefault();});
	}
}
function gTo(f){
	var g=(f+"").substring((f+"").search("#")+1),h=window.pageYOffset||document.body.scrollTop;g=Math.max(0,document.getElementById(g).offsetTop-Umg);
	if(h<g-5&&h!=Ubot){window.scrollBy(0,Math.max((g-h)/30,Math.min(80,(g-h)/4)));Utt=setTimeout(function(){gTo(f);},12);}
	else if(h>g+5){window.scrollBy(0,Math.min((g-h)/30,Math.max(-80,(g-h)/4)));Utt=setTimeout(function(){gTo(f);},12);}
	else{window.scrollTo(0,g+1);clearTimeout(Utt);}
	Ubot=h;
}
function onScroll(){
	var s=window.pageYOffset|document.body.scrollTop,c=0,d=0;
	for(v=Umenu.length-1;v>=0;v--){
		if(Umenu[v].nodeName.toUpperCase()=="LI"){
			var b=Umenu[v].firstChild;
			if(b.nodeName.toUpperCase()=="A"&&b.href.search("#")!=-1){
				if(c)d=c;c=b.href.substring(b.href.search("#")+1);
				if ((v==0||document.getElementById(c).offsetTop<=s+Umg)&&(!d||document.getElementById(d).offsetTop>s+Umg))b.className="active";
				else b.className="";
			}
		}
	}
}
