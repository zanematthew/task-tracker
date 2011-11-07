/* BEGIN Hash Tag Stuff */
var _filters = {};

// @todo if we have a hash store it to filter on later
function addHash( hash ) {
    if( typeof arguments[1] !== "undefined" && arguments[1] == true) {
        _filters = {};
    }
    if ( hash ) {
        var thishash, theseterms;
        var thesehashes = hash.split('/');

        for(var i = 0; i < thesehashes.length; i++) {
            if(thesehashes[i].indexOf('_') > -1) {
                thishash = thesehashes[i].split('_'); 
                theseterms = thishash[1].split(',');
                for(var j = 0; j < theseterms.length; j++ ) {
                    if(typeof _filters[ thishash[0] ] !== "object") {
                        _filters[ thishash[0] ] = [];
                    }
                    _filters[ thishash[0] ].push(theseterms[j]);
                    jQuery("#" + thishash[0] + "-" + theseterms[j].toLowerCase()).prop("checked", true);
                }
                jQuery("#select_" + thishash[0] + " option[data-value=" + thishash[1].toLowerCase() + "]").attr("selected", "selected");
            }
        }
    }
}
function changeHash() {
    var hash = "/";
    for(var j in _filters) {
        hash += j + "_" + _filters[j].join(",") + "/";
    }
    window.location.hash = hash;
}

function filterRows() {
    var showhide;
    var noResults = true;
    for( var i in _data ) {
        showhide = true;
        for(var j in _filters) {
            if ( jQuery.inArray(_data[i][j], _filters[j]) === -1) {
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
            if(typeof _filters[this.name] !== "object") {
                _filters[this.name] = [];
            }
            _filters[this.name].push(jQuery('option:selected', this).attr("data-value"));
        }
    });
    jQuery( "#filter_task_form input[type=checkbox]").each(function() {
        if(jQuery(this).prop('checked')) {
            if(typeof _filters[this.name] !== "object") {
                _filters[this.name] = [];
            }
            _filters[this.name].push(jQuery(this).attr("data-value"));
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
