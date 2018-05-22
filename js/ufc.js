jQuery(document).ready(function($) {

    jQuery('.ufc_associated').each(function() {

        var element = jQuery(this);

        var parent = element.parent();

        parent.click(function() {
            if (jQuery("#ufc_content_" + element.val()).hasClass("ufc_hide_" + element.val())) {
                jQuery("#ufc_content_" + element.val()).addClass("ufc_show_" + element.val());
                jQuery("#ufc_content_" + element.val()).removeClass("ufc_hide_" + element.val());
            } else {
                jQuery("#ufc_content_" + element.val()).addClass("ufc_hide_" + element.val());
                jQuery("#ufc_content_" + element.val()).removeClass("ufc_show_" + element.val());
            }
        });

    });


    window.onscroll = function() { scrollFunction() };

    function scrollFunction() {
        if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
            jQuery("#scroll_back_btn").fadeIn(400);
        } else {
            jQuery("#scroll_back_btn").fadeOut(400);
        }
    }

});