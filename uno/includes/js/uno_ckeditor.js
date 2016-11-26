// CMSUno
// Custom configuration for CKEditor
//

// Plugin & Langue
UconfigNum=0;

CKEDITOR.editorConfig=function(config){
	config.height = '500'; //hauteur fenêtre
	config.filebrowserBrowseUrl = 'uno/includes/elfinder/elfinder.html';
	config.format_tags = 'p;h2;h3;h4;pre'; // Select Format dans la barre de bouton
	config.removeButtons = 'Styles,PageBreak,Symbol'; // Supprime des boutons d'un groupe
	config.language=Ulang;
	if(UconfigFile.length>UconfigNum)config.customConfig=UconfigFile[UconfigNum];
}
