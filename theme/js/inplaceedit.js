(function($){
    $.fn.inPlaceEdit = function( method ) {   
        var methods = {
            init: function(options) {
                this.inPlaceEdit.settings = $.extend({}, this.inPlaceEdit.defaults, options);
                return this.each(function() {
                    var $element = $(this), element = this;
                    if(!$element.hasClass('inPlaceEdit')) {
                        $element.addClass('inPlaceEdit');
                    }
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
                            $divInput = $("#" + divid + " .inputtable");
                            $div.css("display", "block");
                            $divInput.focus();
                        } else {
                            var formHTML = '<div class="inPlaceEdit" id="' + divid + '" style="display: none">';
                            if(options.field == "title") {
                                formHTML += '<input type="text" class="inputtable" name="postTitle" value="' + $element.text() + '" />';
                            } else if(options.field == "content") {
                                formHTML += '<textarea class="inputtable" name="postContent">' + $element.text() + '</textarea>';
                            }
                            formHTML += '</div>';
                            $element.after(formHTML);
                            $div = $("#" + divid);
                            $divInput = $("#" + divid + " .inputtable");
                            console.log($divInput);
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
                            });
                            $div.css({
                                position: "absolute", 
                                left: $element.position().left,
                                top: $element.position().top, 
                                width: $element.innerWidth(),
                                height: $element.innerHeight(),
                                display: "block"
                            });
                            $divInput.blur(function() {
                                $div.css("display", "none");
                            });
                            $divInput.keyup(function(event){
                                if(event.keyCode === 27) {
                                    $divInput.blur();
                                } 
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
