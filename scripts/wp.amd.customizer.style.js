!function($, l10n) {
    function createStyleContainer() {
        if ($("#wp_amd_style_preview_container").length) return null;
        var _element = $('<style type="text/css" id="wp_amd_style_preview_container"></style>');
        $("head").append(_element);
    }
    function updateStyles(style) {
        var d = document.getElementById(l10n.id);
        d && d.parentNode.removeChild(d), $("head #wp_amd_style_preview_container").text(style);
    }
    wp.customize(l10n.name, function(style) {
        var intent;
        createStyleContainer(), style.bind(function(style) {
            console.log("stylesChanged", style), window.clearTimeout(intent), intent = window.setTimeout(function() {
                updateStyles(style);
            }, 200);
        });
    });
}(jQuery, wp_amd_themecustomizer);