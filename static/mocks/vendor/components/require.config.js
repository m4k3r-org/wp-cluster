var components = {
    "packages": [
        {
            "name": "caroufredsel",
            "main": "caroufredsel-built.js"
        },
        {
            "name": "dotdotdot",
            "main": "dotdotdot-built.js"
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
            "name": "knockout",
            "main": "knockout-built.js"
        },
        {
            "name": "masonry",
            "main": "masonry-built.js"
        },
        {
            "name": "sticky",
            "main": "sticky-built.js"
        }
    ],
    "baseUrl": "application/static/scripts/src",
    "mainConfigFile": "vendor/components/require.config.js",
    "paths": {
        "components": "../../../../vendor/components",
        "jquery": "../../../../vendor/components/jquery/jquery"
    },
    "shim": {
        "components/dotdotdot": {
            "deps": [
                "jquery"
            ],
            "exports": "dotdotdot"
        },
        "components/stickem": {
            "deps": [
                "jquery"
            ],
            "exports": "stickem"
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