(function($){
    $.fn.inPlaceEdit = function( method ) {   
        var methods = {
            init: function(options) {
                this.inPlaceEdit.settings = $.extend({}, this.inPlaceEdit.defaults, options);
                return this.each(function() {
                    var $element = $(this), element = this;
                    if(typeof $element.data("originalBGColor") === "undefined") {
                        var getPBG = $element;
                        while(!getPBG.css('backgroundColor') || getPBG.css('backgroundColor') == "rgba(0, 0, 0, 0)") {
                            getPBG = getPBG.parent();
                        }
                        $element.data("originalBGColor", getPBG.css("backgroundColor"));
                    }
                    $element.hover(function() {
                        $(this).stop().animate({ backgroundColor: "rgb(255,255,188)" });
                    }, function() {
                        $(this).stop().animate({ backgroundColor: $element.data("originalBGColor") });
                    });
                    $element.click(function() {
                        var divid = 'inPlaceEdit-' + _post_id + '-' + options.field;
                        if($("#" + divid).length) {
                            $div = $("#" + divid);
                            $divInput = $(".inputtable", $div).eq(0);
                            $div.css("display", "block");
                            $divInput.focus();
                        } else {
                            var formHTML = '<div class="inPlaceEdit" id="' + divid + '" style="display: none">';
                                formHTML += '<form name="default_update" id="default_update" data-post_id="' + _post_id + '" data-field="' + options.field + '" class="update_content" action="javascript://" method="post">';
                                if(options.field == "title") {
                                    formHTML += '<input type="text" class="inputtable" id="' + divid + '-inputtable" name="postTitle" value="' + $element.text() + '" />';
                                } else if(options.field == "content") {
                                    formHTML += '<textarea class="inputtable" id="' + divid + '-inputtable" name="postContent">' + $element.text() + '</textarea>';
                                }
                                formHTML += '<div class="zm-tt-form-container">';
                                    formHTML += '<div class="button-container">';
                                        formHTML += '<input type="submit" class="button save" value="Save"  id="save_' + divid + '" />';
                                        formHTML += '<ul class="entry-utility-container">';
                                            formHTML += '<li><a href="javascript://" class="exit">Exit</a></li>';
                                        formHTML += '</ul>';
                                    formHTML += '</div>';
                                formHTML += '</div>';
                                
                                formHTML += '</form>';
                            formHTML += '</div>';
                            $element.after(formHTML);
                            $div = $("#" + divid);
                            $(".exit", $div).click(function() {
                                $(this).parents('.inPlaceEdit').eq(0).css("display", "none");
                            });
                            $divInput = $(".inputtable", $div).eq(0);
                            $divInput.css({
                                width: $element.innerWidth(),
                                height: $element.innerHeight(),
                                fontSize: $element.css("fontSize"),
                                color: $element.css("color"),
                                marginLeft: $element.css("marginLeft"),
                                marginRight: $element.css("marginRight"),
                                marginTop: $element.css("marginTop"),
                                marginBottom: $element.css("marginBottom"),
                                paddingLeft: $element.css("paddingLeft"),
                                paddingRight: $element.css("paddingRight"),
                                paddingTop: $element.css("paddingTop"),
                                paddingBottom: $element.css("paddingBottom")
                            }).focusout(function() {
                                $(this).parent().parent().css("display", "none");
                            }).keyup(function(event){
                                if(event.keyCode === 27) {
                                    $divInput.blur();
                                } 
                            });
                            $div.css({
                                position: "absolute", 
                                left: $element.position().left,
                                top: $element.position().top, 
                                width: $element.innerWidth(),
                                height: $element.innerHeight(),
                                display: "block"
                            });
                        }
                    });
                });
            }
        };
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error( 'Method "' +  method + '" does not exist in inPlaceEdit!');
        }
        $.fn.pluginName.defaults = {
            foo: 'bar'
        };
        $.fn.pluginName.settings = {};
    };
})(jQuery);
