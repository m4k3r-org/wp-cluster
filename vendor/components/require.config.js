var components = {
    "packages": [
        {
            "name": "lib-model",
            "main": "lib-model-built.js"
        },
        {
            "name": "utility",
            "main": "utility-built.js"
        },
        {
            "name": "caroufredsel",
            "main": "caroufredsel-built.js"
        },
        {
            "name": "dotdotdot",
            "main": "dotdotdot-built.js"
        },
        {
            "name": "fancybox2",
            "main": "fancybox2-built.js"
        },
        {
            "name": "iscroll",
            "main": "iscroll-built.js"
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
            "name": "raygun4js",
            "main": "raygun4js-built.js"
        }
    ],
    "baseUrl": "components"
};
if (typeof require !== "undefined" && require.config) {
    require.config(components);
} else {
    var require = components;
}
if (typeof exports !== "undefined" && typeof module !== "undefined") {
    module.exports = components;
}