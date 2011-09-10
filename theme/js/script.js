/**
 * Run jQuery in no-conflict mode but still have access to $
 */
var _plugindir = "theme/";

jQuery(document).ready(function( $ ){
    $('a[title]').qtip();
    
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
    $( '.zm-tt-container tr' ).hover(function(){
        $(this).find('.utility-container').addClass( 'zm-base-visible').removeClass( 'zm-base-hidden');
    }, function(){
        $(this).find('.utility-container').addClass( 'zm-base-hidden wtf').removeClass( 'zm-base-visible');
    });

    /** @todo update [task]: needs to be part of class for dialog */
    /**
     * Updating a task
     */    
    $( '#update_task' ).submit(function(){
        /** @props petemilkman.com for being right, concatinate data */
        $.ajax({
            data: "action=postTypeUpdate&" + $(this).serialize(), 
            success: function( msg ){
                $('select', this).attr('disabled',' ');
                location.reload( true );
            }
        });    
    }); // End 'update'
    
    /** @todo create dialog defaults [task]: needs to be part of class for dialog */
    /** 
     * Setup our dialog for create a ticket 
     */
    $( '#create_ticket_dialog' ).dialog({ 
        autoOpen: false,
        minWidth: 750,
        maxWidth: 800,
        minHeight: 630,
        title: 'Create a <em>Task</em>',
        modal: true
    });

    $( '#login_dialog' ).dialog({ 
        autoOpen: false,
        title: 'Please <em>LTFO</em>',
        modal: true
    });
    
    $( '#login_exit' ).live('click', function(){
        $( '#login_dialog' ).dialog( 'close' ); 
    });
    
    function temp_load( params ) {
        // @todo templating still handled via php, consider js templating?
        data = { 
            action: "loadTemplate",
            template: params.template
        };

        $.ajax({
            data: data,
            success: function( msg ){
                $( params.target_div ).fadeIn().html( msg );
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
    
        if ( !$( '#tt_update_container' ).length ) {
            $('#tt_main_target').fadeOut();   

            // @todo templating still handled via php, consider js templating?
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
        $('.ui-dialog').effect("highlight", {}, 3000);            
    }

    /** @todo clear [task] event: needs to be part of class for dialog */    
    $( '#clear' ).live('click', clear_form);

    /** @todo create [task]: needs to be part of class for dialog */
    $( '#create_task_form' ).live('submit', function(){
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
                template = "theme/archive-table.php";
                data = { 
                    action: "tt_load_template",
                    template: template
                };
                $.ajax({
                    data: data,
                    success: function( msg ){
                        $('#tt_main_target').fadeIn().html( msg );
                    }
                });
                return false;
            }
            var search_on = $( this ).attr( 'rel' );
            search_on = search_on.split( "_" );
            for( var i in _task ) {
                if ( _task[i][search_on[0]] == search_on[1] ) {
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
            console.log('here');
        } else {
            console.log('there');
            $( '#tt_filter_target' ).toggle( "slow", function(){                
                template = _plugindir + $this.attr( 'tt_template' );
                data = {
                    action: "loadTemplate",
                    template: template
                };
           
                $.ajax({
                    data: data,
                    success: function( msg ){
                        $('#tt_filter_target').fadeIn().html( msg );
                    }
                });            
            });                    
        }
    });    

    /** @todo filter [task] archive: needs to be part of class for dialog */    
    $( '#tt_filter_target select' ).live( 'change', function() {   
        var searchClass = '';     
        
        $( "#filter_task_form select" ).each(function() { 
            if( $( this ).val() != "" ) 
                searchClass += "." + $(this).val(); 
        }); 
        
        searchTemp( searchClass );
    });

    function searchTemp( searchClass ) {        
        if ( searchClass != '' ) {            
            $( "#archive_table tbody tr" + searchClass ).fadeIn();                
            $( "#archive_table tbody tr" ).not(searchClass).fadeOut(); 
        } else {
            $( "#archive_table tbody tr" ).fadeIn();            
        } 
    }        

    $( window ).load(function(){
                    
        // @todo if we have a hash store it to filter on later
        if ( window.location[ 'hash' ] )
            var search_on = window.location['hash'].split('-');            

        /** @todo load [task] archive: needs to be part of class for dialog */    
        if ( $('.sample').length ) {
            template = $( '.sample' ).attr('tt_template');

            data = { 
                action: "loadTemplate",
                post_type: "task",
                post_status: "publish",
                template: template
            };

           $.ajax({
                data: data,
                success: function( msg ){
                    $('#tt_main_target').fadeIn().html( msg );
                    if ( search_on ) {
                        for( var i in _task ) {
                            // @todo would be better to tell it '#' vs. 1
                            if ( _task[i][search_on[0].substr( 1 ) ] == search_on[1] ) {
                                $( ".post-" + i ).fadeIn();
                            } else {
                                $( ".post-" + i ).fadeOut();
                            }
                        } // End 'for'          
                    }
                } // End 'suckit' 
            });
            return false;
        } // End 'if'
    });
});
