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

    // language
    var language = new Namespace.Language();
    if( language.hasLanguage() ){
        language.setUpForm();
    }

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
    dialog.on('hidden.bs.modal', function(e){
        dialog.remove();
    });
}


var Namespace = Namespace || {};
(function (win, doc, ns) {

    ns.Cookie = function(){

        this.set = function(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires="+ d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        };

        this.get = function(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return false;
        };

        return this;
    };

    ns.Language = function(){
        var _container = null,
            _buttons = null,
            _cookies = null,
            _classActive = 'btn-primary',
            _classDesactive = 'btn-default';

        this.init = function() {
            _cookies = new ns.Cookie();
            _container = $('.box-languages');
        }

        this.hasLanguage = function(){
            this.init();

            if( _container.length > 0 ){
                return true;
            }
            return false;
        };

        this.setUpForm = function(){
            _buttons = _container.find('button');

            _buttons.click(function(e) {
                if( $(this).hasClass(_classActive) ){
                    var counter = 0;
                    _buttons.each(function(){
                        if( $(this).hasClass(_classActive) ){
                            counter++;
                        }
                    });
                    if( counter > 1 ){
                        desactivate($(this));
                    }
                }else{
                    activate($(this));
                }

                e.preventDefault();
                return false;
            });

            var found = false;
            _buttons.each(function(){
                var langID = $(this).val();
                if( _cookies.get('lang_' + langID) ){
                    found = true;
                    activate($(this), langID);
                }else{
                    desactivate($(this), langID);
                }
            });

            if( !found ){
                var button = $(_buttons[0]);
                activate($(_buttons[0]), null);
            }
        };

        function activate(button, langID){
            if( langID == null ){
                langID = button.val();
            }

            button.removeClass(_classDesactive).addClass(_classActive);
            $('.lang_' + langID).show();
            _cookies.set('lang_' + langID, 'true', 1);
        }

        function desactivate(button, langID){
            if( langID == null ){
                langID = button.val();
            }

            button.removeClass(_classActive).addClass(_classDesactive);
            $('.lang_' + langID).hide();
            _cookies.set('lang_' + langID, 'true', -1);
        }

        return this;
    };

}(window, document, Namespace));