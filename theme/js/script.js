/**
 * Run jQuery in no-conflict mode but still have access to $
 */
var _plugindir = "theme/";

/* BEGIN Hash Tag Stuff */
var _filters = {};

// @todo if we have a hash store it to filter on later
function addHash( hash ) {
    if( typeof arguments[1] !== "undefined" && arguments[1] == true) {
        _filters = {};
    }
    if ( hash ) {
        var thishash;
        var thesehashes = hash.split('/');
        for(var i = 0; i < thesehashes.length; i++) {
            if(thesehashes[i].indexOf('__') > -1) {
                thishash = thesehashes[i].split('__'); _filters[ thishash[0] ] = thishash[1];
                jQuery("#select_" + thishash[0] + " option[data-value=" + thishash[1].toLowerCase() + "]").attr("selected", "selected");
            }
        }
    }
}
function changeHash() {
    var hash = "/";
    for(var j in _filters) {
        hash += j + "__" + _filters[j] + "/";
    }
    window.location.hash = hash;
}

function filterRows() {
    var showhide;
    var noResults = true;
    for( var i in _data ) {
        showhide = true;
        for(var j in _filters) {
            if ( _data[i][j] != _filters[j] ) {
                showhide = false;
            }
        }
        if(showhide) {
            noResults = false;
            jQuery( ".post-" + i ).fadeIn();
        } else {
            jQuery( ".post-" + i ).fadeOut();
        }
    }
    if(noResults) {
        if( jQuery("#archive_table tbody tr.no-results").length ) {
            jQuery("#archive_table tbody tr.no-results").fadeIn();
        } else {
            var colspan = jQuery("td", jQuery("#archive_table tbody tr").eq(0)).length;
            jQuery("#archive_table tbody")
                .append('<tr class="no-results"><td colspan="' + colspan + '"><em>No Tasks match the selected criteria.</em></td></tr>');
        }
    } else {
        jQuery("#archive_table tbody tr.no-results").fadeOut();
    }
    changeHash();
}

function build_filters() {
    var searchClasses = '';
    _filters = {};
    jQuery( "#filter_task_form select" ).each(function() { 
        if(jQuery(this).val()) {
//            searchClasses += "." + jQuery(this).val();
            _filters[this.name] = jQuery('option:selected', this).attr("data-value");
        }
    });
    filterRows();
}

addHash(window.location.hash, false);

jQuery('a[href*="http://' + location.host + location.pathname + '#/"]').live('click', function() {
    addHash(
        jQuery(this).attr('href').replace('http://' + location.host + location.pathname, ''), true
    );
    filterRows();
    return false;
});

/* END Hash Tag Stuff */

jQuery( 'a[title], label[title]' ).live( "mouseover mouseout", function( event ) {
    jQuery( this ).qtip({
        overwrite: false,
        content: jQuery( this ).attr( "title" ),
        show: {
            event: event.type,
            ready: true
        }
    }, event);              
}).each(function( i ) {
   jQuery.attr( this, 'oldtitle', jQuery.attr( this, 'title' ));
   this.removeAttribute( 'title' );
});


jQuery(document).ready(function( $ ){
    
    /**
     * Default ajax setup
     */
    $.ajaxSetup({
        type: "POST",
        url: ajaxurl
    });
    
    /* @todo this needs to be tied down via a class? */
    $( '.zm-default-container tr' ).live( "mouseover mouseout", function( event ){
        if ( event.type == "mouseover" ) {                
            $(this).find('.utility-container').addClass( 'zm-base-visible').removeClass( 'zm-base-hidden');
        } else {
            $(this).find('.utility-container').addClass( 'zm-base-hidden wtf').removeClass( 'zm-base-visible');            
        }
    });

    /**
     * Check if the inPlaceEdit plugin is loaded    
     */
    if ( jQuery().inPlaceEdit ) {

        var $overlay = $('<div class="ui-widget-overlay"></div>').hide().appendTo('body');

        $('.post-title, .post-content').click(function(){        
            $('.ui-widget-overlay').fadeIn();
            setOverlayDimensionsToCurrentDocumentDimensions(); //remember to call this when the document dimensions change
        });

        $('.inplace-edit-container .exit').live( 'click', function(){
            $('.ui-widget-overlay').fadeOut();
        });

        $(window).resize(function(){
            setOverlayDimensionsToCurrentDocumentDimensions();
        });

        function setOverlayDimensionsToCurrentDocumentDimensions() {
            $('.ui-widget-overlay').width($(document).width());
            $('.ui-widget-overlay').height($(document).height());
        }        

        if ( typeof _post_id !== "undefined" && $(".post-title").length ) {
            $(".post-title").inPlaceEdit({ 
                    postId: _post_id, 
                    field: "title" 
            });

            $(".post-content").inPlaceEdit({ 
                    postId: _post_id, 
                    field: "content" 
            });
        }
    }

    /** 
     * @todo update [task]: 
     * needs to be part of class for dialog 
     * This can be used as a default updating of CPT
     *  - move to default.js
     */
    /**
     * Updating a task
     */       
    $( '.update_content' ).live( 'submit', function(){
        $.ajax({
            data: "action=postTypeUpdate&ID=" + $(this).attr('data-post_id') + "&"+ $(this).serialize(), 
            success: function( msg ){
                location.reload( true );            
                //$('.ui-widget-overlay').fadeOut();                
            }
        });    
    }); // End 'update'

    $( '#default_utility_udpate_form' ).live('submit', function(){
        $.ajax({
            data: "action=defaultUtilityUpdate&" + $(this).serialize(), 
            success: function( msg ){                
               location.reload( true );
            }
        });    
    });
    
    $( '.default_delete' ).live( "click", function(){
        var post_id = $( this ).attr( 'data-post_id');
        $( "#delete_dialog" )
            .attr("data-post_id", $(this).attr("data-post_id"))
            .attr("data-security", $(this).attr("data-security"))
            .dialog('open');
    });

    /** @todo create dialog defaults [task]: needs to be part of class for dialog */
    /** 
     * Setup our dialog for create a ticket 
     */
    dialogs = {
        "create_ticket_dialog":  { 
            autoOpen: false,        
            minWidth: 600,
            maxWidth: 600,
            minHeight: 630,
            title: 'Create a <em>Task</em>',
            modal: true        
        },
        "login_dialog": { 
            autoOpen: false,
            title: 'Please <em>Login</em>',
            modal: true
        },
        "delete_dialog": { 
            resizable: false,
            autoOpen: false,
            title: 'Delete this item?',
            modal: true,
            dialogClass: "confirmation-container",
            buttons: {
                "Delete this item": function() {
                    data = {
                        action: "postTypeDelete",
                        post_id: $( this ).attr( 'data-post_id' ),
                        security: $( this ).attr( 'data-security' )
                    };
                    var post_id = $( this ).attr( 'data-post_id');
                    $.ajax({            
                        data: data,
                        success: function( msg ){                
                            $( '.post-' + post_id ).fadeOut();
                        }
                    });            
                    $( this ).dialog( "close" );
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        }
    };

    $( '#create_ticket_dialog, #login_dialog, #delete_dialog' ).each(function() {
        $(this).dialog(dialogs[this.id]);
    });

    $( '#login_exit' ).live('click', function(){
        $( '#login_dialog' ).dialog( 'close' ); 
    });
    
    // @todo templating still handled via php, consider js templating?
    function temp_load( params ) {

        params.action = "loadTemplate";        
        console.log( 'show loading icon in target' );
        $.ajax({
            data: params,
            success: function( msg ){                
                $( params.target_div ).fadeIn().html( msg );
            },
            error: function( xhr ){
                console.log( params );
                console.log( 'XHR Error: ' + xhr );
            }
        });
    }
    
    /** Load dialog box and get create ticket form */
    /** @todo create dialog [task]: needs to be part of class for dialog */
    $( '#create_ticket' ).click(function(){
        $('#create_ticket_dialog').dialog('open');        
        temp_load({
            "target_div": "#create_ticket_target",
            "template": $( this ).attr("data-template"),
            "post_type": $( this ).attr("data-post_type")
        });
    });   

    // @todo look up^^ very similar!
    $( '#ltfo_handle' ).click(function(){
        $( '#login_dialog' ).dialog( 'open' );
        temp_load({
            "target_div": "#login_target",
            "template": $( this ).attr( 'data-template' )            
        });        
    });
    

    /** @todo create [task]: needs to be part of class for dialog */
    $( '#login_form' ).live('submit', function(){
        $.ajax({
            data: "action=siteLoginSubmit&" + $(this).serialize(), 
            success: function( msg ){
                location.reload( true );
            }
        });    
    });

    /** @todo exit dialog [task]: needs to be part of class for dialog */    
    /**
     * Exit our dialog box on click and reload our archive view
     */
    $( '#exit' ).live('click', function(){
    
        if ( $( '#archive_table' ).length ) {

            $('#tt_main_target').fadeOut();   

            template = $( this ).attr( 'data-template' );
            
            data = { 
                action: "loadTemplate",
                template: template,
                post_type: $( this ).attr( 'data-post_type' ),
                post_status: "published"
            };
    
            $.ajax({
                data: data,
                success: function( msg ){
                    $('#create_ticket_dialog').dialog('close');        
                    $('#tt_main_target').fadeIn().html( msg );
                }
            });

            return false;
        } else {
            $('#create_ticket_dialog').dialog('close');                
        }
    });

    /** @todo clear [task] form: needs to be part of class for dialog */
    /** clear our form */
    function clear_form() {
        $(':input','#create_default_form')
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .removeAttr('checked')
            .removeAttr('selected');   
        
        if ( $('.ui-dialog').css('display') != 'none' ) {
            $('.ui-dialog:first').effect("highlight", {}, 3000);
        }
    }

    /** @todo clear [task] event: needs to be part of class for dialog */    
    $( '#clear' ).live('click', clear_form);

    function submit_boo( payload ){        
        $.ajax({            
            data: "action=postTypeSubmit&" + payload,
            success: function( msg ) {                                 
                if ( msg.length ) {
                    $( '#default_message_target' ).fadeIn().html( msg ).delay(1000).fadeOut();                    
                }
            }
        }); 
    }    
    
    $( '#save_exit' ).live( 'click', function(){

        submit_boo( $( '#create_default_form' ).serialize() );        
        
        $(this).delay( 2000 );

        $('#create_ticket_dialog').dialog('close');

        if ( $( '#archive_table' ).length ) {

            $('#tt_main_target').fadeOut();               
            
            data = { 
                action: "loadTemplate",
                template: $(this).attr( 'data-template' ),
                post_type: $( this ).attr( 'data-post_type' ),
                post_status: "published"
            };
    
            $.ajax({
                data: data,
                success: function( msg ){
                    $('#create_ticket_dialog').dialog('close');        
                    $('#tt_main_target').fadeIn().html( msg );
                }
            });

            return false;
        } else {
            $('#create_ticket_dialog').dialog('close');                
        }

    });

    $( '#save_add' ).live( 'click', function(){                
        submit_boo( $( '#create_default_form' ).serialize() );        
    }); 

    /** @todo filter [task] onclick: needs to be part of class for dialog */    
    $(".zm-base-item a").live("click", function() {
        
        var link = $( this ).attr( 'href' );
        
        // This is not fucking ajax
        // we can't just check for 'http://' cause chrome removes it
        // should be a better way to do this.
        if ( link.substring( 0, 1 ) == '#' || link == 'javascript://' ) {
            if ( !$( '#archive_table' ).length ) {
                $('#tt_main_target').fadeOut();
                template = "theme/default/archive-table.php";
                data = { 
                    action: "tt_load_template",
                    template: template
                };
                $.ajax({
                    data: data,
                    success: function( msg ){
                        $('#tt_main_target').html( msg ).fadeIn();
                    }
                });
                return false;
            }
            var search_on = $( this ).attr( 'rel' );
            search_on = search_on.split( "_" );
            for( var i in _data ) {
                if ( _data[i][search_on[0]] == search_on[1] ) {
                    $( ".post-" + i ).fadeIn();
                } else {
                    $( ".post-" + i ).fadeOut();
                }
            }
        } // End 'fuckery'
    });
        
    /** @todo load [task] filters: needs to be part of class for dialog */    
    $( '#filter_handle' ).click(function(){  
        var $this = $(this);
        if ( $( '#filter_task_form' ).length ) {
            $( '#filter_task_form' ).toggle( 'slow' );
        } else {
            $( '#tt_filter_target' ).toggle( "slow", function(){                

                template = _plugindir + $this.attr( 'data-template' );
                type = $this.attr( 'data-post_type');

                data = {
                    action: "loadTemplate",
                    template: template,
                    post_type: type
                };

                $.ajax({
                    data: data,
                    success: function( msg ){
                        $('#tt_filter_target').html( msg ).fadeIn();
                        if(_filters != {}) {
                            for(var j in _filters) {
                                _filterClass = "option.taxonomy-" + j + ".term-" + _filters[j];
                                $(_filterClass).attr("selected", "selected");
                            }
                        }
                    }
                });            
            });                    
        }
    });    

    /** @todo filter [task] archive: needs to be part of class for dialog */    
    $( '#tt_filter_target select' ).live( 'change', build_filters );

    $( window ).load(function(){


        /** @todo load [task] archive: needs to be part of class for dialog */    
        if ( $('.sample').length ) {

            template = $( '.sample' ).attr('data-template');            
            post_type = $( '.sample' ).attr('data-post_type');

            if ( post_type == undefined || template == undefined )                
                console.log( 'no post type, and/or tempalte: please use data-post_type/template="[your cpt]"');                            

            data = { 
                action: "loadTemplate",
                post_type: post_type,
                post_status: "publish",
                template: template
            };

           $.ajax({
                data: data,
                success: function( msg ){

                    var match = true;

                    $('#tt_main_target').fadeIn().html( msg );
                    filterRows();
                } // End 'suckit' 
            });
            return false;
        } // End 'if'

        /**
         * If we're on a single task page load our entry utility.         
         * @todo define: "entry utility"
         */
        if ( $('#task_entry_utility_handle').length ) {
            temp_load({
                "target_div": "#task_entry_utility_target",
                "template": $( '#task_entry_utility_handle' ).attr( 'data-template' ),
                "post_id": $( '#task_entry_utility_handle' ).attr( 'data-post_id' ),
                "post_type": $( '#task_entry_utility_handle' ).attr( 'data-post_type' )
            });
        } // End 'check for entry utility'

        if ( !$( '.comments-container' ).length ) {
            
            $( '#task_comment_target .tt_loading').show();

            temp_load({
                "target_div": "#task_comment_target",
                "template": $( '#task_comment_handle' ).attr( 'data-template' ),
                "post_id": $( '#task_comment_handle' ).attr( 'data-post_id' )
            });
        }

    }); // End 'window.load'        

    $( '#utiliy_update_handle' ).live('click', function(){
        $( '#task_entry_utility_target' ).fadeOut();
        $( '#default_utility_update_container' ).fadeIn();
    });    

    $( '#default_utility_update_exit').live( 'click', function(){        
        $( '#task_entry_utility_target' ).fadeIn();
        $( '#default_utility_update_container' ).fadeOut();
    });
        

    /**
     * Submit new comment, note comments are loaded via ajax
     */
     $( '#default_add_comment_form' ).live( 'submit', function(){

        data = {
            action: "addComment",
            post_id: _post_id,
            comment: $( '#comment' ).val()
        };

        $.ajax({
            data: data, 
            success: function( msg ){                                
                temp_load({
                    "target_div": "#task_comment_target",
                    "template": $( '#task_comment_handle' ).attr( 'data-template' ),
                    "post_id": $( '#task_comment_handle' ).attr( 'data-post_id' )
                });
            },
            error: function( xhr ) {
                console.log( 'XHR Error: ' + xhr );
            }
        });
    });
});
