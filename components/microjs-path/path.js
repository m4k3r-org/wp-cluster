var current = null,
    root = null,
    rescue = null,
    previous = null,
    defined = {};
var Path = {
    map: function (path) {
        if (defined.hasOwnProperty(path)) {
            return defined[path];
        } else {
            return new Route(path);
        }
    },
    root: function (path) {
        root = path;
    },
    rescue: function (fn) {
        rescue = fn;
    },
    history: {
        initial:{}, // Empty container for "Initial Popstate" checking variables.
        pushState: function(state, title, path){
            if(Path.history.supported){
                if(Path.dispatch(path)){
                    history.pushState(state, title, path);
                }
            } else {
                if(Path.history.fallback){
                    location.hash = "#" + path;
                }
            }
        },
        popState: function(event){
            var initialPop = !Path.history.initial.popped && location.href == Path.history.initial.URL;
            Path.history.initial.popped = true;
            if(initialPop) return;
            Path.dispatch(document.location.pathname);
        },
        listen: function(fallback){
            Path.history.supported = !!(window.history && window.history.pushState);
            Path.history.fallback  = fallback;

            if(Path.history.supported){
                Path.history.initial.popped = false;//('state' in window.history);
                Path.history.initial.URL = location.href;
                window.onpopstate = Path.history.popState;
            } else {
                if(Path.history.fallback){
                    for(route in defined){
                        if(route.charAt(0) != "#"){
                          defined["#"+route] = defined[route];
                          defined["#"+route].path = "#"+route;
                        }
                    }
                    Path.listen();
                }
            }
        }
    },
    match: function (path, parameterize) {
        var params = {}, route = null, possible_routes, slice, i, j, compare;
        for (route in defined) {
            if (route !== null && route !== undefined) {
                route = defined[route];
                possible_routes = route.partition();
                for (j = 0; j < possible_routes.length; j++) {
                    slice = possible_routes[j];
                    compare = path;
                    if (slice.search(/:/) > 0) {
                        for (i = 0; i < slice.split("/").length; i++) {
                            if ((i < compare.split("/").length) && (slice.split("/")[i].charAt(0) === ":")) {
                                params[slice.split('/')[i].replace(/:/, '')] = compare.split("/")[i];
                                compare = compare.replace(compare.split("/")[i], slice.split("/")[i]);
                            }
                        }
                    }
                    if (slice === compare) {
                        if (parameterize) {
                            route.params = params;
                        }
                        return route;
                    }
                }
            }
        }
        return null;
    },
    dispatch: function (passed_route) {
        var previous_route, matched_route;
        if (current !== passed_route) {
            previous = current;
            current = passed_route;
            matched_route = Path.match(passed_route, true);

            if (previous) {
                previous_route = Path.match(previous);
                if (previous_route !== null && previous_route.do_exit !== null) {
                    previous_route.do_exit();
                }
            }

            if (matched_route !== null) {
                matched_route.run();
                return true;
            } else {
                if (rescue !== null) {
                    rescue();
                }
            }
        }
    },
    listen: function () {
        var fn = function(){ Path.dispatch(location.hash); }

        if (location.hash === '' || location.hash === '#') {
            if (root !== null) {
                location.hash = root;
            }
        }
        
        window.onhashchange = fn;

        if(location.hash !== "") {
            Path.dispatch(location.hash);
        }
    }
};


function Route(path) {
    this.path = path;
    this.action = null;
    this.do_enter = [];
    this.do_exit = null;
    this.params = {};
    defined[path] = this;
}
Route.prototype = {
    to: function (fn) {
        this.action = fn;
        return this;
    },
    enter: function (fns) {
        if (fns instanceof Array) {
            this.do_enter = this.do_enter.concat(fns);
        } else {
            this.do_enter.push(fns);
        }
        return this;
    },
    exit: function (fn) {
        this.do_exit = fn;
        return this;
    },
    partition: function () {
        var parts = [], options = [], re = /\(([^}]+?)\)/g, text, i;
        while (text = re.exec(this.path)) {
            parts.push(text[1]);
        }
        options.push(this.path.split("(")[0]);
        for (i = 0; i < parts.length; i++) {
            options.push(options[options.length - 1] + parts[i]);
        }
        return options;
    },
    run: function () {
        var halt_execution = false, i, result, previous;

        if (defined[this.path].hasOwnProperty("do_enter")) {
            if (defined[this.path].do_enter.length > 0) {
                for (i = 0; i < defined[this.path].do_enter.length; i++) {
                    result = defined[this.path].do_enter[i].call(this);
                    if (result === false) {
                        halt_execution = true;
                        break;
                    }
                }
            }
        }
        if (!halt_execution) {
            defined[this.path].action();
        }
    }
};

if (typeof module === 'object' && module && module.exports)
    module.exports = Path;