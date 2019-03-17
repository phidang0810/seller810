/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
        config.enterMode = CKEDITOR.ENTER_BR;
        config.shiftEnterMode = CKEDITOR.ENTER_BR;
        config.entities = false;
        config.entities_greek = false;
        config.entities_latin = false;
        config.htmlEncodeOutput = true;
        config.entities_processNumerical = false;
        config.allowedContent = true;
        config.font_names = 'SF Pro Display;San Francisco Display';
    config.filebrowserBrowseUrl = window.location.protocol + "//" + window.location.hostname + '/public/fck/ckfinder/ckfinder.html';

    config.filebrowserImageBrowseUrl = window.location.protocol + "//" + window.location.hostname + '/public/fck/ckfinder/ckfinder.html?type=Images';

    config.filebrowserFlashBrowseUrl = window.location.protocol + "//" + window.location.hostname + '/public/fck/ckfinder/ckfinder.html?type=Flash';

    config.filebrowserUploadUrl = window.location.protocol + "//" + window.location.hostname + '/public/fck/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';

    config.filebrowserImageUploadUrl = window.location.protocol + "//" + window.location.hostname + '/public/fck/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';

    config.filebrowserFlashUploadUrl = window.location.protocol + "//" + window.location.hostname + '/public/fck/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';
};
