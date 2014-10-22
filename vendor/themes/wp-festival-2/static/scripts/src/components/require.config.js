var components = {
    "packages": [
        {
            "name": "dotdotdot",
            "main": "dotdotdot-built.js"
        },
        {
            "name": "fancybox2",
            "main": "fancybox2-built.js"
        },
        {
            "name": "fitvids",
            "main": "fitvids-built.js"
        },
        {
            "name": "imagesloaded",
            "main": "imagesloaded-built.js"
        },
        {
            "name": "iscroll",
            "main": "iscroll-built.js"
        },
        {
            "name": "jquery",
            "main": "jquery-built.js"
        },
        {
            "name": "jquery-selectBoxIt",
            "main": "jquery-selectBoxIt-built.js"
        },
        {
            "name": "knockout",
            "main": "knockout-built.js"
        },
        {
            "name": "malihu-custom-scrollbar-plugin",
            "main": "malihu-custom-scrollbar-plugin-built.js"
        },
        {
            "name": "masonry",
            "main": "masonry-built.js"
        },
        {
            "name": "sticky",
            "main": "sticky-built.js"
        },
        {
            "name": "moment",
            "main": "moment-built.js"
        },
        {
            "name": "lib-model",
            "main": "lib-model-built.js"
        },
        {
            "name": "utility",
            "main": "utility-built.js"
        }
    ],
    "baseUrl": "static/scripts/src",
    "name": "app",
    "out": "static/scripts/app.js",
    "mainConfigFile": "static/scripts/src/components/require.config.js",
    "paths": {
        "jquery": "lib/jquery"
    },
    "uglify": {
        "no_mangle": true
    },
    "shim": {
        "third-party/imagelightbox.min": {
            "deps": [
                "jquery"
            ]
        },
        "components/dotdotdot/dotdotdot-built": {
            "deps": [
                "jquery"
            ]
        },
        "'components/sticky/jquery.sticky": {
            "deps": [
                "jquery"
            ]
        },
        "third-party/imagelightbox": {
            "deps": [
                "jquery"
            ]
        },
        "components/fancybox2/fancybox2-built": {
            "deps": [
                "jquery"
            ]
        },
        "components/fitvids/fitvids-built": {
            "deps": [
                "jquery"
            ]
        },
        "components/jquery-selectBoxIt/jquery-selectBoxIt-built": {
            "deps": [
                "jquery",
                "jquery.ui"
            ]
        },
        "third-party/jquery-ui-1.11.1.custom/jquery-ui.min": {
            "deps": [
                "jquery"
            ]
        }
    }
};
if (typeof require !== "undefined" && require.config) {
    require.config(components);
} else {
    var require = components;
}
if (typeof exports !== "undefined" && typeof module !== "undefined") {
    module.exports = components;
}