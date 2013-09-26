CKEDITOR.addTemplates('egzakt', {

    // The name of the subfolder that contains the preview images of the templates.
    imagesPath : '/bundles/egzaktsystem/backend/images/templates/',

    // Template definitions.
    templates :
    [
        {
            title:  'Box',
            image:  'box.png',
            html:   '<div class="tmpl_box"><p>Lorem ipsum dolor sit amet</p></div>'
        },
        {
            title:  'Colored Table',
            image:  'colored_table.png',
            html:   '<div class="tmpl_colored_table">' +
                        '<table>' +
                            '<thead>' +
                                '<tr>' +
                                    '<th>Header 1</th>' +
                                    '<th>Header 2</th>' +
                                    '<th>Header 3</th>' +
                                '</tr>' +
                            '</thead>' +
                            '<tbody>' +
                                '<tr>' +
                                    '<td>Cell</td>' +
                                    '<td>Cell</td>' +
                                    '<td>Cell</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td>Cell</td>' +
                                    '<td>Cell</td>' +
                                    '<td>Cell</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td>Cell</td>' +
                                    '<td>Cell</td>' +
                                    '<td>Cell</td>' +
                                '</tr>' +
                            '</tbody>' +
                        '</table>' +
                    '</div>'
        },
        {
            title:  'PDF Document',
            image:  'pdf.gif',
            html:   '<a href="#" class="tmpl_file pdf">PDF Document</a>'
        },
        {
            title:  'Word Document',
            image:  'word.gif',
            html:   '<a href="#" class="tmpl_file doc">Word Document</a>'
        }
    ]
});