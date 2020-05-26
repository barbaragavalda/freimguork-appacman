$(function () {

    new Namespace.Form();

    // language
    var language = new Namespace.Language();
    if( language.hasLanguage() ){
        language.setUpForm();
    }

    // push notifications
    var push = new Namespace.Push();
    push.init();

    // maps
    $('.map').each(function(){
        var maps = new Namespace.Maps();
        maps.init($(this));
        maps.autocomplete();
    });

});

var Namespace = Namespace || {};
(function (win, doc, ns) {

    ns.Form = function (form) {

        this.init = function(form){
            this.select(form);
            this.check(form);
            this.events(form);
        };

        this.select = function(form){
            // select without search field
            var selects = this.get(form, '.select2');
            selects.select2({
                minimumResultsForSearch: 10,
                allowClear: true
            });

            var multiSelects = this.get(form, '.select2-multi');
            multiSelects.select2({
                multiple: true,
                minimumResultsForSearch: 10,
                allowClear: true
            });
            multiSelects.each(function(){
                $(this).find('option')[0].remove();
            });

            var multiSelectsChecks = this.get(form, '.select-all-checkbox');
            multiSelectsChecks.on('ifChanged', function(){
                var id = $(this).attr('id').replace('_selectAll', ''),
                    select = $('select[name="' + id + '"]');
                if( select.length === 0 ) select = $('select[name="' + id + '[]"]');

                var addProp = $(this).is(':checked'),
                    options = select.find('option');
                if( addProp ) {
                    options.prop('selected', 'selected');
                }else{
                    options.removeAttr('selected');
                    select.val(null);
                }
                select.trigger('change');
            });
        };

        this.check = function(form){
            var checkboxs = this.get(form, 'input[type="checkbox"].custom-check'),
                radios = this.get(form, 'input[type="radio"].custom-radio');

            checkboxs.iCheck({
                checkboxClass:  'icheckbox_flat-green',
                radioClass:     'iradio_flat-green'
            });
            radios.iCheck({
                checkboxClass:  'icheckbox_flat-green',
                radioClass:     'iradio_flat-green'
            });
        };

        this.events = function(form){
            //prevent submission
            var inputs = this.get(form, 'input,textarea');
            inputs.keypress(function(e){
                if( e.which === 13 ){
                    $(this).next().focus();  //Use whatever selector necessary to focus the 'next' input
                    return false;
                }
            });
        };

        this.get = function(form, selector){
            if( typeof(form) === 'undefined' ){
                return $(selector);
            }
            return form.find(selector);
        };

        this.init(form);

        return this;
    };

    ns.Textarea = function(){

        this.completeTextarea = function(uploadDomain){
            // summernote WYSIWYG - text editor
            $('.wysiwyg-textarea textarea').summernote({
                height: 250,
                toolbar: [
                    ['style', ['style', 'bold', 'italic', 'underline', 'strikethrough', 'clear']],
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
                    },
                    onPaste: function (e) {
                        var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                        e.preventDefault();
                        document.execCommand('insertText', false, bufferText);
                    }
                }
            });
        };

        this.simpleTextarea = function(){
            // summernote WYSIWYG - simple text editor
            $('.wysiwyg-textarea-simple textarea').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline']],
                    ['media', ['link']],
                    ['extra', ['codeview', 'clear']]
                ],
                callbacks: {
                    onPaste: function (e) {
                        var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                        e.preventDefault();
                        document.execCommand('insertText', false, bufferText);
                    }
                }
            });
        };

        return this;
    };

    ns.Dynamic = function(errorTitle, errorClose){
        var _errorTitle = errorTitle,
            _errorClose = errorClose;

        this.add = function(path){
            var that = this;
            $('.add-dynamic-field').click(function(e){
                var fieldName = $(this).attr('data-field'),
                    data = new FormData(),
                    loader = new Namespace.Loader(),
                    content = $('#content-' + fieldName),
                    position = null;

                content.find('select.select2').each(function(){
                    if( typeof($(this).attr('multiple')) !== 'undefined' ) {
                        var id = $(this).attr('id').replace('[]', ''),
                            name = $(this).attr('data-name');
                        id = parseInt(id.replace(name, ''), 10);
                        if (position === null || id > position) {
                            position = id;
                        }
                    }
                });
                if( position != null ) position++;

                data.append('field', fieldName);
                data.append('id', $(this).attr('data-id'));
                data.append('table', $(this).attr('data-table'));
                data.append('position', position);

                loader.show();

                $.ajax({
                    url: path,
                    type: 'POST',
                    data: data,
                    cache: false,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        var form = $('#content-' + fieldName);
                        form.append( result['html'] );
                        loader.hide();

                        new Namespace.Form(form);
                        that.delete();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Handle errors here
                        loader.hide();
                    }
                });

                e.preventDefault();
                return false;
            });
        };

        this.delete = function(path, errorText, content, btnOkLabel, btnCancelLabel){
            var that = this;
            $('.delete-dynamic-field').unbind('click').bind('click', function(e){
                var id = $(this).attr('data-id'),
                    button = this;

                if( id === '' ){
                    that.removeForm( $(button) );
                }else{
                    $(this).confirmation({
                        rootSelector: '[data-toggle=confirmation]',
                        singleton: true,
                        popout: true,
                        placement: 'left',
                        btnOkClass: 'btn btn-danger',
                        btnCancelClass: 'btn btn-default',
                        content: content,
                        btnOkLabel: btnOkLabel,
                        btnCancelLabel: btnCancelLabel,
                        onConfirm: function(){
                            var data = new FormData(),
                                loader = new Namespace.Loader();

                            data.append('field', $(button).attr('data-field'));
                            data.append('id', $(button).attr('data-id'));

                            loader.show();

                            $.ajax({
                                url: path,
                                type: 'POST',
                                data: data,
                                cache: false,
                                dataType: 'json',
                                processData: false,
                                contentType: false,
                                success: function(result) {
                                    if( !result['error'] ){
                                        that.removeForm( $(button) );
                                    }else{
                                        error(errorText);
                                    }
                                    loader.hide();
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    error(errorText);
                                    // Handle errors here
                                    loader.hide();
                                }
                            });
                        }
                    });
                }

                e.preventDefault();
                return false;
            });
        };

        function error(errorText){
            alertError(_errorTitle, errorText, _errorClose);
        };

        this.removeForm = function(object){
            object.parent().parent().remove();
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
        };

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

    ns.Maps = function(){
        var _id = '',
            _map = [],
            _marker = null,
            _latitude = null,
            _longitude = null,
            _hasCutomPosition = true,
            _customLatitude = null,
            _customLongitude = null;

        this.init = function(object){
            _id = object.attr('id').substring(4);
            _latitude = $('input[name="latitude"]');
            _longitude = $('input[name="longitude"]');
            _hasCutomPosition = true;
            if( _latitude.length == 0 || _longitude.length == 0 ){
            	_hasCutomPosition = false;
            	_latitude = $('input[name="latitude-' + _id + '"]');
            	_longitude = $('input[name="longitude-' + _id + '"]');
            }

            _map = new google.maps.Map(
                document.getElementById('map-' + _id),
                {
                    streetViewControl: false,
                    mapTypeControl: false,
                    rotateControl: false,
                    zoomControl: false,
                    scrollwheel: false,
                    draggable: true,
                    clickableIcons: false,
                    zoomControlOptions: {
                        position: google.maps.ControlPosition.RIGHT_BOTTOM
                    }
                }
            );

            var center = new google.maps.LatLng(41.38701, 2.16785),
                latitude = _latitude.val(),
                longitude = _longitude.val();
                
            if( latitude && longitude && latitude > 0 && longitude > 0 ){
                center = new google.maps.LatLng(latitude, longitude);
                this.addMarker( center );
            }else{
                _map.setZoom(13);
                _map.setCenter( center );
            }
            
            if( _hasCutomPosition ){
				var that = this;
				_customLatitude = $('input[name="latitude"]');
				$('input[name="latitude"]').keyup(function(){
					that.changeMarker();
				});
				_customLongitude = $('input[name="longitude"]');
				$('input[name="longitude"]').keyup(function(){
					that.changeMarker();
				});
            }
        };

        this.autocomplete = function(){
            var searchInput = document.getElementById(_id);
            var autocomplete = new google.maps.places.Autocomplete(searchInput);
            autocomplete.bindTo('bounds', _map);

            var that = this;
            if( $(searchInput).val() == "" && !_hasCutomPosition ) {
            	that.removeMarker();
            }
            $(searchInput).keyup(function(){
                if( $(this).val() == "" ){
            		console.log('removeMarker 2');
                    that.removeMarker();
                }
            });

            autocomplete.addListener('place_changed', function() {
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }

                that.addMarker( place.geometry.location );
            	_latitude.val(place.geometry.location.lat());
            	_longitude.val(place.geometry.location.lng());
            });
        };

        this.addMarker = function(latLang){
            this.removeMarker();

            _map.setZoom(16);
            _map.setCenter( latLang );
            _marker = new google.maps.Marker({
                position: latLang,
                map: _map
            });
            
            if( !_hasCutomPosition ){
            	_latitude.val(latLang.lat());
            	_longitude.val(latLang.lng());
            }
        };
        
        this.changeMarker = function(){
            var latitude = $('input[name="latitude"]'),
            	longitude = $('input[name="longitude"]');
            this.addMarker( new google.maps.LatLng(latitude.val(), longitude.val()) );
        };

        this.removeMarker = function(){
            if( _marker != null ){
                _marker.setMap(null);
            }
            if( !_hasCutomPosition ){
            	_latitude.val('');
            	_longitude.val('');
            }
        };

        return this;
    };

}(window, document, Namespace));