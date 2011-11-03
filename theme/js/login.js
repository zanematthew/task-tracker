// Start login
jQuery(document).ready(function( $ ){
    $( '#login_exit' ).live('click', function(){
        $( '#login_dialog' ).dialog( 'close' ); 
    });

    // @todo look up^^ very similar!
    $( '#ltfo_handle' ).click(function(){
        $( '#login_dialog' ).dialog( 'open' );
        temp_load({
            "target_div": "#login_target",
            "template": $( this ).attr( 'data-template' ),
            "callback": function() {
                $("#user_name").focus();
            }
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
});
// End login