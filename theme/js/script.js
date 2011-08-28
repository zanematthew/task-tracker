/**
 * Run jQuery in no-conflict mode but still have access to $
 */
_plugindir = "theme/";

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
    $('.tt_loading').ajaxStart(function(){
        $( this ).fadeIn();
    });

    /* @todo this needs to be tied down via a class? */
    $('tr', this).hover(function(){
        $(this).find('.utility-container').addClass( 'zm-base-visible').removeClass( 'zm-base-hidden');
    }, function(){
        $(this).find('.utility-container').addClass( 'zm-base-hidden wtf').removeClass( 'zm-base-visible');
    });

    /** @todo update [task]: needs to be part of class for dialog */
    /**
     * Updating a task
     */    
    $('#update_task', this).submit(function(){
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
    $('#create_ticket_dialog').dialog({ 
        autoOpen: false,
        minWidth: 600,
        maxWidth: 800,
        minHeight: 630,
        title: 'Create a <em>Task</em>',
        modal: true
    });

    /** Load dialog box and get create ticket form */
    /** @todo create dialog [task]: needs to be part of class for dialog */
    $('#create_ticket').click(function(){
        $('#create_ticket_dialog').dialog('open');        
        
        // @todo templating still handled via php, consider js templating?
        template = $(this).attr( 'tt_template' );
        
        data = { 
            action: "loadTemplate",
            template: template
            };

        $.ajax({
            data: data,
            success: function( msg ){
                $('#create_ticket_target').fadeIn().html( msg );
            }
        });
    });   
    
    /** @todo exit dialog [task]: needs to be part of class for dialog */    
    /**
     * Exit our dialog box on click and reload our archive view
     */
    $('#exit').live('click', function(){
    
        if ( $( '#tt_update_container' ).length != 1 ) {
            $('#tt_main_target').fadeOut();   

            // @todo templating still handled via php, consider js templating?
            template = $(this).attr( 'tt_template' );
    
            data = { 
                action: "tt_load_template",
                template: template
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
    $('#clear').live('click', function(){
        clear_form();
    });

    /** @todo create [task]: needs to be part of class for dialog */
    $('#create_task_form', this).live('submit', function(){
        $.ajax({
            data: "action=postTypeSubmit&" + $(this).serialize(), 
            success: function( msg ){
                clear_form();                    
            }
        });    
    });

    /** @todo filter [task] onclick: needs to be part of class for dialog */    
    $(".zm-base-item a").live("click", function() {

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
    
        for( var i in _tasks ) {
            if ( _tasks[i][search_on[0]] == search_on[1] ) {
                $( ".post-" + i ).fadeIn();
            } else {
                $( ".post-" + i ).fadeOut();
            }
        }
    });
        
    /** @todo load [task] filters: needs to be part of class for dialog */    
    $( '#filter_handle' ).click(function(){  
        var _this = this;  
        if ( $( '#filter_task_form' ).length ) {
            $( '#filter_task_form' ).toggle( 'slow' );
        } else {
            $('#tt_filter_target').toggle( "slow", function(){                
                template = _plugindir + $( _this ).attr( 'tt_template' );
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
        
        if ( searchClass != '' ) {            
            $( "#archive_table tbody tr" + searchClass ).fadeIn();                
            $( "#archive_table tbody tr" ).not(searchClass).fadeOut(); 
        } else {
            $( "#archive_table tbody tr" ).fadeIn();            
        } 
    });
        
    $( window ).load(function(){
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
                }
            });
            return false;
        } // End 'if'
    });
});
