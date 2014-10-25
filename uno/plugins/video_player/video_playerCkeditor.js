//
// CMS Uno
// Plugin Video Player
//

	configNum++;
	
	CKEDITOR.plugins.addExternal('video', '../../plugins/video_player/video/');
	CKEDITOR.editorConfig = function(config)
		{
		config.extraPlugins += ',video';
		config.toolbarGroups.push('video');
		config.extraAllowedContent += '; video(*)[*]{*}; source(*)[*]{*}';
		if(configFile.length>configNum)config.customConfig=configFile[configNum];   
		};
