jQuery(document).ready(function($) {

    jQuery('.ufc_associated').each(function() {

        var element = jQuery(this);

        var parent = element.parent();

        parent.click(function() {
            if (jQuery("#ufc_content_" + element.val()).hasClass("ufc_hide_" + element.val())) {
                jQuery("#ufc_content_" + element.val()).addClass("ufc_show_" + element.val());
                jQuery("#ufc_content_" + element.val()).removeClass("ufc_hide_" + element.val());
                parent.find(".btn_hide").css("display", "block");
                parent.find(".btn_show").css("display", "none");
            } else {
                jQuery("#ufc_content_" + element.val()).addClass("ufc_hide_" + element.val());
                jQuery("#ufc_content_" + element.val()).removeClass("ufc_show_" + element.val());
                parent.find(".btn_hide").css("display", "none");
                parent.find(".btn_show").css("display", "block");
            }
        });

    });

    window.onscroll = function() { scrollFunction() };

    function scrollFunction() {

        jQuery('.ufc_scroll_code').each(function() {
            var element = jQuery(this);
            var parent = element.parent();

            if (document.body.scrollTop > element.val() || document.documentElement.scrollTop > element.val()) {
                parent.fadeIn(400);
            } else {
                parent.fadeOut(400);
            }
        });
    }
});