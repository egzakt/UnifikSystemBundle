function collapsible_block(object) {
    object.next().slideToggle("normal");
    object.toggleClass("selected");
    object.parent().toggleClass("collapsible_block_selected");
}

$(document).ready(function() {

    // Collapsible blocks
    $(".collapsible_content").not('.open').hide();

    $("a.collapsible_link").click(function(e) {
        collapsible_block($(this));
		e.preventDefault();
    });

    // Pre-open a block if an anchor is present in the url
    anchor = document.location.hash;
    if (anchor) {
        blockId = anchor.substr(1, anchor.length);
        objLink = $("#" + blockId).children("a.collapsible_link");
        collapsible_block(objLink);
    }

    // Submit a form on the keydown (otherwise it doesn't work in IE and Safari)
    $("input").keydown(function(e) {
        if (e.keyCode == 13) {
            $(this).parents("form").submit();
            return false;
        }
    });

    if ($.fancybox) {
        $("a.fancybox").fancybox({
            'speedIn'		:	600,
            'speedOut'		:	200,
            'transitionIn'  :   'elastic',
            'opacity'	    :   true,
            'titleShow' 	: 	false
        });
    }

    $('a.print_link').click(function(e) {
        e.preventDefault();
        window.print();
        return false;
    });
});


$(function(){
    // (Ne peut pas être dans document_ready, car doit passer après le file de language du datepicker)
    if ($.datepicker) {
        $('.calendar').datepicker({
            showAnim: 'fadeIn',
            dateFormat: 'yy-mm-dd',
            firstDay: 7
        });

        $.datepicker.setDefaults($.datepicker.regional[locale == 'en' ? '' : locale]);
    }
});