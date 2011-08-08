jQuery(document).ready(function( $ ){
    $('tr', this).hover(function(){
        $(this).find('.utility-container').addClass( 'zm-base-visible').removeClass( 'zm-base-hidden');
    }, function(){
        $(this).find('.utility-container').addClass( 'zm-base-hidden wtf').removeClass( 'zm-base-visible');
    });

    $('#update_task', this).submit(function(){
        /** 
         * @todo 1 this should be part of a global ajax setup, where when the request is made
         * ALL form fields are DISABLED! and enabled on success
         */
//        $('select', this).attr('disabled','disabled');

        /** @props petemilkman.com for being right */
        /** ajax post request */
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: "action=project_wp_update_post&" + $(this).serialize(), 
            success: function( msg ){
                /** @todo see 1 */
                $('select', this).attr('disabled',' ');
                location.reload( true );
            }
        });    
    }); // End 'update'
    
    /** Setup our dialog for create a ticket */
    /** @todo needs to be part of class for dialog */
    $('#create_ticket_dialog').dialog({ 
        autoOpen: false,
        minWidth: 600,
        maxWidth: 800,
        minHeight: 630,
        title: 'Create a <em>Task</em>',
        modal: true
    });

    /** Show dialog box and get create ticket form */
    /** @todo needs to be part of class for dialog */
    $('#create_ticket').click(function(){
        $('#create_ticket_dialog').dialog('open');        
        
        template = $(this).attr( 'tt_template' );
        
        data = { 
            action: "tt_load_template",
            template: template
            };

        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: data,
            success: function( msg ){
                $('#create_ticket_target').fadeIn().html( msg );
            }
        });
    });   
    
    /** @todo needs to be part of class for dialog */
    $('#exit').live('click', function(){
        if ( $( '#tt_update_container' ).length != 1 ) {
            $('#tt_main_target').fadeOut();
    
            template = $(this).attr( 'tt_template' );
    
            data = { 
                action: "tt_load_template",
                template: template
                };
    
            $.ajax({
                type: "POST",
                url: ajaxurl,
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

    /** clear our form */
    function clear_form() {
        $(':input','#create_task_form')
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .removeAttr('checked')
            .removeAttr('selected');   
        /** @todo why does this blink like 8 fucking times? is it doing it based on number of 'clears' */
        $('.ui-dialog').effect("highlight", {}, 3000);            
    }

    $('#clear').live('click', function(){
        clear_form();
    });

    /** @todo needs to be part of class for dialog */
    $('#create_task_form', this).live('submit', function(){
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: "action=project_submit_task&" + $(this).serialize(), 
            success: function( msg ){
                clear_form();                    
            }
        });    
    });

    $(".zm-base-item a").live("click", function() {

        if ( $( '#archive_table' ).length != 0 ) {
            $('#tt_main_target').fadeOut();
            template = "theme/archive-table.php";
            data = { 
                action: "tt_load_template",
                template: template
            };

           $.ajax({
                type: "POST",
                url: ajaxurl,
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
});
