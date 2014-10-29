/** vim: et:ts=4:sw=4:sts=4
 * @license RequireJS 2.1.5 Copyright (c) 2010-2012, The Dojo Foundation All Rights Reserved.
 * Available via the MIT or new BSD license.
 * see: http://github.com/jrburke/requirejs for details
 */
//Not using strict: uneven strict support in browsers, #392, and causes
//problems with requirejs.exec()/transpiler plugins that may not be strict.
/*jslint regexp: true, nomen: true, sloppy: true */
/*global window, navigator, document, importScripts, setTimeout, opera */

var requirejs, require, define;
(function (global) {
    var req, s, head, baseElement, dataMain, src,
        interactiveScript, currentlyAddingScript, mainScript, subPath,
        version = '2.1.5',
        commentRegExp = /(\/\*([\s\S]*?)\*\/|([^:]|^)\/\/(.*)$)/mg,
        cjsRequireRegExp = /[^.]\s*require\s*\(\s*["']([^'"\s]+)["']\s*\)/g,
        jsSuffixRegExp = /\.js$/,
        currDirRegExp = /^\.\//,
        op = Object.prototype,
        ostring = op.toString,
        hasOwn = op.hasOwnProperty,
        ap = Array.prototype,
        apsp = ap.splice,
        isBrowser = !!(typeof window !== 'undefined' && navigator && document),
        isWebWorker = !isBrowser && typeof importScripts !== 'undefined',
        //PS3 indicates loaded and complete, but need to wait for complete
        //specifically. Sequence is 'loading', 'loaded', execution,
        // then 'complete'. The UA check is unfortunate, but not sure how
        //to feature test w/o causing perf issues.
        readyRegExp = isBrowser && navigator.platform === 'PLAYSTATION 3' ?
                      /^complete$/ : /^(complete|loaded)$/,
        defContextName = '_',
        //Oh the tragedy, detecting opera. See the usage of isOpera for reason.
        isOpera = typeof opera !== 'undefined' && opera.toString() === '[object Opera]',
        contexts = {},
        cfg = {},
        globalDefQueue = [],
        useInteractive = false;

    function isFunction(it) {
        return ostring.call(it) === '[object Function]';
    }

    function isArray(it) {
        return ostring.call(it) === '[object Array]';
    }

    /**
     * Helper function for iterating over an array. If the func returns
     * a true value, it will break out of the loop.
     */
    function each(ary, func) {
        if (ary) {
            var i;
            for (i = 0; i < ary.length; i += 1) {
                if (ary[i] && func(ary[i], i, ary)) {
                    break;
                }
            }
        }
    }

    /**
     * Helper function for iterating over an array backwards. If the func
     * returns a true value, it will break out of the loop.
     */
    function eachReverse(ary, func) {
        if (ary) {
            var i;
            for (i = ary.length - 1; i > -1; i -= 1) {
                if (ary[i] && func(ary[i], i, ary)) {
                    break;
                }
            }
        }
    }

    function hasProp(obj, prop) {
        return hasOwn.call(obj, prop);
    }

    function getOwn(obj, prop) {
        return hasProp(obj, prop) && obj[prop];
    }

    /**
     * Cycles over properties in an object and calls a function for each
     * property value. If the function returns a truthy value, then the
     * iteration is stopped.
     */
    function eachProp(obj, func) {
        var prop;
        for (prop in obj) {
            if (hasProp(obj, prop)) {
                if (func(obj[prop], prop)) {
                    break;
                }
            }
        }
    }

    /**
     * Simple function to mix in properties from source into target,
     * but only if target does not already have a property of the same name.
     */
    function mixin(target, source, force, deepStringMixin) {
        if (source) {
            eachProp(source, function (value, prop) {
                if (force || !hasProp(target, prop)) {
                    if (deepStringMixin && typeof value !== 'string') {
                        if (!target[prop]) {
                            target[prop] = {};
                        }
                        mixin(target[prop], value, force, deepStringMixin);
                    } else {
                        target[prop] = value;
                    }
                }
            });
        }
        return target;
    }

    //Similar to Function.prototype.bind, but the 'this' object is specified
    //first, since it is easier to read/figure out what 'this' will be.
    function bind(obj, fn) {
        return function () {
            return fn.apply(obj, arguments);
        };
    }

    function scripts() {
        return document.getElementsByTagName('script');
    }

    //Allow getting a global that expressed in
    //dot notation, like 'a.b.c'.
    function getGlobal(value) {
        if (!value) {
            return value;
        }
        var g = global;
        each(value.split('.'), function (part) {
            g = g[part];
        });
        return g;
    }

    /**
     * Constructs an error with a pointer to an URL with more information.
     * @param {String} id the error ID that maps to an ID on a web page.
     * @param {String} message human readable error.
     * @param {Error} [err] the original error, if there is one.
     *
     * @returns {Error}
     */
    function makeError(id, msg, err, requireModules) {
        var e = new Error(msg + '\nhttp://requirejs.org/docs/errors.html#' + id);
        e.requireType = id;
        e.requireModules = requireModules;
        if (err) {
            e.originalError = err;
        }
        return e;
    }

    if (typeof define !== 'undefined') {
        //If a define is already in play via another AMD loader,
        //do not overwrite.
        return;
    }

    if (typeof requirejs !== 'undefined') {
        if (isFunction(requirejs)) {
            //Do not overwrite and existing requirejs instance.
            return;
        }
        cfg = requirejs;
        requirejs = undefined;
    }

    //Allow for a require config object
    if (typeof require !== 'undefined' && !isFunction(require)) {
        //assume it is a config object.
        cfg = require;
        require = undefined;
    }

    function newContext(contextName) {
        var inCheckLoaded, Module, context, handlers,
            checkLoadedTimeoutId,
            config = {
                //Defaults. Do not set a default for map
                //config to speed up normalize(), which
                //will run faster if there is no default.
                waitSeconds: 7,
                baseUrl: './',
                paths: {},
                pkgs: {},
                shim: {},
                config: {}
            },
            registry = {},
            //registry of just enabled modules, to speed
            //cycle breaking code when lots of modules
            //are registered, but not activated.
            enabledRegistry = {},
            undefEvents = {},
            defQueue = [],
            defined = {},
            urlFetched = {},
            requireCounter = 1,
            unnormalizedCounter = 1;

        /**
         * Trims the . and .. from an array of path segments.
         * It will keep a leading path segment if a .. will become
         * the first path segment, to help with module name lookups,
         * which act like paths, but can be remapped. But the end result,
         * all paths that use this function should look normalized.
         * NOTE: this method MODIFIES the input array.
         * @param {Array} ary the array of path segments.
         */
        function trimDots(ary) {
            var i, part;
            for (i = 0; ary[i]; i += 1) {
                part = ary[i];
                if (part === '.') {
                    ary.splice(i, 1);
                    i -= 1;
                } else if (part === '..') {
                    if (i === 1 && (ary[2] === '..' || ary[0] === '..')) {
                        //End of the line. Keep at least one non-dot
                        //path segment at the front so it can be mapped
                        //correctly to disk. Otherwise, there is likely
                        //no path mapping for a path starting with '..'.
                        //This can still fail, but catches the most reasonable
                        //uses of ..
                        break;
                    } else if (i > 0) {
                        ary.splice(i - 1, 2);
                        i -= 2;
                    }
                }
            }
        }

        /**
         * Given a relative module name, like ./something, normalize it to
         * a real name that can be mapped to a path.
         * @param {String} name the relative name
         * @param {String} baseName a real name that the name arg is relative
         * to.
         * @param {Boolean} applyMap apply the map config to the value. Should
         * only be done if this normalization is for a dependency ID.
         * @returns {String} normalized name
         */
        function normalize(name, baseName, applyMap) {
            var pkgName, pkgConfig, mapValue, nameParts, i, j, nameSegment,
                foundMap, foundI, foundStarMap, starI,
                baseParts = baseName && baseName.split('/'),
                normalizedBaseParts = baseParts,
                map = config.map,
                starMap = map && map['*'];

            //Adjust any relative paths.
            if (name && name.charAt(0) === '.') {
                //If have a base name, try to normalize against it,
                //otherwise, assume it is a top-level require that will
                //be relative to baseUrl in the end.
                if (baseName) {
                    if (getOwn(config.pkgs, baseName)) {
                        //If the baseName is a package name, then just treat it as one
                        //name to concat the name with.
                        normalizedBaseParts = baseParts = [baseName];
                    } else {
                        //Convert baseName to array, and lop off the last part,
                        //so that . matches that 'directory' and not name of the baseName's
                        //module. For instance, baseName of 'one/two/three', maps to
                        //'one/two/three.js', but we want the directory, 'one/two' for
                        //this normalization.
                        normalizedBaseParts = baseParts.slice(0, baseParts.length - 1);
                    }

                    name = normalizedBaseParts.concat(name.split('/'));
                    trimDots(name);

                    //Some use of packages may use a . path to reference the
                    //'main' module name, so normalize for that.
                    pkgConfig = getOwn(config.pkgs, (pkgName = name[0]));
                    name = name.join('/');
                    if (pkgConfig && name === pkgName + '/' + pkgConfig.main) {
                        name = pkgName;
                    }
                } else if (name.indexOf('./') === 0) {
                    // No baseName, so this is ID is resolved relative
                    // to baseUrl, pull off the leading dot.
                    name = name.substring(2);
                }
            }

            //Apply map config if available.
            if (applyMap && map && (baseParts || starMap)) {
                nameParts = name.split('/');

                for (i = nameParts.length; i > 0; i -= 1) {
                    nameSegment = nameParts.slice(0, i).join('/');

                    if (baseParts) {
                        //Find the longest baseName segment match in the config.
                        //So, do joins on the biggest to smallest lengths of baseParts.
                        for (j = baseParts.length; j > 0; j -= 1) {
                            mapValue = getOwn(map, baseParts.slice(0, j).join('/'));

                            //baseName segment has config, find if it has one for
                            //this name.
                            if (mapValue) {
                                mapValue = getOwn(mapValue, nameSegment);
                                if (mapValue) {
                                    //Match, update name to the new value.
                                    foundMap = mapValue;
                                    foundI = i;
                                    break;
                                }
                            }
                        }
                    }

                    if (foundMap) {
                        break;
                    }

                    //Check for a star map match, but just hold on to it,
                    //if there is a shorter segment match later in a matching
                    //config, then favor over this star map.
                    if (!foundStarMap && starMap && getOwn(starMap, nameSegment)) {
                        foundStarMap = getOwn(starMap, nameSegment);
                        starI = i;
                    }
                }

                if (!foundMap && foundStarMap) {
                    foundMap = foundStarMap;
                    foundI = starI;
                }

                if (foundMap) {
                    nameParts.splice(0, foundI, foundMap);
                    name = nameParts.join('/');
                }
            }

            return name;
        }

        function removeScript(name) {
            if (isBrowser) {
                each(scripts(), function (scriptNode) {
                    if (scriptNode.getAttribute('data-requiremodule') === name &&
                            scriptNode.getAttribute('data-requirecontext') === context.contextName) {
                        scriptNode.parentNode.removeChild(scriptNode);
                        return true;
                    }
                });
            }
        }

        function hasPathFallback(id) {
            var pathConfig = getOwn(config.paths, id);
            if (pathConfig && isArray(pathConfig) && pathConfig.length > 1) {
                removeScript(id);
                //Pop off the first array value, since it failed, and
                //retry
                pathConfig.shift();
                context.require.undef(id);
                context.require([id]);
                return true;
            }
        }

        //Turns a plugin!resource to [plugin, resource]
        //with the plugin being undefined if the name
        //did not have a plugin prefix.
        function splitPrefix(name) {
            var prefix,
                index = name ? name.indexOf('!') : -1;
            if (index > -1) {
                prefix = name.substring(0, index);
                name = name.substring(index + 1, name.length);
            }
            return [prefix, name];
        }

        /**
         * Creates a module mapping that includes plugin prefix, module
         * name, and path. If parentModuleMap is provided it will
         * also normalize the name via require.normalize()
         *
         * @param {String} name the module name
         * @param {String} [parentModuleMap] parent module map
         * for the module name, used to resolve relative names.
         * @param {Boolean} isNormalized: is the ID already normalized.
         * This is true if this call is done for a define() module ID.
         * @param {Boolean} applyMap: apply the map config to the ID.
         * Should only be true if this map is for a dependency.
         *
         * @returns {Object}
         */
        function makeModuleMap(name, parentModuleMap, isNormalized, applyMap) {
            var url, pluginModule, suffix, nameParts,
                prefix = null,
                parentName = parentModuleMap ? parentModuleMap.name : null,
                originalName = name,
                isDefine = true,
                normalizedName = '';

            //If no name, then it means it is a require call, generate an
            //internal name.
            if (!name) {
                isDefine = false;
                name = '_@r' + (requireCounter += 1);
            }

            nameParts = splitPrefix(name);
            prefix = nameParts[0];
            name = nameParts[1];

            if (prefix) {
                prefix = normalize(prefix, parentName, applyMap);
                pluginModule = getOwn(defined, prefix);
            }

            //Account for relative paths if there is a base name.
            if (name) {
                if (prefix) {
                    if (pluginModule && pluginModule.normalize) {
                        //Plugin is loaded, use its normalize method.
                        normalizedName = pluginModule.normalize(name, function (name) {
                            return normalize(name, parentName, applyMap);
                        });
                    } else {
                        normalizedName = normalize(name, parentName, applyMap);
                    }
                } else {
                    //A regular module.
                    normalizedName = normalize(name, parentName, applyMap);

                    //Normalized name may be a plugin ID due to map config
                    //application in normalize. The map config values must
                    //already be normalized, so do not need to redo that part.
                    nameParts = splitPrefix(normalizedName);
                    prefix = nameParts[0];
                    normalizedName = nameParts[1];
                    isNormalized = true;

                    url = context.nameToUrl(normalizedName);
                }
            }

            //If the id is a plugin id that cannot be determined if it needs
            //normalization, stamp it with a unique ID so two matching relative
            //ids that may conflict can be separate.
            suffix = prefix && !pluginModule && !isNormalized ?
                     '_unnormalized' + (unnormalizedCounter += 1) :
                     '';

            return {
                prefix: prefix,
                name: normalizedName,
                parentMap: parentModuleMap,
                unnormalized: !!suffix,
                url: url,
                originalName: originalName,
                isDefine: isDefine,
                id: (prefix ?
                        prefix + '!' + normalizedName :
                        normalizedName) + suffix
            };
        }

        function getModule(depMap) {
            var id = depMap.id,
                mod = getOwn(registry, id);

            if (!mod) {
                mod = registry[id] = new context.Module(depMap);
            }

            return mod;
        }

        function on(depMap, name, fn) {
            var id = depMap.id,
                mod = getOwn(registry, id);

            if (hasProp(defined, id) &&
                    (!mod || mod.defineEmitComplete)) {
                if (name === 'defined') {
                    fn(defined[id]);
                }
            } else {
                getModule(depMap).on(name, fn);
            }
        }

        function onError(err, errback) {
            var ids = err.requireModules,
                notified = false;

            if (errback) {
                errback(err);
            } else {
                each(ids, function (id) {
                    var mod = getOwn(registry, id);
                    if (mod) {
                        //Set error on module, so it skips timeout checks.
                        mod.error = err;
                        if (mod.events.error) {
                            notified = true;
                            mod.emit('error', err);
                        }
                    }
                });

                if (!notified) {
                    req.onError(err);
                }
            }
        }

        /**
         * Internal method to transfer globalQueue items to this context's
         * defQueue.
         */
        function takeGlobalQueue() {
            //Push all the globalDefQueue items into the context's defQueue
            if (globalDefQueue.length) {
                //Array splice in the values since the context code has a
                //local var ref to defQueue, so cannot just reassign the one
                //on context.
                apsp.apply(defQueue,
                           [defQueue.length - 1, 0].concat(globalDefQueue));
                globalDefQueue = [];
            }
        }

        handlers = {
            'require': function (mod) {
                if (mod.require) {
                    return mod.require;
                } else {
                    return (mod.require = context.makeRequire(mod.map));
                }
            },
            'exports': function (mod) {
                mod.usingExports = true;
                if (mod.map.isDefine) {
                    if (mod.exports) {
                        return mod.exports;
                    } else {
                        return (mod.exports = defined[mod.map.id] = {});
                    }
                }
            },
            'module': function (mod) {
                if (mod.module) {
                    return mod.module;
                } else {
                    return (mod.module = {
                        id: mod.map.id,
                        uri: mod.map.url,
                        config: function () {
                            return (config.config && getOwn(config.config, mod.map.id)) || {};
                        },
                        exports: defined[mod.map.id]
                    });
                }
            }
        };

        function cleanRegistry(id) {
            //Clean up machinery used for waiting modules.
            delete registry[id];
            delete enabledRegistry[id];
        }

        function breakCycle(mod, traced, processed) {
            var id = mod.map.id;

            if (mod.error) {
                mod.emit('error', mod.error);
            } else {
                traced[id] = true;
                each(mod.depMaps, function (depMap, i) {
                    var depId = depMap.id,
                        dep = getOwn(registry, depId);

                    //Only force things that have not completed
                    //being defined, so still in the registry,
                    //and only if it has not been matched up
                    //in the module already.
                    if (dep && !mod.depMatched[i] && !processed[depId]) {
                        if (getOwn(traced, depId)) {
                            mod.defineDep(i, defined[depId]);
                            mod.check(); //pass false?
                        } else {
                            breakCycle(dep, traced, processed);
                        }
                    }
                });
                processed[id] = true;
            }
        }

        function checkLoaded() {
            var map, modId, err, usingPathFallback,
                waitInterval = config.waitSeconds * 1000,
                //It is possible to disable the wait interval by using waitSeconds of 0.
                expired = waitInterval && (context.startTime + waitInterval) < new Date().getTime(),
                noLoads = [],
                reqCalls = [],
                stillLoading = false,
                needCycleCheck = true;

            //Do not bother if this call was a result of a cycle break.
            if (inCheckLoaded) {
                return;
            }

            inCheckLoaded = true;

            //Figure out the state of all the modules.
            eachProp(enabledRegistry, function (mod) {
                map = mod.map;
                modId = map.id;

                //Skip things that are not enabled or in error state.
                if (!mod.enabled) {
                    return;
                }

                if (!map.isDefine) {
                    reqCalls.push(mod);
                }

                if (!mod.error) {
                    //If the module should be executed, and it has not
                    //been inited and time is up, remember it.
                    if (!mod.inited && expired) {
                        if (hasPathFallback(modId)) {
                            usingPathFallback = true;
                            stillLoading = true;
                        } else {
                            noLoads.push(modId);
                            removeScript(modId);
                        }
                    } else if (!mod.inited && mod.fetched && map.isDefine) {
                        stillLoading = true;
                        if (!map.prefix) {
                            //No reason to keep looking for unfinished
                            //loading. If the only stillLoading is a
                            //plugin resource though, keep going,
                            //because it may be that a plugin resource
                            //is waiting on a non-plugin cycle.
                            return (needCycleCheck = false);
                        }
                    }
                }
            });

            if (expired && noLoads.length) {
                //If wait time expired, throw error of unloaded modules.
                err = makeError('timeout', 'Load timeout for modules: ' + noLoads, null, noLoads);
                err.contextName = context.contextName;
                return onError(err);
            }

            //Not expired, check for a cycle.
            if (needCycleCheck) {
                each(reqCalls, function (mod) {
                    breakCycle(mod, {}, {});
                });
            }

            //If still waiting on loads, and the waiting load is something
            //other than a plugin resource, or there are still outstanding
            //scripts, then just try back later.
            if ((!expired || usingPathFallback) && stillLoading) {
                //Something is still waiting to load. Wait for it, but only
                //if a timeout is not already in effect.
                if ((isBrowser || isWebWorker) && !checkLoadedTimeoutId) {
                    checkLoadedTimeoutId = setTimeout(function () {
                        checkLoadedTimeoutId = 0;
                        checkLoaded();
                    }, 50);
                }
            }

            inCheckLoaded = false;
        }

        Module = function (map) {
            this.events = getOwn(undefEvents, map.id) || {};
            this.map = map;
            this.shim = getOwn(config.shim, map.id);
            this.depExports = [];
            this.depMaps = [];
            this.depMatched = [];
            this.pluginMaps = {};
            this.depCount = 0;

            /* this.exports this.factory
               this.depMaps = [],
               this.enabled, this.fetched
            */
        };

        Module.prototype = {
            init: function (depMaps, factory, errback, options) {
                options = options || {};

                //Do not do more inits if already done. Can happen if there
                //are multiple define calls for the same module. That is not
                //a normal, common case, but it is also not unexpected.
                if (this.inited) {
                    return;
                }

                this.factory = factory;

                if (errback) {
                    //Register for errors on this module.
                    this.on('error', errback);
                } else if (this.events.error) {
                    //If no errback already, but there are error listeners
                    //on this module, set up an errback to pass to the deps.
                    errback = bind(this, function (err) {
                        this.emit('error', err);
                    });
                }

                //Do a copy of the dependency array, so that
                //source inputs are not modified. For example
                //"shim" deps are passed in here directly, and
                //doing a direct modification of the depMaps array
                //would affect that config.
                this.depMaps = depMaps && depMaps.slice(0);

                this.errback = errback;

                //Indicate this module has be initialized
                this.inited = true;

                this.ignore = options.ignore;

                //Could have option to init this module in enabled mode,
                //or could have been previously marked as enabled. However,
                //the dependencies are not known until init is called. So
                //if enabled previously, now trigger dependencies as enabled.
                if (options.enabled || this.enabled) {
                    //Enable this module and dependencies.
                    //Will call this.check()
                    this.enable();
                } else {
                    this.check();
                }
            },

            defineDep: function (i, depExports) {
                //Because of cycles, defined callback for a given
                //export can be called more than once.
                if (!this.depMatched[i]) {
                    this.depMatched[i] = true;
                    this.depCount -= 1;
                    this.depExports[i] = depExports;
                }
            },

            fetch: function () {
                if (this.fetched) {
                    return;
                }
                this.fetched = true;

                context.startTime = (new Date()).getTime();

                var map = this.map;

                //If the manager is for a plugin managed resource,
                //ask the plugin to load it now.
                if (this.shim) {
                    context.makeRequire(this.map, {
                        enableBuildCallback: true
                    })(this.shim.deps || [], bind(this, function () {
                        return map.prefix ? this.callPlugin() : this.load();
                    }));
                } else {
                    //Regular dependency.
                    return map.prefix ? this.callPlugin() : this.load();
                }
            },

            load: function () {
                var url = this.map.url;

                //Regular dependency.
                if (!urlFetched[url]) {
                    urlFetched[url] = true;
                    context.load(this.map.id, url);
                }
            },

            /**
             * Checks if the module is ready to define itself, and if so,
             * define it.
             */
            check: function () {
                if (!this.enabled || this.enabling) {
                    return;
                }

                var err, cjsModule,
                    id = this.map.id,
                    depExports = this.depExports,
                    exports = this.exports,
                    factory = this.factory;

                if (!this.inited) {
                    this.fetch();
                } else if (this.error) {
                    this.emit('error', this.error);
                } else if (!this.defining) {
                    //The factory could trigger another require call
                    //that would result in checking this module to
                    //define itself again. If already in the process
                    //of doing that, skip this work.
                    this.defining = true;

                    if (this.depCount < 1 && !this.defined) {
                        if (isFunction(factory)) {
                            //If there is an error listener, favor passing
                            //to that instead of throwing an error.
                            if (this.events.error) {
                                try {
                                    exports = context.execCb(id, factory, depExports, exports);
                                } catch (e) {
                                    err = e;
                                }
                            } else {
                                exports = context.execCb(id, factory, depExports, exports);
                            }

                            if (this.map.isDefine) {
                                //If setting exports via 'module' is in play,
                                //favor that over return value and exports. After that,
                                //favor a non-undefined return value over exports use.
                                cjsModule = this.module;
                                if (cjsModule &&
                                        cjsModule.exports !== undefined &&
                                        //Make sure it is not already the exports value
                                        cjsModule.exports !== this.exports) {
                                    exports = cjsModule.exports;
                                } else if (exports === undefined && this.usingExports) {
                                    //exports already set the defined value.
                                    exports = this.exports;
                                }
                            }

                            if (err) {
                                err.requireMap = this.map;
                                err.requireModules = [this.map.id];
                                err.requireType = 'define';
                                return onError((this.error = err));
                            }

                        } else {
                            //Just a literal value
                            exports = factory;
                        }

                        this.exports = exports;

                        if (this.map.isDefine && !this.ignore) {
                            defined[id] = exports;

                            if (req.onResourceLoad) {
                                req.onResourceLoad(context, this.map, this.depMaps);
                            }
                        }

                        //Clean up
                        cleanRegistry(id);

                        this.defined = true;
                    }

                    //Finished the define stage. Allow calling check again
                    //to allow define notifications below in the case of a
                    //cycle.
                    this.defining = false;

                    if (this.defined && !this.defineEmitted) {
                        this.defineEmitted = true;
                        this.emit('defined', this.exports);
                        this.defineEmitComplete = true;
                    }

                }
            },

            callPlugin: function () {
                var map = this.map,
                    id = map.id,
                    //Map already normalized the prefix.
                    pluginMap = makeModuleMap(map.prefix);

                //Mark this as a dependency for this plugin, so it
                //can be traced for cycles.
                this.depMaps.push(pluginMap);

                on(pluginMap, 'defined', bind(this, function (plugin) {
                    var load, normalizedMap, normalizedMod,
                        name = this.map.name,
                        parentName = this.map.parentMap ? this.map.parentMap.name : null,
                        localRequire = context.makeRequire(map.parentMap, {
                            enableBuildCallback: true
                        });

                    //If current map is not normalized, wait for that
                    //normalized name to load instead of continuing.
                    if (this.map.unnormalized) {
                        //Normalize the ID if the plugin allows it.
                        if (plugin.normalize) {
                            name = plugin.normalize(name, function (name) {
                                return normalize(name, parentName, true);
                            }) || '';
                        }

                        //prefix and name should already be normalized, no need
                        //for applying map config again either.
                        normalizedMap = makeModuleMap(map.prefix + '!' + name,
                                                      this.map.parentMap);
                        on(normalizedMap,
                            'defined', bind(this, function (value) {
                                this.init([], function () { return value; }, null, {
                                    enabled: true,
                                    ignore: true
                                });
                            }));

                        normalizedMod = getOwn(registry, normalizedMap.id);
                        if (normalizedMod) {
                            //Mark this as a dependency for this plugin, so it
                            //can be traced for cycles.
                            this.depMaps.push(normalizedMap);

                            if (this.events.error) {
                                normalizedMod.on('error', bind(this, function (err) {
                                    this.emit('error', err);
                                }));
                            }
                            normalizedMod.enable();
                        }

                        return;
                    }

                    load = bind(this, function (value) {
                        this.init([], function () { return value; }, null, {
                            enabled: true
                        });
                    });

                    load.error = bind(this, function (err) {
                        this.inited = true;
                        this.error = err;
                        err.requireModules = [id];

                        //Remove temp unnormalized modules for this module,
                        //since they will never be resolved otherwise now.
                        eachProp(registry, function (mod) {
                            if (mod.map.id.indexOf(id + '_unnormalized') === 0) {
                                cleanRegistry(mod.map.id);
                            }
                        });

                        onError(err);
                    });

                    //Allow plugins to load other code without having to know the
                    //context or how to 'complete' the load.
                    load.fromText = bind(this, function (text, textAlt) {
                        /*jslint evil: true */
                        var moduleName = map.name,
                            moduleMap = makeModuleMap(moduleName),
                            hasInteractive = useInteractive;

                        //As of 2.1.0, support just passing the text, to reinforce
                        //fromText only being called once per resource. Still
                        //support old style of passing moduleName but discard
                        //that moduleName in favor of the internal ref.
                        if (textAlt) {
                            text = textAlt;
                        }

                        //Turn off interactive script matching for IE for any define
                        //calls in the text, then turn it back on at the end.
                        if (hasInteractive) {
                            useInteractive = false;
                        }

                        //Prime the system by creating a module instance for
                        //it.
                        getModule(moduleMap);

                        //Transfer any config to this other module.
                        if (hasProp(config.config, id)) {
                            config.config[moduleName] = config.config[id];
                        }

                        try {
                            req.exec(text);
                        } catch (e) {
                            return onError(makeError('fromtexteval',
                                             'fromText eval for ' + id +
                                            ' failed: ' + e,
                                             e,
                                             [id]));
                        }

                        if (hasInteractive) {
                            useInteractive = true;
                        }

                        //Mark this as a dependency for the plugin
                        //resource
                        this.depMaps.push(moduleMap);

                        //Support anonymous modules.
                        context.completeLoad(moduleName);

                        //Bind the value of that module to the value for this
                        //resource ID.
                        localRequire([moduleName], load);
                    });

                    //Use parentName here since the plugin's name is not reliable,
                    //could be some weird string with no path that actually wants to
                    //reference the parentName's path.
                    plugin.load(map.name, localRequire, load, config);
                }));

                context.enable(pluginMap, this);
                this.pluginMaps[pluginMap.id] = pluginMap;
            },

            enable: function () {
                enabledRegistry[this.map.id] = this;
                this.enabled = true;

                //Set flag mentioning that the module is enabling,
                //so that immediate calls to the defined callbacks
                //for dependencies do not trigger inadvertent load
                //with the depCount still being zero.
                this.enabling = true;

                //Enable each dependency
                each(this.depMaps, bind(this, function (depMap, i) {
                    var id, mod, handler;

                    if (typeof depMap === 'string') {
                        //Dependency needs to be converted to a depMap
                        //and wired up to this module.
                        depMap = makeModuleMap(depMap,
                                               (this.map.isDefine ? this.map : this.map.parentMap),
                                               false,
                                               !this.skipMap);
                        this.depMaps[i] = depMap;

                        handler = getOwn(handlers, depMap.id);

                        if (handler) {
                            this.depExports[i] = handler(this);
                            return;
                        }

                        this.depCount += 1;

                        on(depMap, 'defined', bind(this, function (depExports) {
                            this.defineDep(i, depExports);
                            this.check();
                        }));

                        if (this.errback) {
                            on(depMap, 'error', this.errback);
                        }
                    }

                    id = depMap.id;
                    mod = registry[id];

                    //Skip special modules like 'require', 'exports', 'module'
                    //Also, don't call enable if it is already enabled,
                    //important in circular dependency cases.
                    if (!hasProp(handlers, id) && mod && !mod.enabled) {
                        context.enable(depMap, this);
                    }
                }));

                //Enable each plugin that is used in
                //a dependency
                eachProp(this.pluginMaps, bind(this, function (pluginMap) {
                    var mod = getOwn(registry, pluginMap.id);
                    if (mod && !mod.enabled) {
                        context.enable(pluginMap, this);
                    }
                }));

                this.enabling = false;

                this.check();
            },

            on: function (name, cb) {
                var cbs = this.events[name];
                if (!cbs) {
                    cbs = this.events[name] = [];
                }
                cbs.push(cb);
            },

            emit: function (name, evt) {
                each(this.events[name], function (cb) {
                    cb(evt);
                });
                if (name === 'error') {
                    //Now that the error handler was triggered, remove
                    //the listeners, since this broken Module instance
                    //can stay around for a while in the registry.
                    delete this.events[name];
                }
            }
        };

        function callGetModule(args) {
            //Skip modules already defined.
            if (!hasProp(defined, args[0])) {
                getModule(makeModuleMap(args[0], null, true)).init(args[1], args[2]);
            }
        }

        function removeListener(node, func, name, ieName) {
            //Favor detachEvent because of IE9
            //issue, see attachEvent/addEventListener comment elsewhere
            //in this file.
            if (node.detachEvent && !isOpera) {
                //Probably IE. If not it will throw an error, which will be
                //useful to know.
                if (ieName) {
                    node.detachEvent(ieName, func);
                }
            } else {
                node.removeEventListener(name, func, false);
            }
        }

        /**
         * Given an event from a script node, get the requirejs info from it,
         * and then removes the event listeners on the node.
         * @param {Event} evt
         * @returns {Object}
         */
        function getScriptData(evt) {
            //Using currentTarget instead of target for Firefox 2.0's sake. Not
            //all old browsers will be supported, but this one was easy enough
            //to support and still makes sense.
            var node = evt.currentTarget || evt.srcElement;

            //Remove the listeners once here.
            removeListener(node, context.onScriptLoad, 'load', 'onreadystatechange');
            removeListener(node, context.onScriptError, 'error');

            return {
                node: node,
                id: node && node.getAttribute('data-requiremodule')
            };
        }

        function intakeDefines() {
            var args;

            //Any defined modules in the global queue, intake them now.
            takeGlobalQueue();

            //Make sure any remaining defQueue items get properly processed.
            while (defQueue.length) {
                args = defQueue.shift();
                if (args[0] === null) {
                    return onError(makeError('mismatch', 'Mismatched anonymous define() module: ' + args[args.length - 1]));
                } else {
                    //args are id, deps, factory. Should be normalized by the
                    //define() function.
                    callGetModule(args);
                }
            }
        }

        context = {
            config: config,
            contextName: contextName,
            registry: registry,
            defined: defined,
            urlFetched: urlFetched,
            defQueue: defQueue,
            Module: Module,
            makeModuleMap: makeModuleMap,
            nextTick: req.nextTick,
            onError: onError,

            /**
             * Set a configuration for the context.
             * @param {Object} cfg config object to integrate.
             */
            configure: function (cfg) {
                //Make sure the baseUrl ends in a slash.
                if (cfg.baseUrl) {
                    if (cfg.baseUrl.charAt(cfg.baseUrl.length - 1) !== '/') {
                        cfg.baseUrl += '/';
                    }
                }

                //Save off the paths and packages since they require special processing,
                //they are additive.
                var pkgs = config.pkgs,
                    shim = config.shim,
                    objs = {
                        paths: true,
                        config: true,
                        map: true
                    };

                eachProp(cfg, function (value, prop) {
                    if (objs[prop]) {
                        if (prop === 'map') {
                            if (!config.map) {
                                config.map = {};
                            }
                            mixin(config[prop], value, true, true);
                        } else {
                            mixin(config[prop], value, true);
                        }
                    } else {
                        config[prop] = value;
                    }
                });

                //Merge shim
                if (cfg.shim) {
                    eachProp(cfg.shim, function (value, id) {
                        //Normalize the structure
                        if (isArray(value)) {
                            value = {
                                deps: value
                            };
                        }
                        if ((value.exports || value.init) && !value.exportsFn) {
                            value.exportsFn = context.makeShimExports(value);
                        }
                        shim[id] = value;
                    });
                    config.shim = shim;
                }

                //Adjust packages if necessary.
                if (cfg.packages) {
                    each(cfg.packages, function (pkgObj) {
                        var location;

                        pkgObj = typeof pkgObj === 'string' ? { name: pkgObj } : pkgObj;
                        location = pkgObj.location;

                        //Create a brand new object on pkgs, since currentPackages can
                        //be passed in again, and config.pkgs is the internal transformed
                        //state for all package configs.
                        pkgs[pkgObj.name] = {
                            name: pkgObj.name,
                            location: location || pkgObj.name,
                            //Remove leading dot in main, so main paths are normalized,
                            //and remove any trailing .js, since different package
                            //envs have different conventions: some use a module name,
                            //some use a file name.
                            main: (pkgObj.main || 'main')
                                  .replace(currDirRegExp, '')
                                  .replace(jsSuffixRegExp, '')
                        };
                    });

                    //Done with modifications, assing packages back to context config
                    config.pkgs = pkgs;
                }

                //If there are any "waiting to execute" modules in the registry,
                //update the maps for them, since their info, like URLs to load,
                //may have changed.
                eachProp(registry, function (mod, id) {
                    //If module already has init called, since it is too
                    //late to modify them, and ignore unnormalized ones
                    //since they are transient.
                    if (!mod.inited && !mod.map.unnormalized) {
                        mod.map = makeModuleMap(id);
                    }
                });

                //If a deps array or a config callback is specified, then call
                //require with those args. This is useful when require is defined as a
                //config object before require.js is loaded.
                if (cfg.deps || cfg.callback) {
                    context.require(cfg.deps || [], cfg.callback);
                }
            },

            makeShimExports: function (value) {
                function fn() {
                    var ret;
                    if (value.init) {
                        ret = value.init.apply(global, arguments);
                    }
                    return ret || (value.exports && getGlobal(value.exports));
                }
                return fn;
            },

            makeRequire: function (relMap, options) {
                options = options || {};

                function localRequire(deps, callback, errback) {
                    var id, map, requireMod;

                    if (options.enableBuildCallback && callback && isFunction(callback)) {
                        callback.__requireJsBuild = true;
                    }

                    if (typeof deps === 'string') {
                        if (isFunction(callback)) {
                            //Invalid call
                            return onError(makeError('requireargs', 'Invalid require call'), errback);
                        }

                        //If require|exports|module are requested, get the
                        //value for them from the special handlers. Caveat:
                        //this only works while module is being defined.
                        if (relMap && hasProp(handlers, deps)) {
                            return handlers[deps](registry[relMap.id]);
                        }

                        //Synchronous access to one module. If require.get is
                        //available (as in the Node adapter), prefer that.
                        if (req.get) {
                            return req.get(context, deps, relMap, localRequire);
                        }

                        //Normalize module name, if it contains . or ..
                        map = makeModuleMap(deps, relMap, false, true);
                        id = map.id;

                        if (!hasProp(defined, id)) {
                            return onError(makeError('notloaded', 'Module name "' +
                                        id +
                                        '" has not been loaded yet for context: ' +
                                        contextName +
                                        (relMap ? '' : '. Use require([])')));
                        }
                        return defined[id];
                    }

                    //Grab defines waiting in the global queue.
                    intakeDefines();

                    //Mark all the dependencies as needing to be loaded.
                    context.nextTick(function () {
                        //Some defines could have been added since the
                        //require call, collect them.
                        intakeDefines();

                        requireMod = getModule(makeModuleMap(null, relMap));

                        //Store if map config should be applied to this require
                        //call for dependencies.
                        requireMod.skipMap = options.skipMap;

                        requireMod.init(deps, callback, errback, {
                            enabled: true
                        });

                        checkLoaded();
                    });

                    return localRequire;
                }

                mixin(localRequire, {
                    isBrowser: isBrowser,

                    /**
                     * Converts a module name + .extension into an URL path.
                     * *Requires* the use of a module name. It does not support using
                     * plain URLs like nameToUrl.
                     */
                    toUrl: function (moduleNamePlusExt) {
                        var ext,
                            index = moduleNamePlusExt.lastIndexOf('.'),
                            segment = moduleNamePlusExt.split('/')[0],
                            isRelative = segment === '.' || segment === '..';

                        //Have a file extension alias, and it is not the
                        //dots from a relative path.
                        if (index !== -1 && (!isRelative || index > 1)) {
                            ext = moduleNamePlusExt.substring(index, moduleNamePlusExt.length);
                            moduleNamePlusExt = moduleNamePlusExt.substring(0, index);
                        }

                        return context.nameToUrl(normalize(moduleNamePlusExt,
                                                relMap && relMap.id, true), ext,  true);
                    },

                    defined: function (id) {
                        return hasProp(defined, makeModuleMap(id, relMap, false, true).id);
                    },

                    specified: function (id) {
                        id = makeModuleMap(id, relMap, false, true).id;
                        return hasProp(defined, id) || hasProp(registry, id);
                    }
                });

                //Only allow undef on top level require calls
                if (!relMap) {
                    localRequire.undef = function (id) {
                        //Bind any waiting define() calls to this context,
                        //fix for #408
                        takeGlobalQueue();

                        var map = makeModuleMap(id, relMap, true),
                            mod = getOwn(registry, id);

                        delete defined[id];
                        delete urlFetched[map.url];
                        delete undefEvents[id];

                        if (mod) {
                            //Hold on to listeners in case the
                            //module will be attempted to be reloaded
                            //using a different config.
                            if (mod.events.defined) {
                                undefEvents[id] = mod.events;
                            }

                            cleanRegistry(id);
                        }
                    };
                }

                return localRequire;
            },

            /**
             * Called to enable a module if it is still in the registry
             * awaiting enablement. A second arg, parent, the parent module,
             * is passed in for context, when this method is overriden by
             * the optimizer. Not shown here to keep code compact.
             */
            enable: function (depMap) {
                var mod = getOwn(registry, depMap.id);
                if (mod) {
                    getModule(depMap).enable();
                }
            },

            /**
             * Internal method used by environment adapters to complete a load event.
             * A load event could be a script load or just a load pass from a synchronous
             * load call.
             * @param {String} moduleName the name of the module to potentially complete.
             */
            completeLoad: function (moduleName) {
                var found, args, mod,
                    shim = getOwn(config.shim, moduleName) || {},
                    shExports = shim.exports;

                takeGlobalQueue();

                while (defQueue.length) {
                    args = defQueue.shift();
                    if (args[0] === null) {
                        args[0] = moduleName;
                        //If already found an anonymous module and bound it
                        //to this name, then this is some other anon module
                        //waiting for its completeLoad to fire.
                        if (found) {
                            break;
                        }
                        found = true;
                    } else if (args[0] === moduleName) {
                        //Found matching define call for this script!
                        found = true;
                    }

                    callGetModule(args);
                }

                //Do this after the cycle of callGetModule in case the result
                //of those calls/init calls changes the registry.
                mod = getOwn(registry, moduleName);

                if (!found && !hasProp(defined, moduleName) && mod && !mod.inited) {
                    if (config.enforceDefine && (!shExports || !getGlobal(shExports))) {
                        if (hasPathFallback(moduleName)) {
                            return;
                        } else {
                            return onError(makeError('nodefine',
                                             'No define call for ' + moduleName,
                                             null,
                                             [moduleName]));
                        }
                    } else {
                        //A script that does not call define(), so just simulate
                        //the call for it.
                        callGetModule([moduleName, (shim.deps || []), shim.exportsFn]);
                    }
                }

                checkLoaded();
            },

            /**
             * Converts a module name to a file path. Supports cases where
             * moduleName may actually be just an URL.
             * Note that it **does not** call normalize on the moduleName,
             * it is assumed to have already been normalized. This is an
             * internal API, not a public one. Use toUrl for the public API.
             */
            nameToUrl: function (moduleName, ext, skipExt) {
                var paths, pkgs, pkg, pkgPath, syms, i, parentModule, url,
                    parentPath;

                //If a colon is in the URL, it indicates a protocol is used and it is just
                //an URL to a file, or if it starts with a slash, contains a query arg (i.e. ?)
                //or ends with .js, then assume the user meant to use an url and not a module id.
                //The slash is important for protocol-less URLs as well as full paths.
                if (req.jsExtRegExp.test(moduleName)) {
                    //Just a plain path, not module name lookup, so just return it.
                    //Add extension if it is included. This is a bit wonky, only non-.js things pass
                    //an extension, this method probably needs to be reworked.
                    url = moduleName + (ext || '');
                } else {
                    //A module that needs to be converted to a path.
                    paths = config.paths;
                    pkgs = config.pkgs;

                    syms = moduleName.split('/');
                    //For each module name segment, see if there is a path
                    //registered for it. Start with most specific name
                    //and work up from it.
                    for (i = syms.length; i > 0; i -= 1) {
                        parentModule = syms.slice(0, i).join('/');
                        pkg = getOwn(pkgs, parentModule);
                        parentPath = getOwn(paths, parentModule);
                        if (parentPath) {
                            //If an array, it means there are a few choices,
                            //Choose the one that is desired
                            if (isArray(parentPath)) {
                                parentPath = parentPath[0];
                            }
                            syms.splice(0, i, parentPath);
                            break;
                        } else if (pkg) {
                            //If module name is just the package name, then looking
                            //for the main module.
                            if (moduleName === pkg.name) {
                                pkgPath = pkg.location + '/' + pkg.main;
                            } else {
                                pkgPath = pkg.location;
                            }
                            syms.splice(0, i, pkgPath);
                            break;
                        }
                    }

                    //Join the path parts together, then figure out if baseUrl is needed.
                    url = syms.join('/');
                    url += (ext || (/\?/.test(url) || skipExt ? '' : '.js'));
                    url = (url.charAt(0) === '/' || url.match(/^[\w\+\.\-]+:/) ? '' : config.baseUrl) + url;
                }

                return config.urlArgs ? url +
                                        ((url.indexOf('?') === -1 ? '?' : '&') +
                                         config.urlArgs) : url;
            },

            //Delegates to req.load. Broken out as a separate function to
            //allow overriding in the optimizer.
            load: function (id, url) {
                req.load(context, id, url);
            },

            /**
             * Executes a module callack function. Broken out as a separate function
             * solely to allow the build system to sequence the files in the built
             * layer in the right sequence.
             *
             * @private
             */
            execCb: function (name, callback, args, exports) {
                return callback.apply(exports, args);
            },

            /**
             * callback for script loads, used to check status of loading.
             *
             * @param {Event} evt the event from the browser for the script
             * that was loaded.
             */
            onScriptLoad: function (evt) {
                //Using currentTarget instead of target for Firefox 2.0's sake. Not
                //all old browsers will be supported, but this one was easy enough
                //to support and still makes sense.
                if (evt.type === 'load' ||
                        (readyRegExp.test((evt.currentTarget || evt.srcElement).readyState))) {
                    //Reset interactive script so a script node is not held onto for
                    //to long.
                    interactiveScript = null;

                    //Pull out the name of the module and the context.
                    var data = getScriptData(evt);
                    context.completeLoad(data.id);
                }
            },

            /**
             * Callback for script errors.
             */
            onScriptError: function (evt) {
                var data = getScriptData(evt);
                if (!hasPathFallback(data.id)) {
                    return onError(makeError('scripterror', 'Script error', evt, [data.id]));
                }
            }
        };

        context.require = context.makeRequire();
        return context;
    }

    /**
     * Main entry point.
     *
     * If the only argument to require is a string, then the module that
     * is represented by that string is fetched for the appropriate context.
     *
     * If the first argument is an array, then it will be treated as an array
     * of dependency string names to fetch. An optional function callback can
     * be specified to execute when all of those dependencies are available.
     *
     * Make a local req variable to help Caja compliance (it assumes things
     * on a require that are not standardized), and to give a short
     * name for minification/local scope use.
     */
    req = requirejs = function (deps, callback, errback, optional) {

        //Find the right context, use default
        var context, config,
            contextName = defContextName;

        // Determine if have config object in the call.
        if (!isArray(deps) && typeof deps !== 'string') {
            // deps is a config object
            config = deps;
            if (isArray(callback)) {
                // Adjust args if there are dependencies
                deps = callback;
                callback = errback;
                errback = optional;
            } else {
                deps = [];
            }
        }

        if (config && config.context) {
            contextName = config.context;
        }

        context = getOwn(contexts, contextName);
        if (!context) {
            context = contexts[contextName] = req.s.newContext(contextName);
        }

        if (config) {
            context.configure(config);
        }

        return context.require(deps, callback, errback);
    };

    /**
     * Support require.config() to make it easier to cooperate with other
     * AMD loaders on globally agreed names.
     */
    req.config = function (config) {
        return req(config);
    };

    /**
     * Execute something after the current tick
     * of the event loop. Override for other envs
     * that have a better solution than setTimeout.
     * @param  {Function} fn function to execute later.
     */
    req.nextTick = typeof setTimeout !== 'undefined' ? function (fn) {
        setTimeout(fn, 4);
    } : function (fn) { fn(); };

    /**
     * Export require as a global, but only if it does not already exist.
     */
    if (!require) {
        require = req;
    }

    req.version = version;

    //Used to filter out dependencies that are already paths.
    req.jsExtRegExp = /^\/|:|\?|\.js$/;
    req.isBrowser = isBrowser;
    s = req.s = {
        contexts: contexts,
        newContext: newContext
    };

    //Create default context.
    req({});

    //Exports some context-sensitive methods on global require.
    each([
        'toUrl',
        'undef',
        'defined',
        'specified'
    ], function (prop) {
        //Reference from contexts instead of early binding to default context,
        //so that during builds, the latest instance of the default context
        //with its config gets used.
        req[prop] = function () {
            var ctx = contexts[defContextName];
            return ctx.require[prop].apply(ctx, arguments);
        };
    });

    if (isBrowser) {
        head = s.head = document.getElementsByTagName('head')[0];
        //If BASE tag is in play, using appendChild is a problem for IE6.
        //When that browser dies, this can be removed. Details in this jQuery bug:
        //http://dev.jquery.com/ticket/2709
        baseElement = document.getElementsByTagName('base')[0];
        if (baseElement) {
            head = s.head = baseElement.parentNode;
        }
    }

    /**
     * Any errors that require explicitly generates will be passed to this
     * function. Intercept/override it if you want custom error handling.
     * @param {Error} err the error object.
     */
    req.onError = function (err) {
        throw err;
    };

    /**
     * Does the request to load a module for the browser case.
     * Make this a separate function to allow other environments
     * to override it.
     *
     * @param {Object} context the require context to find state.
     * @param {String} moduleName the name of the module.
     * @param {Object} url the URL to the module.
     */
    req.load = function (context, moduleName, url) {
        var config = (context && context.config) || {},
            node;
        if (isBrowser) {
            //In the browser so use a script tag
            node = config.xhtml ?
                    document.createElementNS('http://www.w3.org/1999/xhtml', 'html:script') :
                    document.createElement('script');
            node.type = config.scriptType || 'text/javascript';
            node.charset = 'utf-8';
            node.async = true;

            node.setAttribute('data-requirecontext', context.contextName);
            node.setAttribute('data-requiremodule', moduleName);

            //Set up load listener. Test attachEvent first because IE9 has
            //a subtle issue in its addEventListener and script onload firings
            //that do not match the behavior of all other browsers with
            //addEventListener support, which fire the onload event for a
            //script right after the script execution. See:
            //https://connect.microsoft.com/IE/feedback/details/648057/script-onload-event-is-not-fired-immediately-after-script-execution
            //UNFORTUNATELY Opera implements attachEvent but does not follow the script
            //script execution mode.
            if (node.attachEvent &&
                    //Check if node.attachEvent is artificially added by custom script or
                    //natively supported by browser
                    //read https://github.com/jrburke/requirejs/issues/187
                    //if we can NOT find [native code] then it must NOT natively supported.
                    //in IE8, node.attachEvent does not have toString()
                    //Note the test for "[native code" with no closing brace, see:
                    //https://github.com/jrburke/requirejs/issues/273
                    !(node.attachEvent.toString && node.attachEvent.toString().indexOf('[native code') < 0) &&
                    !isOpera) {
                //Probably IE. IE (at least 6-8) do not fire
                //script onload right after executing the script, so
                //we cannot tie the anonymous define call to a name.
                //However, IE reports the script as being in 'interactive'
                //readyState at the time of the define call.
                useInteractive = true;

                node.attachEvent('onreadystatechange', context.onScriptLoad);
                //It would be great to add an error handler here to catch
                //404s in IE9+. However, onreadystatechange will fire before
                //the error handler, so that does not help. If addEventListener
                //is used, then IE will fire error before load, but we cannot
                //use that pathway given the connect.microsoft.com issue
                //mentioned above about not doing the 'script execute,
                //then fire the script load event listener before execute
                //next script' that other browsers do.
                //Best hope: IE10 fixes the issues,
                //and then destroys all installs of IE 6-9.
                //node.attachEvent('onerror', context.onScriptError);
            } else {
                node.addEventListener('load', context.onScriptLoad, false);
                node.addEventListener('error', context.onScriptError, false);
            }
            node.src = url;

            //For some cache cases in IE 6-8, the script executes before the end
            //of the appendChild execution, so to tie an anonymous define
            //call to the module name (which is stored on the node), hold on
            //to a reference to this node, but clear after the DOM insertion.
            currentlyAddingScript = node;
            if (baseElement) {
                head.insertBefore(node, baseElement);
            } else {
                head.appendChild(node);
            }
            currentlyAddingScript = null;

            return node;
        } else if (isWebWorker) {
            try {
                //In a web worker, use importScripts. This is not a very
                //efficient use of importScripts, importScripts will block until
                //its script is downloaded and evaluated. However, if web workers
                //are in play, the expectation that a build has been done so that
                //only one script needs to be loaded anyway. This may need to be
                //reevaluated if other use cases become common.
                importScripts(url);

                //Account for anonymous modules
                context.completeLoad(moduleName);
            } catch (e) {
                context.onError(makeError('importscripts',
                                'importScripts failed for ' +
                                    moduleName + ' at ' + url,
                                e,
                                [moduleName]));
            }
        }
    };

    function getInteractiveScript() {
        if (interactiveScript && interactiveScript.readyState === 'interactive') {
            return interactiveScript;
        }

        eachReverse(scripts(), function (script) {
            if (script.readyState === 'interactive') {
                return (interactiveScript = script);
            }
        });
        return interactiveScript;
    }

    //Look for a data-main script attribute, which could also adjust the baseUrl.
    if (isBrowser) {
        //Figure out baseUrl. Get it from the script tag with require.js in it.
        eachReverse(scripts(), function (script) {
            //Set the 'head' where we can append children by
            //using the script's parent.
            if (!head) {
                head = script.parentNode;
            }

            //Look for a data-main attribute to set main script for the page
            //to load. If it is there, the path to data main becomes the
            //baseUrl, if it is not already set.
            dataMain = script.getAttribute('data-main');
            if (dataMain) {
                //Set final baseUrl if there is not already an explicit one.
                if (!cfg.baseUrl) {
                    //Pull off the directory of data-main for use as the
                    //baseUrl.
                    src = dataMain.split('/');
                    mainScript = src.pop();
                    subPath = src.length ? src.join('/')  + '/' : './';

                    cfg.baseUrl = subPath;
                    dataMain = mainScript;
                }

                //Strip off any trailing .js since dataMain is now
                //like a module name.
                dataMain = dataMain.replace(jsSuffixRegExp, '');

                //Put the data-main script in the files to load.
                cfg.deps = cfg.deps ? cfg.deps.concat(dataMain) : [dataMain];

                return true;
            }
        });
    }

    /**
     * The function that handles definitions of modules. Differs from
     * require() in that a string for the module should be the first argument,
     * and the function to execute after dependencies are loaded should
     * return a value to define the module corresponding to the first argument's
     * name.
     */
    define = function (name, deps, callback) {
        var node, context;

        //Allow for anonymous modules
        if (typeof name !== 'string') {
            //Adjust args appropriately
            callback = deps;
            deps = name;
            name = null;
        }

        //This module may not have dependencies
        if (!isArray(deps)) {
            callback = deps;
            deps = [];
        }

        //If no name, and callback is a function, then figure out if it a
        //CommonJS thing with dependencies.
        if (!deps.length && isFunction(callback)) {
            //Remove comments from the callback string,
            //look for require calls, and pull them into the dependencies,
            //but only if there are function args.
            if (callback.length) {
                callback
                    .toString()
                    .replace(commentRegExp, '')
                    .replace(cjsRequireRegExp, function (match, dep) {
                        deps.push(dep);
                    });

                //May be a CommonJS thing even without require calls, but still
                //could use exports, and module. Avoid doing exports and module
                //work though if it just needs require.
                //REQUIRES the function to expect the CommonJS variables in the
                //order listed below.
                deps = (callback.length === 1 ? ['require'] : ['require', 'exports', 'module']).concat(deps);
            }
        }

        //If in IE 6-8 and hit an anonymous define() call, do the interactive
        //work.
        if (useInteractive) {
            node = currentlyAddingScript || getInteractiveScript();
            if (node) {
                if (!name) {
                    name = node.getAttribute('data-requiremodule');
                }
                context = contexts[node.getAttribute('data-requirecontext')];
            }
        }

        //Always save off evaluating the def call until the script onload handler.
        //This allows multiple modules to be in a file without prematurely
        //tracing dependencies, and allows for anonymous module support,
        //where the module name is not known until the script onload event
        //occurs. If no context, use the global queue, and get it processed
        //in the onscript load callback.
        (context ? context.defQueue : globalDefQueue).push([name, deps, callback]);
    };

    define.amd = {
        jQuery: true
    };


    /**
     * Executes the text. Normally just uses eval, but can be modified
     * to use a better, environment-specific call. Only used for transpiling
     * loader plugins, not for plain JS modules.
     * @param {String} text the text to execute/evaluate.
     */
    req.exec = function (text) {
        /*jslint evil: true */
        return eval(text);
    };

    //Set up with config info.
    req(cfg);
}(this));
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
define('lib-js-elastic-filter', function (require, exports, module) {
/**
 * jQuery ElasticSearch Filter Implementation
 *
 * @version 3.0.2
 *
 * Copyright 2012 Usability Dynamics, Inc. (usabilitydynamics.com)
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL Alexandru Marasteanu BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/* jshint maxparams: 6 */
/* global ko */
/* global ejs */
/* jshint -W030 */

;(function( $ ) {

  "use strict";

  /**
   * ElasticSeach
   *
   * @param {type} settings
   * @returns {@this;|_L6.$.fn.ddpElasticSuggest}
   */
  $.fn.elasticSearch = function( settings ) {

    var

      /**
       * Reference to this
       * @type @this;
       */
      self = this,

      /**
       * Defaults
       * @type object
       */
      options = $.extend({
        debug: false,
        timeout: 30000
      }, settings ),

      /**
       * Debug functions
       * @type type
       */
      _console = {

        /**
         * Log
         *
         * @param {type} a
         * @param {type} b
         */
        log: function( a, b ) {
          if ( typeof console === 'object' && options.debug ) {
            console.log( a, b );
          }
        },

        /**
         * Debug
         *
         * @param {type} a
         * @param {type} b
         */
        debug: function( a, b ) {
          if ( typeof console === 'object' && options.debug ) {
            console.debug( a, b );
          }
        },

        /**
         * Error
         *
         * @param {type} a
         * @param {type} b
         */
        error: function( a, b ) {
          if ( typeof console === 'object' && options.debug ) {
            console.error( a, b );
          }
        }
      },

      /**
       * Global viewmodel
       * @type function
       */
      ViewModel = function( scopes, suggesters ) {

        window.elasticSearchVM = this;

        /**
         * Autocompletion Object
         */
        this._suggester_model = function( scope ) {

          /**
           * Reference to this
           * @type @this;
           */
          var self = this;

          /**
           * Current scope
           */
          this.scope = scope;

          /**
           * Manual notifier
           */
          this._notify = ko.observable();

          /**
           * Documents Collection
           */
          this.documents = ko.observableArray( [] );

          /**
           * Types
           */
          this.types = ko.observable( {} );

          /**
           * Visibility flag
           */
          this.loading = ko.observable( false );

          /**
           * Autocompletion docs count
           */
          this.count = ko.computed(function() {
            return self.documents().length;
          });

          /**
           * Whether has text in input or not
           */
          this.has_text = ko.computed(function() {
            $('[data-suggest="'+self.scope+'"]').one('keyup', function(){
              self._notify.notifySubscribers();
            });
            self._notify();
            return typeof $('[data-suggest="'+self.scope+'"]').val() !== 'undefined' ? $('[data-suggest="'+self.scope+'"]').val().length : false;
          });

          /**
           * Autocompletion visibility
           */
          this.visible = ko.computed(function() {
            return self.has_text() && $('[data-suggest="'+self.scope+'"]').val().length >= (function() { return bindings.elasticSuggest[self.scope].min_chars; }());
          });

          /**
           * Clear search input
           */
          this.clear = function() {
            $('[data-suggest="'+self.scope+'"]').val('').keyup().change();
          };
        };

        /**
         * Filter instance exemplar
         * @param {type} scope
         */
        this._filter_model = function( scope ) {

          /**
           * Reference to this
           * @type @this;
           */
          var self = this;

          /**
           * Current scope
           */
          this.scope = scope;

          /**
           * Filtered documents collection
           */
          this.documents = ko.observableArray( [] );

          /**
           * Total filtered documents
           */
          this.total = ko.observable( 0 );

          /**
           * Filter facets collection
           */
          this.facets = ko.observableArray( [] );

          /**
           * More button docs count
           */
          this.moreCount = ko.observable( 0 );

          /**
           * Human facet labels
           */
          this.facetLabels = ko.observable( {} );

          /**
           * Filtered docs count
           */
          this.count = ko.computed(function() {
            return self.documents().length;
          });

          /**
           * Determine whether filter has more documents to show oe not
           */
          this.has_more_documents = ko.computed(function() {
            return self.total() > self.count();
          });
        };

        /**
         * Init by scopes
         */
        for ( var i in scopes ) {
          if ( scopes.hasOwnProperty(i) ) {
            this[scopes[i]] = new this._filter_model( scopes[i] );
          }
        }

        /**
         *
         * @type type
         */
        for ( var j in suggesters ) {
          if ( suggesters.hasOwnProperty(j) ) {
            this[suggesters[j]] = new this._suggester_model( suggesters[j] );
          }
        }

      },

      /**
       * Knockout custom bindings
       * @type Object
       */
      bindings = window.elasticSearchBindings = {

        /**
         * Suggester for sitewide search
         */
        elasticSuggest: {

          /**
           * Default settings
           */
          settings: function() {

            /**
             * Minimum number of chars to start search for
             */
            this.min_chars = 3;

            /**
             * Fields to return
             */
            this.return_fields = [
              'post_title',
              'permalink'
            ];

            /**
             * Typing timeout
             */
            this.timeout = 100;

            /**
             * Doc types to search in
             */
            this.document_type = {
              unknown:'Unknown'
            },

            /**
             * Default search direction
             */
            this.sort_dir = 'asc';

            /**
             * Default request size
             */
            this.size = 20;

            /**
             * Autocompletion form selector
             */
            this.selector = '#autocompletion';

            /**
             * Elements for result set
             * @type {string}
             */
            this.resultList = '.search-autocomplete-list';

            /**
             * Ability to change query before execution
             */
            this.custom_query = {};
          },

          /**
           * Container for setTimeout reference
           */
          timeout: null,

          /**
           * Build DSL query
           */
          buildQuery: function( query_string, scope ) {

            /**
             * Validate
             */
            if ( !query_string || !query_string.length ) {
              _console.error( 'Wrong query string', query_string );
            }

            var _query = {
              query: {
                filtered: {
                  query: {
                    match: {
                      _all: {
                        query: query_string,
                        operator: "and"
                      }
                    }
                  }
                }
              },
              fields: this[scope].return_fields,
              sort: {
                _type: {
                  order: this[scope].sort_dir
                }
              },
              size: this[scope].size
            };

            /**
             * Return query object with the ability to extend or change it
             */
            return $.extend( _query, this[scope].custom_query );
          },

          /**
           * Autocomplete submit function
           */
          submit: function( viewModel, element, scope ) {
            _console.log( 'Typing search input', arguments );

            /**
             * Stop submitting if already ran
             */
            if ( this.timeout ) {
              window.clearTimeout( this.timeout );
            }

            /**
             * Do nothing if not enough chars typed
             */
            if ( element.val().length < this[scope].min_chars ) {
              viewModel[scope].loading(false);
              viewModel[scope].documents([]);
              return true;
            }

            _console.log( 'Search fired for ', element.val() );

            /**
             * Activate loading
             */
            viewModel[scope].loading(true);

            /**
             * Configure API
             */
            api.index( this[scope].index ).controllers( this[scope].controllers );

            /**
             * Run
             */
            this.timeout = window.setTimeout(

              /**
               * API method
               */
              api.search,

              /**
               * Typing timeout
               */
              this[scope].timeout,

              /**
               * Build and pass query
               */
              this.buildQuery( element.val(), scope ),

              /**
               * Types
               */
              Object.keys(this[scope].document_type),

              /**
               * Success handler
               *
               * @param {type} data
               * @param {type} xhr
               */
              function( data, xhr ) {
                _console.debug( 'Autocompletion Search Success', [data, xhr] );

                viewModel[scope].documents( data.hits.hits );
                viewModel[scope].loading(false);
              },

              /**
               * Error handler
               */
              function() {
                _console.error( 'Autocompletion Search Error', arguments );

                viewModel[scope].loading(false);
              },

              /**
               * Whether abort other requests or not
               */
              true
            );
          },

          /**
           * Suggester Initialization
           */
          init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            _console.debug( 'elasticSuggest init', [element, valueAccessor, allBindings, viewModel, bindingContext] );

            var
              /**
               * Suggest binding object to work with
               */
              Suggest = bindings.elasticSuggest,

              /**
               *
               * @type @exp;form@call;data
               */
              scope = $(element).data( 'suggest' );

            /**
             * Apply settings passed
             */
            Suggest[scope] = $.extend( new Suggest.settings(), valueAccessor() );

            /**
             * Set types
             */
            viewModel[scope].types( Suggest[scope].document_type );

            /**
             * Fire autocomplete function on input typing
             */
            $(element).on('keyup', function(){
              Suggest.submit( viewModel, $(this), scope );
            });

            /**
             * Prevent form submitting on Enter key
             */
            $(element).keypress(function(e) {
              var code = e.keyCode || e.which;
              if(code === 13) {
                return false;
              }
            });

            /**
             * Control dropdown visibility
             */
            $('html').on('click', function() {
              viewModel[scope].documents([]);
              $('[data-suggest="'+scope+'"]').val('').keyup().change();
            });

            $( Suggest[scope].selector ).on('click', function(e) {
              e.stopPropagation();
            });

            // If resultList exists AND has a "data-state" attribute, we switch it to "ready" now that we're initialized.
            if( $( Suggest[scope].resultList ).length && $( Suggest[scope].resultList ).attr( 'data-state' ) ) {
              $( Suggest[scope].resultList ).attr( 'data-state', 'ready' );
            }

          }

        },

        /**
         * Regular filter binding
         */
        elasticFilter: {

          /**
           * Filter defaults
           */
          settings: function() {

            /**
             * Time point for present. Will be used for period filtering.
             */
            this.middle_timepoint = {
              gte: 'now',
              lte: 'now'
            };

            /**
             * Default period direction
             */
            this.period = 'upcoming';

            /**
             * Default field that is responsible for date filtering
             */
            this.period_field = 'date';

            /**
             * Default sort option
             */
            this.sort_by = 'date';

            /**
             * Default sorting direction
             */
            this.sort_dir = 'asc';

            /**
             * Default number of document per page
             */
            this.per_page = 20;

            /**
             * Offset number
             */
            this.offset = 0;

            /**
             * Bool flag for more button
             */
            this.is_more = false;

            /**
             * Facets set
             */
            this.facets = {};

            /**
             * Default type
             */
            this.type = 'unknown';

            /**
             * Fields to return
             */
            this.return_fields = null;

            /**
             * Ability to query before execution
             */
            this.custom_query = {};

            /**
             * Control location
             */
            this.location = false;

            /**
             * Configurable location field
             */
            this.location_field = 'location';

            /**
             * Facet size
             */
            this.facet_size = 100;

            /**
             * Facet input name base
             */
            this.facet_input = 'terms';

            /**
             * Date Range input name base
             */
            this.date_range_input = 'date_range';

            /**
             * Default loading indicator selector
             */
            this.loader_selector = '.df_overlay_back, .df_overlay';

            /**
             * Store initial value of per page
             */
            this.initial_per_page = 20;

            /**
             * Store current filter options to use after re-rendering
             */
            this.current_filters = null;

            /**
             * DOM Element of filter form
             */
            this.form = null;
          },

          /**
           * Loading indicator
           */
          loader: null,

          /**
           *
           * @param {type} scope
           */
          determinePeriod: function( scope ) {

            var period = { range: {} };

            switch( this[scope].period ) {

              case 'upcoming':

                period.range[this[scope].period_field] = {
                   gte:this[scope].middle_timepoint.gte
                };

                break;

              case 'past':

                period.range[this[scope].period_field] = {
                   lte:this[scope].middle_timepoint.lte
                };

                break;

              default: break;
            }

            return period;
          },

          /**
           *
           */
          determineDateRange: function( scope ) {
            var range = { range: {} };

            range.range[this[scope].period_field] = this[scope].current_filters[this[scope].date_range_input];

            return range;
          },

          /**
           *
           */
          buildSortOptions: function( scope ) {
            var sort_type = {};
            var _return;

            switch( this[scope].sort_by ) {

              case 'distance':

                var lat = Number( localStorage.getItem( 'elasticSearch_latitude' ) ) ? Number( localStorage.getItem( 'elasticSearch_latitude' ) ):0;
                var lon = Number( localStorage.getItem( 'elasticSearch_longitude' ) ) ? Number( localStorage.getItem( 'elasticSearch_longitude' ) ):0;

                var _geo_distance = {};
                _geo_distance[this[scope].location_field] = {
                  lat: lat, lon: lon
                };
                _geo_distance.order = this[scope].sort_dir;
                _geo_distance.unit = "m";

                _return = {
                  _geo_distance: _geo_distance
                };

                break;
              default:

                sort_type[this[scope].sort_by] = {
                  order: this[scope].sort_dir
                };

                _return = sort_type;

                break;
            }

            return _return;
          },

          /**
           * DSL Query builder function
           * @return DSL object that should be passed as query argument to ElasticSearch
           */
          buildQuery: function( scope ) {

            /**
             * Reference to this
             */
            var self = this;

            /**
             * Get form filter data
             */
            this[scope].current_filters = this[scope].form ? this[scope].form.serializeObject() : {};

            /**
             * Clean object from empty/null values
             */
            cleanObject( this[scope].current_filters );

            _console.log('Current filter data:', this[scope].current_filters);

            /**
             * Start building the Query
             */
            var filter = {
              bool: {
                must: []
              }
            };

            /**
             * Determine filter period
             */
            if ( this[scope].period ) {
              var period = this.determinePeriod( scope );
              filter.bool.must.push( period );
            }

            /**
             * Determine date range if is set
             */
            if ( !$.isEmptyObject( this[scope].current_filters[this[scope].date_range_input] ) ) {
              var range = this.determineDateRange( scope );
              filter.bool.must.push( range );
            }

            /**
             * Build filter terms based on filter form
             */
            if ( this[scope].current_filters[this[scope].facet_input] ) {
              $.each( this[scope].current_filters[this[scope].facet_input], function(key, value) {
                if ( value !== "0" ) {
                  var _term = {};
                  _term[key] = value;
                  filter.bool.must.push({
                    term: _term
                  });
                }
              });
            }

            /**
             * Build facets
             */
            var facets = {};
            $.each( this[scope].facets, function( field, val ) {
              _console.log( 'Facets foreach', [ field, val ] );
              facets[field] = {
                terms: { field: field, size: self[scope].facet_size }
              };
            });

            /**
             * Build sort option
             */
            var sort = [];
            if ( this[scope].sort_by ) {
              sort.push( this.buildSortOptions( scope ) );
            }

            /**
             * Return ready DSL object with the ability to extend it
             */
            return $.extend({
              size: this[scope].per_page,
              from: this[scope].offset,
              query: {
                filtered: {
                  filter: filter
                }
              },
              fields: this[scope].return_fields,
              facets: facets,
              sort: sort
            }, this[scope].custom_query );
          },

          /**
           * Submit filter request
           */
          submit: function( viewModel, scope ) {

            /**
             * Reference to this
             * @type @this;
             */
            var self = this;

            /**
             * Show loader indicator
             */
            this.loader.show();

            /**
             * Run search request
             */
            api
              .index( self[scope].index )
              .controllers( self[scope].controllers )
              .search(

              /**
               * Build and pass DSL Query
               */
              this.buildQuery( scope ),

              /**
               * Documents type
               */
              this[scope].type,

              /**
               * Search success handler
               *
               * @param {type} data
               * @param {type} xhr
               */
              function( data, xhr ) {
                _console.log('Filter Success', [ data, xhr ]);

                /**
                 * If is a result of More request then append hits to existing.
                 * Otherwise just replace.
                 */
                if ( self[scope].is_more ) {
                  var current_hits = viewModel.documents();

                  $.each( data.hits.hits, function(k, hit) {
                    current_hits.push( hit );
                  });

                  viewModel.documents( current_hits );
                } else {
                  viewModel.documents( data.hits.hits );
                }

                /**
                 * Store total
                 */
                viewModel.total( data.hits.total );

                /**
                 * Update facets when needed
                 */
                if ( typeof data.facets !== 'undefined' ) {

                  var _total = 0;
                  $.each( data.facets, function( key, value ) {
                    _total += value.total;
                  });

                  if ( _total ) {
                    viewModel.facets([]);
                    $.each( data.facets, function( key, value ) {
                      value.key = key;
                      viewModel.facets.push(value);
                    });
                  }
                }

                /**
                 * Hide loader indicator
                 */
                self.loader.hide();

                /**
                 * Trigger custom event on success
                 */
                $(document).trigger( 'elasticFilter.submit.success', arguments );
              },

              /**
               * Error Handler
               */
              function() {
                _console.error('Filter Error', arguments);
                self.loader.hide();
              },

              /**
               * Whether abort other requests or not
               * @param {type} scope
               */
              false
            );

          },

          /**
           * Flush filter settings
           */
          flushSettings: function( scope ) {
            this[scope].is_more  = false;
            this[scope].offset   = 0;
            this[scope].per_page = this[scope].initial_per_page;
          },

          /**
           *
           * @param {type} Filter
           * @param {type} scope
           */
          determineCoords: function( Filter, scope, viewModel ) {
            /**
             * If no coords passed
             */
            if ( !Filter[scope].location ) {

              /**
               * If no coords in cookies
               */
              if ( ( !Number( localStorage.getItem( 'elasticSearch_latitude' ) ) || !Number( localStorage.getItem( 'elasticSearch_longitude' ) ) ) || localStorage.getItem( 'elasticSearch_geo_expire' ) < ( Math.round( Date.now()/1000 ) ) ) {

                /**
                 * If geo API exists
                 */
                if ( navigator.geolocation ) {

                  /**
                   * Get position
                   */
                  navigator.geolocation.getCurrentPosition(

                    /**
                     * Success handler
                     */
                    function( position ) {
                      _console.log( 'GeoLocation Success', arguments );

                      /**
                       * Remember coords
                       */
                      localStorage.setItem( 'elasticSearch_latitude', position.coords.latitude );
                      localStorage.setItem( 'elasticSearch_longitude', position.coords.longitude );
                      localStorage.setItem( 'elasticSearch_geo_expire', Math.round( Date.now()/1000 ) + 3600 );
                      
                      _console.log( 'localStorage - elasticSearch_latitude', localStorage.getItem('elasticSearch_latitude') );
                      _console.log( 'localStorage - elasticSearch_longitude', localStorage.getItem('elasticSearch_longitude') );
                      _console.log( 'localStorage - elasticSearch_geo_expire', localStorage.getItem('elasticSearch_geo_expire') );

                      /**
                       * Run filter again with new coords
                       */
                      Filter.submit( viewModel[scope], scope );
                    },

                    /**
                     * Error handler
                     */
                    function() {
                      _console.log( 'GeoLocation Erros', arguments );
                    },

                    /**
                     * Options
                     */
                    {enableHighAccuracy: true,maximumAge: 0}
                  );
                }
              }
            } else {
              /**
               * Remember passed coords
               */
              localStorage.setItem( 'elasticSearch_latitude', Filter[scope].location.latitude );
              localStorage.setItem( 'elasticSearch_longitude', Filter[scope].location.longitude );
              localStorage.setItem( 'elasticSearch_geo_expire', Math.round( Date.now()/1000 ) + 3600 );
              
              _console.log( 'localStorage - elasticSearch_latitude', localStorage.getItem('elasticSearch_latitude') );
              _console.log( 'localStorage - elasticSearch_longitude', localStorage.getItem('elasticSearch_longitude') );
              _console.log( 'localStorage - elasticSearch_geo_expire', localStorage.getItem('elasticSearch_geo_expire') );
            }
          },


          /**
           * Initialize elasticFilter binding
           * @param {type} element
           * @param {type} valueAccessor
           * @param {type} allBindings
           * @param {type} viewModel
           * @param {type} bindingContext
           * @returns {unresolved}
           */
          init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            _console.debug( 'elasticFilterFacets init', [ element, valueAccessor, allBindings, viewModel, bindingContext ] );

            var
              /**
               * Filter object to work with
               */
              Filter  = bindings.elasticFilter,

              /**
               * Filter form
               */
              form    = $( element ),

              /**
               * Filter controls
               */
              filters = $( 'input,select', form );

            /**
             * Define Scope
             */
            var scope = form.data( 'scope' );

            /**
             * Define settings
             */
            if ( typeof Filter[scope] === 'undefined' ) {
              Filter[scope] = {};
            }
            Filter[scope]                  = $.extend( new Filter.settings(), valueAccessor() );
            Filter.loader                  = $( Filter[scope].loader_selector );
            Filter[scope].form             = form;
            Filter[scope].initial_per_page = Filter[scope].per_page;
            viewModel[scope].facetLabels( Filter[scope].facets );

            /**
             *
             */
            Filter.determineCoords( Filter, scope, viewModel );

            /**
             * Render new facets
             */
            $(document).on('elasticFilter.submit.success', function() {
              if ( Filter[scope].current_filters && Filter[scope].current_filters.terms ) {
                $.each( Filter[scope].current_filters.terms, function(key, value) {
                  /**
                   * WOW! Closure!
                   */
                  $( '[name="'+(function(){return Filter[scope].facet_input;}).call(this)+'['+key+']"]', Filter[scope].form ).val( value );
                });
              }
              $(document).trigger( 'elasticFilter.facets.render', [Filter[scope].form] );
            });

            _console.log( 'Current Filter settings', Filter[scope] );

            /**
             * Bind change event
             */
            filters.live('change', function(){
              Filter.flushSettings( scope );
              Filter.submit( viewModel[scope], scope );
            });

            /**
             * Initial filter submit
             */
            Filter.submit( viewModel[scope], scope );
          }

        },

        /**
         * Elastic filter sorting controls binding
         */
        elasticSortControl: {

          /**
           * Default settings
           */
          settings: function() {

            /**
             * Button class selector
             */
            this.button_class = 'df_element';

            /**
             * Active button class
             */
            this.active_button_class = 'df_sortable_active';
          },

          /**
           * Initialize current binding
           */
          init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            _console.log( 'elasticSortControl Init', [ element, valueAccessor, allBindings, viewModel, bindingContext ] );

            var
              /**
               * Filter object to work with
               */
              Filter = bindings.elasticFilter,

              /**
               * Reference to tis sorter object
               */
              Sorter = bindings.elasticSortControl,

              /**
               * Current scope
               * Allows to use multiple filters on one page
               */
              scope = $(element).data('scope');

            /**
             * Set settings
             */
            Sorter[scope] = $.extend( new Sorter.settings(), valueAccessor() );

            /**
             * Bind buttons events
             */
            var buttons = $('.'+Sorter[scope].button_class, element);
            $(document).on('elasticFilter.submit.success', function() {

              buttons.unbind('click');

              buttons.on('click', function() {

                buttons.removeClass(Sorter[scope].active_button_class);
                $(this).addClass(Sorter[scope].active_button_class);

                var data = $(this).data();

                if ( !data.direction ) {
                  $(this).data('direction', Filter[scope].sort_dir);
                }

                $(this).data('direction', data.direction==='asc'?'desc':'asc');

                Filter.flushSettings( scope );
                Filter[scope].sort_by = data.type;
                Filter[scope].sort_dir = data.direction;
                Filter.submit( viewModel[scope], scope );
              });
            });
          }
        },

        /**
         * Elastic filter time control binding
         */
        elasticTimeControl: {

          /**
           * Default settings
           */
          settings: function() {

            /**
             * Button class selector
             */
            this.button_class = 'df_element';

            /**
             * Active button selector
             */
            this.active_button_class = 'df_sortable_active';
          },

          /**
           * Initialize current binding
           */
          init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            _console.log( 'elasticTimeControl Init', [ element, valueAccessor, allBindings, viewModel, bindingContext ] );

            var
              /**
               * Filter object to work with
               */
              Filter = bindings.elasticFilter,

              /**
               * Time controll object
               */
              Time = bindings.elasticTimeControl,

              /**
               * Current scope
               * Allows to use multiple filters on one page
               */
              scope = $(element).data('scope');

            /**
             * Set settings
             */
            Time[scope] = $.extend( new Time.settings(), valueAccessor() );

            /**
             * Bind button events
             */
            var buttons = $( '.' + Time[scope].button_class, element );
            $(document).on( 'elasticFilter.submit.success', function() {

              buttons.unbind( 'click' );

              buttons.on( 'click', function() {

                buttons.removeClass( Time[scope].active_button_class );
                $(this).addClass( Time[scope].active_button_class );

                var data = $(this).data();

                Filter.flushSettings( scope );
                if ( data.direction ) {
                  Filter[scope].sort_dir = data.direction;
                }
                Filter[scope].period = $(this).data('type');
                Filter.submit( viewModel[scope], scope );
              });
            });
          }
        },

        /**
         * Show More button binding
         */
        filterShowMoreControl: {

          /**
           * Default settings
           */
          settings: function() {

            /**
             * Show more count
             */
            this.count = 10;

          },

          /**
           * Initialize current binding
           */
          init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            _console.log( 'filterShowMoreControl init', [ element, valueAccessor, allBindings, viewModel, bindingContext ] );

            var
              /**
               * Show more object
               */
              ShowMore = bindings.filterShowMoreControl,

              /**
               * Filter object
               */
              Filter = bindings.elasticFilter,

              /**
               *
               * @type @call;$@call;data
               */
              scope = $(element).data('scope');

            /**
             * Set settings
             */
            ShowMore[scope]         = $.extend( new ShowMore.settings(), valueAccessor() );
            viewModel[scope].moreCount( ShowMore[scope].count );

            /**
             * Bind button events
             */
            $(element).on('click', function(){
              Filter[scope].per_page = ShowMore[scope].count;
              Filter[scope].offset   = viewModel[scope].count();
              Filter[scope].is_more  = true;
              Filter.submit( viewModel[scope], scope );
            });
          }
        },

        /**
         * Foreach for Object
         */
        foreachprop: {

          /**
           * Transform object to array
           * @param {type} obj
           * @returns {Array}
           */
          transformObject: function (obj) {
            var properties = [];
            for (var key in obj) {
              if (obj.hasOwnProperty(key)) {
                properties.push({ key: key, value: obj[key] });
              }
            }
            return properties;
          },

          /**
           * Initialize binding
           */
          init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            _console.log( 'foreachprop', [ element, valueAccessor, allBindings, viewModel, bindingContext ] );

            var value = ko.utils.unwrapObservable(valueAccessor()),
            properties = ko.bindingHandlers.foreachprop.transformObject(value);
            ko.applyBindingsToNode(element, { foreach: properties }, bindingContext);
            return { controlsDescendantBindings: true };
          }
        }

      },

      /**
       * HTTP Client
       * @type object
       */
      client = null,

      /**
       * The API. Currently does search only
       * @type type
       */
      api = {

        /**
         * Default index
         */
        _index: 'documents',

        /**
         * Default controllers uri
         */
        _controllers: {
          search: '_search'
        },

        /**
         * Index setter
         * @param {type} index
         * @returns {_L20.$.fn.elasticSearch.api}
         */
        index: function( index ) {
          _console.debug( 'API Index extend', index );

          if ( index ) {
            this._index = index;
          }

          return this;
        },

        /**
         * Controllers setter
         * @param {type} controllers
         * @returns {_L20.$.fn.elasticSearch.api}
         */
        controllers: function( controllers ) {
          _console.debug( 'API Controllers extend', controllers );

          $.extend( this._controllers, controllers );

          return this;
        },

        /**
         * Do Search request
         * @param {type} query
         * @param {type} type
         * @param {type} success
         * @param {type} error
         *
         */
        search: function( query, type, success, error, abort ) {
          _console.log( 'API', api );
          _console.log( 'API Search', arguments );

          if ( !type ) {
            type = '';
          }

          if ( client ) {
            if ( typeof this.ejs_xhr !== 'undefined' && abort ) {
              this.ejs_xhr.abort();
            }
            this.ejs_xhr = client.get( api._index+'/'+type+'/'+api._controllers.search, 'source='+encodeURIComponent(JSON.stringify( query )), success, error );
          } else {
            _console.error( 'API Search Error', 'Client is undefined' );
          }

          return api;
        }

      },

      /**
       * Init Client and Apply Bindings
       * @returns {_L6.$.fn.ddpElasticSuggest}
       */
      init = function() {
        _console.debug( 'Plugin init', {self:self, options:options});

        /**
         * Needs KO
         */
        if ( typeof ko === 'undefined' ) {
          _console.error( typeof ko, 'Knockout.js is required.' );
        }

        /**
         * Needs HTTP client
         */
        if ( typeof ejs.HttpClient === 'undefined' ) {
          _console.error( typeof ejs.HttpClient, 'HttpClient is required.' );
        }

        /**
         * Register bindings
         */
        for( var i in bindings ) {
          if ( bindings.hasOwnProperty( i ) ) {
            ko.bindingHandlers[i] = bindings[i];
          }
        }
        _console.debug( 'Bindings registered', ko.bindingHandlers );

        /**
         * Init Client
         */
        client = ejs.HttpClient( options.endpoint );
        _console.debug( 'Init Options', options );

        if ( options.headers ) {
          for( i in options.headers ) {
            if ( options.headers.hasOwnProperty( i ) ) {
              client.addHeader( i, options.headers[i] );
            }
          }
        }
        _console.debug( 'Client init', client );

        var scopes = [];
        $( '[data-scope]', self[0] ).each( function() {
          if ( scopes.indexOf( $( this ).data('scope') ) < 0 ) {
            scopes.push( $( this ).data('scope') );
          }
        });
        _console.log( 'Filters enabled', scopes );

        var suggesters = [];
        $( '[data-suggest]', self[0] ).each( function() {
          if ( suggesters.indexOf( $( this ).data('suggest') ) < 0 ) {
            suggesters.push( $( this ).data('suggest') );
          }
        });
        _console.log( 'Suggesters enabled', suggesters );

        /**
         * Virtualize 'html' binding. Needs to be able to use html binding with virtual elements.
         */
        {
          var overridden = ko.bindingHandlers.html.update;

          ko.bindingHandlers.html.update = function(element, valueAccessor) {
            if (element.nodeType === 8) {
              var html = ko.utils.unwrapObservable(valueAccessor());

              ko.virtualElements.emptyNode(element);
              if ((html !== null) && (html !== undefined)) {
                if (typeof html !== 'string') {
                  html = html.toString();
                }

                var parsedNodes = ko.utils.parseHtmlFragment(html);
                if (parsedNodes) {
                  var endCommentNode = element.nextSibling;
                  for (var i = 0, j = parsedNodes.length; i < j; i++) {
                    endCommentNode.parentNode.insertBefore(parsedNodes[i], endCommentNode);
                  }
                }
              }
            } else { // plain node
              overridden(element, valueAccessor);
            }
          };
        }
        ko.virtualElements.allowedBindings.html = true;

        /**
         * Apply view model
         */
        ko.applyBindings( new ViewModel( scopes, suggesters ), self[0] );

        return self;
      };

    return init();

  };

  /**
   * Form Serialize Object
   */
  $.fn.serializeObject = function() {
    var self = this,
        json = {},
        push_counters = {},
        patterns = {
          "validate": /^[a-zA-Z][a-zA-Z0-9_.-]*(?:\[(?:\d*|[a-zA-Z0-9_.-]+)\])*$/,
          "key": /[a-zA-Z0-9_.-]+|(?=\[\])/g,
          "push": /^$/,
          "fixed": /^\d+$/,
          "named": /^[a-zA-Z0-9_.-]+$/
        };

    this.build = function(base, key, value) {
      base[key] = value;
      return base;
    };

    this.push_counter = function(key) {
      if (push_counters[key] === undefined) {
        push_counters[key] = 0;
      }
      return push_counters[key]++;
    };

    $.each($(this).serializeArray(), function() {

      if (!patterns.validate.test(this.name)) {
        return;
      }

      var k,
          keys = this.name.match(patterns.key),
          merge = this.value,
          reverse_key = this.name;

      while ((k = keys.pop()) !== undefined) {

        reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

        if (k.match(patterns.push)) {
          merge = self.build([], self.push_counter(reverse_key), merge);
        }

        else if (k.match(patterns.fixed)) {
          merge = self.build([], k, merge);
        }

        else if (k.match(patterns.named)) {
          merge = self.build({}, k, merge);
        }
      }

      json = $.extend(true, json, merge);
    });

    return json;
  };

  /**
   * Clean object from empty values
   * @param {type} target
   * @returns {unresolved}
   */
  var cleanObject = $.fn.cleanObject = function ( target ) {
    Object.keys( target ).map( function ( key ) {
      if ( target[ key ] instanceof Object ) {
        if ( ! Object.keys( target[ key ] ).length && typeof target[ key ].getMonth !== 'function') {
          delete target[ key ];
        }
        else {
          cleanObject( target[ key ] );
        }
      }
      else if ( target[ key ] === "" || target[ key ] === null ) {
        delete target[ key ];
      }
    } );
    return target;
  };

})(jQuery);

});

define('lib-model', function (require, exports, module) {
define("udx.model",["udx.utility","async","jquery"],function(){console.debug("udx.utility.model","loaded");require("async").auto,require("async").series,require("udx.utility");return{load:function(a,b){return console.debug("udx.utility.model","load()"),jQuery.ajax({url:_ajax,cache:!1,dataType:"json",data:{action:"wpp_xmli_model",model:a||"state"},complete:function(){var a;return arguments[0].responseJSON&&(a=arguments[0].responseJSON),"object"==typeof a&&a.ok&&a.data?b(null,a.data):b(new Error("Could not load model."))}})},loadMultiple:function(){}}});
define("udx.model.validation",function(){});
});

define('utility', function (require, exports, module) {
define("analysis.client",["jquery.elasticsearch","analysis.visualizer"],function(){function a(){return h||b()}function b(a,b,c){return i={host:a,index:b,type:c},h=new jQuery.es.Client({hosts:a})}function c(b,c){console.debug("getMapping",typeof c);var d={index:i.index||"deafult",type:i.type||"profile"};return a().indices.getMapping(d).then(function(a){"function"==typeof c&&c.call(null,a["jezf-truq-qgox-hfxp"].mappings.profile||{},d)})}function d(b){console.debug("getResults",typeof handleResponse);var c={index:i.index||"deafult",type:i.type||"profile",from:0,size:0,body:b};return a().suggest(c).then(function(a){console.debug("getSuggestion","parseResponse",typeof a),"function"==typeof handleResponse&&handleResponse.call(null,null,a,"suggest",c)})}function e(b,c){console.debug("getMeta",typeof c);var d={index:i.index||"deafult",type:i.type||"profile"};return a().indices.getMapping(d).then(function(a){"function"==typeof c&&c.call(null,a["jezf-truq-qgox-hfxp"].mappings[i.type]._meta||{},"meta",d)})}function f(b,c,d){console.debug("getFacets",typeof d);var e={index:i.index||"deafult",type:i.type||"profile",from:0,size:0,body:{query:{filtered:{query:b}},facets:c}};return a().search(e).then(function(a){"function"==typeof d&&d.call(null,null,a.facets,"facets",e)}).then(function(a){console.debug("handleError",arguments),"function"==typeof d&&d.call(a,null,{},"facets",e)})}function g(b,c){console.debug("getResults",typeof c);var d={index:i.index||"deafult",type:i.type||"profile",from:0,size:0,body:b};return a().search(d).then(function(a){console.debug("getResults","parseResponse",typeof a),"function"==typeof c&&c.call(null,null,a,"search",d)})}console.debug("analysis.client","loaded");var h,i={};return{client:a,createClient:b,getSuggestion:d,getMeta:e,getMapping:c,getFacets:f,getResults:g}});
define("analysis.visualizer",["http://www.google.com/jsapi/"],function(){function a(){console.debug("analysis.visualizer","googleVisualizationReady")}function b(a){var b={raleigh:"Raleigh",port:"New Port","new":"Wilmington",west:"West River",lake:"Lake",south:"South Raleigh",north:"North Raleigh",est:"Eastern",east:"East Brook",wilfred:"Wilfred",zena:"Durham",en_us:"English",twitter:"Twitter",facebook:"Facebook",male:"Male",female:"Female"};return b[a]||a}function c(a,b){console.debug("Pie");var c=google.visualization.arrayToDataTable(b),d=jQuery('<div class="result-piegraph"></div>');return jQuery(".query-result").append(d),new google.visualization.PieChart(d.get(0)).draw(c,{title:a}),g[a]={title:a,element:d},f}function d(a,b){console.debug("Map");var c=google.visualization.arrayToDataTable(b.raw),d=jQuery('<div class="result-map"></div>');return jQuery(".query-result").append(d),new google.visualization.GeoChart(d.get(0)).draw(c,{region:"US",displayMode:"regions",resolution:"provinces",enableRegionInteractivity:!0}),g[a]={title:a,element:d},f}function e(a,c){console.debug("Table",c);var d=new google.visualization.DataTable,e=c.total,h=c.terms||[];d.addColumn("string","Metric"),d.addColumn("number","Percentage"),d.addColumn("number","Count"),h.forEach(function(a){d.addRows([[b(a.term),{v:Math.round(a.count/e*100),f:Math.round(a.count/e*100)+"%"},{v:a.count}]])});var i=jQuery('<div class="result-table"></div>');return jQuery(".query-result").append(i),new google.visualization.Table(i.get(0)).draw(d,{showRowNumber:!1,pageSize:5}),g[a]={title:a,element:i},f}console.debug("analysis.visualizer"),google.load("visualization","1",{packages:["geochart","corechart","table"],callback:a});var f=this,g={};return{Map:d,Table:e,Pie:c,_cached:g}});
!function(a){a.fn.smart_dom_button=function(b){var c=a.extend({debug:!1,action_attribute:"action_attribute",response_container:"response_container",ajax_action:"action",label_attributes:{process:"processing_label",revert_label:"revert_label",verify_action:"verify_action"}},b);return log=function(a,b){c.debug&&window.console&&console.debug&&("error"==b?console.error(a):console.log(a))},get_label=function(b){var c=a(b).get(0).tagName,d="";switch(c){case"SPAN":d=a(b).text();break;case"INPUT":d=a(b).val()}return d},set_label=function(b,c){switch(c.type){case"SPAN":a(c.button).text(b);break;case"INPUT":a(c.button).val(b)}return b},do_execute=function(b){var d={button:b,type:a(b).get(0).tagName,original_label:a(b).attr("original_label")?a(b).attr("original_label"):get_label(b)};c.wrapper&&a(d.button).closest(c.wrapper).length?(d.wrapper=a(d.button).closest(c.wrapper),d.use_wrapper=!0):(d.wrapper=d.button,d.use_wrapper=!1),d.the_action=a(d.wrapper).attr(c.action_attribute)?a(d.wrapper).attr(c.action_attribute):!1,c.label_attributes.processing&&a(d.wrapper).attr(c.label_attributes.processing)&&(d.processing_label=a(d.wrapper).attr(c.label_attributes.processing)?a(d.wrapper).attr(c.label_attributes.processing):!1),c.label_attributes.verify_action&&a(d.wrapper).attr(c.label_attributes.verify_action)&&(d.verify_action=a(d.wrapper).attr(c.label_attributes.verify_action)?a(d.wrapper).attr(c.label_attributes.verify_action):!1),c.label_attributes.revert_label&&a(d.wrapper).attr(c.label_attributes.revert_label)&&(d.revert_label=a(d.wrapper).attr(c.label_attributes.revert_label)?a(d.wrapper).attr(c.label_attributes.revert_label):!1,a(d.wrapper).attr("original_label")||(d.original_label=get_label(d.button),a(d.wrapper).attr("original_label",d.original_label))),d.the_action&&(!d.verify_action||confirm(d.verify_action))&&(d.use_wrapper&&(a(c.response_container,d.wrapper).length||a(d.wrapper).append('<span class="response_container"></span>'),d.response_container=a(".response_container",d.wrapper),a(d.response_container).removeClass(),a(d.response_container).addClass("response_container"),d.processing_label&&a(d.response_container).html(d.processing_label)),"ui"==d.the_action?(d.revert_label&&(get_label(d.button)==d.revert_label?set_label(d.original_label,d):set_label(d.revert_label,d)),a(d.wrapper).attr("toggle")&&a(a(d.wrapper).attr("toggle")).toggle(),a(d.wrapper).attr("show")&&a(a(d.wrapper).attr("show")).show(),a(d.wrapper).attr("hide")&&a(a(d.wrapper).attr("hide")).hide()):a.post(ajaxurl,{_wpnonce:flawless_admin.actions_nonce,action:c.ajax_action,the_action:d.the_action},function(b){b&&b.success&&(a(d.response_container).show(),b.css_class&&a(d.response_container).addClass(b.css_class),b.remove_element&&a(b.remove_element).length&&a(b.remove_element).remove(),a(d.response_container).html(b.message),setTimeout(function(){a(d.response_container).fadeOut("slow",function(){a(d.response_container).remove()})},1e4))},"json"))},a(this).click(function(){log("Button triggered."),do_execute(this)}),this}}(jQuery);
!function(a){"use strict";a.prototype.date_selector=function(b){var d={element:this,cache:{}};d.options=a.extend(!0,{flat:!1,starts:1,prev:"&#9664;",next:"&#9654;",lastSel:!1,mode:"single",view:"days",calendars:1,format:"Y-m-d",position:"bottom",eventName:"click",onRender:function(){return{}},onChange:function(){return!0},onShow:function(){return!0},onBeforeShow:function(){return!0},locale:{days:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],daysShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat","Sun"],daysMin:["Su","Mo","Tu","We","Th","Fr","Sa","Su"],months:["January","February","March","April","May","June","July","August","September","October","November","December"],monthsShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],weekMin:"wk"},views:{years:"date_selector_view_years",moths:"date_selector_view_months",days:"date_selector_view_days"},template:{wrapper:'<div class="date_selector"><div class="date_selector_borderT" /><div class="date_selector_borderB" /><div class="date_selector_borderL" /><div class="date_selector_borderR" /><div class="date_selector_borderTL" /><div class="date_selector_borderTR" /><div class="date_selector_borderBL" /><div class="date_selector_borderBR" /><div class="date_selector_container"><table cellspacing="0" cellpadding="0"><tbody><tr></tr></tbody></table></div></div>',head:["<td>",'<table cellspacing="0" cellpadding="0">',"<thead>","<tr>",'<th class="date_selectorGoPrev"><a href="#"><span><%=prev%></span></a></th>','<th colspan="6" class="date_selector_month"><a href="#"><span></span></a></th>','<th class="date_selectorGoNext"><a href="#"><span><%=next%></span></a></th>',"</tr>",'<tr class="date_selector_dow">',"<th><span><%=week%></span></th>","<th><span><%=day1%></span></th>","<th><span><%=day2%></span></th>","<th><span><%=day3%></span></th>","<th><span><%=day4%></span></th>","<th><span><%=day5%></span></th>","<th><span><%=day6%></span></th>","<th><span><%=day7%></span></th>","</tr>","</thead>","</table></td>"],space:'<td class="date_selector_space"><div></div></td>',days:['<tbody class="date_selector_days">',"<tr>",'<th class="date_selectorWeek"><a href="#"><span><%=weeks[0].week%></span></a></th>','<td class="<%=weeks[0].days[0].classname%>"><a href="#"><span><%=weeks[0].days[0].text%></span></a></td>','<td class="<%=weeks[0].days[1].classname%>"><a href="#"><span><%=weeks[0].days[1].text%></span></a></td>','<td class="<%=weeks[0].days[2].classname%>"><a href="#"><span><%=weeks[0].days[2].text%></span></a></td>','<td class="<%=weeks[0].days[3].classname%>"><a href="#"><span><%=weeks[0].days[3].text%></span></a></td>','<td class="<%=weeks[0].days[4].classname%>"><a href="#"><span><%=weeks[0].days[4].text%></span></a></td>','<td class="<%=weeks[0].days[5].classname%>"><a href="#"><span><%=weeks[0].days[5].text%></span></a></td>','<td class="<%=weeks[0].days[6].classname%>"><a href="#"><span><%=weeks[0].days[6].text%></span></a></td>',"</tr>","<tr>",'<th class="date_selectorWeek"><a href="#"><span><%=weeks[1].week%></span></a></th>','<td class="<%=weeks[1].days[0].classname%>"><a href="#"><span><%=weeks[1].days[0].text%></span></a></td>','<td class="<%=weeks[1].days[1].classname%>"><a href="#"><span><%=weeks[1].days[1].text%></span></a></td>','<td class="<%=weeks[1].days[2].classname%>"><a href="#"><span><%=weeks[1].days[2].text%></span></a></td>','<td class="<%=weeks[1].days[3].classname%>"><a href="#"><span><%=weeks[1].days[3].text%></span></a></td>','<td class="<%=weeks[1].days[4].classname%>"><a href="#"><span><%=weeks[1].days[4].text%></span></a></td>','<td class="<%=weeks[1].days[5].classname%>"><a href="#"><span><%=weeks[1].days[5].text%></span></a></td>','<td class="<%=weeks[1].days[6].classname%>"><a href="#"><span><%=weeks[1].days[6].text%></span></a></td>',"</tr>","<tr>",'<th class="date_selectorWeek"><a href="#"><span><%=weeks[2].week%></span></a></th>','<td class="<%=weeks[2].days[0].classname%>"><a href="#"><span><%=weeks[2].days[0].text%></span></a></td>','<td class="<%=weeks[2].days[1].classname%>"><a href="#"><span><%=weeks[2].days[1].text%></span></a></td>','<td class="<%=weeks[2].days[2].classname%>"><a href="#"><span><%=weeks[2].days[2].text%></span></a></td>','<td class="<%=weeks[2].days[3].classname%>"><a href="#"><span><%=weeks[2].days[3].text%></span></a></td>','<td class="<%=weeks[2].days[4].classname%>"><a href="#"><span><%=weeks[2].days[4].text%></span></a></td>','<td class="<%=weeks[2].days[5].classname%>"><a href="#"><span><%=weeks[2].days[5].text%></span></a></td>','<td class="<%=weeks[2].days[6].classname%>"><a href="#"><span><%=weeks[2].days[6].text%></span></a></td>',"</tr>","<tr>",'<th class="date_selectorWeek"><a href="#"><span><%=weeks[3].week%></span></a></th>','<td class="<%=weeks[3].days[0].classname%>"><a href="#"><span><%=weeks[3].days[0].text%></span></a></td>','<td class="<%=weeks[3].days[1].classname%>"><a href="#"><span><%=weeks[3].days[1].text%></span></a></td>','<td class="<%=weeks[3].days[2].classname%>"><a href="#"><span><%=weeks[3].days[2].text%></span></a></td>','<td class="<%=weeks[3].days[3].classname%>"><a href="#"><span><%=weeks[3].days[3].text%></span></a></td>','<td class="<%=weeks[3].days[4].classname%>"><a href="#"><span><%=weeks[3].days[4].text%></span></a></td>','<td class="<%=weeks[3].days[5].classname%>"><a href="#"><span><%=weeks[3].days[5].text%></span></a></td>','<td class="<%=weeks[3].days[6].classname%>"><a href="#"><span><%=weeks[3].days[6].text%></span></a></td>',"</tr>","<tr>",'<th class="date_selectorWeek"><a href="#"><span><%=weeks[4].week%></span></a></th>','<td class="<%=weeks[4].days[0].classname%>"><a href="#"><span><%=weeks[4].days[0].text%></span></a></td>','<td class="<%=weeks[4].days[1].classname%>"><a href="#"><span><%=weeks[4].days[1].text%></span></a></td>','<td class="<%=weeks[4].days[2].classname%>"><a href="#"><span><%=weeks[4].days[2].text%></span></a></td>','<td class="<%=weeks[4].days[3].classname%>"><a href="#"><span><%=weeks[4].days[3].text%></span></a></td>','<td class="<%=weeks[4].days[4].classname%>"><a href="#"><span><%=weeks[4].days[4].text%></span></a></td>','<td class="<%=weeks[4].days[5].classname%>"><a href="#"><span><%=weeks[4].days[5].text%></span></a></td>','<td class="<%=weeks[4].days[6].classname%>"><a href="#"><span><%=weeks[4].days[6].text%></span></a></td>',"</tr>","<tr>",'<th class="date_selectorWeek"><a href="#"><span><%=weeks[5].week%></span></a></th>','<td class="<%=weeks[5].days[0].classname%>"><a href="#"><span><%=weeks[5].days[0].text%></span></a></td>','<td class="<%=weeks[5].days[1].classname%>"><a href="#"><span><%=weeks[5].days[1].text%></span></a></td>','<td class="<%=weeks[5].days[2].classname%>"><a href="#"><span><%=weeks[5].days[2].text%></span></a></td>','<td class="<%=weeks[5].days[3].classname%>"><a href="#"><span><%=weeks[5].days[3].text%></span></a></td>','<td class="<%=weeks[5].days[4].classname%>"><a href="#"><span><%=weeks[5].days[4].text%></span></a></td>','<td class="<%=weeks[5].days[5].classname%>"><a href="#"><span><%=weeks[5].days[5].text%></span></a></td>','<td class="<%=weeks[5].days[6].classname%>"><a href="#"><span><%=weeks[5].days[6].text%></span></a></td>',"</tr>","</tbody>"],months:['<tbody class="<%=className%>">',"<tr>",'<td colspan="2"><a href="#"><span><%=data[0]%></span></a></td>','<td colspan="2"><a href="#"><span><%=data[1]%></span></a></td>','<td colspan="2"><a href="#"><span><%=data[2]%></span></a></td>','<td colspan="2"><a href="#"><span><%=data[3]%></span></a></td>',"</tr>","<tr>",'<td colspan="2"><a href="#"><span><%=data[4]%></span></a></td>','<td colspan="2"><a href="#"><span><%=data[5]%></span></a></td>','<td colspan="2"><a href="#"><span><%=data[6]%></span></a></td>','<td colspan="2"><a href="#"><span><%=data[7]%></span></a></td>',"</tr>","<tr>",'<td colspan="2"><a href="#"><span><%=data[8]%></span></a></td>','<td colspan="2"><a href="#"><span><%=data[9]%></span></a></td>','<td colspan="2"><a href="#"><span><%=data[10]%></span></a></td>','<td colspan="2"><a href="#"><span><%=data[11]%></span></a></td>',"</tr>","</tbody>"]}},b),d.log="function"==typeof d.log?d.log:function(){},d.initialize="function"==typeof d.initialize?d.initialize:function(){return d.log("s.initialize()","function"),d.extendDate(d.options.locale),d.options.calendars=Math.max(1,parseInt(d.options.calendars,10)||1),d.options.mode=/single|multiple|range/.test(d.options.mode)?d.options.mode:"single",d.element.each(function(b,c){var j,k,l="date_selector_"+parseInt(1e3*Math.random());if(a(d.element).data("date_selector_initialized"))return void d.log("s.initialize() - DOM element already initialized, skipping.","function");if(d.options.date||(d.options.date=new Date),d.options.date.constructor===String&&(d.options.date=g(d.options.date,d.options.format),d.options.date.setHours(0,0,0,0)),"single"!=d.options.mode)if(d.options.date.constructor!=Array)d.options.date=[d.options.date.valueOf()],"range"===d.options.mode&&d.options.date.push(new Date(d.options.date[0]).setHours(23,59,59,0).valueOf());else{for(var n=0;n<d.options.date.length;n++)d.options.date[n]=g(d.options.date[n],d.options.format).setHours(0,0,0,0).valueOf();"range"===d.options.mode&&(d.options.date[1]=new Date(d.options.date[1]).setHours(23,59,59,0).valueOf())}else d.options.date=d.options.date.valueOf();d.options.current=d.options.current?g(d.options.current,d.options.format):new Date,d.options.current.setDate(1),d.options.current.setHours(0,0,0,0),d.options.id=l,a(c).data("date_selector_id",d.options.id),j=a(d.options.template.wrapper).attr("id",l).bind("mousedown",i).data("date_selector",d.options),d.options.className&&j.addClass(d.options.className);for(var o="",n=0;n<d.options.calendars;n++)k=d.options.starts,n>0&&(o+=d.options.template.space),o+=e(d.options.template.head.join(""),{week:d.options.locale.weekMin,prev:d.options.prev,next:d.options.next,day1:d.options.locale.daysMin[k++%7],day2:d.options.locale.daysMin[k++%7],day3:d.options.locale.daysMin[k++%7],day4:d.options.locale.daysMin[k++%7],day5:d.options.locale.daysMin[k++%7],day6:d.options.locale.daysMin[k++%7],day7:d.options.locale.daysMin[k++%7]});j.find("tr:first").append(o).find("table").addClass(d.options.views[d.options.view]),f(j.get(0)),d.options.flat?(j.appendTo(this).show().css("position","relative"),h(j.get(0))):(j.appendTo(document.body),a(this).bind(d.options.eventName,m)),a(d.element).data("date_selector_initialized",!0)})};var e=function(a,b){d.log("tmpl()","function");var c=/\W/.test(a)?new Function("obj","var p=[],print=function(){p.push.apply(p,arguments);};with(obj){p.push('"+a.replace(/[\r\t\n]/g," ").split("<%").join("	").replace(/((^|%>)[^\t]*)'/g,"$1\r").replace(/\t=(.*?)%>/g,"',$1,'").split("	").join("');").split("%>").join("p.push('").split("\r").join("\\'")+"');}return p.join('');"):d.cache[a]=d.cache[a]||e(document.getElementById(a).innerHTML);return b?c(b):c},f=function(b){d.log("fill()","function"),b=a(b);var c,f,g,h,i,j,k,l,m,n=Math.floor(d.options.calendars/2),o=0;b.find("td>table tbody").remove();for(var p=0;p<d.options.calendars;p++){if(c=new Date(d.options.current),c.addMonths(-n+p),m=b.find("table").eq(p+1),"object"==typeof m&&"undefined"!=typeof m[0])switch(m[0].className){case"date_selector_view_days":g=d.formatDate(c,"B, Y");break;case"date_selector_view_months":g=c.getFullYear();break;case"date_selector_view_years":g=c.getFullYear()-6+" - "+(c.getFullYear()+5)}m.find("thead tr:first th:eq(1) span").text(g),g=c.getFullYear()-6,f={data:[],className:"date_selector_years"};for(var q=0;12>q;q++)f.data.push(g+q);l=e(d.options.template.months.join(""),f),c.setDate(1),f={weeks:[],test:10},h=c.getMonth();var g=(c.getDay()-d.options.starts)%7;for(c.addDays(-(g+(0>g?7:0))),i=-1,o=0;42>o;){j=parseInt(o/7,10),k=o%7,f.weeks[j]||(i=c.getWeekNumber(),f.weeks[j]={week:i,days:[]}),f.weeks[j].days[k]={text:c.getDate(),classname:[]},h!=c.getMonth()&&f.weeks[j].days[k].classname.push("date_selectorNotInMonth"),0===c.getDay()&&f.weeks[j].days[k].classname.push("date_selectorSunday"),6===c.getDay()&&f.weeks[j].days[k].classname.push("date_selectorSaturday");var r=d.options.onRender(c),s=c.valueOf();(r.selected||d.options.date===s||a.inArray(s,d.options.date)>-1||"range"===d.options.mode&&s>=d.options.date[0]&&s<=d.options.date[1])&&f.weeks[j].days[k].classname.push("date_selector_selected"),r.disabled&&f.weeks[j].days[k].classname.push("date_selector_disabled"),r.className&&f.weeks[j].days[k].classname.push(r.className),f.weeks[j].days[k].classname=f.weeks[j].days[k].classname.join(" "),o++,c.addDays(1)}l=e(d.options.template.days.join(""),f)+l,f={data:d.options.locale.monthsShort,className:"date_selector_months"},l=e(d.options.template.months.join(""),f)+l,m.append(l)}},g=function(a,b){if(d.log("parseDate()","function"),a){if(a.constructor===Date)return new Date(a);for(var c,e,f,g,h,i=a.split(/\W+/),j=b.split(/\W+/),k=new Date,l=0;l<i.length;l++)switch(j[l]){case"d":case"e":c=parseInt(i[l],10);break;case"m":e=parseInt(i[l],10)-1;break;case"Y":case"y":f=parseInt(i[l],10),f+=f>100?0:29>f?2e3:1900;break;case"H":case"I":case"k":case"l":g=parseInt(i[l],10);break;case"P":case"p":/pm/i.test(i[l])&&12>g?g+=12:/am/i.test(i[l])&&g>=12&&(g-=12);break;case"M":h=parseInt(i[l],10)}return new Date(void 0===f?k.getFullYear():f,void 0===e?k.getMonth():e,void 0===c?k.getDate():c,void 0===g?k.getHours():g,void 0===h?k.getMinutes():h,0)}};d.formatDate="function"==typeof d.formatDate?d.formatDate:function(a,b){g&&g.log("s.formatDate()","function");var c=a.getMonth(),d=a.getDate(),e=a.getFullYear(),f=(a.getWeekNumber(),a.getDay()),g={},h=a.getHours(),i=h>=12,j=i?h-12:h,k=a.getDayOfYear();0===j&&(j=12);for(var l,m=a.getMinutes(),n=a.getSeconds(),o=b.split(""),p=0;p<o.length;p++){switch(l=o[p],o[p]){case"a":l=a.getDayName();break;case"A":l=a.getDayName(!0);break;case"b":l=a.getMonthName();break;case"B":l=a.getMonthName(!0);break;case"C":l=1+Math.floor(e/100);break;case"d":l=10>d?"0"+d:d;break;case"e":l=d;break;case"H":l=10>h?"0"+h:h;break;case"I":l=10>j?"0"+j:j;break;case"j":l=100>k?10>k?"00"+k:"0"+k:k;break;case"k":l=h;break;case"l":l=j;break;case"m":l=9>c?"0"+(1+c):1+c;break;case"M":l=10>m?"0"+m:m;break;case"p":case"P":l=i?"PM":"AM";break;case"s":l=Math.floor(a.getTime()/1e3);break;case"S":l=10>n?"0"+n:n;break;case"u":l=f+1;break;case"w":l=f;break;case"y":l=(""+e).substr(2,2);break;case"Y":l=e}o[p]=l}return o.join("")},d.extendDate="function"==typeof d.extendDate?d.extendDate:function(a){Date.prototype.tempDate||(Date.prototype.tempDate=null,Date.prototype.months=a.months,Date.prototype.monthsShort=a.monthsShort,Date.prototype.days=a.days,Date.prototype.daysShort=a.daysShort,Date.prototype.getMonthName=function(a){return this[a?"months":"monthsShort"][this.getMonth()]},Date.prototype.getDayName=function(a){return this[a?"days":"daysShort"][this.getDay()]},Date.prototype.addDays=function(a){this.setDate(this.getDate()+a),this.tempDate=this.getDate()},Date.prototype.addMonths=function(a){null===this.tempDate&&(this.tempDate=this.getDate()),this.setDate(1),this.setMonth(this.getMonth()+a),this.setDate(Math.min(this.tempDate,this.getMaxDays()))},Date.prototype.addYears=function(a){null===this.tempDate&&(this.tempDate=this.getDate()),this.setDate(1),this.setFullYear(this.getFullYear()+a),this.setDate(Math.min(this.tempDate,this.getMaxDays()))},Date.prototype.getMaxDays=function(){var a,b=new Date(Date.parse(this)),c=28;for(a=b.getMonth(),c=28;b.getMonth()===a;)c++,b.setDate(c);return c-1},Date.prototype.getFirstDay=function(){var a=new Date(Date.parse(this));return a.setDate(1),a.getDay()},Date.prototype.getWeekNumber=function(){var a=new Date(this);a.setDate(a.getDate()-(a.getDay()+6)%7+3);var b=a.valueOf();return a.setMonth(0),a.setDate(4),Math.round((b-a.valueOf())/6048e5)+1},Date.prototype.getDayOfYear=function(){var a=new Date(this.getFullYear(),this.getMonth(),this.getDate(),0,0,0),b=new Date(this.getFullYear(),0,0,0,0,0),c=a-b;return Math.floor(c/24*60*60*1e3)})};var h=function(b){d.log("layout()","function");var c=a(b).data("date_selector"),e=a("#"+c.id);if(!d.options.extraHeight){var f=a(b).find("div");d.options.extraHeight=f.get(0).offsetHeight+f.get(1).offsetHeight,d.options.extraWidth=f.get(2).offsetWidth+f.get(3).offsetWidth}var g=e.find("table:first").get(0),h=g.offsetWidth,i=g.offsetHeight;e.css({width:h+d.options.extraWidth+"px",height:i+d.options.extraHeight+"px"}).find("div.date_selector_container").css({width:h+"px",height:i+"px"})},i=function(b){d.log("click()","function"),a(b.target).is("span")&&(b.target=b.target.parentNode);var c=a(b.target);if(c.is("a")){if(b.stopPropagation(),b.preventDefault(),b.target.blur(),c.hasClass("date_selector_disabled"))return!1;var e=d.options,g=c.parent(),h=g.parent().parent().parent(),i=a("table",this).index(h.get(0))-1,k=new Date(e.current),l=!1,m=!1;if(g.is("th")){if(g.hasClass("date_selectorWeek")&&"range"===d.options.mode&&!g.next().hasClass("date_selector_disabled")){var n=parseInt(g.next().text(),10);k.addMonths(i-Math.floor(d.options.calendars/2)),g.next().hasClass("date_selectorNotInMonth")&&k.addMonths(n>15?-1:1),k.setDate(n),d.options.date[0]=k.setHours(0,0,0,0).valueOf(),k.setHours(23,59,59,0),k.addDays(6),d.options.date[1]=k.valueOf(),m=!0,l=!0,d.options.lastSel=!1}else if(g.hasClass("date_selector_month"))switch(k.addMonths(i-Math.floor(d.options.calendars/2)),h.get(0).className){case"date_selector_view_days":h.get(0).className="date_selector_view_months",c.find("span").text(k.getFullYear());break;case"date_selector_view_months":h.get(0).className="date_selector_view_years",c.find("span").text(k.getFullYear()-6+" - "+(k.getFullYear()+5));break;case"date_selector_view_years":h.get(0).className="date_selector_view_days",c.find("span").text(d.formatDate(k,"B, Y"))}else if(g.parent().parent().is("thead")){switch(h.get(0).className){case"date_selector_view_days":d.options.current.addMonths(g.hasClass("date_selectorGoPrev")?-1:1);break;case"date_selector_view_months":d.options.current.addYears(g.hasClass("date_selectorGoPrev")?-1:1);break;case"date_selector_view_years":d.options.current.addYears(g.hasClass("date_selectorGoPrev")?-12:12)}m=!0}}else if(g.is("td")&&!g.hasClass("date_selector_disabled")){switch(h.get(0).className){case"date_selector_view_months":d.options.current.setMonth(h.find("tbody.date_selector_months td").index(g)),d.options.current.setFullYear(parseInt(h.find("thead th.date_selector_month span").text(),10)),d.options.current.addMonths(Math.floor(d.options.calendars/2)-i),h.get(0).className="date_selector_view_days";break;case"date_selector_view_years":d.options.current.setFullYear(parseInt(c.text(),10)),h.get(0).className="date_selector_view_months";break;default:var n=parseInt(c.text(),10);switch(k.addMonths(i-Math.floor(d.options.calendars/2)),g.hasClass("date_selectorNotInMonth")&&k.addMonths(n>15?-1:1),k.setDate(n),d.options.mode){case"multiple":n=k.setHours(0,0,0,0).valueOf(),a.inArray(n,d.options.date)>-1?a.each(d.options.date,function(a,b){return b===n?(d.options.date.splice(a,1),!1):void 0}):d.options.date.push(n);break;case"range":d.options.lastSel||(d.options.date[0]=k.setHours(0,0,0,0).valueOf()),n=k.setHours(23,59,59,0).valueOf(),n<d.options.date[0]?(d.options.date[1]=d.options.date[0]+86399e3,d.options.date[0]=n-86399e3):d.options.date[1]=n,d.options.lastSel=!d.options.lastSel;break;default:d.options.date=k.valueOf()}}m=!0,l=!0}m&&f(this),l&&d.options.onChange.apply(this,j(e))}},j=function(){d.log("prepareDate()","function");var b;return"single"===d.options.mode?(b=new Date(d.options.date),[d.formatDate(b,d.options.format),b,d.options.el]):(b=[[],[],d.options.el],a.each(d.options.date,function(a,c){var e=new Date(c);b[0].push(d.formatDate(e,d.options.format)),b[1].push(e)}),b)},k=function(){d.log("getViewport()","function");var a="CSS1Compat"===document.compatMode;return{l:window.pageXOffset||(a?document.documentElement.scrollLeft:document.body.scrollLeft),t:window.pageYOffset||(a?document.documentElement.scrollTop:document.body.scrollTop),w:window.innerWidth||(a?document.documentElement.clientWidth:document.body.clientWidth),h:window.innerHeight||(a?document.documentElement.clientHeight:document.body.clientHeight)}},l=function(a,b,c){if(d.log("isChildOf()","function"),a===b)return!0;if(a.contains)return a.contains(b);if(a.compareDocumentPosition)return!!(16&a.compareDocumentPosition(b));for(var e=d.element.parentNode;e&&e!=c;){if(e===a)return!0;e=e.parentNode}return!1},m=function(){d.log("show()","function");var b=this,e=a("#"+a(this).data("date_selector_id"));if(!e.is(":visible")){var g=e.get(0);f(g),d.options.onBeforeShow.apply(this,[g]);{var i=a(b).offset(),j=k(),l=i.top,m=i.left;a.curCSS(g,"display")}switch(e.css({visibility:"hidden",display:"block"}),h(g),d.options.position){case"top":l-=g.offsetHeight;break;case"left":m-=c.offsetWidth;break;case"right":m+=b.offsetWidth;break;case"bottom":l+=b.offsetHeight}l+g.offsetHeight>j.t+j.h&&(l=i.top-g.offsetHeight),l<j.t&&(l=i.top+this.offsetHeight+g.offsetHeight),m+g.offsetWidth>j.l+j.w&&(m=i.left-g.offsetWidth),m<j.l&&(m=i.left+this.offsetWidth),e.css({visibility:"visible",display:"block",top:l+"px",left:m+"px"}),0!=d.options.onShow.apply(this,[g])&&e.show(),a(document).bind("mousedown",{element:b,calendar_object:e,trigger:this},n)}return!1},n=function(b){d.log("hide()","function"),b.target==b.data.trigger||l(b.data.calendar_object,b.target,b.data.element)||(b.data.calendar_object.hide(),a(document).unbind("mousedown",n))};return this.showPicker=d.showPicker="function"==typeof d.showPicker?d.showPicker:function(){return d.log("showPicker()","function"),this.each(function(){a(this).data("date_selector_id")&&m.apply(this)})},this.hidePicker=d.hidePicker="function"==typeof d.hidePicker?d.hidePicker:function(){return d.log("hidePicker()","function"),this.each(function(){a(this).data("date_selector_id")&&a("#"+a(this).data("date_selector_id")).hide()})},this.setDate=d.setDate="function"==typeof d.setDate?d.setDate:function(b,c){return d.log("setDate()","function"),d.element.each(function(){if(!a(this).data("date_selector_id"))return void d.log("setDate() - Element not initialized.","function");a("#"+a(this).data("date_selector_id"));if(d.options.date=b,d.options.date||(d.options.date=new Date),d.options.date.constructor===String&&(d.options.date=g(d.options.date,d.options.format),d.options.date.setHours(0,0,0,0)),"single"!=d.options.mode)if(d.options.date.constructor!=Array)d.options.date=[d.options.date.valueOf()],"range"===d.options.mode&&d.options.date.push(new Date(d.options.date[0]).setHours(23,59,59,0).valueOf());else{for(var e=0;e<d.options.date.length;e++)"undefined"!=typeof d.options.date[e]&&(d.options.date[e]=g(d.options.date[e],d.options.format).setHours(0,0,0,0).valueOf());"range"===d.options.mode&&(d.options.date[1]=new Date(d.options.date[1]).setHours(23,59,59,0).valueOf())}else d.options.date=d.options.date.valueOf();c&&(d.options.current=new Date("single"!=d.options.mode?d.options.date[0]:d.options.date)),f(d.element.get(0))})},this.getDate=d.getDate="function"==typeof d.getDate?d.getDate:function(){return d.log("getDate()","function"),this.size()>0?j(a("#"+a(this).data("date_selector_id")).data("date_selector"))[formated?0:1]:void 0},this.Clear=d.clear="function"==typeof d.clear?d.clear:function(){return d.log("clear()","function"),this.each(function(){if(a(this).data("date_selector_id")){{a("#"+a(this).data("date_selector_id"))}d.options=d.element.data("date_selector"),"single"!=d.options.mode&&(d.options.date=[],f(d.element.get(0)))}})},this.fixLayout=d.fixLayout="function"==typeof d.fixLayout?d.fixLayout:function(){return d.log("fixLayout()","function"),this.each(function(){if(a(this).data("date_selector_id")){{a("#"+a(this).data("date_selector_id"))}d.options=d.element.data("date_selector"),d.options.flat&&h(d.element.get(0))}})},d.initialize(),this}}(jQuery);
!function(a){"use strict";a.prototype.dynamic_filter=function(b){this.s=b=a.extend(!0,{ajax:{args:{},async:!0,cache:!0,format:"json"},active_timers:{status:{}},attributes:{},attribute_defaults:{label:"",concatenation_character:", ",display:!0,default_filter_label:"",related_attributes:[],sortable:!1,filter:!1,filter_always_show:!1,filter_collapsable:4,filter_multi_select:!1,filter_show_count:!1,filter_show_label:!0,filter_note:"",filter_placeholder:"",filter_show_disabled_values:!1,filter_range:{},filter_ux:[],filter_value_order:"native",filter_values:[]},callbacks:{result_format:function(a){return a}},data:{filterable_attributes:{},current_filters:{},sortable_attributes:{},dom_results:{},rendered_query:[]},filter_types:{checkbox:{filter_show_count:!0,filter_multi_select:!0},input:{filter_always_show:!0,filter_ux:[{autocomplete:{}}]},dropdown:{default_filter_label:"Show All",filter_show_count:!0,filter_always_show:!0},range:{filter_always_show:!0}},helpers:{},instance:{},settings:{auto_request:!0,chesty_puller:!1,debug:!1,dom_limit:200,filter_id:a(this).attr("dynamic_filter")?a(this).attr("dynamic_filter"):"df_"+location.host+location.pathname,load_ahead_multiple:2,sort_by:"",sort_direction:"",set_url_hashes:!0,per_page:25,request_range:{},use_instances:!0,timers:{notice:{dim:5e3,hide:2500},filter_intent:1600,initial_request:0},messages:{no_results:"No results found.",show_more:"Show More",show_less:"Show Less",loading:"Loading...",server_fail:"Could not retrieve results due to a server error, please notify the website administrator.",total_results:"There are {1} total results.",load_more:"Showing {1} of {2} results. Show {3} more."},unique_tag:!1},classes:{wrappers:{ui_debug:"df_ui_debug",element:"df_top_wrapper",results_wrapper:"df_results_wrapper",sorter:"df_sorter",results:"df_results",filter:"df_filter",load_more:"df_load_more",status_wrapper:"df_status_wrapper"},inputs:{input:"df_input",checkbox:"df_checkbox",start_range:"df_start_range",end_range:"df_end_range",range_slider:"df_range_slider"},labels:{range_slider:"df_range_slider_label",attribute:"df_attribute_label",checkbox:"df_checkbox"},status:{success:"df_alert_success",error:"df_alert_error"},results:{row:"df_result_row",result_data:"df_result_data",list_item:"df_list_item"},element:{ajax_loading:"df_ajax_loading",filter_pending:"df_filter_pending",server_fail:"df_server_fail",have_results:"df_have_results"},filter:{inputs_list_wrapper:"df_filter_inputs_list_wrapper",inputs_list:"df_filter_inputs_list",value_wrapper:"df_filter_value_wrapper",value_label:"df_filter_value_label",value_title:"df_filter_title",value_count:"df_filter_value_count",trigger:"df_filter_trigger",filter_label:"df_filter_label",filter_note:"df_filter_note",show_more:"df_filter_toggle_list df_show_more",show_less:"df_filter_toggle_list df_show_less",selected:"df_filter_selected",extended_option:"df_extended_option",currently_extended:"df_currently_extended"},sorter:{button:"df_sortable_button",button_active:"df_sortable_active"},close:"df_close",separator:"df_separator",selected_page:"df_current",disabled_item:"df_disabled_item"},css:{results:{hidden_row:" display: none; ",visible_row:" display: block; "}},ux:{element:this,results_wrapper:a("<div></div>"),results:a("<ul></ul>"),result_item:a("<li ></li>"),sorter:a("<div></div>"),sorter_button:a("<div></div>"),filter:a("<div></div>"),filter_label:a("<div></div>"),load_more:a("<div></div>"),status:a("<div></div>")},status:{},supported:{isotope:"function"==typeof a.prototype.isotope?!0:!1,jquery_ui:"object"==typeof a.ui?!0:!1,jquery_widget:"function"==typeof a.widget?!0:!1,jquery_position:"object"==typeof a.ui.position?!0:!1,autocomplete:"object"==typeof a.ui&&"function"==typeof a.widget&&"object"==typeof a.ui.position&&"function"==typeof a.prototype.autocomplete?!0:!1,date_selector:"function"==typeof a.prototype.date_selector?!0:!1,slider:"function"==typeof a.prototype.slider?!0:!1,window_history:"object"==typeof history&&"function"==typeof history.pushState?!0:!1}},b);var c=(this.get_log=function(d,e){d="undefind"!=typeof d?d:!1,e="undefind"!=typeof e?e:!1,"object"==typeof b.log_history&&a.each(b.log_history,function(a,b){d&&d!=b.type||c(b.notice,b.type,b.console_type,!0)})},this.log=function(a,c,d,e){if(c="undefined"!=typeof c?c:"log",d=d?d:"log",b.log_history.push({notice:a,type:c,console_type:d}),a="string"==typeof a||"number"==typeof a?"DF::"+a:a,!(e||b.settings.debug&&window.console))return a;if(!e&&"object"==typeof b.debug_detail&&!b.debug_detail[c])return a;if(window.console&&console.debug)switch(d){case"error":console.error(a);break;case"info":console.info(a);break;case"time":"undefined"!=typeof console.time&&console.time(a);break;case"timeEnd":"undefined"!=typeof console.timeEnd&&console.timeEnd(a);break;case"debug":"undefined"!=typeof console.debug?console.debug(a):console.log(a);break;case"dir":"undefined"!=typeof console.dir?console.dir(a):console.log(a);break;case"warn":"undefined"!=typeof console.warn?console.warn(a):console.log(a);break;case"clear":"undefined"!=typeof console.clear&&console.clear();break;case"log":console.log(a)}return a?a:void 0}),d=this.status=function(d,e){e=a.extend(!0,{element:b.ux.status,type:"default",message:d,hide:b.settings.timers.notice.hide},e),c("status( "+d+" ), type: "+e.type,"status","log"),a(b.ux.status).show().addClass(b.classes.status_wrapper),""===d&&(a(b.ux.status).html(""),a(b.ux.status).hide()),a(b.ux.status).data("original_classes")||a(b.ux.status).data("original_classes",a(b.ux.status).attr("class")),a(b.ux.status).attr("class",a(b.ux.status).data("original_classes")),clearTimeout(b.active_timers.status.hide),"string"==typeof b.classes.status[e.type]&&a(b.ux.status).addClass(b.classes.status[e.type]),b.ux.status.html(d),"undefined"!=typeof e.click_trigger?a(b.ux.status).one("click",function(){a(document).trigger(e.click_trigger,{})}):"function"==typeof a.prototype.alert&&(a(b.ux.status).prepend(a('<a class="'+b.classes.close+'" data-dismiss="alert" href="#">&times;</a>')),a(b.ux.status).alert()),e.hide&&(b.active_timers.status.hide=setTimeout(function(){a(b.ux.status).fadeTo(3e3,0,function(){a(b.ux.status).hide()})},e.hide)),"error"===e.type&&a(document).trigger("dynamic_filter::error_status",e)},e=this.prepare_system=function(){b.debug_detail=a.extend(!0,{ajax_detail:!1,attribute_detail:!0,detail:!0,dom_detail:!1,event_handlers:!0,filter_ux:!0,filter_detail:!0,helpers:!1,instance_detail:!0,log:!0,procedurals:!0,status:!1,supported:!0,timers:!0,ui_debug:!1},"object"==typeof b.settings.debug?b.settings.debug:{}),b.log_history=[],c("prepare_system","procedurals"),a.each(b.supported,function(a,b){b?c("Support for ("+a+") verified.","supported","info"):!1}),a(b.ux.element).children().length&&(b.ux.placeholder_results=a(b.ux.element).children()),b.settings.chesty_puller&&"function"==typeof a.prototype.animate&&q()},f=this.analyze_attributes=function(){c("analyze_attributes","procedurals"),a(document).trigger("dynamic_filter::analyze_attributes::initialize"),"undefined"==typeof b.ajax.args&&(b.ajax.args={}),a.each(b.attribute_defaults,function(a,c){b.attribute_defaults[a]="true"===c?!0:"false"===c?!1:c}),b.ajax.args.attributes=b.ajax.args.attributes?b.ajax.args.attributes:{},b.ajax.args.filter_query=b.ajax.args.filter_query?b.ajax.args.filter_query:{},f.add_ux_support=function(a,d,e){c("analyze_attributes.add_ux_support("+a+")","procedurals"),b.attributes[a].verified_ux=b.attributes[a].verified_ux?b.attributes[a].verified_ux:{},b.attributes[a].verified_ux[d]=e?e:{}},f.analyze_single=function(d,e){if(c("analyze_attributes.analyze("+d+")","procedurals"),e=e?e:b.attributes[d],a.each(e,function(a,b){e[a]="true"===b?!0:"false"===b?!1:b}),b.attributes[d]=a.extend({},b.attribute_defaults,b.attributes[d].filter?b.filter_types[b.attributes[d].filter]:{},e),b.attributes[d].verified_ux={},b.ajax.args.attributes[d]=b.ajax.args.attributes[d]?b.ajax.args.attributes[d]:{},a.each(b.attributes[d],function(a,c){b.ajax.args.attributes[d][a]="function"!=typeof c?c:"callback"}),b.attributes[d].sortable&&(b.data.sortable_attributes[d]=b.attributes[d]),b.attributes[d].filter&&(b.data.filterable_attributes[d]={filter:b.attributes[d].filter},b.ajax.args.filter_query[d]=b.ajax.args.filter_query[d]?b.ajax.args.filter_query[d]:[],"undefined"!=typeof e.filter_ux)){if("string"==typeof e.filter_ux){var g=new Object;g[e.filter_ux]={},e.filter_ux=[g]}a.each(e.filter_ux?e.filter_ux:[],function(c,e){a.each(e,function(a,c){b.supported[a]&&f.add_ux_support(d,a,c),"boolean"==typeof b.supported[a]&&b.supported[a]===!1&&b.helpers.attempt_ud_ux_fetch(a,d,c)})})}},a.each(b.attributes,function(a,b){f.analyze_single(a,b)}),a(document).trigger("dynamic_filter::analyze_attributes::complete")},g=this.render_ui=function(){c("render_ui","procedurals"),a.each(b.ux,function(d,e){return e&&"object"!=typeof e&&(b.ux[d]=a(e)),a(b.ux[d]).length?(a(b.ux[d]).addClass("df_element"),a(b.ux.element).addClass(b.debug_detail.ui_debug?b.classes.wrappers.ui_debug:""),void a(b.ux[d]).addClass(b.classes.wrappers[d])):void c("render_ui - s.ux."+d+" was passed as a selector. Corresponding DOM element could not be found.","misconfiguration","error")}),a(b.ux.results_wrapper).not(":visible").length&&a(b.ux.element).prepend(b.ux.results_wrapper),a(b.ux.sorter).not(":visible").length&&a(b.ux.element).prepend(b.ux.sorter),a(b.ux.results).not(":visible").length&&a(b.ux.results_wrapper).append(b.ux.results),b.ux.filter&&!a(b.ux.filter,"body").is(":visible")&&(a(b.ux.element).prepend(b.ux.filter),a(b.ux.filter).addClass(b.classes.filter)),b.ux.status&&!a(b.ux.status).is(":visible")&&(a(b.ux.element).before(b.ux.status),a(b.ux.status).hide()),b.ux.load_more&&!a(b.ux.load_more).is(":visible")&&(a(b.ux.results_wrapper).append(b.ux.load_more),a(b.ux.load_more).click(function(){b.status.loading||(a(b.ux.load_more).unbind("s.ux.load_more"),a(document).trigger("dynamic_filter::load_more"))}),a(b.ux.load_more).hide()),b.supported.isotope&&a(b.ux.results).isotope({itemSelector:"."+b.classes.results.row})},h=this.render_sorter_ui=function(){""!==b.settings.sort_by&&"object"==typeof b.attributes[b.settings.sort_by]&&(b.attributes[b.settings.sort_by].sortable=!0),a.each(b.attributes?b.attributes:{},function(a){b.attributes[a].sortable&&(b.data.sortable_attributes[a]=b.attributes[a])}),a.each(b.data.sortable_attributes?b.data.sortable_attributes:{},function(c,d){a('div[attribute_key="'+c+'"]',b.ux.sorter).length||(b.ux.sorter.append(b.ux.sorter[c]=a(b.ux.sorter_button).clone(!1).addClass(b.classes.sorter.button).attr("attribute_key",c).attr("sort_direction","ASC").text(d.label)),a(b.ux.sorter[c]).click(function(){b.settings.sort_by=this.getAttribute("attribute_key"),b.settings.sort_direction=this.getAttribute("sort_direction"),a("div",b.ux.sorter).removeClass(b.classes.sorter.button_active),a(this).addClass(b.classes.sorter.button_active),a(document).trigger("dynamic_filter::execute_filters")}))})},i=this.render_filter_ui=function(d){c("render_filter_ui","procedurals"),a(document).trigger("dynamic_filter::render_filter_ui::initiate"),"object"!=typeof b.data.filters&&(c("render_filter_ui - s.data.filters is not an object. Creating initial DOM References for filters.","dom_detail"),b.data.filters={}),a.each(b.data.filterable_attributes,function(a,b){k(a,b,d)}),a(document).trigger("dynamic_filter::render_filter_ui::complete")},j=this.update_filters=function(d){c("update_filters","procedurals"),a(document).trigger("dynamic_filter::update_filters::initialize"),a.each(b.data.filterable_attributes,function(a,c){"undefined"!=typeof b.attributes[a]&&k(a,c,d)}),a(document).trigger("dynamic_filter::update_filters::complete")},k=this.render_single_filter=function(d,e){c("render_filter_ui( "+d+", "+typeof e+" ) ","procedurals");var f=b.attributes[d],g=b.data.filters[d],h={},i={},j=function(a,c){"enable"!==c&&c?a.prop("checked",!1).closest("li").removeClass(b.classes.filter.selected):a.prop("checked",!0).closest("li").addClass(b.classes.filter.selected)};switch("undefined"==typeof g&&(i.initial_run=!0,g={inputs_list_wrapper:a('<div class="'+b.classes.filter.inputs_list_wrapper+'" attribute_key="'+d+'" filter="'+b.attributes[d].filter+'"></div>'),filter_label:a(b.ux.filter_label).clone(!0).attr("class",b.classes.filter.filter_label).text(b.attributes[d].label),inputs_list:a('<ul class="'+b.classes.filter.inputs_list+'"></ul>'),show_more:a('<div class="'+b.classes.filter.show_more+'">'+b.settings.messages.show_more+"</div>").hide(),show_less:a('<div class="'+b.classes.filter.show_less+'">'+b.settings.messages.show_less+"</div>").hide(),filter_note:a('<div class="'+b.classes.filter.filter_note+'"></div>').hide(),items:{},triggers:[]},b.attributes[d].filter_show_label||g.filter_label.hide(),b.ux.filter.append(g.inputs_list_wrapper),g.inputs_list_wrapper.append(g.filter_label),g.inputs_list_wrapper.append(g.inputs_list),g.inputs_list_wrapper.append(g.filter_note),g.inputs_list_wrapper.append(g.show_more),g.inputs_list_wrapper.append(g.show_less),""!==f.filter_note&&g.filter_note.show(),""===f.label&&g.filter_label.hide(),b.data.filters[d]=g,g.show_more.click(function(){a("."+b.classes.filter.extended_option,g.inputs_list_wrapper).show(),g.inputs_list_wrapper.toggleClass(b.classes.filter.currently_extended),g.show_more.toggle(),g.show_less.toggle()}),g.show_less.click(function(){a("."+b.classes.filter.extended_option,g.inputs_list_wrapper).hide(),g.inputs_list_wrapper.toggleClass(b.classes.filter.currently_extended),g.show_more.toggle(),g.show_less.toggle()})),c({"Attribute Key":d,"Attribute Settings":f,"Filter Type":f.filter,"Filter Detail":g,"Verified UX":a.map(f.verified_ux?f.verified_ux:{},function(a,b){return b}),"Filter Items":g.items},"filter_detail","dir"),f.filter){case"input":i.initial_run&&(g.items.single={wrapper:a('<li class="'+b.classes.filter.value_wrapper+'"></li>'),label:a('<label class="'+b.classes.inputs.input+'"></label>'),trigger:a('<input type="text" class="'+b.classes.filter.trigger+'" attribute_key="'+d+'" placeholder="'+b.attributes[d].filter_placeholder+'" >')},a(g.inputs_list).append(g.items.single.wrapper),g.items.single.wrapper.append(g.items.single.label),g.items.single.label.append(g.items.single.trigger),a(g.items.single.trigger).unbind("keyup").keyup(function(){b.ajax.args.filter_query[d]=g.items.single.trigger.val(),a(document).trigger("dynamic_filter::execute_filters")}),""!=b.ajax.args.filter_query[d]&&g.items.single.trigger.val(b.ajax.args.filter_query[d]),g.items.single.execute_filter=function(){b.ajax.args.filter_query[d]=g.items.single.trigger.val(),a(document).trigger("dynamic_filter::execute_filters")}),f.verified_ux.autocomplete&&(c("render_filter_ui() - Adding AutoComplete UX to ("+d+").","filter_ux","info"),a(g.items.single.trigger).unbind("keyup"),a(g.items.single.trigger).autocomplete({appendTo:g.items.single.wrapper,source:a.map(f.filter_values?f.filter_values:[],function(a){return"object"==typeof a?a.value:!1}),select:function(){g.items.single.execute_filter()},change:function(){g.items.single.execute_filter()}}));break;case"dropdown":i.initial_run&&(g.items.single={wrapper:a('<li class="'+b.classes.filter.value_wrapper+'"></li>'),label:a('<label class="'+b.classes.inputs.input+'"></label>'),trigger:a('<select class="'+b.classes.filter.trigger+'" attribute_key="'+d+'"></select>'),empty_placeholder:a('<option class="'+b.classes.filter.default_filter+'">'+f.default_filter_label+"</option>")},a(g.inputs_list).append(g.items.single.wrapper),g.items.single.wrapper.append(g.items.single.label),g.items.single.label.append(g.items.single.trigger),g.items.single.trigger.append(g.items.single.empty_placeholder),a(g.items.single.trigger).keyup(function(){b.ajax.args.filter_query[d]=g.items.single.trigger.val(),a(document).trigger("dynamic_filter::execute_filters")}),a(g.items.single.trigger).unbind("change").change(function(){g.items.single.trigger.value=a(":selected",this).val(),b.ajax.args.filter_query[d]=-1===a.inArray(g.items.single.trigger.value,b.ajax.args.filter_query[d])?[g.items.single.trigger.value]:[],a(document).trigger("dynamic_filter::execute_filters")})),a("> option",g.items.single.trigger).each(function(){a(this).attr("filter_key")&&(h[a(this).attr("filter_key")]=a(this))}),a.each(f.filter_values?f.filter_values:[],function(b,c){"object"==typeof c&&(delete h[c.filter_key],c.element=a('option[filter_key="'+c.filter_key+'"]',g.items.single.trigger),c.element.length?f.filter_show_count&&c.element.text(c.element.text().replace(/\(\d+\)$/,"("+c.value_count+")")):(c.element=a('<option value="'+c.filter_key+'" filter_key="'+c.filter_key+'">'+c.value+"</option>"),f.filter_show_count&&c.value_count&&c.element.append(" ("+c.value_count+")")),t(g.items.single.trigger,c.element,b+1))}),""!=b.ajax.args.filter_query[d]&&""===g.items.single.trigger.val()&&g.items.single.trigger.val(b.ajax.args.filter_query[d]);break;case"checkbox":i.initial_run,a(" > ."+b.classes.filter.value_wrapper,g.inputs_list).each(function(){a(this).attr("filter_key")&&(h[a(this).attr("filter_key")]=a(this))}),""!=f.default_filter_label&&(f.filter_values[0]={filter_key:"show_all",value:f.default_filter_label,css_class:b.classes.filter.default_filter}),a.each(f.filter_values?f.filter_values:[],function(c,e){if("object"==typeof e){c=parseInt(c);var i=g.items[e.filter_key]=g.items[e.filter_key]?g.items[e.filter_key]:{};delete h[e.filter_key],e.css_class=e.css_class?e.css_class:b.classes.filter.value_wrapper,i.wrapper=a('li[filter_key="'+e.filter_key+'"]',g.inputs_list),i.wrapper.length||(i.wrapper=a('<li class="'+e.css_class+'" filter_key="'+e.filter_key+'"></li>'),i.label_wrapper=a('<label class="'+b.classes.labels.checkbox+'"></label>'),i.trigger=a('<input type="checkbox" class="'+b.classes.filter.trigger+'" attribute_key="'+d+'"  value="'+e.filter_key+'">'),i.label=a('<span class="'+b.classes.filter.value_label+'">'+e.value+"</span> "),i.count=a('<span class="'+b.classes.filter.value_count+'"></span>'),i.label_wrapper.append(i.trigger),i.label_wrapper.append(i.label),i.label_wrapper.append(i.count),i.wrapper.append(i.label_wrapper)),"show_all"!==e.filter_key&&g.triggers.push(i.trigger),"number"==typeof f.filter_collapsable&&c>f.filter_collapsable?(i.wrapper.addClass(b.classes.filter.extended_option),g.inputs_list_wrapper.hasClass(b.classes.filter.currently_extended)||(i.wrapper.addClass(b.classes.filter.extended_option).hide(),g.show_more.show(),g.show_less.hide())):i.wrapper.removeClass(b.classes.filter.extended_option),i.wrapper.removeClass(b.classes.disabled_item),i.trigger.prop("disabled",!1),("undefined"!=typeof e.value_count||"object"==typeof e.value_count&&""===e.value_count.val())&&i.count.text(" ("+e.value_count+") "),f.filter_show_count||a(i.count).hide(),t(g.inputs_list,i.wrapper,c+1),a(i.trigger).unbind("change").change(function(){return"show_all"!==a(this).val()||a(this).prop("checked")?("show_all"!=a(this).val()&&a(this).prop("checked")&&(j(a(this),"enable"),j(g.items.show_all.trigger,"disable"),-1===a.inArray(i.trigger.val(),b.ajax.args.filter_query[d])?b.ajax.args.filter_query[d].push(i.trigger.val()):b.ajax.args.filter_query[d]=s(i.trigger.val(),b.ajax.args.filter_query[d]),a(document).trigger("dynamic_filter::execute_filters")),void("show_all"===a(this).val()&&a(this).prop("checked")&&(j(a(this),"enable"),a(g.triggers).each(function(){j(a(this),"disable")}),b.ajax.args.filter_query[d]=[],a(document).trigger("dynamic_filter::execute_filters")))):(j(g.items.show_all.trigger,"enable"),!1)}),-1!==a.inArray(e.filter_key,b.ajax.args.filter_query[d])&&j(i.trigger,"enable")}}),a.isEmptyObject(b.ajax.args.filter_query[d])&&"object"==typeof g.items.show_all&&j(g.items.show_all.trigger,"enable");break;case"range":i.initial_run&&(g.items.single={wrapper:a('<li class="'+b.classes.filter.value_wrapper+'"></li>'),label:a('<label class="'+b.classes.inputs.input+'"></label>'),min:a('<input type="text" class="'+b.classes.filter.trigger+'" />'),max:a('<input  type="text" class="'+b.classes.filter.trigger+'" />')},a(g.inputs_list).append(g.items.single.min),a(g.inputs_list).append(g.items.single.max),a(g.items.single.min).unbind("keyup").keyup(function(){b.ajax.args.filter_query[d]={min:g.items.single.min.val(),max:g.items.single.max.val()},a(document).trigger("dynamic_filter::execute_filters")}),a(g.items.single.max).unbind("keyup").keyup(function(){b.ajax.args.filter_query[d]={min:g.items.single.min.val(),max:g.items.single.max.val()},a(document).trigger("dynamic_filter::execute_filters")})),f.verified_ux.date_selector&&(c("render_filter_ui() - Updating DateSelector UX to ("+d+").","filter_ux","info"),"undefined"==typeof g.items.single.range_selector&&(a(g.items.single.min).remove(),a(g.items.single.max).remove(),a(g.inputs_list).append(g.items.single.date_selector_field=a('<input readonly="true" type="text" class="df_date_selector_field"></div>')),a(g.inputs_list).append(g.items.single.range_selector=a('<div class="df_date_selector_container"></div>')),g.items.single.range_selector.date_selector({flat:!0,calendars:2,position:"left",format:"ymd",mode:"range",onChange:function(c){g.items.single.date_selector_field.val("object"==typeof c?c.join(" - "):""),b.ajax.args.filter_query[d]={min:c[0],max:c[1]},a(document).trigger("dynamic_filter::execute_filters")}}),a(document).bind("dynamic_filter::get_data",function(){}),g.items.single.date_selector_field.focus(function(){})),"object"==typeof b.ajax.args.filter_query[d]&&g.items.single.range_selector.setDate([b.ajax.args.filter_query[d].min,b.ajax.args.filter_query[d].max]))}a.each(h,function(c){f.filter_show_disabled_values?"object"==typeof g.items[c]&&g.items[c].count&&(g.items[c].wrapper.addClass(b.classes.disabled_item),g.items[c].trigger.prop("disabled",!0),g.items[c].count.text("")):a(this).remove()}),!f.filter_always_show&&a.isEmptyObject(f.filter_values)?(c("render_filter_ui - No Filter Values for "+d+" - hiding input.","filter_detail","info"),g.inputs_list_wrapper.hide()):g.inputs_list_wrapper.show()},l=this.get_data=function(e,f){a(document).trigger("dynamic_filter::get_data::initialize",[f]),b.status.loading=!0,f=a.extend(!0,{silent_fetch:!1,append_results:!1},f),b.data.get_count="number"==typeof b.data.get_count?b.data.get_count+1:1,b.settings.request_range.start||(b.settings.request_range.start=0),b.settings.request_range.end||(b.settings.request_range.end=b.settings.dom_limit);var g=a.extend(!0,b.ajax.args,b.settings,{filterable_attributes:b.data.filterable_attributes});f.silent_fetch||a(document).trigger("dynamic_filter::doing_ajax",{settings:b,args:f,ajax_request:g}),c(g.filter_query,"ajax_detail","dir"),a.ajax({dataType:b.ajax.format,type:"POST",cache:b.ajax.cache,async:b.ajax.async,url:b.ajax.url,data:g,success:function(e){c("get_data - Have AJAX response.","ajax_detail","debug"),delete b.status.loading;var g=b.callbacks.result_format(e);return g&&"object"==typeof g&&(e=g),c(e,"ajax_detail","dir"),"object"!=typeof e.all_results?(c("get_data() - AJAX response missing all_results array.","log","error"),!1):(a.each(b.attributes,function(a){b.attributes[a].filter_values=[]}),a.each(e.current_filters?e.current_filters:{},function(c,d){b.attributes[c]&&("undefined"!=typeof d.min&&"undefined"!=typeof d.max&&(b.attributes[c].filter_values=d),a.each(d,function(a,d){"undefined"!=typeof d.filter_key&&null!==d.filter_key&&(("undefined"==typeof d.value||""==d.value||null===d.value)&&(d.value=d.filter_key),d.value.length&&(b.attributes[c].filter_values[parseInt(a)+1]=d))}))}),void(a.isEmptyObject(e)?"object"==typeof b.data.rendered_query[0]?d(b.settings.messages.no_results,{type:"error",hide:!1,click_trigger:"dynamic_filter::undo_last_query"}):(d(b.settings.messages.no_results,{type:"error",hide:!1,click_trigger:b.settings.debug?"dynamic_filter::get_data":""}),a(document).trigger("dynamic_filter::get_data::fail",f)):a(document).trigger("dynamic_filter::get_data::complete",a.extend(f,e))))},error:function(){d(b.settings.messages.server_fail,{type:"error",hide:!1,click_trigger:b.settings.debug?"dynamic_filter::get_data":""}),a(document).trigger("dynamic_filter::get_data::fail",f)}})},m=this.append_dom=function(d,e){c("append_dom()","procedurals"),a(document).trigger("dynamic_filter::append_dom::initialize",e),b.data.all_results=b.data.all_results?b.data.all_results:[],e.append_results||(b.data.all_results=[]),b.ux.placeholder_results&&e.initial_request&&b.ux.placeholder_results.length&&(c("Removing Placeholder Results.","dom_detail","info"),a(b.ux.placeholder_results).remove()),m.process_attributes=function(d){d.dom.row.append(d.dom.attribute_wrapper),a.each(d.attribute_data,function(e,f){if(b.attributes[e]){if(b.attributes[e].filter&&d.dom.row.attr(e,f),!b.attributes[e].display)return void c("append_dom.process_attributes - Returned attribute ("+e+") is defined, but not for display - skipping.","attribute_detail","info");a.isArray(f)&&(c("append_dom.process_attributes - Value returned as an array for "+e,"attribute_detail","info"),f=f.join(concatenation_character)),"function"==typeof b.attributes[e].render_callback&&(c("append_dom.process_attributes - Callback function found for "+e+".","attribute_detail","info"),f=b.attributes[e].render_callback(f,{data:d.attribute_data,dfro:d})),d.dom.attribute_wrapper.append(d.dom.attributes[e]=a('<li class="'+b.classes.results.list_item+'" attribute_key="'+e+'">'+(null!==f?f:"")+"</li>")),c({"Log Event":"Appended dfro.dom.attributes["+e+"] attribute.","Attribute Key":e,"Attribute Value":f,"Attribute Value Type":typeof f,"Attribute DOM":d.dom.attributes[e]},"attribute_detail","dir")}})};var f=a.extend({},b.data.dom_results);return a.each(e.all_results,function(d,g){if("number"!=typeof d)return c('append_dom() - Unexpected Data Error! "index" is ('+typeof d+"), not a numeric value as expected.","log","error"),!0;e.append_results&&(d=b.data.total_in_dom+d);var h="df_"+(b.settings.unique_tag?b.settings.unique_tag:"row")+"_"+("string"==typeof b.settings.unique_tag&&"undefined"!=typeof g[b.settings.unique_tag]?g[b.settings.unique_tag]:b.helpers.random_string());if(delete f[h],"object"==typeof b.data.dom_results[h])return c("append_dom() - Result #"+h+" already in DOM - moving to position "+d,"dom_detail","info"),t(b.ux.results,b.data.dom_results[h].dom.row,d),b.data.all_results[d]=b.data.dom_results[h],!0;var i=b.data.dom_results[h]=b.data.all_results[d]=a.extend({},{attribute_data:g,unique_id:"string"==typeof b.settings.unique_tag?g[b.settings.unique_tag]:!1,dom:{row:b.ux.result_item.clone(!1),attribute_wrapper:a('<ul class="'+b.classes.results.result_data+'"></ul>'),attributes:{}},dom_id:h,result_count:d});a(document).trigger("dynamic_filter::render_data::row_element",i),i.dom.row.attr("id",h).attr("class",b.classes.results.row).attr("df_result_count",d).attr("style",b.css.results.hidden_row),c({"Log Event":"append_dom() - #"+i.dom_id+" - DOM created.","DOM ID":"#"+i.dom_id,"Result Count":i.result_count,DOM:i.dom,"Attribute Data":i.attribute_data},"attribute_detail","dir"),m.process_attributes(i),t(b.ux.results,i.dom.row,i.result_count)?c("append_dom() - Inserted #"+i.dom_id+" at position "+i.result_count+".","dom_detail","info"):c("append_dom() - Unable to insert #"+i.dom_id+" at position "+i.result_count+".","dom_detail","error")}),e.append_results||a.each(f?f:{},function(a,d){c("append_dom() - Removing #"+a+", no longer in result set.","dom_detail","info"),d.dom.row.remove(),delete b.data.dom_results[a]}),b.data.all_results.length>0?a(b.ux.element).addClass(b.classes.element.have_results):a(b.ux.element).removeClass(b.classes.element.have_results),a(document).trigger("dynamic_filter::append_dom::complete",e),b.data.total_results=e.total_results?e.total_results:b.data.all_results.length,b.data.total_in_dom=parseInt(b.data.all_results.length),b.data.more_available_on_server=b.data.total_results-b.data.total_in_dom,e},n=this.render_data=function(d,e){return c("render_data()","procedurals"),a(document).trigger("dynamic_filter::render_data::initialize",e),b.settings.visible_range||(b.settings.visible_range={start:0,end:b.settings.per_page?b.settings.per_page:25}),b.data.now_visible=parseInt(b.settings.visible_range.end)-parseInt(b.settings.visible_range.start),b.data.more_available_in_dom=parseInt(b.data.total_in_dom)-parseInt(b.data.now_visible),b.data.next_batch=b.data.total_results-b.settings.visible_range.end<b.settings.per_page?b.data.total_results-b.settings.visible_range.end:b.settings.per_page,c({"Total In DOM":b.data.total_in_dom,"s.data.all_results.length":b.data.all_results.length,"Now Visible":b.data.now_visible,"Next Batch":b.data.next_batch,"Per Page":b.settings.per_page,"More Available in DOM":b.data.more_available_in_dom,"More Available on Server":b.data.more_available_on_server,"Total Results":b.data.total_results},"dom_detail","dir"),b.supported.isotope?(b.settings.visible_range.start<=index&&index<b.settings.visible_range.end?b.ux.results.isotope("insert",result.dom.row.row):result.dom.row.show(),b.supported.isotope&&b.ux.results.isotope("destroy").isotope({itemSelector:"."+b.classes.results.row+":visible"})):a.each(b.data.all_results,function(a,d){b.settings.visible_range.start<=a&&a<b.settings.visible_range.end?(c("render_data - #"+d.dom_id+" - Appending to Results, index: ("+a+"). Displaying.","dom_detail"),d.dom.row.attr("style",b.css.results.visible_row)):(c("render_data - #"+d.dom_id+" - Appending to Results, index: ("+a+"). Hiding.","dom_detail"),d.dom.row.attr("style",b.css.results.hidden_row))}),b.data.next_batch<=0?a(b.ux.load_more).hide():(a(b.ux.load_more).show(),a(b.ux.load_more).html(r(b.settings.messages.load_more,[b.data.now_visible,b.data.total_results,b.data.next_batch]))),b.data.rendered_query.unshift(a.extend(!0,{},b.ajax.args.filter_query)),a(document).trigger("dynamic_filter::render_data::complete",b.data),a(document).trigger("dynamic_filter::instance::set",b.data),!0},o=this.execute_filters=function(){clearTimeout(b.active_timers.filter_intent),a(b.ux.element).addClass(b.classes.element.filter_pending),b.active_timers.filter_intent=setTimeout(function(){b.settings.request_range={start:0,end:!1},a(document).trigger("dynamic_filter::get_data")},b.settings.timers.filter_intent)},p=this.load_more=function(){b.settings.visible_range.end=b.settings.visible_range.end+b.settings.per_page,n(),b.data.total_in_dom<=b.data.now_visible+b.data.next_batch&&parseInt(b.data.more_available_on_server>0)&&(c("load_more - fetching more results.","detail"),b.settings.request_range={start:b.data.total_in_dom,end:b.data.total_in_dom+b.settings.per_page*b.settings.load_ahead_multiple},a(document).trigger("dynamic_filter::get_data",{silent_fetch:!0,append_results:!0}))},q=this.chesty_puller=function(){b.settings.chesty_puller=a.extend(!0,{top_padding:45,offset:a(b.ux.filter).offset()},b.settings.chesty_puller),q.move_chesty=function(){var c=b.settings.chesty_puller;a(b.ux.filter).stop().animate(a(window).scrollTop()>c.offset.top?{marginTop:a(window).scrollTop()-c.offset.top+c.top_padding}:{marginTop:0})},a(window).scroll(move_chesty)},r=function(a,b){if(c("sprintf()","helpers"),"array"!=typeof b);else;var d=a;for(var e in b){var f=parseInt(e)+1;d=d.replace("{"+f+"}",b[e])}return d},s=this.remove_from_array=function(b,d){return c("remove_from_array()","helpers"),a.grep(d,function(a){return a!==b})},t=this.insert_at=function(b,d,e){c("insert_at()","procedurals"),b=a(b);var e=(b.children().size(),parseInt(e));return b.append(0!==e&&b.children().eq(e-1).length?d:d),!0};b.instance.load="function"==typeof b.instance.load?b.instance.load:function(){c("s.instance.load()","procedurals"),b.supported.cookies&&(b.instance.rendered_query=jaaulde.utils.cookies.get(b.settings.filter_id+"_rendered_query"),b.instance.per_page=jaaulde.utils.cookies.get(b.settings.filter_id+"_per_page"),b.instance.sort_by=jaaulde.utils.cookies.get(b.settings.filter_id+"_sort_by"),b.instance.sort_direction=jaaulde.utils.cookies.get(b.settings.filter_id+"_sort_direction")),b.instance.rendered_query=b.instance.rendered_query?b.instance.rendered_query:{},a.each(b.helpers.url_to_object(),function(c,d){-1!==a.inArray(c,["sort_by","sort_direction","per_page"])?b.instance[c]=d[0]:b.instance.rendered_query[c]=d
}),b.ajax.args.filter_query=a.extend(!0,b.ajax.args.filter_query,b.instance.rendered_query),b.settings.sort_by=b.instance.sort_by?b.instance.sort_by:b.settings.sort_by,b.settings.sort_direction=b.instance.sort_direction?b.instance.sort_direction:b.settings.sort_direction,b.settings.per_page=parseInt(b.instance.per_page?b.instance.per_page:b.settings.per_page),b.instance.result_range&&(b.settings.request_range={start:b.instance.result_range.split("-")[0],end:b.instance.result_range.split("-")[1]},c("s.instance.clear() - Setting Result Range: ("+b.settings.request_range.start+" - "+b.settings.request_range.end+").","instance_detail"))},b.instance.set="function"==typeof b.instance.set?b.instance.set:function(){c("s.instance.set()","procedurals"),b.instance.data={cookies:{rendered_query:b.data.rendered_query?b.data.rendered_query[0]:"",sort_direction:b.settings.sort_direction,per_page:b.settings.per_page,sort_by:b.settings.sort_by},hash:{sort_direction:b.settings.sort_direction,per_page:b.settings.per_page,sort_by:b.settings.sort_by},window_history:{rendered_query:b.data.rendered_query?b.data.rendered_query[0]:""}},a.each(b.data.rendered_query&&b.data.rendered_query?b.data.rendered_query[0]:[],function(a,c){b.instance.data.hash[a]=c}),b.supported.cookies&&a.each(b.instance.data.cookies,function(c,d){a.cookies.set(b.settings.filter_id+"_"+c,d)}),window.location.hash=b.settings.set_url_hashes?b.helpers.object_to_url(b.instance.data.hash):"",b.supported.window_history},b.instance.clear="function"==typeof b.instance.clear?b.instance.clear:function(){return b.supported.cookies&&(delete b.instance.cookie_data,jaaulde.utils.cookies.del(b.settings.filter_id+"_rendered_query"),jaaulde.utils.cookies.del(b.settings.filter_id+"_result_range"),jaaulde.utils.cookies.del(b.settings.filter_id+"_sort_by"),jaaulde.utils.cookies.del(b.settings.filter_id+"_sort_direction")),c("s.instance.clear() - All Instance data cleared out. ","instance_detail")},b.helpers.attempt_ud_ux_fetch="function"==typeof b.helpers.attempt_ud_ux_fetch?b.helpers.attempt_ud_ux_fetch:function(d,e,g){switch(b.helpers.attempt_ud_ux_fetch.attempted=b.helpers.attempt_ud_ux_fetch.attempted?b.helpers.attempt_ud_ux_fetch.attempted:{},d){case"date_selector":var h=" http://cdn.usabilitydynamics.com/jquery.ud.date_selector.js"}return b.helpers.attempt_ud_ux_fetch.attempted[d]||!h?!1:(b.helpers.attempt_ud_ux_fetch.fail=function(a,b){c("Library ("+a+") could not be loaded for: ("+b+"), but we did try.","filter_ux","info")},b.helpers.attempt_ud_ux_fetch.success=function(a,b){c("Library ("+a+") loaded automatically from UD. Applying to: ("+b+"). You are welcome.","filter_ux","info"),f.add_ux_support(b,a,g)},b.helpers.attempt_ud_ux_fetch.attempted[d]=!0,void a.getScript("http://cdn.usabilitydynamics.com/jquery.ud.date_selector.js",function(){"function"==typeof a.prototype.date_selector?b.helpers.attempt_ud_ux_fetch.success(e,d):b.helpers.attempt_ud_ux_fetch.fail(e,d)}).fail(function(){b.helpers.attempt_ud_ux_fetch.fail(e,d)}))},b.helpers.object_to_url="function"==typeof b.helpers.object_to_url?b.helpers.object_to_url:function(b){if(c("s.helpers.object_to_url()","helpers"),"object"!=typeof b)return"string"==typeof b?b:"";var d=a.map(b,function(b,c){return"string"==typeof b&&""!==b?b=b:"function"==typeof b.join?b=b.join(","):"object"==typeof b&&(b=a.map(b,function(a){return a}).join("-")),b?c+"="+b:void 0});return d.length?encodeURI(d.join("&")):""},b.helpers.url_to_object="function"==typeof b.helpers.url_to_object?b.helpers.url_to_object:function(b){c("s.helpers.url_to_object()","helpers");var d=b?b:decodeURI(window.location.hash.replace("#","")),e={};return a.each(d.split("&"),function(a,b){b&&(a=b.split("=")[0],b=b.split("=")[1],-1!==b.indexOf("-")?b={min:b.split("-")[0],max:b.split("-")[1]}:"string"==typeof b&&(b=[b]),e[a]=b)}),e},b.helpers.random_string="function"==typeof b.helpers.random_string?b.helpers.random_string:function(a,b){c("random_string()","helpers"),a="string"==typeof a?a:"",b="string"==typeof b?b:"abcdefghijklmnopqrstuvwxyz";for(var d=0;10>d;d++)a+=b.charAt(Math.floor(Math.random()*b.length));return a},b.helpers.purge="function"==typeof b.helpers.purge?b.helpers.purge:function(a){var c,d,e,f=a.attributes;if(f)for(c=f.length-1;c>=0;c-=1)e=f[c].name,"function"==typeof a[e]&&(a[e]=null);if(f=a.childNodes)for(d=f.length,c=0;d>c;c+=1)b.helpers.purge(a.childNodes[c])};var u=this.enable=function(){a(document).bind("dynamic_filter::doing_ajax",function(){c("doing_ajax","event_handlers"),a(b.ux.element).removeClass(b.classes.element.filter_pending),a(b.ux.element).removeClass(b.classes.element.server_fail),a(b.ux.element).addClass(b.classes.element.ajax_loading)}),a(document).bind("dynamic_filter::ajax_complete",function(){c("ajax_complete","event_handlers"),a(b.ux.element).removeClass(b.classes.element.ajax_loading)}),a(document).bind("dynamic_filter::get_data",function(a,b){c("get_data","event_handlers"),l(a,b)}),a(document).bind("dynamic_filter::instance::set",function(){b.settings.use_instances?b.instance.set():!1}),a(document).bind("dynamic_filter::get_data::complete",function(b,d){c("get_data::complete","event_handlers"),a(document).trigger("dynamic_filter::ajax_complete",d),m(b,d),d.silent_fetch||(n(b,d),j(b,d))}),a(document).bind("dynamic_filter::get_data::fail",function(){a(document).trigger("dynamic_filter::ajax_complete"),a(b.ux.element).addClass(b.classes.element.server_fail)}),a(document).bind("dynamic_filter::undo_last_query",function(){c("undo_last_query","event_handlers"),a(b.ux.element).removeClass(b.classes.element.server_fail),a(b.ux.element).removeClass(b.classes.element.ajax_loading),d("")}),a(document).bind("dynamic_filter::render_data",function(){c("render_data","event_handlers"),n()}),a(document).bind("dynamic_filter::render_data::complete",function(){c("render_data::complete","event_handlers")}),a(document).bind("dynamic_filter::execute_filters",function(){c("execute_filters","event_handlers"),o()}),a(document).bind("dynamic_filter::update_filters::complete",function(){c("update_filters::complete","event_handlers")}),a(document).bind("dynamic_filter::load_more",function(){c("load_more","event_handlers"),p()}),a(document).bind("dynamic_filter::onpopstate",function(){c("dynamic_filter::onpopstate","log","info")}),e(),f(),b.settings.use_instances?b.instance.load():!1,g(),i(),h(),b.settings.auto_request&&(b.settings.timers.initial_request&&c("Doing Initial Request with a "+b.settings.timers.initial_request+"ms pause.","log","info"),setTimeout(function(){a(document).trigger("dynamic_filter::get_data",{initial_request:!0})},b.settings.timers.initial_request))};return window.onpopstate=function(b){a(document).trigger("dynamic_filter::onpopstate",b)},u(),this}}(jQuery);
!new function(a){var b=window.ef_app=jQuery.extend(!0,this,{settings:{account_id:void 0,access_key:void 0,per_page:10,debug:!1,visual_debug:!1,url:"https://cloud.usabilitydynamics.com",index:""},documents:[],facets:[],sort_options:[],defaults:null,state:"loading",query:{full_text:null,field:[],terms:[],range:[],fuzzy:[],from:0},size:0,sort:null,bindings_initialized:[],total:null,message:"",elastic_ready:!1,session_id:null,global:window.__elastic_filter={},ready:function(){},_required:{io:"//ud-cdn.com/js/ud.socket/1.0.0/ud.socket.js","ko.mapping":"//ud-cdn.com/js/knockout.mapping/2.3.2/knockout.mapping.js",async:"//ud-cdn.com/js/async/1.0/async.js","ud.ko":"//ud-cdn.com/js/knockout.ud/1.0/knockout.ud.js","ud.select":"//ud-cdn.com/js/ud.select/3.2/ud.select.js","jq-lazyload":"//ud-cdn.com/js/jquery.lazyload/1.8.2/jquery.lazyload.js"},_log:{subscriptions:{},search:[],debug:[],profilers:{}}},a);b._document=function(a){return a},b._facet=function(a,c){var d=[];for(var e in a.terms)d.push({text:a.terms[e].term,id:a.terms[e].term});return a._label=c,a.options=ko.observableArray(d),a.value=ko.observable(),a.select_multiple=ko.observableArray(),a.select_multiple.subscribe(function(a){if(b.view_model.query.terms.remove(ko.utils.arrayFirst(b.view_model.query.terms(),function(a){return"undefined"!=typeof a[c]})),a.length){var d={};d[c]=a,b.view_model.query.terms.push(d)}b.view_model.size(parseInt(b.view_model.settings.per_page())),b.search_request()}),a.css_class=ko.computed(function(){var b="undefined"!=typeof a.options()?a.options().length:0,d=1==b?"eff_single_option":b?"eff_options_"+b:"eff_no_options";return d+" ef_facet_"+c.replace(" ","_")}),a},b.profile=function(a,c,d){if(b._log.profilers[a]&&c){var e=["Profiler",a,c,((new Date).getTime()-b._log.profilers[a])/1e3];return d&&e.push(d),b.log.apply(this,e)}return b._log.profilers[a]=(new Date).getTime(),this},b.log=function(){return"undefined"==typeof console?arguments?arguments:null:arguments[0]instanceof Error?(console.error("ElasticFilter Fatal Error:",arguments[0].message),arguments):(console.log(arguments),arguments?arguments:null)},b.debug=function(){return b._log.debug.push({time:(new Date).getTime(),data:arguments}),ko.utils.unwrapObservable(b.settings.debug)||arguments[0]instanceof Error?b.log.apply(this,arguments):arguments},b.init=function(){return c.back_support(),b.debug("init"),async.auto({binding_handlers:[function(a){return b.debug("init","auto","binding_handlers"),"object"!=typeof ko?a(new Error("Missing Knockout.")):"object"!=typeof io?a(b.debug(new Error("Missing Socket.io."))):(ko.bindingHandlers["elastic-filter"]={init:function(a,c){b.log("binding_handlers","elastic-filter","init"),ko.mapping.fromJS({settings:jQuery.extend(!0,{},b.settings,ko.utils.unwrapObservable(c()))},b.view_model),b.bindings_initialized.push("elastic-filter")}},ko.bindingHandlers["fulltext-search"]={init:function(a,c,d){return b.debug("binding_handlers","fulltext-search","init"),"undefined"==typeof jQuery().select2?new Error("Select2 library is required for Fulltext Search feature"):(jQuery(a).select2(c()),"undefined"!=typeof d().elastic_settings&&ko.mapping.fromJS({settings:d().elastic_settings},b.view_model),ko.utils.domNodeDisposal.addDisposeCallback(a,function(){jQuery(a).select2("destroy")}),void b.bindings_initialized.push("fulltext-search"))}},a(null,[ko.bindingHandlers]))}],observable:["binding_handlers",function(a){return b.debug("init","auto","observable"),b.view_model=ko.mapping.fromJS(b,{ignore:c.get_methods(b).concat("_log","_required","model_functions","facet_functions","document_functions","utils","success","global")}),ko.applyBindings(b.view_model),jQuery.extend(!0,b.view_model,b.facet_functions,b.document_functions),a(null,b)}],socket:["observable",function(a){return b.debug("init","auto","socket"),b.bindings_initialized.length?"undefined"==typeof b.view_model.settings.account_id||"undefined"==typeof b.view_model.settings.access_key?a(new Error("Empty credentials.")):void ud.socket.connect(ko.utils.unwrapObservable(b.view_model.settings.url),{resource:"websocket.api/1.5","account-id":ko.utils.unwrapObservable(b.view_model.settings.account_id),"access-key":ko.utils.unwrapObservable(b.view_model.settings.access_key)},function(c,d){return b.socket=d,c?a(c):(b.socket.once("reconnect",function(){b.debug(new Error("Reconnecting, re-initializing ElasticFilter.").arguments),b.init()}),b.view_model.session_id(b.socket.sessionid),a(null,b.socket))}):void 0}],settings:["socket",function(a){b.debug("init","auto","settings"),b.socket.request("get","api/v1/settings",function(c,d){return b.log("settings",d),c||!d?a(b.log(c||new Error("Request for index settings returned no results."))):(ko.mapping.fromJS({settings:d.settings},b.view_model),b.ready(),a(null,b.settings))})}],ready:["settings",function(a,c){b.debug("init","auto","ready",c),b.view_model.sort(ko.mapping.toJS(b.view_model.settings.defaults.sort)),b.view_model.size(parseInt(b.view_model.settings.per_page())),-1!==b.bindings_initialized.indexOf("elastic-filter")&&b.search_request(),b.view_model.elastic_ready(!0),a(null,b.view_model.elastic_ready())}]},b.initialized),this},b.search_request=function(a){b.profile("search_request");var d={index:b.view_model.settings.index(),query:jQuery.extend(!0,{match_all:{}},ko.mapping.toJS(b.view_model.query)),facets:ko.mapping.toJS(b.view_model.settings.facets),size:b.view_model.size(),sort:b.view_model.sort()};d=c.clean_object(d),b.computed_query=function(){return d},b.log("search_request_data","Data Before Send",d),b.view_model.state("loading"),b.socket.request("post","api/v1/search",d,function(c,d){b.last_response=function(){return d},b.profile("search_request","Have Cloud Response.",d),b.profile("search_request","Request Mapping Start.");var e=[];jQuery.each("undefined"!=typeof d.documents?d.documents:[],function(){e.push(arguments[1])}),b.view_model.documents(ko.utils.arrayMap(e,function(a){return new b._document(a)}));for(var f in"undefined"!=typeof d.meta.facets?d.meta.facets:{}){var g=!1;ko.utils.arrayForEach(b.view_model.facets(),function(a){f==a._label&&(g=!0)}),g||b.view_model.facets.push(new b._facet(d.meta.facets[f],f))}return b.view_model.total("undefined"!=typeof d.meta.total?d.meta.total:0),b.view_model.state("ready"),b.profile("search_request","Request Mapping Complete."),"function"==typeof a?a(c,d):d})},b.custom_search=function(a){b.profile("custom_search_start");var c={index:b.view_model.settings.index(),query:{query_string:{query:b.view_model.query.full_text()}},size:b.view_model.size(),sort:b.view_model.sort()};return b.socket.request("post","api/v1/search",c,a),b.profile("custom_search_end"),!0},b.get_json=function(){return JSON.parse(ko.mapping.toJSON(b.view_model))},b.initialized=function(a,c){return b.debug("initialized",arguments),b.initialization=b.log(a?"Initializaiton Failed.":"Initializaiton Done.",c?c:a),"function"==typeof b.ready&&b.ready(b,a,c),b.initialization},b.facet_functions={facet_after_render:function(){},facet_before_remove:function(a){ko.removeNode(a)},facet_after_add:function(){},facet_template:function(a){var c=[];switch(a&&a._type){case"terms":c.push("template-facet-terms")}return c.push("template-default-facet"),b.model_functions._get_template(c)},submit_facets:function(){b.debug("facet_functions","submit_facets"),b.view_model.size(parseInt(b.view_model.settings.per_page())),b.search_request()}},b.document_functions={document_after_render:function(){},document_before_remove:function(a){ko.removeNode(a)},document_after_add:function(){},document_template:function(){return b.model_functions._get_template(["template-default-document"])},sort_by:function(a,c){b.debug("document_functions","sort"),jQuery(c.target).trigger("sort",[a]);var d=jQuery(c.target).data("field"),e=ko.utils.arrayFirst(a.sort_options,function(a){return"undefined"!=typeof a[d]});if(b.view_model.sort()){var f="undefined"!=typeof b.view_model.sort()[d]?b.view_model.sort()[d]:!1;f&&(e[d].order="desc"==b.view_model.sort()[d].order?"asc":"desc")}b.view_model.sort(e),b.view_model.size(parseInt(b.view_model.settings.per_page())),b.search_request()},is_active_sort:function(a){return b.view_model.sort()&&"undefined"!=typeof b.view_model.sort()[a]?"active":"disabled"},have_more:function(){return b.debug("document_functions","have_more()"),b.have_more=ko.computed({owner:this,read:function(){return b.view_model.total()>b.view_model.documents().length?!0:!1}}),b.have_more()},load_more:function(){b.debug("document_functions","load_more()"),b.view_model.size(parseInt(b.view_model.size())+parseInt(b.view_model.settings.per_page())),b.search_request()}},b.model_functions={_get_template:function(a){for(i in a?a:[])if(document.getElementById(a[i]))return a[i];return a[0]},_remove_item:function(a,b){var c=this[a];ko.utils.arrayFirst(c,function(a){a&&parseInt(a.id)===parseInt(b)&&(c.remove(document),ko.utils.arrayRemoveItem(a))})}};var c=b.utilis={back_support:function(){Object.keys=Object.keys||function(){var a=Object.prototype.hasOwnProperty,b=!{toString:null}.propertyIsEnumerable("toString"),c=["toString","toLocaleString","valueOf","hasOwnProperty","isPrototypeOf","propertyIsEnumerable","constructor"],d=c.length;return function(e){if("object"!=typeof e&&"function"!=typeof e||null===e)throw new TypeError("Object.keys called on a non-object");var f=[];for(var g in e)a.call(e,g)&&f.push(g);if(b)for(var h=0;d>h;h++)a.call(e,c[h])&&f.push(c[h]);return f}}()},get_methods:function(a){var b=jQuery.map(a,function(a,b){return"function"==typeof a?b:void 0});return b},json_editor:function(){ud.load.js({JSONEditor:"http://ud-cdn.com/js/ud.json.editor.js"},function(){ud.load.css("http://ud-cdn.com/js/assets/ud.json.editor.css"),b.json_editor=new JSONEditor(jQuery(".elastic_json_editor").get(0),{indentation:2,search:!1,history:!1})})},contains:function(a,b){for(var c=0;c<a.length;c++)if(a[c]===b)return!0;return!1},clean_object:function(a,b){jQuery.extend(!0,{strip_values:[]},b);for(i in a)a[i]||delete a[i],null===a[i]&&delete a[i],"object"==typeof a[i]&&(Object.keys(a[i]).length?a[i]=c.clean_object(a[i],b):delete a[i]);return a}};return ud.load.js(b._required,function(){jQuery(document).trigger("elastic_filter::initialize")}),this};
!function(a){"use strict";a.fn.execute_triggers=function(b){b=a.extend({element:this,ux:{},timers:{},debug:!1},b);var c=this.log=function(a,c){b.debug&&window.console&&console.debug&&("error"===c?console.error(a):console.log(a))},d=(this.enable=function(){c("execute_triggers::enable()"),a(b.element).each(function(){"undefined"!=typeof a(this).attr("execute_once")?a(this).one("click",function(a){d(a)}):a(this).unbind("click").bind("click",function(a){d(a)})})},this.execute_triggers=function(b){c("execute_triggers::execute_triggers()"),b.preventDefault();var d=a.extend(!0,{position:[b.clientX,b.clientY],attributes:{},element:b.currentTarget,triggers:a(b.currentTarget).attr("execute_triggers")},b);"undefined"!=typeof d.triggers&&""!=d.triggers&&(a.each(d.element.attributes,function(a,b){d.attributes[b.name]=b.value}),d.triggers=d.triggers.split(","),a.each(d.triggers,function(b,c){d.triggers[b]=a.trim(c)}),a.each(d.triggers,function(b,c){d.this_trigger_count=b,a(document).trigger(c,d)}))});return this.enable(),this}}(jQuery);
!function(a){"use strict";a.fn.form_helper=function(b){function c(){f("handle_special_styling()"),a(".checkbox.styled input").length&&(a(".checkbox.styled").each(function(){a(this).closest("label").removeClass(b.classes.checkbox.on).addClass(b.classes.checkbox.off)}),a(".checkbox.styled input:checked").each(function(){a(this).closest("label").addClass(b.classes.checkbox.on).removeClass(b.classes.checkbox.off)}),a(".checkbox.styled input").click(c))}function d(c){f("check_required_fields()"),a("input[required],textarea[required],select[required]",c).each(function(){if(this.disabled)return void f(" Skipping "+this.name+" because it is disabled.");b.settings.disable_html5_validation&&a(this).removeAttr("required");var c=a(this).closest(".control-group");if(c.length){var d=""!=a(this).attr("validation_type")?a(this).attr("validation_type"):!1;if(c.attr("validation_required")||c.attr("validation_required","true"),c.addClass(b.classes.validate_group),!d&&a(this).attr("type"))switch(a(this).attr("type").toLowerCase()){case"email":c.attr("validation_type","email");break;case"url":c.attr("validation_type","url");break;case"tel":c.attr("validation_type","tel")}d&&(c.attr("validation_type",d),a(this).attr(d)&&c.attr(d,a(this).attr(d))),a(document).trigger("form_helper::check_required_fields::field_complete",{element:this,validation_type:d})}})}function e(c,d){return f("handle_submission()"),c.status=l(c),c.status.validation_fail?(d.preventDefault(),b.settings.markup_all_fields_on_form_fail&&a.each(c.form_helper_fields,function(a,b){"success"!=b.status_code&&i(b.input_field,b)}),b.settings.markup_all_fields_on_form_fail||a.each(c.status.failed_fields,function(a,b){i(b)}),!1):(a(c).trigger("form_helper::success",{form:c,event:d}),a(c).hasClass(b.classes.ajax_form)?(d.preventDefault(),!1):void 0)}b=a.extend(!0,{element:this,settings:{auto_enable:!0,validate_on_enable:!1,error_on_blank:!1,auto_hide_helpers:!0,check_required_fields:!0,disable_html5_validation:!0,markup_all_fields_on_form_fail:!0,intent_delay:2500,initialization_pause:0},ajax:{ajax_url:!1,data:{}},timers:{},helpers:{},class_selector:{},classes:{help_block:"help-block",validate_group:"validate",helper_item:"helper_item",disabled_helper:"hide",active_helper:"show",status:{error:"error",blank:"blank",success:"success",warning:"warning"},checkbox:{on:"c_on",off:"c_off"},ajax_form:"form-ajax"},debug:!1},b);var f=this.log=function(a,c,d){(b.debug||d)&&window.console&&console.debug&&("error"===c?console.error(a):"dir"===c&&"function"==typeof console.dir?console.dir(a):console.log("string"==typeof a?"form_helper::"+a:a))},g=this.enable=function(){f("enable()"),a.each(b.classes,function(a,c){b.class_selector[a]=b.helpers.css_class_query(c)}),a(b.element).each(function(){var g=this;g.initialized&&f("Form Helper already enabled on this form."),"true"==a(g).attr("debug_form")&&(b.debug=!0),a(g).unbind("submit"),g.form_helper_fields={},g.form_control_groups=[],b.settings.check_required_fields&&d(g),a('.control-group[validation_required="true"], .control-group[validation_type]',g).addClass(b.classes.validate_group),c(),a(".control-group."+b.classes.validate_group,g).each(function(){var c={form:g,messages:{},timers:{},control_group:this,helpers:!1,attributes:{}};g.form_control_groups.push(c.control_group),a.each(b.classes.status,function(b){c.messages[b]=a(c.control_group).attr(b+"_message")}),a.each(c.control_group.attributes,function(a,b){c.attributes[b.name]=b.value}),c.control_group=a(c.control_group),c.validation_required="true"==c.control_group.attr("validation_required")?!0:!1,c.validation_type=""!=c.control_group.attr("validation_type")?c.control_group.attr("validation_type"):"not_empty",c.do_not_markup=void 0!==c.control_group.attr("do_not_markup")?!0:!1,c.validation_type&&a(c.control_group).attr(c.validation_type)&&(c[c.validation_type]=a(c.control_group).attr(c.validation_type)),a(b.class_selector.help_block,c.control_group).length&&(c.helpers=a(b.class_selector.help_block,c.control_group)),a("input,textarea,select",c.control_group).each(function(){var d={input_element:a(this),input_type:a(this).attr("type"),name:a(this).attr("name"),type:this.nodeName.toLowerCase()};d.related_fields=a.map(a(d.type,c.control_group).not(d.input_element),function(a){return a}),a(d.input_element).unbind("change"),b.settings.disable_html5_validation||a(d.input_element).attr("aria-required","true"),g.form_helper_fields[d.name]=a.extend({},c,d)})}),f(g.form_helper_fields,"dir"),a.each(g.form_helper_fields,function(c,d){a(d.input_element).data("validation_settings",d),d.inputs_in_group=a("input,textarea,select",d.control_group).length,b.helpers.update_inline_help(d.control_group,d),a(window).load(function(){setTimeout(function(){f("enable() - Executing initial field validation."),i(d.input_element,d)},b.settings.initialization_pause)}),d.intent_delay="string"==typeof d.control_group.attr("intent_delay")?d.control_group.attr("intent_delay"):b.settings.intent_delay,h(d.input_element,d,{check_related:!0})}),g.initialized=!0,a(g).submit(function(a){e(g,a)})})},h=function(b,c,d){d=a.extend({},{check_related:!1},d),a(b).bind("change",function(){i(b,c)}),("input"==c.type||"textarea"==c.type)&&(a(b).keyup(function(){clearTimeout(c.timers.intent_delay),c.timers.intent_delay=setTimeout(function(){i(b,c)},c.intent_delay)}),a(b).blur(function(){clearTimeout(c.timers.intent_delay)})),a(c.input_element).addClass("monitored"),d.check_related&&a.each(c.related_fields,function(b,d){a(d).hasClass("monitored")||h(d,c)})},i=this.validate_field=function(c,d){if(f("validate_field("+("object"==typeof d?d.name:"?")+")"),c&&"object"!=typeof c&&(c=a('[name="'+c+'"]'),f(c.length?"validate_field(): Input Element not passed as object, but found using name in DOM.":"validate_field(): Input Element not passed as object, and could not be found by name  ("+c+") in DOM.")),c&&!d&&(d=c.data("validation_settings")),!d)return f("validate_field(): Warning. validate_field() was called on an Input Field that could not be found, or does not have Validation Settings.","error",!0);var e=a.extend(!0,{validation_type:"not_empty"},d),g="string"==typeof a(c).val()?a(c).val():"";"undefined"==typeof e.form.form_helper_fields[e.name].status_code&&(e.initial_run=!0);var h={detail_log:[]};switch(h.detail_log.push((e.initial_run?"Initial Run":"Secondary Run")+": "+e.name),h.detail_log.push("args.validation_required: "+e.validation_required),h.detail_log.push("args.inputs_in_group: "+e.inputs_in_group),h.detail_log.push("current_value: "+g),h.detail_log.push("validation_type: "+e.validation_type),e.validation_type){case"checked":e.status_code="checkbox"!=a(c).attr("type")?"success":a(c).is(":checked")?"success":"error";break;case"selection_limit":h.detail_log.push("Total related: "+e.related_fields.length);var i=a.map(a(e.related_fields),function(a){return a.checked?a:void 0});h.detail_log.push("Total checked: "+i.length),h.detail_log.push("Selection limit: "+d.selection_limit),0===i.length&&(e.status_code="error",e.messages.success="Please make a selection."),i.length>0&&i.length<d.selection_limit&&(e.status_code="success",e.messages.success="You may select "+(d.selection_limit-i.length)+" more."),i.length==d.selection_limit&&(e.status_code="success",e.messages.success="You can not select anymore."),i.length>d.selection_limit&&a(c).removeAttr("checked"),b.helpers.update_inline_help(e.control_group,e);break;case"password":case"matching_passwords":var l;if(a.each(e.related_fields,function(b,c){l=a(c).val()}),""===g&&""===l){h.detail_log.push("Passwords are empty: "+g+" - "+l),e.status_code="error";break}if(g===l){h.detail_log.push("Passwords match: "+g+" - "+l),e.status_code="success",e.password_strength=Math.round(l.length/13*100),e.messages.success=a('<div class="">Password Strength:</div><div class="progress progress-striped"><div class="bar" style="width: '+e.password_strength+'%;"></div></div>'),b.helpers.update_inline_help(e.control_group,e);break}if(g!=l){h.detail_log.push("Passwords do not match: "+g+" - "+l),e.status_code="error";break}break;case"email":e.status_code=""==g?"blank":b.helpers.validate_email(g)?"success":"error";break;case"url":e.status_code=""==g?"blank":b.helpers.validate_url(g)?"success":"error";break;case"domain":e.status_code=""==g?"blank":b.helpers.validate_url(g,{use_http:!1})?"success":"error";break;case"address":if(""==g){e.status_code="blank";break}if("object"!=typeof google||"object"!=typeof google.maps)break;e.remote_request=!0,e.geocoder=e.geocoder?e.geocoder:new google.maps.Geocoder,e.clean_value=g.replace(/(\r\n|\n|\r)/gm," "),e.geocoder.geocode({address:e.clean_value},function(c){"object"==typeof c&&(a.each(c,function(a,b){"ROOFTOP"==b.geometry.location_type?(e.messages.success="Validated: "+b.formatted_address,e.status_code="success"):e.status_code="ZERO_RESULTS"==b.status?"error":"warning"}),b.helpers.update_inline_help(e.control_group,e),j(g,h,e))});break;case"ajax":if(!e.validation_ajax)break;e.remote_request=!0;var m=a.extend({},{action:e.validation_ajax,field_name:e.name,field_value:g,field_type:e.type},b.settings.ajax_url);a.ajax({url:b.settings.ajax_url,data:m,success:function(a){e.status_code="false"==a.success?"error":"success",e.messages[e.status_code]=a.message,k(e.control_group,e),h.detail_log.push(e.name+" - Custom Ajax Validation. Result: "+e.status_code),j(g,h,e)},dataType:"json"});break;case"pattern":var n=new RegExp(e.attributes.pattern,"g");""==g?e.status_code="blank":n.test(g)?e.status_code="success":(e.status_code="error",e.messages[e.status_code]="Please, match the requested format"+("undefined"!=typeof e.title?":"+e.title:""));break;case"not_empty":default:e.status_code=""==g?"blank":"success",e.status_code=""==g?"blank":"success"}e.remote_request||j(g,h,e)},j=this.finalize_field_validation=function(c,d,e){"blank"==e.status_code&&b.settings.error_on_blank&&(e.status_code="error"),d.detail_log.push("new status_code: "+e.status_code),a(e.input_element).attr("validation_status_code",e.status_code),a(e.control_group).attr("validation_status_code",e.status_code),e.form.form_helper_fields[e.name].status_code=e.status_code,e.validation_required&&("success"!=e.status_code&&d.detail_log.push("Field validation fail."),l(e.form).validation_fail?(a(e.form).addClass("validation_fail"),a(e.form).data("do_not_process",!0),d.detail_log.push("Form validation fail.")):(a(e.form).removeClass("validation_fail"),a(e.form).removeData("do_not_process"),d.detail_log.push("Form passed validation."))),f(d.detail_log,"dir"),(!e.initial_run||b.settings.validate_on_enable)&&k(e.control_group,e)},k=this.update_control_group_ui=function(c,d){f("update_control_group_ui()"),d.do_not_markup||(a.each(b.classes.status,function(b,d){a(c).removeClass(d)}),d.helpers&&(a("> ."+b.classes.helper_item,d.helpers).removeClass(b.classes.active_helper).addClass(b.classes.disabled_helper),a("."+d.status_code,d.helpers).removeClass(b.classes.disabled_helper).addClass(b.classes.active_helper)),a(c).addClass(b.classes.status[d.status_code]))},l=this.form_status=function(b){if(f("form_status()"),"object"!=typeof b.form_helper_fields)return f("form_status() - form.form_helper_fields is not an object, leaving."),{};var c={validation_fail:!1,failed_fields:[]};return a.each(b.form_helper_fields,function(a,b){"success"!=b.status_code&&(f("form_status() - Input Field failed validation: "+a),c.failed_fields.push(a))}),c.failed_fields.length?(f("form_status() - Form failed validation. Invalid fields: "+c.failed_fields.length),b.validation_fail=c.validation_fail=!0,b.failed_fields=c.failed_fields):f("form_status() - Form is valid. "),c};return"function"!=typeof b.helpers.update_inline_help&&(b.helpers.update_inline_help=function(c,d){f("helpers.update_inline_help()"),d.helpers||(d.helpers=a(b.class_selector.help_block,c)),d.helpers.length||a(d.type+":last",c).after(d.helpers=a('<span class="'+b.classes.help_block+'"></span>')),a.each(d.messages,function(c,e){var f=b.classes.status[c],g=a("span."+f,d.helpers);g.length||a(d.helpers).append(g=a("<span></span>")),g.addClass(b.classes.helper_item),g.addClass(f),b.settings.auto_hide_helpers&&g.addClass(b.classes.disabled_helper),a(g).html(e)})}),"function"!=typeof b.helpers.css_class_query&&(b.helpers.css_class_query=function(b){var c=[];if("object"==typeof b||"array"==typeof b){var d=!1;if(a.each(b,function(b,e){return c.push(e),a.isNumeric(b)?void 0:void(d=!0)}),d)return b}else"string"==typeof b&&(c=b.split(" "));return("."+c.join(".")).replace(/\.\./g,".")}),"function"!=typeof b.helpers.remove_from_array&&(b.helpers.remove_from_array=function(b,c){return a.grep(c,function(a){return a!==b})}),"function"!=typeof b.helpers.validate_url&&(b.helpers.validate_url=function(b,c){return f("helpers.validate_url("+b+")"),c=a.extend({use_http:!0},c),c.use_http?/^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?/i.test(b):/^[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?/i.test(b)}),"function"!=typeof b.helpers.validate_email&&(b.helpers.validate_email=function(a){f("helpers.validate_email("+a+")");var b=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;return b.test(a)}),b.settings.auto_enable&&g(),this}}(jQuery);
!function(a){a.fn.smart_dom_button=function(b){var c=a.extend({debug:!1,action_attribute:"action_attribute",response_container:"response_container",ajax_action:"action",label_attributes:{process:"processing_label",revert_label:"revert_label",verify_action:"verify_action"}},b);return log=function(a,b){c.debug&&window.console&&console.debug&&("error"==b?console.error(a):console.log(a))},get_label=function(b){var c=a(b).get(0).tagName,d="";switch(c){case"SPAN":d=a(b).text();break;case"INPUT":d=a(b).val()}return d},set_label=function(b,c){switch(c.type){case"SPAN":a(c.button).text(b);break;case"INPUT":a(c.button).val(b)}return b},do_execute=function(b){var d={button:b,type:a(b).get(0).tagName,original_label:a(b).attr("original_label")?a(b).attr("original_label"):get_label(b)};c.wrapper&&a(d.button).closest(c.wrapper).length?(d.wrapper=a(d.button).closest(c.wrapper),d.use_wrapper=!0):(d.wrapper=d.button,d.use_wrapper=!1),d.the_action=a(d.wrapper).attr(c.action_attribute)?a(d.wrapper).attr(c.action_attribute):!1,c.label_attributes.processing&&a(d.wrapper).attr(c.label_attributes.processing)&&(d.processing_label=a(d.wrapper).attr(c.label_attributes.processing)?a(d.wrapper).attr(c.label_attributes.processing):!1),c.label_attributes.verify_action&&a(d.wrapper).attr(c.label_attributes.verify_action)&&(d.verify_action=a(d.wrapper).attr(c.label_attributes.verify_action)?a(d.wrapper).attr(c.label_attributes.verify_action):!1),c.label_attributes.revert_label&&a(d.wrapper).attr(c.label_attributes.revert_label)&&(d.revert_label=a(d.wrapper).attr(c.label_attributes.revert_label)?a(d.wrapper).attr(c.label_attributes.revert_label):!1,a(d.wrapper).attr("original_label")||(d.original_label=get_label(d.button),a(d.wrapper).attr("original_label",d.original_label))),d.the_action&&(!d.verify_action||confirm(d.verify_action))&&(d.use_wrapper&&(a(c.response_container,d.wrapper).length||a(d.wrapper).append('<span class="response_container"></span>'),d.response_container=a(".response_container",d.wrapper),a(d.response_container).removeClass(),a(d.response_container).addClass("response_container"),d.processing_label&&a(d.response_container).html(d.processing_label)),"ui"==d.the_action?(d.revert_label&&(get_label(d.button)==d.revert_label?set_label(d.original_label,d):set_label(d.revert_label,d)),a(d.wrapper).attr("toggle")&&a(a(d.wrapper).attr("toggle")).toggle(),a(d.wrapper).attr("show")&&a(a(d.wrapper).attr("show")).show(),a(d.wrapper).attr("hide")&&a(a(d.wrapper).attr("hide")).hide()):a.post(ajaxurl,{action:c.ajax_action,the_action:d.the_action},function(b){if(b&&b.success){a(d.response_container).show(),b.css_class&&a(d.response_container).addClass(b.css_class),b.remove_element&&a(b.remove_element).length&&a(b.remove_element).remove(),a(d.response_container).html(b.message);var c;a(d.response_container).mouseover(function(){c=setTimeout(function(){a(d.response_container).fadeOut("slow",function(){a(d.response_container).remove()})},1e4)}).mouseout(function(){clearTimeout(c)})}},"json"))},a(this).click(function(){log("Button triggered."),do_execute(this)}),this}}(jQuery);
!function(a){"use strict";a.fn.social=function(b){b=a.extend({element:this,networks:{linkedin:{profile_fields:{id:"network_id",firstName:"first_name",lastName:"last_name",pictureUrl:"user_image",headline:"headline",industry:"industry",summary:"summary",specialties:"specialties",location:"location",associations:"associations",certifications:"certifications",educations:"educations",skills:"skills",patents:"patents",honors:"honors",proposalComments:"proposal_comments","three-current-positions":"current_positions","recommendations-received":"recommendations","main-address":"primary_address","member-url-resources":"url_resources","phone-numbers":"phone_number","public-profile-url":"profile_url","im-accounts":"im_accounts"}}},user_data:{},debug:!0},b);{var c=this.log=function(a,c){b.debug&&window.console&&console.debug&&("error"===c?console.error(a):console.log(a))},d=this.handle_linkedin=function(){c("handle_linkedin()"),b.networks.linkedin.active=!0,"undefined"!=typeof IN.Event&&(a(".linkedin_asset").show(),IN.Event.on(IN,"auth",function(){c("IN.Event::auth ");var d=[];a.each(b.networks.linkedin.profile_fields,function(a){d.push(a)}),IN.API.Profile("me").fields(d).result(function(d){c("IN.API.Profile()"),a.each(d.values[0],function(a,c){var d=b.networks.linkedin.profile_fields[a];if("undefined"!=typeof d&&""!=c){switch(a){case"location":c=c.name}b.user_data[d]=c}}),b.user_data.display_name=b.user_data.first_name+" "+b.user_data.last_name,b.user_data&&a(".linked_in_login").html('<p class="linkedin_authentication alert alert-info">'+("string"==typeof b.user_data.user_image?'<img src="'+b.user_data.user_image+'" class="user_image">':"")+'<span class="welcome_text">Hello, <b>'+b.user_data.first_name+"</b>! </span>"+("string"==typeof b.user_data.headline?'<span class="linkedin_headline">'+b.user_data.headline+"</span>":"")+("string"==typeof b.user_data.industry?'<span class="linkedin_industry">'+b.user_data.industry+"</span>":"")+"</p>"),a(document).trigger("social::user_data_update",b.user_data)}),a(document).bind("social::user_logout",function(){"object"==typeof IN&&"undefined"!=typeof IN.User&&IN.User.logout()})}))};this.user_logout=function(){a(document).trigger("social::user_logout")},this.enable=function(){c("social::enable()"),"object"==typeof IN&&d()}}return this.enable(),this}}(jQuery);
!function(a){a.fn.super_search=function(b){var c,d,e=this,f={},g={},h={current:"",previous:""};this.attr("ss_element","search_input");var i=a.extend({action:"super_search",ajax_url:ajaxurl,input_classes:{no_results:"ss_no_results",processing:"ss_processing",error:"ss_error"},response_classes:{response_wrapper:"ss_response_container",show_scroll:"ss_show_scroll",item_class:""},append_to:a(e).parent(),search_trigger:!1,search_result_gap:200,limit:5,timers:{abandonment:1e3,search_entry:2e3},async:!1,debug:!0,success:!1,beforeSend:!1,ui:{}},b);"function"!=typeof i.log&&(i.log=function(a){i.debug&&console.log(a)}),a(i.append_to).length||i.log("The ("+i.append_to+") element does not exist.","warning"),i.search_trigger&&"object"==typeof i.search_trigger&&a(i.search_trigger).click(function(){h.current!=h.previous&&a.fn.super_search.do_search()}),this.keyup(function(){return h.current=e.val(),d&&!h.current?void clearTimeout(d):void(h.current!=h.previous&&(d&&clearTimeout(d),d=setTimeout(a.fn.super_search.do_search,i.timers.search_entry)))}),this.focus(function(){a.fn.super_search.ux_change()}),this.blur(function(){a.fn.super_search.ux_change()}),a.fn.super_search.do_search=function(){i.log("do_search()"),h.current=e.val();var b={action:i.action,limit:i.limit,query:h.current};cb_data={post_data:b,settings:i},a.each(i.input_classes,function(b,c){a(e).removeClass(c)}),a(e).addClass(i.input_classes.processing),a.ajax({url:i.ajax_url,async:i.async,data:b,beforeSend:function(a,b){i.log("do_search.beforeSend() - have callback, executing"),cb_data.settings=b,"function"==typeof i.beforeSend&&i.beforeSend.call(this,cb_data)},complete:function(b,c){i.log("do_search.complete( jqXHR, "+c+" )"),a(e).removeClass(i.input_classes.processing),i.log("Ajax response received.")},success:function(b,c,d){i.log("do_search.success()"),"function"==typeof i.success&&(i.log("do_search.success() - have callback, executing"),i.success.call(b,c,d))||(h.previous=h.current,a.fn.super_search.remove_rendered_results(),b.results?(i.last_results=b.results,a.fn.super_search.render_results(b.results)):a(e).addClass(i.input_classes.no_results),b.other&&i.log("Search Debug Data:".data.debug_response))},error:function(){i.log("do_search.error()"),a(e).addClass(i.input_classes.error)},dataType:"json"})},a.fn.super_search.remove_rendered_results=function(){i.log("remove_rendered_results()"),i.rendered_element&&i.rendered_element.length&&a(i.rendered_element).fadeOut(300,function(){}),a.fn.super_search.update_dom_triggers()},a.fn.super_search.update_dom_triggers=function(){i.log("update_dom_triggers()"),a(i.rendered_element).off("mouseenter"),a(i.rendered_element).off("mouseleave"),a(i.rendered_element).mouseenter(function(){g.results_over=!0,a.fn.super_search.ux_change()}).mouseleave(function(){g.results_over=!1,a.fn.super_search.ux_change()})},a.fn.super_search.ux_change=function(){i.log("ux_change()"),a(e).is(":focus")?(g.input_focus=!0,h.current==h.previous&&i.rendered_element&&i.rendered_element.show()):g.input_focus=!1,i.rendered_element&&!g.input_focus&&g.results_over===!1?c=setTimeout(a.fn.super_search.remove_rendered_results,i.timers.abandonment):c&&clearTimeout(c)},a.fn.super_search.render_results=function(b){i.log("render_results()");var c=b.length-1;html=[],html.push(i.ui.response_container='<ul ss_element="response_container" class="ss_element '+i.response_classes.response_wrapper+'">'),console.log(i.ui.response_container),a.each(b,function(a,b){var d=[];i.response_classes.item_class&&d.push(i.response_classes.item_class),b.item_class&&d.push(b.item_class),a==c&&d.push("last_item"),html.push('<li ss_element="single_item" class="ss_element '+d.join(" ")+'">'),b.url&&html.push('<a href="'+b.url+'">'),html.push(b.title),b.url&&html.push("</a>"),html.push("</li>")}),html.push("</ul>"),i.rendered_element=a(html.join("")),a(i.append_to).append(i.rendered_element),f.window_height=a(window).height(),f.rendered_element=a(i.rendered_element).height(),f.rendered_element+i.search_result_gap>f.window_height&&(a(i.rendered_element).css("max-height",f.window_height-i.search_result_gap+"px"),a(i.rendered_element).addClass(i.response_classes.show_scroll),f.rendered_element=a(i.rendered_element).height()),a.fn.super_search.update_dom_triggers()}}}(jQuery);
!function(a,b){a.widget("ui.progressbar",{options:{value:0,max:100},min:0,_create:function(){this.element.addClass("ui-progressbar ui-widget ui-widget-content ui-corner-all").attr({role:"progressbar","aria-valuemin":this.min,"aria-valuemax":this.options.max,"aria-valuenow":this._value()}),this.valueDiv=a("<div class='ui-progressbar-value ui-widget-header ui-corner-left'></div>").appendTo(this.element),this.oldValue=this._value(),this._refreshValue()},destroy:function(){this.element.removeClass("ui-progressbar ui-widget ui-widget-content ui-corner-all").removeAttr("role").removeAttr("aria-valuemin").removeAttr("aria-valuemax").removeAttr("aria-valuenow"),this.valueDiv.remove(),a.Widget.prototype.destroy.apply(this,arguments)},value:function(a){return a===b?this._value():(this._setOption("value",a),this)},_setOption:function(b,c){"value"===b&&(this.options.value=c,this._refreshValue(),this._value()===this.options.max&&this._trigger("complete")),a.Widget.prototype._setOption.apply(this,arguments)},_value:function(){var a=this.options.value;return"number"!=typeof a&&(a=0),Math.min(this.options.max,Math.max(this.min,a))},_percentage:function(){return 100*this._value()/this.options.max},_refreshValue:function(){var a=this.value(),b=this._percentage();this.oldValue!==a&&(this.oldValue=a,this._trigger("change")),this.valueDiv.toggleClass("ui-corner-right",a===this.options.max).width(b.toFixed(0)+"%"),this.element.attr("aria-valuenow",a)}}),a.extend(a.ui.progressbar,{version:"1.8.10"})}(jQuery);
define("udx.analytics",["//www.google-analytics.com/analytics.js"],function(){function a(a){return"string"==typeof a&&(a={id:a}),"function"!=typeof ga?console.error("udx.analytics","The ga variable is not a function."):a.id?(ga("create",a.id,{userId:a.userId||void 0,cookieDomain:window.location.hostname}),this.setView(),this):console.error("udx.analytics","No id provided.")}return Object.defineProperties(a.prototype,{ga:{value:ga,enumerable:!0,configurable:!0,writable:!0},autoLink:{value:function(){return ga(function(a){a.get("name")}),this},enumerable:!0,configurable:!0,writable:!0},emit:{value:function(a,b,c,d){return ga("send","event",{eventCategory:a,eventAction:b,eventLabel:c,eventValue:d}),this},enumerable:!0,configurable:!0,writable:!0},set:{value:function(a,b){return ga("set",a,b),this},enumerable:!0,configurable:!0,writable:!0},sendHit:{value:function(a,b){return ga("send",{hitType:a||"pageview",page:b||document.location.pathname}),this},enumerable:!0,configurable:!0,writable:!0},setView:{value:function(){return ga("send","pageview"),this},enumerable:!0,configurable:!0,writable:!0},setClient:{value:function(){},enumerable:!0,configurable:!0,writable:!0},setSocial:{value:function(){},enumerable:!0,configurable:!0,writable:!0}}),Object.defineProperties(a,{create:{value:function(b){return new a(b)},enumerable:!0,configurable:!0,writable:!0}}),a});
define("udx.event",function(){function a(){this._events={},this._conf&&b.call(this,this._conf)}function b(a){this._conf=a||e.event.prototype.defaults,console.log("configure",this._conf),this._conf.wildcard&&(this.listenerTree={})}function c(a,b,d,e){if(!d)return[];var f,g,h,i,j,k,l,m=[],n=b.length,o=b[e],p=b[e+1];if(e===n&&d._listeners){if("function"==typeof d._listeners)return a&&a.push(d._listeners),[d];for(f=0,g=d._listeners.length;g>f;f++)a&&a.push(d._listeners[f]);return[d]}if("*"===o||"**"===o||d[o]){if("*"===o){for(h in d)"_listeners"!==h&&d.hasOwnProperty(h)&&(m=m.concat(c(a,b,d[h],e+1)));return m}if("**"===o){l=e+1===n||e+2===n&&"*"===p,l&&d._listeners&&(m=m.concat(c(a,b,d,n)));for(h in d)"_listeners"!==h&&d.hasOwnProperty(h)&&("*"===h||"**"===h?(d[h]._listeners&&!l&&(m=m.concat(c(a,b,d[h],n))),m=m.concat(c(a,b,d[h],e))):m=m.concat(h===p?c(a,b,d[h],e+2):c(a,b,d[h],e)));return m}m=m.concat(c(a,b,d[o],e+1))}if(i=d["*"],i&&c(a,b,i,e+1),j=d["**"])if(n>e){j._listeners&&c(a,b,j,n);for(h in j)"_listeners"!==h&&j.hasOwnProperty(h)&&(h===p?c(a,b,j[h],e+2):h===o?c(a,b,j[h],e+1):(k={},k[h]=j[h],c(a,b,{"**":k},e+1)))}else j._listeners?c(a,b,j,n):j["*"]&&j["*"]._listeners&&c(a,b,j["*"],n);return m}function d(a,b){a="string"==typeof a?a.split(this._conf.delimiter):a.slice();for(var c=0,d=a.length;d>c+1;c++)if("**"===a[c]&&"**"===a[c+1])return;for(var e=this.listenerTree,g=a.shift();g;){if(e[g]||(e[g]={}),e=e[g],0===a.length){if(e._listeners){if("function"==typeof e._listeners)e._listeners=[e._listeners,b];else if(f(e._listeners)&&(e._listeners.push(b),!e._listeners.warned)){var h=this.defaultMaxListeners;"undefined"!=typeof this._events.maxListeners&&(h=this._events.maxListeners),h>0&&e._listeners.length>h&&(e._listeners.warned=!0,console.error("(node) warning: possible ud.event memory leak detected. %d listeners added. Use emitter.setMaxListeners() to increase limit.",e._listeners.length),console.trace())}}else e._listeners=b;return!0}g=a.shift()}return!0}var e="object"==typeof e?e:{},f=Array.isArray?Array.isArray:function(a){return"[object Array]"===Object.prototype.toString.call(a)};e.event=function(a){this._events={},b.call(this,a)},e.event.bestow=function(a,b){var c=new e.event(b);a=a&&"object"==typeof a?a:{};for(var d in c)"function"==typeof Object.defineProperty?Object.defineProperty(a,d,{value:c[d],enumerable:"function"==typeof c[d]?!1:!0,writable:"function"==typeof c[d]?!1:!0}):a[d]=c[d];return a},e.event.prototype.defaults={wildcard:!0,defaultMaxListeners:20,delimiter:"."},e.event.prototype.setMaxListeners=function(b){this._events||a.call(this),this._events.maxListeners=b,this._conf||(this._conf={}),this._conf.maxListeners=b},e.event.prototype.event="",e.event.prototype.once=function(a,b){return this.many(a,1,b),this},e.event.prototype.many=function(a,b,c){function d(){0===--b&&e.off(a,d),c.apply(this,arguments)}var e=this;if("function"!=typeof c)throw new Error("many only accepts instances of Function");return d._origin=c,this.on(a,d),e},e.event.prototype.emit=function(){this._events||a.call(this);var b=arguments[0];if(this._all){for(var d=arguments.length,e=new Array(d-1),f=1;d>f;f++)e[f-1]=arguments[f];for(f=0,d=this._all.length;d>f;f++)this.event=b,this._all[f].apply(this,e)}if("error"===b&&!(this._all||this._events.error||this._conf.wildcard&&this.listenerTree.error))throw arguments[1]instanceof Error?arguments[1]:new Error("Uncaught, unspecified 'error' event.");var g;if(this._conf.wildcard){g=[];var h="string"==typeof b?b.split(this._conf.delimiter):b.slice();c.call(this,g,h,this.listenerTree,0)}else g=this._events[b];if("function"==typeof g){if(this.event=b,1===arguments.length)g.call(this);else if(arguments.length>1)switch(arguments.length){case 2:g.call(this,arguments[1]);break;case 3:g.call(this,arguments[1],arguments[2]);break;default:for(var d=arguments.length,e=new Array(d-1),f=1;d>f;f++)e[f-1]=arguments[f];g.apply(this,e)}return!0}if(g){for(var d=arguments.length,e=new Array(d-1),f=1;d>f;f++)e[f-1]=arguments[f];for(var i=g.slice(),f=0,d=i.length;d>f;f++)this.event=b,i[f].apply(this,e);return i.length>0||this._all}return this._all},e.event.prototype.on=function(b,c){if("function"==typeof b)return this.onAny(b),this;if("function"!=typeof c)throw new Error("on only accepts instances of Function");if(this._events||a.call(this),this._conf.wildcard)return d.call(this,b,c),this;if(this._events[b]){if("function"==typeof this._events[b])this._events[b]=[this._events[b],c];else if(f(this._events[b])&&(this._events[b].push(c),!this._events[b].warned)){var e=this.defaultMaxListeners;"undefined"!=typeof this._events.maxListeners&&(e=this._events.maxListeners),e>0&&this._events[b].length>e&&(this._events[b].warned=!0,console.error("(node) warning: possible ud.event memory leak detected. %d listeners added. Use emitter.setMaxListeners() to increase limit.",this._events[b].length),console.trace())}}else this._events[b]=c;return this},e.event.prototype.onAny=function(a){if(this._all||(this._all=[]),"function"!=typeof a)throw new Error("onAny only accepts instances of Function");return this._all.push(a),this},e.event.prototype.addListener=e.event.prototype.on,e.event.prototype.off=function(a,b){if("function"!=typeof b)throw new Error("removeListener only takes instances of Function");var d,e=[];if(this._conf.wildcard){var g="string"==typeof a?a.split(this._conf.delimiter):a.slice();e=c.call(this,null,g,this.listenerTree,0)}else{if(!this._events[a])return this;d=this._events[a],e.push({_listeners:d})}for(var h=0;h<e.length;h++){var i=e[h];if(d=i._listeners,f(d)){for(var j=-1,k=0,l=d.length;l>k;k++)if(d[k]===b||d[k].listener&&d[k].listener===b||d[k]._origin&&d[k]._origin===b){j=k;break}if(0>j)return this;this._conf.wildcard?i._listeners.splice(j,1):this._events[a].splice(j,1),0===d.length&&(this._conf.wildcard?delete i._listeners:delete this._events[a])}else(d===b||d.listener&&d.listener===b||d._origin&&d._origin===b)&&(this._conf.wildcard?delete i._listeners:delete this._events[a])}return this},e.event.prototype.offAny=function(a){var b,c=0,d=0;if(a&&this._all&&this._all.length>0){for(b=this._all,c=0,d=b.length;d>c;c++)if(a===b[c])return b.splice(c,1),this}else this._all=[];return this},e.event.prototype.removeListener=e.event.prototype.off,e.event.prototype.removeAllListeners=function(b){if(0===arguments.length)return!this._events||a.call(this),this;if(this._conf.wildcard)for(var d="string"==typeof b?b.split(this._conf.delimiter):b.slice(),e=c.call(this,null,d,this.listenerTree,0),f=0;f<e.length;f++){var g=e[f];g._listeners=null}else{if(!this._events[b])return this;this._events[b]=null}return this},e.event.prototype.listeners=function(b){if(this._conf.wildcard){var d=[],e="string"==typeof b?b.split(this._conf.delimiter):b.slice();return c.call(this,d,e,this.listenerTree,0),d}return this._events||a.call(this),this._events[b]||(this._events[b]=[]),f(this._events[b])||(this._events[b]=[this._events[b]]),this._events[b]},e.event.prototype.listenersAny=function(){return this._all?this._all:[]}});
define("udx.filter",function(){return{apply_filter:function(a,b){return"undefined"==typeof b||"undefined"==typeof a||"string"!=typeof a?b:"undefined"==typeof jQuery?b:"undefined"==typeof window.__ud_filters||"undefined"==typeof window.__ud_filters[a]?b:(jQuery.each(window.__ud_filters[a],function(a,c){if("function"==typeof c)b=c(b);else if("object"==typeof c){if("object"!=typeof b)return!1;b=jQuery.extend(!0,b,c)}}),b)},add_filter:function(a,b){"undefined"!=typeof b&&"undefined"!=typeof a&&"string"==typeof a&&("undefined"==typeof window.__ud_filters&&(window.__ud_filters={}),"undefined"==typeof window.__ud_filters[a]&&(window.__ud_filters[a]=[]),window.__ud_filters[a].push(b))}}});
!function(a,b){"undefined"!=typeof module?module.exports=b():"function"==typeof define&&"object"==typeof define.amd?define(b):this[a]=b()}("fleck",function(){var a={pluralRules:[[new RegExp("(m)an$","gi"),"$1en"],[new RegExp("(pe)rson$","gi"),"$1ople"],[new RegExp("(child)$","gi"),"$1ren"],[new RegExp("^(ox)$","gi"),"$1en"],[new RegExp("(ax|test)is$","gi"),"$1es"],[new RegExp("(octop|vir)us$","gi"),"$1i"],[new RegExp("(alias|status)$","gi"),"$1es"],[new RegExp("(bu)s$","gi"),"$1ses"],[new RegExp("(buffal|tomat|potat)o$","gi"),"$1oes"],[new RegExp("([ti])um$","gi"),"$1a"],[new RegExp("sis$","gi"),"ses"],[new RegExp("(?:([^f])fe|([lr])f)$","gi"),"$1$2ves"],[new RegExp("(hive)$","gi"),"$1s"],[new RegExp("([^aeiouy]|qu)y$","gi"),"$1ies"],[new RegExp("(matr|vert|ind)ix|ex$","gi"),"$1ices"],[new RegExp("(x|ch|ss|sh)$","gi"),"$1es"],[new RegExp("([m|l])ouse$","gi"),"$1ice"],[new RegExp("(quiz)$","gi"),"$1zes"],[new RegExp("s$","gi"),"s"],[new RegExp("$","gi"),"s"]],singularRules:[[new RegExp("(m)en$","gi"),"$1an"],[new RegExp("(pe)ople$","gi"),"$1rson"],[new RegExp("(child)ren$","gi"),"$1"],[new RegExp("([ti])a$","gi"),"$1um"],[new RegExp("((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$","gi"),"$1$2sis"],[new RegExp("(hive)s$","gi"),"$1"],[new RegExp("(tive)s$","gi"),"$1"],[new RegExp("(curve)s$","gi"),"$1"],[new RegExp("([lr])ves$","gi"),"$1f"],[new RegExp("([^fo])ves$","gi"),"$1fe"],[new RegExp("([^aeiouy]|qu)ies$","gi"),"$1y"],[new RegExp("(s)eries$","gi"),"$1eries"],[new RegExp("(m)ovies$","gi"),"$1ovie"],[new RegExp("(x|ch|ss|sh)es$","gi"),"$1"],[new RegExp("([m|l])ice$","gi"),"$1ouse"],[new RegExp("(bus)es$","gi"),"$1"],[new RegExp("(o)es$","gi"),"$1"],[new RegExp("(shoe)s$","gi"),"$1"],[new RegExp("(cris|ax|test)es$","gi"),"$1is"],[new RegExp("(octop|vir)i$","gi"),"$1us"],[new RegExp("(alias|status)es$","gi"),"$1"],[new RegExp("^(ox)en","gi"),"$1"],[new RegExp("(vert|ind)ices$","gi"),"$1ex"],[new RegExp("(matr)ices$","gi"),"$1ix"],[new RegExp("(quiz)zes$","gi"),"$1"],[new RegExp("s$","gi"),""]],uncountableWords:{equipment:!0,information:!0,rice:!0,money:!0,species:!0,series:!0,fish:!0,sheep:!0,moose:!0,deer:!0,news:!0},inflect:function(b){for(var c=1,d=arguments.length;d>c;c++)b=a[arguments[c]](b);return b},capitalize:function(a){return a.charAt(0).toUpperCase()+a.substring(1).toLowerCase()},camelize:function(b,c){return c?a.upperCamelize(b):b.replace(/[-_]+(.)?/g,function(a,b){return b?b.toUpperCase():""})},upperCamelize:function(b){return a.camelize(a.capitalize(b))},dasherize:function(a){return a.replace(/\s|_/g,"-")},ordinalize:function(a){var b,c,d;if(d=parseInt(a,10)%100,b={11:!0,12:!0,13:!0}[d])return a+"th";switch(d=parseInt(a,10)%10){case 1:c=a+"st";break;case 2:c=a+"nd";break;case 3:c=a+"rd";break;default:c=a+"th"}return c},pluralize:function(b){var c=a.uncountableWords[b.toLowerCase()];if(c)return b;for(var d=a.pluralRules,e=0,f=d.length;f>e;e++)if(b.match(d[e][0])){b=b.replace(d[e][0],d[e][1]);break}return b},singularize:function(b){var c=a.uncountableWords[b.toLowerCase()];if(c)return b;for(var d=a.singularRules,e=0,f=d.length;f>e;e++)if(b.match(d[e][0])){b=b.replace(d[e][0],d[e][1]);break}return b},strip:function(a){return a.replace(/^\s+/,"").replace(/\s+$/,"")},underscore:function(a){return a.replace(/::/g,"_").replace(/([A-Z]+)([A-Z][a-z])/g,"$1_$2").replace(/([a-z\d])([A-Z])/g,"$1_$2").replace(/[-\.]/g,"_").toLowerCase()},uncountable:function(){for(var b=0,c=arguments.length;c>b;b++)a.uncountableWords[arguments[b]]=!0;return a}};return a});
var ud,ko,Application={};Application.version="0.1",Application.define=function(a,b){if("object"!=typeof this.__)return null;var c=this.__;switch(a=a.split("."),a[0]){case"core":c.core=c.core||{},c.core[a[1]]=b;break;case"model":c.models=c.models||{},c.models[a[1]]=b}},Application.load=function(a){if("object"==typeof this.__)return this.__;var b=jQuery.extend(!0,{_required:{},version:Application.version,url:"//"+location.host+"/",model_url:"//"+location.host+"/model/",view_url:"//"+location.host+"/view/",modules:{},models:{},socket:!1,default_module:!1,ui:{content:"#content"},listeners:{rendered:function(){return null},add_menu_item:function(a){return a},add_content_wrapper:function(a){return a},section_selected:function(){return null},socket_connected:function(){return null}}},"object"==typeof a?a:"string"==typeof a?{model:a}:{},{});return b.rendered=!1,b.sections={},b._required=jQuery.extend(!0,b._required,{js:{async:"//ud-cdn.com/js/async/1.0/async.js",ko:"//ud-cdn.com/js/knockout/latest/knockout.js","ko.mapping":"//ud-cdn.com/js/knockout.mapping/latest/knockout.mapping.js","knockout.ud":"//ud-cdn.com/js/knockout.ud/latest/knockout.ud.js",Sammy:"//ud-cdn.com/js/sammy/0.7.1/sammy.js",io:"//ud-cdn.com/js/ud.socket/1.0.0/ud.socket.js",bootstrap:"//ud-cdn.com/js/bootstrap/2.2.2/bootstrap.min.js","Application.__.core.view_model":"//ud-cdn.com/js/ud.happ/"+b.version+"/core/view_model.js","Application.__.core.socket":"//ud-cdn.com/js/ud.happ/"+b.version+"/core/socket.js","Application.__.core.json_editor":"//ud-cdn.com/js/ud.happ/"+b.version+"/core/json_editor.js"},css:{}}),b.show_error=function(a,b){return"undefined"!=typeof console&&console.error("undefined"!=typeof b?b:"ERROR",a),null},b.is_url=function(a){return"string"==typeof a&&/^((https?|ftp):)?\/\/([\-A-Z0-9.]+)(\/[\-A-Z0-9+&@#\/%=~_|!:,.;]*)?(\?[A-Z0-9+&@#\/%=~_|!:,.;]*)?/i.test(a)?!0:!1},b._init=function(){if(b.rendered)return null;for(var a in b.modules){switch(b.modules[a]=jQuery.extend(!0,{name:a,description:"",type:"module",menu:"#modules_menu",id:"module_"+a,href:!1,parent:!1,model:b.model_url+a+".js",view:b.view_url+a+".tpl",args:{},view_model:null},"object"==typeof b.modules[a]?b.modules[a]:{name:b.modules[a]}),b.modules[a].type){case"module":if("string"==typeof b.modules[a].model&&b.is_url(b.modules[a].model)&&(b._required.js["module."+a]=b.modules[a].model),b.sections[b.modules[a].id]=a,jQuery(b.ui.content).length>0&&!jQuery("#"+b.modules[a].id).length>0){var c='<div id="'+b.modules[a].id+'" class="module-container"></div>';try{content=b.listeners.add_content_wrapper(c,b.modules[a],b)}catch(d){b.show_error("add_content_wrapper",d)}"string"==typeof c&&jQuery(b.ui.content).append(c)}break;case"link":}if(jQuery(b.modules[a].menu).length>0){var e=b.is_url(b.modules[a].href)?b.modules[a].href:"#"+b.modules[a].id,f='<a href="'+e+'">'+b.modules[a].name+"</a>";try{f=b.listeners.add_menu_item(f,b.modules[a],b)}catch(d){b.show_error("add_menu_item",d)}"string"==typeof f&&(f=jQuery(f),f.each(function(c,d){var e="undefined"!=typeof jQuery(d).attr("href")?jQuery(d):jQuery("a",d);e.length>0&&e.attr("module",a).attr("type",b.modules[a].type)}),jQuery(b.modules[a].menu).append(f))}}"object"==typeof b._required.css&&jQuery.each(b._required.css,function(a,b){ud.load.css(b)}),ud.load.js(b._required.js,function(){b.router=b.router();var a={};jQuery.each(b.models,function(b,c){"object"==typeof c&&"object"==typeof c._required&&("object"==typeof c._required.js&&jQuery.each(c._required.js,function(b,c){a[b]=c}),"object"==typeof c._required.css&&jQuery.each(c._required.css,function(a,b){ud.load.css(b)}))}),Object.size=function(a){var b,c=0;for(b in a)a.hasOwnProperty(b)&&c++;return c},Object.size(a)>0?ud.load.js(a,b._run):b._run()})},b._run=function(){return b.rendered?null:void(b.socket&&"object"==typeof b.socket?b.core.socket(b.socket,function(a){b.socket=a;try{b.listeners.socket_connected(b)}catch(c){b.show_error("socket_connected",c)}b.router.run()}):b.router.run())},b.router=function(){return new Sammy(function(){this.home=b.url,this.loading=!1,this.get(/\#(.*)/,function(a){if(a.app.loading)return null;a.app.loading=!0;var c={section:!1,args:[]};jQuery.each(a.params.splat[0].split("/"),function(a,b){return 0==b.length?null:void(c.section?c.args.push(b):c.section=b)});var d="undefined"!=typeof b.sections[c.section]?b.sections[c.section]:!1;if(!d||"undefined"==typeof b.modules[d])return b.show_error("Module with the hash '"+c.section+"' doesn't exist.","Sammy.get( '#:module' )"),a.app.loading=!1,"undefined"!=typeof b.modules[b.default_module]&&"undefined"!=typeof b.sections[b.modules[b.default_module].id]&&a.app.runRoute("get","#"+b.modules[b.default_module].id),null;null===b.modules[d].view_model||"object"!=typeof b.modules[d].view_model?(b.modules[d].view_model=b.core.view_model({scope:b,model:"object"==typeof b.models[d]?b.models[d]:{},view:b.modules[d].view,args:jQuery.extend(b.modules[d].args,{module:d}),container:"#"+c.section}),setTimeout(function(){b.modules[d].view_model.apply(c.args)},300)):b.modules[d].view_model.update(c.args);var e=b.sections[c.section];if("undefined"!=typeof b.selected_section&&e===b.selected_section)return a.app.loading=!1,null;b.selected_section=e;for(var f in b.sections)"function"!=typeof b.sections[f]&&jQuery('a[href="#'+f+'"]').removeClass("selected");for(var f in b.sections)if("function"!=typeof b.sections[f]){var g=jQuery("#"+f).get(0);jQuery(g).hide(),jQuery(g).attr("id")===c.section&&(b.modules[d].parent&&"object"==typeof b.modules[b.modules[d].parent]&&jQuery('a[href="#'+b.modules[b.modules[d].parent].id+'"][type="module"]').addClass("selected"),jQuery('a[href="#'+c.section+'"]').addClass("selected"),jQuery(g).fadeIn(500,function(){jQuery(this).show(500,function(){a.app.loading=!1})}))}try{b.listeners.section_selected(c.section,b)}catch(h){b.show_error("section_selected",h)}if(!b.rendered){try{b.listeners.rendered(b)}catch(h){b.show_error("rendered",h)}b.rendered=!0}}),this.get("",function(a){var c=new RegExp("(https?|ftp):","g");a.app.home.replace(c,"")!==location.href.replace(c,"")?window.location=location.href:b.default_module&&jQuery("#"+b.default_module).length>0&&a.app.runRoute("get","#"+b.default_module)})})},this.__=b,b._init(),this.__};
Application.define("core.json_editor",function(a){a=jQuery.extend(!0,{container:!1,instance:"editor",options:{},json:null,callback:function(a){return a},actions:{save:function(){return null},validate:function(){return null}}},"object"==typeof a?a:{});var b="object"==typeof a.container?a.container.get(0):document.getElementById(a.container.replace("#","")),c=function(a,b){var c=this;c._args="object"==typeof b.options?b.options:{},c.options=jQuery.extend(!0,{change:function(){return null},history:!0,mode:"editor",search:!0},"object"==typeof b.options?b.options:{}),c.__=new JSONEditor(a,c.options,"undefined"!=typeof c._args.json?c._args.json:null),c.schema=!1,c.save=function(){if(c.schema&&!c.validate())return!1;try{c._args.actions.save(schema.get())}catch(a){return!1}return!0},c.set=function(b,d){return d=jQuery.extend(!0,{name:null,schema:null},"object"==typeof d?d:{}),c.schema="object"==typeof d.schema&&null!=d.schema?d.schema:!1,c.schema?jQuery("button.jsoneditor-validate-object",a).show():jQuery("button.jsoneditor-validate-object",a).hide(),"string"==typeof d.name?c.__.set(b,d.name):c.__.set(b)},c.get=function(){return console.log("JSON EDITOR: GET"),c.__.get()},c.validate=function(){if("object"!=typeof c.schema||"undefined"==typeof validate)return!0;c.last_validation=validate(c.get(),c.schema);try{c._args.actions.validate(c.last_validation)}catch(a){return!1}return c.last_validation.valid},c._update_menu=function(){var b=jQuery("button.jsoneditor-save-object",a),d=document.createElement("button");d.title="Validate",d.className="jsoneditor-menu jsoneditor-validate-object",d.appendChild(document.createTextNode("Validate")),d.style.display="none",d.onclick=function(){c.validate()},b.click(function(){c.save()}).after(d)},jQuery.each(c.__,function(a,b){"undefined"==typeof c[a]&&(c[a]=b)}),c._update_menu()};async.parallel({js:function(a){ud.load.js({JSONEditor:"//ud-cdn.com/js/ud.json.editor/latest/ud.json.editor.js",validate:"//ud-cdn.com/js/ud.json.validate/1.0/ud.json.validate.js"},function(){a(null,!0)})},css:function(a){window._flags="undefined"!=typeof window._flags?window._flags:{},"undefined"!=typeof window._flags.json_editor_css&&window._flags.json_editor_css||(ud.load.css("//ud-cdn.com/js/ud.json.editor/latest/assets/ud.json.editor.css"),window._flags.json_editor_css=!0),a(null,!0)}},function(){if("function"==typeof a.callback){var d=null;switch(a.instance){case"editor":d=new c(b,a);break;case"formatter":d=new JSONformatter(b,a.options)}a.callback(d)}})});
Application.define("core.socket",function(a,b){return a=jQuery.extend(!0,{port:443,url:!1,resource:"websocket.api/v1.5",secure:!0,"account-id":!1,"access-key":!1},"object"==typeof a?a:{}),"undefined"==typeof b&&(b=function(){return null}),new ud.socket.connect(a.url,a,function(a,c){if(a)return console.error("Socket Callback","Connection Failed",a),null;try{b(c)}catch(d){console.error("Socket Callback","Custom callback failed",d)}})});
Application.define("core.view_model",function(a){a=jQuery.extend(!0,{_required:{},scope:!1,model:{},view:!1,container:!1,args:{l10n:{remove_confirmation:"Are you sure you want to remove it?"}},actions:{update:function(){return null},pre_apply:function(a,b){b(null,!0)},init:function(a,b){b(null,!0)},add_data:function(){return null},remove_data:function(){return null},callback:!1},callback:function(a,b){var c=this;return"object"==typeof c.actions&&"function"==typeof c.actions.callback?c.actions.callback(a,b):(a&&a instanceof Error&&console.error(a.message,b),b)}},"object"==typeof a?a:{});var b=a.container&&"object"!=typeof a.container?jQuery(a.container):a.container;if(!b||"undefined"==typeof b.length||!b.length>0)return a.callback(new Error("ko.view_model. Container is missing, or incorrect."),!1);a.view&&(/^((https?|ftp):)?\/\/([\-A-Z0-9.]+)(\/[\-A-Z0-9+&@#\/%=~_|!:,.;]*)?(\?[A-Z0-9+&@#\/%=~_|!:,.;]*)?/i.test(a.view)&&jQuery.ajax({url:a.view,async:!1,dataType:"html",complete:function(b){a.view=b.responseText}}),b.html(a.view));var c=b.html();b.html("").addClass("ud_view_model ud_ui_loading").append('<div class="ud_ui_spinner"></div>').append('<div class="ud_ui_prepared_interface"></div>').find(".ud_ui_prepared_interface").html(c);var d=function(a,b){var c=this;c._applied=!1,c._args=a,c.scope=a.scope,c.core=a.scope.core,c.socket="object"==typeof a.scope.socket?a.scope.socket:!1,c.container=b,c.add_data=function(a,b,d,e){"function"==typeof b?a.push(new b):"function"==typeof d[b]&&a.push(new d[b]);try{c._args.actions.add_data(c,e,a,b)}catch(f){c._args.callback(f,d)}},c.alert=function(a,b){var d="alert-success";if("undefined"!=typeof b)switch(b){case"error":d="alert-error";break;case"warning":d=""}var e='<div class="container alert fade in '+d+'"><button type="button" class="close" data-dismiss="alert">&times;</button>'+a+"</div>";c.container.prepend(e)},c.remove_data=function(a,b,d){confirm(c.l10n.remove_confirmation)&&a.remove(b);try{c._args.actions.remove_data(c,d,a,b)}catch(e){c._args.callback(e,c)}},c.apply=function(a){var b=this,c=b.container.get(0);return b._applied?b._args.callback(null,b,c):(async.series({pre_apply:function(a){try{b._args.actions.pre_apply(b,a)}catch(c){a("Error occured on VM pre_apply event")}},apply_bindings:function(a){ko.applyBindings(b,b.container.get(0)),b._applied=!0,a(null,!0)},init:function(a){b.container.removeClass("ud_ui_loading").addClass("ud_ui_applied");try{b._args.actions.init(b,a)}catch(c){a("Error occured on VM init event")}}},function(){return b.update("undefined"!=typeof a?a:[]),b._args.callback(null,b)}),null)},c.update=function(a){var b=this;try{b._args.actions.update(b,"undefined"!=typeof a?a:[])}catch(c){b._args.callback(c,b)}};var d={},a={};("object"==typeof c._args.model?c._args.model:{})&&jQuery.each(c._args.model,function(b,e){"undefined"==typeof c._args[b]?d[b]=e:a[b]=e}),c._args=jQuery.extend(!0,c._args,a),c=jQuery.extend(!0,c,"object"==typeof c._args.args?c._args.args:a,d)};return new d(a,b)});
var ud={load:function(a){function b(b,c){var d,e=a.createElement(b);for(d in c)c.hasOwnProperty(d)&&e.setAttribute(d,c[d]);return e}function c(a){var b,c,d=j[a];d&&(b=d.callback,c=d.urls,c.shift(),k=0,c.length||(b&&b.call(d.context,d.obj),j[a]=null,l[a].length&&e(a)))}function d(){var b=navigator.userAgent;h={async:a.createElement("script").async===!0},(h.webkit=/AppleWebKit\//.test(b))||(h.ie=/MSIE/.test(b))||(h.opera=/Opera/.test(b))||(h.gecko=/Gecko\//.test(b))||(h.unknown=!0)}function e(e,k,m,n,o){var p,q,r,s,t,u,v=function(){c(e)},w="css"===e,x=[];if(h||d(),k)if(k="string"==typeof k?[k]:k.concat(),w||h.async||h.gecko||h.opera)l[e].push({urls:k,callback:m,obj:n,context:o});else for(p=0,q=k.length;q>p;++p)l[e].push({urls:[k[p]],callback:p===q-1?m:null,obj:n,context:o});if(!j[e]&&(s=j[e]=l[e].shift())){for(i||(i=a.head||a.getElementsByTagName("head")[0]),t=s.urls,p=0,q=t.length;q>p;++p)u=t[p],w?r=h.gecko?b("style"):b("link",{href:u,rel:"stylesheet"}):(r=b("script",{src:u}),r.async=!1),r.className="lazyload",r.setAttribute("charset","utf-8"),h.ie&&!w?r.onreadystatechange=function(){/loaded|complete/.test(r.readyState)&&(r.onreadystatechange=null,v())}:w&&(h.gecko||h.webkit)?h.webkit?(s.urls[p]=r.href,g()):(r.innerHTML='@import "'+u+'";',f(r)):r.onload=r.onerror=v,x.push(r);for(p=0,q=x.length;q>p;++p)i.appendChild(x[p])}}function f(a){var b;try{b=!!a.sheet.cssRules}catch(d){return k+=1,void(200>k?setTimeout(function(){f(a)},50):b&&c("css"))}c("css")}function g(){var a,b=j.css;if(b){for(a=m.length;--a>=0;)if(m[a].href===b.urls[0]){c("css");break}k+=1,b&&(200>k?setTimeout(g,50):c("css"))}}var h,i,j={},k=0,l={css:[],js:[]},m=a.styleSheets;return{css:function(a,b,c,d){e("css",a,b,c,d)},js:function(a,b,c,d){var f=[];for(name in a)"undefined"==typeof window[name]&&f.push(a[name]);e("js",f,b,c,d)}}}(this.document)};
jQuery.extend(!0,ud,{scope:"ud",developer_mode:!0,console_options:{show_log:!0},strings:{},timers:{},utils:{type_fix:function(a,b){jQuery.extend(b,{"null":!0});try{var c=function(a){switch(typeof a){case"string":switch(a){case"false":a=!1;break;case"true":a=!0;break;case"":a=b.nullify?null:a}break;case"number":a=parseFloat(a);break;case"object":a=d(a)}return a},d=function(a){if("object"!=typeof a)return c(a);for(key in a)a[key]=c(a[key]);return a}}catch(e){}return d(a)}},warning:function(){return"function"==typeof console.warn?(console.warn(arguments[0]),arguments[0]):void 0},log:function(a,b,c){var d=this;return a instanceof Error&&"object"==typeof console.error?(console.error(a.message,{Error:a,Stack:"string"==typeof a.stack?a.stack.split("\n"):null}),a):window.console&&(c||d.developer_mode&&window.console)&&("boolean"!=typeof a||a)?"Explorer"==d.browser_detect("browser")?!1:(d.log.console={log:function(a){switch(a.type){case"info":return d.log.console.info(a);case"error":return d.log.console.error(a);case"dir":return d.log.console.error(a);case"warn":return d.log.console.warn(a);default:return d.console_options&&d.console_options.show_log?console.log.apply(console,a.items):!1}},info:function(a){return console.info.apply(console,a.items)},error:function(a){return console.error.apply(console,a.items)},dir:function(a){return console.dir.apply(console,a.items)},warn:function(a){return console.warn.apply(console,a.items)}},d.log.console.log({items:[d.scope+"::"].concat(jQuery.makeArray("string"==typeof a&&"object"==typeof b?arguments:[a])),type:"string"==typeof b?b:"log"})):!1},get_service:function(a,b,c){"use strict";return this.log(this.scope+".get_service()",arguments),"function"!=typeof b?!1:void this.ajax("get_api_service",{service:a,args:c},function(a){b(a)})},ajax:function(a,b,c,d){"use strict";if(!ajaxurl)return!1;var e=this,f=function(a){var b={};try{if(b.response_text=jQuery.parseJSON(a.responseText),"object"!=typeof b.response_text||""===a.responseText)throw new Error(e.strings.ajax_response_empty);if(500===a.status)throw new Error(e.strings.internal_server_error)}catch(d){d.message="AJAX Error: "+(d.message?d.message:"Unknown."),b.response_text={success:!1,message:d.message}}b=jQuery.extend(!0,{success:!1,status:a.status},b.response_text),"timeout"===a.statusText&&(b.status=408,b.response_text=b.response_text?b.response_text:e.strings.server_timeout),c(b)};return jQuery.ajax(d=jQuery.extend(!0,{url:ajaxurl+"?action="+e.scope+"_"+a,data:jQuery.extend(!0,{args:b},"object"==typeof data?data:{}),dataType:"json",type:"POST",async:!0,timeout:1e3*(("object"==typeof e.server?e.server.max_execution_time:30)-10),beforeSend:function(a){jQuery(document).trigger(e.scope+".ajax.beforeSend"),a.overrideMimeType("application/json; charset=utf-8")},complete:function(a){f(a)},error:function(){}},"object"==typeof d?d:{}))},apply_filter:function(a,b){return"undefined"==typeof b||"undefined"==typeof a||"string"!=typeof a?b:"undefined"==typeof jQuery?b:"undefined"==typeof window.__ud_filters||"undefined"==typeof window.__ud_filters[a]?b:(jQuery.each(window.__ud_filters[a],function(a,c){if("function"==typeof c)b=c(b);else if("object"==typeof c){if("object"!=typeof b)return!1;b=jQuery.extend(!0,b,c)}}),b)},add_filter:function(a,b){"undefined"!=typeof b&&"undefined"!=typeof a&&"string"==typeof a&&("undefined"==typeof window.__ud_filters&&(window.__ud_filters={}),"undefined"==typeof window.__ud_filters[a]&&(window.__ud_filters[a]=[]),window.__ud_filters[a].push(b))},validate_url:function(a){var b=new RegExp("^(http|https|ftp)://([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&amp;%$-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]).(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0).(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0).(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9-]+.)*[a-zA-Z0-9-]+.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(/($|[a-zA-Z0-9.,?'\\+&amp;%$#=~_-]+))*$");return b.test(a)},create_slug:function(a){return"string"!=typeof a?!1:(a=a.replace(/[^a-zA-Z0-9_\s]/g,""),a=a.toLowerCase(),a=a.replace(/\s/g,"_"))},browser_detect:function(a){return"undefined"==typeof a?!1:("undefined"==typeof window._ud_browser_detect&&(window._ud_browser_detect={init:function(){this.browser=this.searchString(this.dataBrowser)||"An unknown browser",this.version=this.searchVersion(navigator.userAgent)||this.searchVersion(navigator.appVersion)||"an unknown version",this.OS=this.searchString(this.dataOS)||"an unknown OS"},searchString:function(a){for(var b=0;b<a.length;b++){var c=a[b].string,d=a[b].prop;if(this.versionSearchString=a[b].versionSearch||a[b].identity,c){if(-1!=c.indexOf(a[b].subString))return a[b].identity}else if(d)return a[b].identity}},searchVersion:function(a){var b=a.indexOf(this.versionSearchString);if(-1!=b)return parseFloat(a.substring(b+this.versionSearchString.length+1))},dataBrowser:[{string:navigator.userAgent,subString:"Chrome",identity:"Chrome"},{string:navigator.userAgent,subString:"OmniWeb",versionSearch:"OmniWeb/",identity:"OmniWeb"},{string:navigator.vendor,subString:"Apple",identity:"Safari",versionSearch:"Version"},{prop:window.opera,identity:"Opera",versionSearch:"Version"},{string:navigator.vendor,subString:"iCab",identity:"iCab"},{string:navigator.vendor,subString:"KDE",identity:"Konqueror"},{string:navigator.userAgent,subString:"Firefox",identity:"Firefox"},{string:navigator.vendor,subString:"Camino",identity:"Camino"},{string:navigator.userAgent,subString:"Netscape",identity:"Netscape"},{string:navigator.userAgent,subString:"MSIE",identity:"Explorer",versionSearch:"MSIE"},{string:navigator.userAgent,subString:"Gecko",identity:"Mozilla",versionSearch:"rv"},{string:navigator.userAgent,subString:"Mozilla",identity:"Netscape",versionSearch:"Mozilla"}],dataOS:[{string:navigator.platform,subString:"Win",identity:"Windows"},{string:navigator.platform,subString:"Mac",identity:"Mac"},{string:navigator.userAgent,subString:"iPhone",identity:"iPhone/iPod"},{string:navigator.platform,subString:"Linux",identity:"Linux"}]},window._ud_browser_detect.init()),"undefined"!=typeof window._ud_browser_detect[a]?window._ud_browser_detect[a]:!1)}}),Array.prototype.indexOf||(Array.prototype.indexOf=function(a,b){for(var c=b||0,d=this.length;d>c;c++)if(this[c]===a)return c;return-1});
define("udx.spa",["module","require","exports","knockout","knockout.mapping","knockout.localStorage","pace","udx.utility","udx.utility.imagesloaded"],function(a,b){function c(a){if(Object.extend(a||{},{name:void 0,debug:void 0,api:window.location.href+"api",bodyClass:"spa-enabled"}),a.node||(a.node=document.body),"function"!=typeof a.node.getAttribute)return console.error("udx.spa","Target not specifed."),this;a.node.getAttribute("data-version"),a.node.getAttribute("data-ajax"),a.node.getAttribute("data-home"),a.node.getAttribute("data-debug")||!1;return this}console.debug("udx.spa","spaReady");{var d=b("pace");b("knockout"),b("knockout.mapping"),b("udx.utility"),b("udx.utility.imagesloaded")}return d.options={document:!0,ajax:!0,eventLag:!1,restartOnPushState:!0,restartOnRequestAfter:!0},d.start(),Object.defineProperties(c.prototype,{configure:{value:function(){},enumerable:!0,configurable:!0,writable:!0},ViewModel:{value:function(){console.debug("udx.spa","ViewModel");var a=this;return a.schedules=b("knockout").observableArray([]),a.processes=b("knockout").observableArray([]),a.state=b("knockout").observableArray([]),"function"==typeof jQuery&&jQuery("li.menu-item > a").click(c.bindNavigation),this},enumerable:!0,configurable:!0,writable:!0},bindNavigation:{value:function(a){return a.preventDefault(),a.target.pathname&&"/"!==a.target.pathname?void jQuery("main").load(a.target.href+" main"):null},enumerable:!0,configurable:!0,writable:!0},applyBindings:{value:function(){},enumerable:!0,configurable:!0,writable:!0}}),Object.defineProperties(c,{create:{value:function(a){return this instanceof Node&&this.parentElement&&(console.log("SPA is instance of Node"),a=a||{}),new c(a||{})},enumerable:!0,configurable:!0,writable:!0}}),c});
define("udx.utility.activity",["udx.utility","async","jquery"],function(){function a(c){return console.debug("udx.utility.activity","new Activity"),this.settings=b.defaults(c,{ajax:null,id:null,activity:"",args:{},poll:15e3,timeout:5e3,protocol:"ajax",format:"json",headers:{},onStart:function(){},onCreate:function(){},onPoll:function(){},onComplete:function(){},onError:function(){},onTimeout:function(){}}),this._timers={create:(new Date).getTime(),poll:void 0},a.instances[this.settings.id]=this}console.debug("udx.utility.activity","loaded");var b=(require("async").auto,require("async").series,require("udx.utility"));return Object.defineProperties(a.prototype,{create:{value:function(){console.debug("udx.utility.activity","createActivity");var a=this;return jQuery.ajax({url:a.settings.ajax,timeout:a.settings.timeout,async:!0,cache:!1,type:"GET",contentType:"application/x-www-form-urlencoded; charset=UTF-8",dataType:a.settings.format||"json",headers:a.settings.headers||{},data:b.extend({id:a.settings.id,activity:a.settings.type,event:"create"},a.settings.args),error:function c(c){console.debug("udx.utility.activity","create","::error",c),a.settings.onStart(new Error("Activity Start Error: "+c))},complete:function(b,c){console.debug("udx.utility.activity","create","::complete",c),b.responseJSON&&a.settings.onCreate(null,b.responseJSON),a._timers.poll=window.setInterval(a.poll.bind(a),a.settings.poll)}}),this},enumerable:!0,configurable:!0,writable:!0},start:{value:function(){console.debug("udx.utility.activity","startActivity");var a=this;return jQuery.ajax({url:a.settings.ajax,timeout:a.settings.timeout,async:!0,cache:!1,type:"GET",contentType:"application/x-www-form-urlencoded; charset=UTF-8",dataType:a.settings.format||"json",headers:a.settings.headers||{},data:b.extend({id:a.settings.id,activity:a.settings.type,event:"start"},a.settings.args),error:function c(c){console.debug("udx.utility.activity","start","::error",c),a.settings.onStart(new Error("Activity Start Error: "+c))},complete:function(b,c){console.debug("udx.utility.activity","start","complete",c),b.responseJSON&&a.settings.onStart(null,b.responseJSON),a._timers.poll=window.setInterval(a.poll.bind(a),a.settings.poll)}}),this},enumerable:!0,configurable:!0,writable:!0},poll:{value:function(){return this},enumerable:!0,configurable:!0,writable:!0},recover:{value:function(){return console.debug("udx.utility.activity","recoverActivity"),this},enumerable:!0,configurable:!0,writable:!0}}),Object.defineProperties(a,{create:{value:function(){return new a(arguments[0])},enumerable:!0,configurable:!0,writable:!0},instances:{value:{},enumerable:!1,configurable:!0,writable:!0}}),a});
define("udx.utility.bus",function(){function a(){return console.debug("udx.utility.bus","ServiceBus"),this}return console.debug("udx.utility.bus","loaded"),Object.defineProperties(a.prototype,{on:{value:function(){return console.debug("udx.utility.bus","on()"),this},enumerable:!0,configurable:!0,writable:!0},off:{value:function(){return console.debug("udx.utility.bus","off()"),this},enumerable:!0,configurable:!0,writable:!0},emit:{value:function(){return console.debug("udx.utility.bus","emit()"),this},enumerable:!0,configurable:!0,writable:!0}}),Object.defineProperties(a.create,{create:{value:function(b,c){return new a(b,c)},enumerable:!0,configurable:!0,writable:!0}}),a});
define("udx.utility.device",function(){function a(){return console.debug("udx.utility.device","getDeviceState()"),a.indicator?window.getComputedStyle(document.querySelector(".udx-state-indicator"),":before").getPropertyValue("content"):"desktop"}return console.debug("udx.utility.device","loaded"),a.indicator=document.createElement("div"),a.indicator.className="udx-state-indicator",document.body.appendChild(a.indicator),a});
define("udx.utility.facebook.like",function(a,b,c){console.log(c.id,"loaded");var d,e=document.getElementsByTagName("script")[0];document.getElementById("facebook-jssdk")||(d=document.createElement("script"),d.id="facebook-jssdk",d.src="//connect.facebook.net/en_US/all.js#xfbml=1&appId=373515126019844",e.parentNode.insertBefore(d,e))});
define("udx.utility.job",function(a,b,c){console.log(c.id,"loaded")});
define("udx.utility",function(){return{isVisible:function(a,b,c,d,e,f,g){"string"==typeof a&&(a=document.getElementById(a));var h=a.parentNode,i=2;return this.elementInDocument(a)?9===h.nodeType?!0:"0"===this.getStyle(a,"opacity")||"none"===this.getStyle(a,"display")||"hidden"===this.getStyle(a,"visibility")?!1:(("undefined"==typeof b||"undefined"==typeof c||"undefined"==typeof d||"undefined"==typeof e||"undefined"==typeof f||"undefined"==typeof g)&&(b=a.offsetTop,e=a.offsetLeft,d=b+a.offsetHeight,c=e+a.offsetWidth,f=a.offsetWidth,g=a.offsetHeight),h?"hidden"!==this.getStyle(h,"overflow")&&"scroll"!==this.getStyle(h,"overflow")||!(e+i>h.offsetWidth+h.scrollLeft||e+f-i<h.scrollLeft||b+i>h.offsetHeight+h.scrollTop||b+g-i<h.scrollTop)?(a.offsetParent===h&&(e+=h.offsetLeft,b+=h.offsetTop),this.isVisible(h,b,c,d,e,f,g)):!1:!0):!1},getStyle:function(a,b){return window.getComputedStyle?document.defaultView.getComputedStyle(a,null)[b]:a.currentStyle?a.currentStyle[b]:void 0},elementInDocument:function(a){for(;a=a.parentNode;)if(a==document)return!0;return!1},remote_get:function(){var a=makeHttpObject(),b=arguments[0],c=arguments[1];a.open("GET",b,!0),a.send(null),a.onreadystatechange=function(){4==a.readyState&&(200==a.status?c(null,a.responseText):failure&&c(new Error(a.statusText)))}},remote_post:function(){var a=makeHttpObject(),b=arguments[0],c=arguments[1];a.open("POST",b,!0),a.send(null),a.onreadystatechange=function(){4==a.readyState&&(200==a.status?c(null,a.responseText):failure&&c(new Error(a.statusText)))}},extend:function(a,b){for(var c in b)b[c]&&b[c].constructor&&b[c].constructor===Object?(a[c]=a[c]||{},arguments.callee(a[c],b[c])):a[c]=b[c];return a},defaults:function(a,b){return this.extend(b||{},a||{})},create_slug:function(a){return a.replace(/[^a-zA-Z0-9_\s]/g,"").toLowerCase().replace(/\s/g,"_")}}});
define("udx.utility.loader",function(a,b,c){console.log(c.id,"loaded")});
define("udx.utility.md5",function(a,b,c){return console.log(c.id,"loaded"),function(a){function b(a,b){var c=a[0],h=a[1],i=a[2],j=a[3];c=d(c,h,i,j,b[0],7,-680876936),j=d(j,c,h,i,b[1],12,-389564586),i=d(i,j,c,h,b[2],17,606105819),h=d(h,i,j,c,b[3],22,-1044525330),c=d(c,h,i,j,b[4],7,-176418897),j=d(j,c,h,i,b[5],12,1200080426),i=d(i,j,c,h,b[6],17,-1473231341),h=d(h,i,j,c,b[7],22,-45705983),c=d(c,h,i,j,b[8],7,1770035416),j=d(j,c,h,i,b[9],12,-1958414417),i=d(i,j,c,h,b[10],17,-42063),h=d(h,i,j,c,b[11],22,-1990404162),c=d(c,h,i,j,b[12],7,1804603682),j=d(j,c,h,i,b[13],12,-40341101),i=d(i,j,c,h,b[14],17,-1502002290),h=d(h,i,j,c,b[15],22,1236535329),c=e(c,h,i,j,b[1],5,-165796510),j=e(j,c,h,i,b[6],9,-1069501632),i=e(i,j,c,h,b[11],14,643717713),h=e(h,i,j,c,b[0],20,-373897302),c=e(c,h,i,j,b[5],5,-701558691),j=e(j,c,h,i,b[10],9,38016083),i=e(i,j,c,h,b[15],14,-660478335),h=e(h,i,j,c,b[4],20,-405537848),c=e(c,h,i,j,b[9],5,568446438),j=e(j,c,h,i,b[14],9,-1019803690),i=e(i,j,c,h,b[3],14,-187363961),h=e(h,i,j,c,b[8],20,1163531501),c=e(c,h,i,j,b[13],5,-1444681467),j=e(j,c,h,i,b[2],9,-51403784),i=e(i,j,c,h,b[7],14,1735328473),h=e(h,i,j,c,b[12],20,-1926607734),c=f(c,h,i,j,b[5],4,-378558),j=f(j,c,h,i,b[8],11,-2022574463),i=f(i,j,c,h,b[11],16,1839030562),h=f(h,i,j,c,b[14],23,-35309556),c=f(c,h,i,j,b[1],4,-1530992060),j=f(j,c,h,i,b[4],11,1272893353),i=f(i,j,c,h,b[7],16,-155497632),h=f(h,i,j,c,b[10],23,-1094730640),c=f(c,h,i,j,b[13],4,681279174),j=f(j,c,h,i,b[0],11,-358537222),i=f(i,j,c,h,b[3],16,-722521979),h=f(h,i,j,c,b[6],23,76029189),c=f(c,h,i,j,b[9],4,-640364487),j=f(j,c,h,i,b[12],11,-421815835),i=f(i,j,c,h,b[15],16,530742520),h=f(h,i,j,c,b[2],23,-995338651),c=g(c,h,i,j,b[0],6,-198630844),j=g(j,c,h,i,b[7],10,1126891415),i=g(i,j,c,h,b[14],15,-1416354905),h=g(h,i,j,c,b[5],21,-57434055),c=g(c,h,i,j,b[12],6,1700485571),j=g(j,c,h,i,b[3],10,-1894986606),i=g(i,j,c,h,b[10],15,-1051523),h=g(h,i,j,c,b[1],21,-2054922799),c=g(c,h,i,j,b[8],6,1873313359),j=g(j,c,h,i,b[15],10,-30611744),i=g(i,j,c,h,b[6],15,-1560198380),h=g(h,i,j,c,b[13],21,1309151649),c=g(c,h,i,j,b[4],6,-145523070),j=g(j,c,h,i,b[11],10,-1120210379),i=g(i,j,c,h,b[2],15,718787259),h=g(h,i,j,c,b[9],21,-343485551),a[0]=m(c,a[0]),a[1]=m(h,a[1]),a[2]=m(i,a[2]),a[3]=m(j,a[3])}function c(a,b,c,d,e,f){return b=m(m(b,a),m(d,f)),m(b<<e|b>>>32-e,c)}function d(a,b,d,e,f,g,h){return c(b&d|~b&e,a,b,f,g,h)}function e(a,b,d,e,f,g,h){return c(b&e|d&~e,a,b,f,g,h)}function f(a,b,d,e,f,g,h){return c(b^d^e,a,b,f,g,h)}function g(a,b,d,e,f,g,h){return c(d^(b|~e),a,b,f,g,h)}function h(a){txt="";var c,d=a.length,e=[1732584193,-271733879,-1732584194,271733878];for(c=64;c<=a.length;c+=64)b(e,i(a.substring(c-64,c)));a=a.substring(c-64);var f=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];for(c=0;c<a.length;c++)f[c>>2]|=a.charCodeAt(c)<<(c%4<<3);if(f[c>>2]|=128<<(c%4<<3),c>55)for(b(e,f),c=0;16>c;c++)f[c]=0;return f[14]=8*d,b(e,f),e}function i(a){var b,c=[];for(b=0;64>b;b+=4)c[b>>2]=a.charCodeAt(b)+(a.charCodeAt(b+1)<<8)+(a.charCodeAt(b+2)<<16)+(a.charCodeAt(b+3)<<24);return c}function j(a){for(var b="",c=0;4>c;c++)b+=n[a>>8*c+4&15]+n[a>>8*c&15];return b}function k(a){for(var b=0;b<a.length;b++)a[b]=j(a[b]);return a.join("")}function l(a){return k(h(a))}function m(a,b){return a+b&4294967295}var n="0123456789abcdef".split("");return l(a)}});
define("udx.utility.process",["udx.utility","async","jquery"],function(){function a(c){return console.debug("udx.utility.process","new Process"),this.settings=b.defaults(c,{id:null,ajax:null,args:{},poll:5e3,timeout:1e4,protocol:"ajax",format:"json",headers:{},onStart:function(){},onComplete:function(){},onError:function(){},onTimeout:function(){}}),this._start=(new Date).getTime(),a.instances[c.id]=this}console.debug("udx.utility.process","loaded");var b=(require("async").auto,require("async").series,require("udx.utility"));return Object.defineProperties(a.prototype,{start:{value:function(){console.debug("udx.utility.process","startProcess");var a=this;return jQuery.ajax({url:a.settings.ajax,timeout:a.settings.timeout,async:!0,cache:!1,type:"GET",contentType:"application/x-www-form-urlencoded; charset=UTF-8",dataType:a.settings.format||"json",headers:a.settings.headers||{},data:b.extend({event:"start-process",id:a.settings.id,type:a.settings.type},a.settings.args),beforeSend:function(){},error:function c(c){console.debug("udx.utility.process","error",arguments),a.settings.onStart(new Error("Process Start Error: "+c))},complete:function(b,c){console.debug("udx.utility.process","complete",c),b.responseJSON&&a.settings.onStart(null,b.responseJSON)},success:function(a){console.debug("udx.utility.process","success"),a.ok}}),this},enumerable:!0,configurable:!0,writable:!0},poll:{value:function(){return console.debug("udx.utility.process","pollProcess"),this},enumerable:!0,configurable:!0,writable:!0},recover:{value:function(){return console.debug("udx.utility.process","recoverProcess"),this},enumerable:!0,configurable:!0,writable:!0}}),Object.defineProperties(a,{create:{value:function(){return new a(arguments[0])},enumerable:!0,configurable:!0,writable:!0},instances:{value:{},enumerable:!1,configurable:!0,writable:!0}}),a});
});
