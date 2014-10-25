// CMSUNO
// Custom configuration for CKEditor
//

// Plugin & Langue
	configNum=0;

	CKEDITOR.editorConfig = function(config)
		{
		config.language=lang;
		if(configFile.length>configNum)config.customConfig=configFile[configNum];   
		}
