// CMSUNO
// Custom configuration for CKEditor
//

// Plugin & Langue
	UconfigNum=0;

	CKEDITOR.editorConfig = function(config)
		{
		config.language=Ulang;
		if(UconfigFile.length>UconfigNum)config.customConfig=UconfigFile[UconfigNum];   
		}
