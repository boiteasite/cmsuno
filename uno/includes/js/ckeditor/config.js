/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		{ name: 'cmsuno' }, // CMSUNO
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];

	config.format_tags = 'p;h2;h3;h4;pre'; // Select Format dans la barre de bouton
	config.removeButtons = 'Styles,PageBreak,Symbol'; // Supprime des boutons d'un groupe
	//config.removeDialogTabs = 'image:advanced;link:advanced'; // Supprime des onglets dans les boites de dialogue des boutons
// ***************
// CMSUNO
//****************
	config.height = '500'; //hauteur fenêtre
	config.filebrowserBrowseUrl = 'uno/includes/elfinder/elfinder.html';
//	config.enterMode = CKEDITOR.ENTER_BR;
	config.customConfig = "../../js/uno_ckeditor.js";   
};
