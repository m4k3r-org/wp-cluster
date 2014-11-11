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
            "name": "jquery-ui",
            "main": "jquery-ui-built.js"
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
            "name": "lib-requires",
            "main": "lib-requires-built.js"
        },
        {
            "name": "utility",
            "main": "utility-built.js"
        }
    ],
    "shim": {
        "jquery-ui": {
            "deps": [
                "jquery"
            ],
            "exports": "jQuery"
        },
        "third-party/imagelightbox.min": {
            "deps": [
                "jquery"
            ]
        },
        "components/dotdotdot/src/js/jquery.dotdotdot.min": {
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
                "components/jquery-ui/jquery-ui-built"
            ]
        },
        "components/jquery-ui/jquery-ui-built": {
            "deps": [
                "jquery"
            ]
        }
    },
    "baseUrl": "static/scripts/src",
    "mainConfigFile": "static/scripts/src/components/require.config.js",
    "name": "app",
    "insertRequire": [
        "app"
    ],
    "out": "static/scripts/app.js",
    "paths": {
        "jquery": "lib/jquery"
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