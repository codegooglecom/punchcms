/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.forcePasteAsPlainText = true;
    config.toolbar = [
                ['Source'],
                ['Cut','Copy','PasteText','PasteFromWord','-','Table'],
                ['Undo','Redo','-','Find','Replace','-','Link','Unlink','Anchor','-','SpecialChar'],
                ['Bold','Italic','-','Subscript','Superscript','-','NumberedList','BulletedList','-', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','TextColor','FontSize','RemoveFormat', 'Maximize']
            ];
    config.width = 500;
    config.filebrowserWindowWidth = '335';
    config.filebrowserWindowHeight = '480';

};
