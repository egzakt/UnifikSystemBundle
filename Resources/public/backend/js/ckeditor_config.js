/*
 Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function(config) {

    config.language = session_locale;
    config.bodyClass = 'editor';
    config.contentsCss = '/bundles/egzaktfrontendcore/css/main.css';
    config.height = 500;
    config.forcePasteAsPlainText = false;
    config.resize_enabled = true;
    config.templates_replaceContent = false;
    config.toolbarCanCollapse = false;
    config.removePlugins = 'elementspath';
    config.enterMode = CKEDITOR.ENTER_P;
    config.extraPlugins = 'MediaEmbed';

    config.toolbar = 'default';
    config.toolbar_default =
        [
            ['Source','-','Undo','Redo'],
            ['Bold','Italic','Strike','-','Subscript','Superscript','SpecialChar'],
            ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
            ['NumberedList','BulletedList','-','Outdent','Indent'],
            ['Link','Unlink','Anchor'],
            ['Image','Table','Templates','HorizontalRule','MediaEmbed'],
            ['Styles','Format','FontSize','RemoveFormat']
        ];

    config.toolbar_intro =
        [
            ['Source','-','Undo','Redo'],
            ['Cut','Copy','Paste','PasteText','SelectAll'],
            ['Find','Replace'],
            ['Bold','Italic','Strike','-','Subscript','Superscript','SpecialChar']
        ];

    config.toolbar_minimal =
        [
            ['Bold','Italic','Strike'],
            ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
            ['NumberedList','BulletedList','-','Outdent','Indent'],
            ['Link','Unlink','Anchor'],
            ['Image','Table','Templates','MediaEmbed'],
            ['Format'],
            ['FontSize']
        ];

    config.filebrowserBrowseUrl = '/bundles/egzaktbackendtext/js/ckfinder/ckfinder.html';
    config.filebrowserImageBrowseUrl = '/bundles/egzaktbackendtext/js/ckfinder/ckfinder.html?Type=Images';
    config.filebrowserFlashBrowseUrl = '/bundles/egzaktbackendtext/js/ckfinder/ckfinder.html?Type=Flash';

    config.templates = 'egzakt';

    CKEditorCustomConfig(config);
};

CKEDITOR.addStylesSet('default',[
	{ name : 'Texte coloré', element : 'span', attributes : { 'class' : 'colore' } }
]);

CKEDITOR.addTemplates('egzakt', {

    // The name of the subfolder that contains the preview images of the templates.
    imagesPath : '/bundles/egzaktbackendtext/images/templates/',

    // Template definitions.
    templates :
    [
        {
            title:  'PDF Document',
            image:  'pdf.gif',
            html:   '<a href="#" class="file file_pdf">PDF Document</a>'
        },
        {
            title:  'Word Document',
            image:  'word.gif',
            html:   '<a href="#" class="file file_doc">Word Document</a>'
        }
    ]
});

// Customs values in dialog boxes
// ---------------------------------------
CKEDITOR.on('dialogDefinition', function(ev) {

    // Take the dialog name and its definition from the event data.
    var dialogName = ev.data.name;
    var dialogDefinition = ev.data.definition;

    // Check if the definition is from the dialog we're interested on

    if (dialogName == 'table') {

        // Get a reference to the tab.
        var infoTab = dialogDefinition.getContents('info');

        var txtWidthField = infoTab.get('txtWidth');
        txtWidthField['default'] = '100%';

        var txtCellSpaceField = infoTab.get('txtCellSpace');
        txtCellSpaceField['default'] = '0';

        var txtCellPadField = infoTab.get('txtCellPad');
        txtCellPadField['default'] = '0';

        var txtBorderField = infoTab.get('txtBorder');
        txtBorderField['default'] = '0';
    }
});
