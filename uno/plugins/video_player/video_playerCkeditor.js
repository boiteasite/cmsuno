//
// CMS Uno
// Plugin Video Player
//

	UconfigNum++;
	
	CKEDITOR.plugins.addExternal('video', '../../../plugins/video_player/video/');
	CKEDITOR.editorConfig = function(config)
		{
		config.extraPlugins += ',video';
		config.toolbarGroups.push('video');
		config.extraAllowedContent += '; video(*)[*]{*}; source(*)[*]{*}';
		if(UconfigFile.length>UconfigNum)config.customConfig=UconfigFile[UconfigNum];   
		};
