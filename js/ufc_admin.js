jQuery(document).ready(function(jQuery) {

    jQuery("#ufc_type").change(function() {
        jQuery(".button_option").css("display", "none");
        jQuery(".content_option").css("display", "none");
        jQuery(".popup_option").css("display", "none");

        if (jQuery(this).val() == "button")
            jQuery(".button_option").css("display", "block");

        if (jQuery(this).val() == "content")
            jQuery(".content_option").css("display", "block");

        if (jQuery(this).val() == "popup")
            jQuery(".popup_option").css("display", "block");
    });

    jQuery('#zone-code2 select').select2({ tags: true });

    if (jQuery("#ufc_type").val() == "button")
        jQuery(".button_option").css("display", "block");

    if (jQuery("#ufc_type").val() == "content")
        jQuery(".content_option").css("display", "block");

    if (jQuery("#ufc_type").val() == "popup")
        jQuery(".popup_option").css("display", "block");
});