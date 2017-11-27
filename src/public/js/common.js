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
            var d = new Date();;
            d.setTime(d.getTime() + (exdays*24*60*60*1000));

            var expires = "expires="+ d.toUTCString();
            cvalue = JSON.stringify(cvalue);

            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        };

        this.get = function(cname) {
            var name = cname + "=",
                decodedCookie = decodeURIComponent(document.cookie),
                ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return JSON.parse(c.substring(name.length, c.length));
                }
            }
            return false;
        };

        return this;
    };

    ns.Delete = function(errorTitle, errorText, errorClose){
        var _errorTitle = errorTitle,
            _errorText = errorText,
            _errorClose = errorClose;

        this.item = function(placement, content, btnOkLabel, btnCancelLabel, url, onSuccess) {
            $('.delete-item').confirmation({
                rootSelector: '[data-toggle=confirmation]',
                singleton: true,
                popout: true,
                placement: placement,
                btnOkClass: 'btn btn-danger',
                btnCancelClass: 'btn btn-default',
                content: content,
                btnOkLabel: btnOkLabel,
                btnCancelLabel: btnCancelLabel,
                onConfirm: function(){
                    var id = $(this).attr('data-id');

                    $.ajax({
                        type:       'POST',
                        url:        url + id,
                        dataType:	'json'
                    })
                    .done( function( result ){
                        if( !result['error'] ){
                            onSuccess();
                        }else{
                            error();
                        }
                    })
                    .fail( function(){
                        error();
                    });
                }
            });
        };

        this.file = function(placement, content, btnOkLabel, btnCancelLabel, url){
            $('.delete-file').confirmation({
                rootSelector: '[data-toggle=confirmation]',
                singleton: true,
                popout: true,
                placement: placement,
                btnOkClass: 'btn btn-danger',
                btnCancelClass: 'btn btn-default',
                content: content,
                btnOkLabel: btnOkLabel,
                btnCancelLabel: btnCancelLabel,
                onConfirm: function(){
                    var id = $(this).attr('data-id'),
                        name = $(this).attr('data-name'),
                        that = $(this);

                    console.log(url);
                    $.ajax({
                        type:       'POST',
                        url:        url,
                        data:       { fieldName: name, fieldID: id },
                        dataType:	'json'
                    })
                    .done( function( result ){
                        if( !result['error'] ){
                            that.parent().html('<input type="file" class="form-control" id="' + name + '" name="' + name + '" value="" />');
                        }else{
                            error();
                        }
                    })
                    .fail( function(){
                        error();
                    });
                }
            });
        };

        function error(){
            alertError(_errorTitle, _errorText, _errorClose);
        };

        return this;
    };

}(window, document, Namespace));