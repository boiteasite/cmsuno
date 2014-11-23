//
// CMS Uno
// Plugin Paypal
//

	var paypalMail='',paypalCurr='',paypalTax='';
	jQuery(document).ready(function(){
		jQuery.getJSON("uno/data/paypal.json?r="+Math.random(),function(r){
			if(r.mail!=undefined)paypalMail=r.mail;
			if(r.url!=undefined)paypalUrl=r.url;
			if(r.home!=undefined)paypalHome=r.home;
			if(r.curr!=undefined)paypalCurr=r.curr;else paypalCurr=0;
			if(r.tax!=undefined)paypalTax=r.tax;else paypalTax=0;
			if(r.app!=undefined)paypalApp=r.app;
			if(r.act!=undefined)paypalAct=r.act;
			if(r.don!=undefined)paypalDonval=r.don;
			if(r.lang5!=undefined)paypalLang5=r.lang5;else paypalLang5='en_US';
			paypalTaxIncl=0; // non utilise
			paypalSand=0; // SANDBOX en global lors du Make
		});
	});

	configNum++;
	
	CKEDITOR.plugins.addExternal('ckpaypal','../../plugins/paypal/ckpaypal/');
	CKEDITOR.editorConfig = function(config)
		{
		config.extraPlugins += ',ckpaypal';
		config.toolbarGroups.push('ckpaypal');
		config.extraAllowedContent += '; input[*](ckpaypal)';
		if(configFile.length>configNum)config.customConfig=configFile[configNum];   
		};
