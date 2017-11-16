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

function alertError(title, body, close){
    var html = '';
    html += '<div id="error" class="modal fade bd-example-modal-sm" role="dialog">';
    html += '   <div class="modal-dialog modal-sm">';
    html += '       <div class="modal-content">';
    html += '           <div class="modal-header btn-danger clearfix">';
    html += '               <h5 class="modal-title pull-left">' + title + '</h5>';
    html += '               <button type="button" class="close" data-dismiss="modal" aria-label="' + close + '">';
    html += '                   <span aria-hidden="true">&times;</span>';
    html += '               </button>';
    html += '           </div>';
    html += '           <div class="modal-body"><p>' + body + '</p></div>';
    html += '           <div class="modal-footer">';
    html += '               <button type="button" class="btn btn-secondary" data-dismiss="modal">' + close + '</button>';
    html += '           </div>';
    html += '       </div>';
    html += '   </div>';
    html += '</div>';
    $('body').prepend(html);
    var dialog = $('#error');
    dialog.modal();
    dialog.on('hidden.bs.modal', function (e) {
        dialog.remove();
    });
}