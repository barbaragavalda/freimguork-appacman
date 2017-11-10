$(function () {

    // bootstrap WYSIHTML5 - text editor
    $('.wysiwyg-textarea textarea').wysihtml5({
        toolbar: {
            "font-styles": true, // Font styling, e.g. h1, h2, etc.
            "emphasis": true, // Italics, bold, etc.
            "lists": true, // (Un)ordered lists, e.g. Bullets, Numbers.
            "html": false, // Button which allows you to edit the generated HTML.
            "link": true, // Button to insert a link.
            "image": true, // Button to insert an image.
            "color": true, // Button to change color of font
            "blockquote": false, // Blockquote
        }
    });
    $('.wysiwyg-textarea-simple textarea').wysihtml5({
        toolbar: {
            "font-styles": false, // Font styling, e.g. h1, h2, etc.
            "emphasis": true, // Italics, bold, etc.
            "lists": false, // (Un)ordered lists, e.g. Bullets, Numbers.
            "html": false, // Button which allows you to edit the generated HTML.
            "link": false, // Button to insert a link.
            "image": false, // Button to insert an image.
            "color": false, // Button to change color of font
            "blockquote": false, // Blockquote
        }
    });

    // multiselect without search field
    $('.select2').select2({
        minimumResultsForSearch: Infinity
    });

});