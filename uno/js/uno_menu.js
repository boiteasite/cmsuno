// CMSUNO
// Smooth Menu in vanilla JS
//
var u=document.getElementById('nav'),a=u.childNodes,tt,mg=u.offsetHeight||30;
window.onscroll=function(){onScroll()}
for(v=0;v<a.length; v++){
	if(a[v].nodeName.toUpperCase()=="LI"&&a[v].firstChild.nodeName.toUpperCase()=="A"){
		if(a[v].firstChild.addEventListener)a[v].firstChild.addEventListener('click',function(event){gTo(this);event.preventDefault();},false);
		else a[v].firstChild.attachEvent('onclick',function(event){gTo(this);event.preventDefault();});
	}
}
function gTo(f){
	var g=(f+"").substring((f+"").search("#")+1);g=Math.max(0,document.getElementById(g).offsetTop-mg);h=window.pageYOffset||document.body.scrollTop;
	if (h<g-5){window.scrollBy(0,Math.min(80,(g-h)/4));tt=setTimeout(function(){gTo(f);},12);}
	else if (h>g+5){window.scrollBy(0,Math.max(-80,(g-h)/4));tt=setTimeout(function(){gTo(f);},12);}
	else{window.scrollTo(0,g);clearTimeout(tt);}
 }
function onScroll(){
	var s=window.pageYOffset|document.body.scrollTop,c=0,d=0;
	if(mg<s){u.style.position='fixed';u.style.top=0;}
	else{u.style.position='absolute';u.style.top=mg;}
	for(v=a.length-1;v>=0;v--){
		if(a[v].nodeName.toUpperCase()=="LI"){
			var b=a[v].firstChild;
			if(b.nodeName.toUpperCase()=="A"&&b.href.search("#")!=-1){
				if(c)d=c;
				c=b.href.substring(b.href.search("#")+1);
				if ((v==0||document.getElementById(c).offsetTop<=s+mg)&&(!d||document.getElementById(d).offsetTop>s+mg))b.className="active";
				else b.className="";
			}
		}
	}
}
