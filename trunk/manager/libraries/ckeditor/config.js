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
                ['Source', 'Maximize'],
                ['Cut','Copy','PasteText','PasteFromWord','-','Table'],
                ['Undo','Redo','-','Find','Replace','-','Link','Unlink','Anchor','-','SpecialChar'],
                ['Bold','Italic','-','Subscript','Superscript','-','NumberedList','BulletedList','-', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','RemoveFormat'],
                ['Format','FontSize','TextColor']
            ];
    config.format_tags = 'p;h1;h2;h3';
    config.resize_enabled = true;
    config.resize_dir = 'both';
    config.width = 600;
    config.resize_minWidth = 500;
    config.resize_maxWidth = 1200;
    config.resize_maxHeight = 600;
    config.filebrowserWindowWidth = 335;
    config.filebrowserWindowHeight = 480;

};
