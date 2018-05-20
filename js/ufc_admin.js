jQuery(document).ready(function(jQuery) {

    jQuery(".wrap_fw_form .jscolor").spectrum({
        showInput: true,
        showInitial: true,
        allowEmpty: true,
        showAlpha: true,
        lat: true,
        preferredFormat: "rgb",
        cancelText: "Cancelar",
        chooseText: "Elegir"
    });


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

    jQuery(".tab_links").click(function() {
        jQuery(".tab_content").css("display", "none");

        jQuery(jQuery(this).attr("title")).css("display", "block");

        jQuery(".tab_links").removeClass("active_tab");

        jQuery(this).addClass("active_tab");
    });

    jQuery(".add_tab").click(function() {
        jQuery("#total_fw").val(parseInt(jQuery("#total_fw").val()) + 1);

        jQuery("#fw_form").submit();
    });


    jQuery(jQuery("#list_options li").first().attr("title")).css("display", "block");

    var elems = Array.prototype.slice.call(document.querySelectorAll('.wrap_fw_form .js-switch'));

    elems.forEach(function(html) {
        var switchery = new Switchery(html);
    });

    if (jQuery("#ufc_type").val() == "button")
        jQuery(".button_option").css("display", "block");

    if (jQuery("#ufc_type").val() == "content")
        jQuery(".content_option").css("display", "block");

    if (jQuery("#ufc_type").val() == "popup")
        jQuery(".popup_option").css("display", "block");
});