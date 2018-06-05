$(function () {

    // multiselect without search field
    $('.select2').select2({
        minimumResultsForSearch: 10,
        allowClear: true
    });

    // check
    $('input[type="checkbox"].custom-check, input[type="radio"].custom-radio').iCheck({
        checkboxClass:  'icheckbox_flat-green',
        radioClass:     'iradio_flat-green'
    });

    // language
    var language = new Namespace.Language();
    if( language.hasLanguage() ){
        language.setUpForm();
    }

    // push notifications
    var push = new Namespace.Push();
    push.init();

});

var Namespace = Namespace || {};
(function (win, doc, ns) {

    ns.Textarea = function(){

        this.completeTextarea = function(uploadDomain){
            // summernote WYSIWYG - text editor
            $('.wysiwyg-textarea textarea').summernote({
                height: 250,
                toolbar: [
                    ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize', 'color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['media', ['picture', 'link']],
                    ['extra', ['codeview']]
                ],
                callbacks: {
                    onImageUpload: function(files) {
                        var loader = new Namespace.Loader(),
                            data = new FormData(),
                            summernote = $(this);

                        loader.show();
                        $.each(files, function(key, value){
                            data.append(key, value);
                        });

                        $.ajax({
                            url: uploadDomain,
                            type: 'POST',
                            data: data,
                            cache: false,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            success: function(result) {
                                if( !result.error ){
                                    var paths = result.path;
                                    for(var i in paths){
                                        var image = $('<img>').attr('src',  paths[i]);
                                        summernote.summernote('insertNode', image[0]);
                                    }
                                }
                                loader.hide();
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                // Handle errors here
                                loader.hide();
                            }
                        });
                    }
                }
            });
        };

        this.simpleTextarea = function(){
            // summernote WYSIWYG - simple text editor
            $('.wysiwyg-textarea-simple textarea').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']]
                ]
            });
        };

        return this;
    };

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

    ns.Push = function() {
        var _mainSelect = null,
            _secondarySelects = null;

        this.init = function(){
            _mainSelect = $('#deeplink');

            if( _mainSelect.length > 0 ){
                _secondarySelects = $('.deepLinkID');

                var that = this;
                _mainSelect.change(function(){
                    that.hideSecondarySelects();
                    that.showSecondarySelect($(this).val());
                });

                if( _secondarySelects.length > 0 ){
                    this.hideSecondarySelects();
                }
                if( _mainSelect.val() != '' ){
                    this.showSecondarySelect(_mainSelect.val());
                }
            }
        };

        this.showSecondarySelect = function(value){
            var id = _mainSelect.find('option[value="' + value + '"]').attr('data-id');
            if( id != '' ){
                $('select[name="' + _mainSelect.attr('id') + '_' + id + '"]').next().show();
            }
        };

        this.hideSecondarySelects = function(){
            _secondarySelects.each(function(){
                $(this).next().hide();
            });
        };

        return this;
    };

}(window, document, Namespace));