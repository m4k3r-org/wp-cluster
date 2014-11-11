var components = {
    "packages": [
        {
            "name": "lib-js-elastic-filter",
            "main": "lib-js-elastic-filter-built.js"
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