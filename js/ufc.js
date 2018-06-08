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
            var id = parent.find(".ufc_popup_id").val();

            if (localStorage.getItem("popcookie" + id) != "1" || parent.hasClass("ufc_type_button")) {
                if (document.body.scrollTop > element.val() || document.documentElement.scrollTop > element.val()) {
                    parent.fadeIn(800);

                    if (parent.hasClass("ufc_type_popup")) {
                        jQuery(".background_popup").css("height", jQuery("html").height());
                        jQuery(".background_popup").fadeIn(800);
                    }
                }
            }

            if (jQuery(window).scrollTop() + jQuery(window).height() == jQuery(document).height() && parent.hasClass("ufc_type_button")) {
                parent.fadeOut(800);
            }
        });
    }

    jQuery('.ufc_popup').each(function() {
        var element = jQuery(this);
        var id = element.find(".ufc_popup_id").val();

        if (localStorage.getItem("popcookie" + id) != "1") {

            if (element.find(".ufc_scroll_code").val() == null)
                jQuery("#ufc_content_" + id).fadeIn(800);
        }

        element.find(".ufc_popup_btn").click(function() {
            localStorage.setItem("popcookie" + id, "1");
            jQuery("#ufc_content_" + id).fadeOut(800);
            jQuery("#background_popup_" + id).fadeOut(800);
        });

    });
});