/**
 * Run jQuery in no-conflict mode but still have access to $
 */
var _plugindir = "theme/";
var _filters = {};

// @todo if we have a hash store it to filter on later
if ( window.location[ 'hash' ] ) {
    var hash = window.location['hash'].substr(1).split('/');
    var thishash;
    for(var i in hash) {
        if(hash[i].indexOf('__') > -1) {
            thishash = hash[i].split('__');
            _filters[ thishash[0] ] = thishash[1];
        }
    }
}

jQuery('a[title], label[title]').live("mouseover", function() {
    jQuery(this).qtip({
        overwrite: false,
        content: jQuery(this).attr("title"),
        show: {
            event: event.type,
            ready: true
        }
    }, event);              
}).each(function(i) {
   jQuery.attr(this, 'oldtitle', jQuery.attr(this, 'title'));
   this.removeAttribute('title');
});

jQuery(document).ready(function( $ ){
        
    /**
     * Default ajax setup
     */
    $.ajaxSetup({
        type: "POST",
        url: ajaxurl
    });
    
    /**
     * Notification icon
     */
    $( '.tt_loading' ).ajaxStart(function(){
        $( this ).fadeIn();
    });

    /* @todo this needs to be tied down via a class? */
    $( '.zm-tt-container tr' ).live( "mouseover mouseout", function( event ){
        if ( event.type == "mouseover" ) {                
            $(this).find('.utility-container').addClass( 'zm-base-visible').removeClass( 'zm-base-hidden');
        } else {
            $(this).find('.utility-container').addClass( 'zm-base-hidden wtf').removeClass( 'zm-base-visible');            
        }
    });

    /** 
     * @todo update [task]: 
     * needs to be part of class for dialog 
     * This can be used as a default updating of CPT
     *  - move to default.js
     */
    /**
     * Updating a task
     */    
    $( '#default_update' ).submit(function(){
        /** @props petemilkman.com for being right, concatinate data */
        $.ajax({
            data: "action=postTypeUpdate&" + $(this).serialize(), 
            success: function( msg ){
                $('select', this).attr('disabled',' ');
                location.reload( true );
            }
        });    
    }); // End 'update'

    $( '#default_utility_udpate_form' ).live('submit', function(){
        $.ajax({
            data: "action=defaultUtilityUpdate&" + $(this).serialize(), 
            success: function( msg ){
                console.log( msg );
                //location.reload( true );
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
    $( '#create_ticket_dialog' ).dialog({ 
        autoOpen: false,        
        minWidth: 600,
        maxWidth: 600,
        minHeight: 630,
        title: 'Create a <em>Task</em>',
        modal: true        
    });

    $( '#login_dialog' ).dialog({ 
        autoOpen: false,
        title: 'Please <em>LTFO</em>',
        modal: true
    });
    
    $( '#delete_dialog' ).dialog({ 
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

    });
    
    $( '#login_exit' ).live('click', function(){
        $( '#login_dialog' ).dialog( 'close' ); 
    });
    
    // @todo templating still handled via php, consider js templating?
    function temp_load( params ) {
        
        data = { 
            action: "loadTemplate",
            template: params.template,
            post_id: params.post_id
        };

        $.ajax({
            data: data,
            success: function( msg ){
                $( params.target_div ).fadeIn().html( msg );
            },
            error: function( xhr ){
                console.log( 'XHR Error: ' + xhr );
            }
        });    
    }
    
    /** Load dialog box and get create ticket form */
    /** @todo create dialog [task]: needs to be part of class for dialog */
    $( '#create_ticket' ).click(function(){
        $('#create_ticket_dialog').dialog('open');        
        var params  = {};
        params.target_div = '#create_ticket_target';
        params.template = $( this ).attr( 'tt_template' );
        temp_load( params );         
    });   

    // @todo look up^^ very similar!
    $( '#ltfo_handle' ).click(function(){
        $( '#login_dialog' ).dialog( 'open' );
        var params  = {};
        params.target_div = '#login_target';
        params.template = $( this ).attr( 'tt_template' );
        temp_load( params );        
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
    
        if ( $( '#tt_update_container' ).length != 0 ) {

            $('#tt_main_target').fadeOut();   

            template = $(this).attr( 'tt_template' );
            
            data = { 
                action: "loadTemplate",
                template: template,
                post_type: "task",
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
        $(':input','#create_task_form')
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .removeAttr('checked')
            .removeAttr('selected');   
        $('.ui-dialog:first').effect("highlight", {}, 3000);            
    }

    /** @todo clear [task] event: needs to be part of class for dialog */    
    $( '#clear' ).live('click', clear_form);

    /** @todo create [task]: needs to be part of class for dialog */
    $( '#save_add' ).live( 'submit', function(){        
        $.ajax({
            data: "action=postTypeSubmit&" + $(this).serialize(), 
            success: function( msg ){
                clear_form();                    
            }
        });    
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

                template = _plugindir + $this.attr( 'tt_template' );
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

    function checkNoResults() {
        // Check No Results works off of the task-active class.
        // Be sure that all tasks that are showing have .task-active
        // and all tasks that are hidden do not have the task

        if( $("#archive_table tbody tr.task-active").length ) {
            $("#archive_table tbody tr.no-results").fadeOut();
        } else {
            if( $("#archive_table tbody tr.no-results").length ) {
                $("#archive_table tbody tr.no-results").fadeIn();
            } else {
                var colspan = $("td", $("#archive_table tbody tr").eq(0)).length;
                $("#archive_table tbody")
                    .append('<tr class="no-results"><td colspan="' + colspan + '"><em>No Tasks match the selected criteria.</em></td></tr>');
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

    function build_filters() {
        var searchClasses = '';
        _filters = {};
        $( "#filter_task_form select" ).each(function() { 
            if($(this).val()) {
                searchClasses += "." + $(this).val();
                _filters[this.name] = $('option:selected', this).attr("data-value");
            }
        });
        if ( searchClasses != '' ) {
            $( "#archive_table tbody tr" + searchClasses ).fadeIn().addClass('task-active');
            $( "#archive_table tbody tr" ).not(searchClasses).fadeOut().removeClass('task-active'); 
        } else {
            $( "#archive_table tbody tr" ).not('.no-results').fadeIn().addClass('task-active');
        } 
        checkNoResults();
        changeHash();
    }
    /** @todo filter [task] archive: needs to be part of class for dialog */    
    $( '#tt_filter_target select' ).live( 'change', build_filters );

    $( window ).load(function(){


        /** @todo load [task] archive: needs to be part of class for dialog */    
        if ( $('.sample').length ) {

            template = $( '.sample' ).attr('tt_template');            
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
                    if ( _filters != {} ) {
                        for( var i in _data ) {
                            match = true;
                            for( var j in _filters) {
                                if ( typeof _data[i][ j.toLowerCase() ] !== "undefined") {
                                    if ( _data[i][ j.toLowerCase() ] != _filters[j].toLowerCase()) {
                                        match = false;
                                    }
                                }
                            }
                            // @todo would be better to tell it '#' vs. 1
                            if ( match ) {
                                $( ".post-" + i ).fadeIn().addClass('task-active');
                            } else {
                                $( ".post-" + i ).fadeOut().removeClass('task-active');
                            }
                        } // End 'for'          
                        checkNoResults();
                    }
                } // End 'suckit' 
            });
            return false;
        } // End 'if'

        /**
         * If we're on a single task page load our entry utility.         
         * @todo define: "entry utility"
         */
        if ( $('#task_entry_utility_handle').length ) {

            params = {};
            params.target_div = '#task_entry_utility_target';
            params.template = $( '#task_entry_utility_handle' ).attr( 'data-template' );
            params.post_id = $( '#task_entry_utility_handle' ).attr( 'data-post_id' );
            temp_load( params );
        } // End 'check for entry utility'

    }); // End 'window.load'        

    /**
     * Load comments and comment form when user clicks on the comment icon
     */
    $('#task_comment_handle').live('click', function(){
        // Quick check to make sure its not already loaded
        if ( $( '.comments-container' ).length == 0 ) {
            params = {};
            params.target_div = '#task_comment_target';
            params.template = $( '#task_comment_handle' ).attr( 'data-template' );
            params.post_id = $( '#task_comment_handle' ).attr( 'data-post_id' );
            temp_load( params );
        }
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
                location.reload( true );
            },
            error: function( xhr ) {
                console.log( 'XHR Error: ' + xhr );
            }
        });
    });
});
