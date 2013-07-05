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

    // Avoid form submission with defaults values inside text inputs
    $("form").submit(function(e) {

        inputs = $(this).find("input:text");

        inputs.each(function(){
            if($(this).attr('alt') == $(this).val()){
                $(this).val('');
                $(this).focus();
                e.preventDefault();
                return false;
            }
        });
    });

    populate_inputs();

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


/*
 * Vérifier si une valeur alt="" est fournis au champs input et textarea
 * et si le input/textarea a l'attribut value vide, mettre la valeur du
 * alt dans l'attribut value.
 *
 * Était direct dans le .ready jusqu'a se que je remarque qu'il ne
 * s'appliquait pas au formulaire loader en ajax, maintenant on n'a qu'a
 * caller populate_inputs() sur le callback du load.
 */
function populate_inputs() {
    $("input,textarea").each(function() {
        if ($(this).attr("alt") != "") {
            $(this).addClass("unfocus");
            if ($(this).val() == "") {
                $(this).val($(this).attr("alt"));
            }
        }
    });

    $("input,textarea").focus(function() {
        if ($(this).attr("alt") != "") {
            if ($(this).val() == $(this).attr("alt")) {
                $(this).val("");
                $(this).removeClass("unfocus");
            }
        }
    });

    $("input,textarea").blur(function() {
        if ($(this).attr("alt") != "") {
            if ($(this).val() == "") {
                $(this).val($(this).attr("alt"));
                $(this).addClass("unfocus");
            }
        }
    });
}

/*
 * @author http://javascript.internet.com/forms/currency-format.html
 * @author Emilie (ajustements pour la culture)
 * @version 2010/07/06
 */
function formatCurrency(num,culture) {
    if (culture == "fr") {
        separateur_milliers = " ";
        separateur_decimales = ",";
    }
    else {
        separateur_milliers = ",";
        separateur_decimales = ".";
    }

    num = num.toString().replace(/\$|\,/g,'');

    if(isNaN(num)) {
        num = "0";
    }

    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num*100+0.50000000001);

    cents = num%100;
    num = Math.floor(num/100).toString();

    if(cents<10) {
        cents = "0" + cents;
    }

    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++) {
        num = num.substring(0,num.length-(4*i+3)) + separateur_milliers + num.substring(num.length-(4*i+3));
    }

    num_final = num + separateur_decimales + cents;

    if (culture == "fr") {
        return (((sign)?'':'-') + num_final + ' $');
    }
    else {
        return (((sign)?'':'-') + '$' + num_final);
    }
}