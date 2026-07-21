/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.toolbar = 'MyToolbar';
 
	config.toolbar_MyToolbar =
	[
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize','TextColor' ] },
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline'] },
		{ name: 'link' , items:['Link','Unlink']},
		{ name: 'alignment' , items:['JustifyLeft','JustifyCenter','JustifyRight']}
	];
	
	config.toolbar_custom = [
		{ name: 'styles', items :[ 'Bold','Italic','Underline']},
		{ name: 'txtcolor', items :[ 'TextColor']},
		{ name: 'link', items :['Link','Unlink']},
		{ name: 'alignment' , items:['JustifyLeft','JustifyCenter','JustifyRight']},
                { name: 'styles', items : [ 'Font','FontSize' ] }
	];
	
	config.toolbar_custom1 =
	[
		{ name: 'styles', items : [ 'FontSize','TextColor' ] },
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline'] },
		
		{ name: 'alignment' , items:['JustifyLeft','JustifyCenter','JustifyRight']}
	];
	
	config.height = '100px';
};
