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
            var id = parseInt(parent.find(".ufc_popup_id").val());

            if (!Number.isInteger(id) || localStorage.getItem("popcookie" + id) != "1") {
                if (document.body.scrollTop > element.val() || document.documentElement.scrollTop > element.val()) {
                    parent.fadeIn(600);
                }
            }
        });
    }

    jQuery('.ufc_popup').each(function() {
        var element = jQuery(this);
        var id = element.find(".ufc_popup_id").val();

        if (localStorage.getItem("popcookie" + id) != "1") {

            if (element.find(".ufc_scroll_code").val() == null)
                jQuery("#ufc_content_" + id).fadeIn(1000);
        }

        if (typeof id !== true && element.find(".ufc_scroll_code").val() == "")
            jQuery("#ufc_content_" + id).fadeIn(1000);

        element.find(".ufc_popup_btn").click(function() {
            localStorage.setItem("popcookie" + id, "1");
        });

    });
});