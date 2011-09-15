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
    
    // @todo templating still handled via php, consider js templating?
    function temp_load( params ) {
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
        $('.ui-dialog:first').effect("highlight", {}, 3000);            
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
        } else {
            $( '#tt_filter_target' ).toggle( "slow", function(){                
                template = _plugindir + $this.attr( 'tt_template' );
console.log( 'loading: ' + template );
                data = {
                    action: "loadTemplate",
                    template: template
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
    $( '#tt_filter_target select' ).live( 'change', function() {   
        var searchClass = '';     
        
        $( "#filter_task_form select" ).each(function() { 
            if( $( this ).val() != "" ) 
                searchClass += "." + $(this).val(); 
        }); 
        $thisclass = $( "option:selected", this ).eq(0).attr("class");
        $thisclass = $thisclass ? $thisclass : "";
        if($thisclass.indexOf("taxonomy-") > -1 && $thisclass.indexOf("term-") > -1) {
            $thisclass = $thisclass.split(/\s+/);
            $term = "";
            $taxonomy = "";
            for (i = 0; i < $thisclass.length; i++) {
                if($thisclass[i].indexOf('taxonomy-') === 0) {
                    $taxonomy = $thisclass[i].replace("taxonomy-", "");
                }
                if($thisclass[i].indexOf('term-') === 0) {
                    $term = $thisclass[i].replace("term-", "");
                }
            }
            if($taxonomy != "" && $term != "") {
                _filters[$taxonomy] = $term;
                changeHash();
            }
        }
        searchTemp( searchClass );
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
                $("#archive_table tbody")
                    .append('<tr class="no-results"><td colspan="6"><em>No Tasks match the selected criteria.</em></td></tr>');
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

    function searchTemp( searchClass ) {        
        if ( searchClass != '' ) {            
            $( "#archive_table tbody tr" + searchClass ).fadeIn().addClass('task-active');
            $( "#archive_table tbody tr" ).not(searchClass).fadeOut().removeClass('task-active'); 
        } else {
            $( "#archive_table tbody tr" ).not('.no-results').fadeIn().addClass('task-active');
        } 
        checkNoResults();
    }        

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

                    var match = true;

                    $('#tt_main_target').fadeIn().html( msg );
                    if ( _filters != {} ) {
                        for( var i in _task ) {
                            match = true;
                            for( var j in _filters) {
                                if ( typeof _task[i][ j.toLowerCase() ] !== "undefined") {
                                    if ( _task[i][ j.toLowerCase() ] != _filters[j].toLowerCase()) {
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
    });
});
