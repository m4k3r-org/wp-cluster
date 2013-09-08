/* require shims */
require('./shims/querySelector.js');

function query(method,root,selector){
    if(!root) return document[method](selector);

    var elem = root(),
        id = elem.id;

        elem.id = 'guid' + root.guid;

    try {
        selector = '#' + elem.id + ' ' + selector;
        return elem[method](selector);
    } catch (e) {
        throw e;
    } finally {
        elem.id = id;
    }    
}

var Query = {
    one: function(root,selector){
        var elem = query('querySelector',root,selector);
        
        if(!elem) return;

        var ret = wrapElement(elem);

        return ret;
    },
    all: function(root,selector){
        var elems = query('querySelectorAll',root,selector);
        
        if(!elems.length) return;

        var ret = [];
    
        for(var i = 0, l = elems.length; i < l; i++ )
            ret[ret.length] = wrapElement(elems.item(i));

        return ret;
    }
};

var Data = {
    store: {},
    guid: 'data' + (new Date().getTime()),
    guidCounter: 1
}; 

function prepare(args){
    var elem, selector, i = 0, 
        args = Array.prototype.slice.call(args);

    if(typeof args[0] === 'string' && args[1] === undefined) selector = args[i++];
    else {
        if(typeof args[i] === 'string') elem = Query.one(undefined,args[i++]);
        else elem = wrapElement(args[i++]);

        if(typeof args[i] === 'string') selector = args[i++];
    }

    if(!elem && !selector) throw TypeError("selector <string> missing");

    return [elem,selector];
}

function Elem(){
    /* get element by selector */
    if(!(this instanceof Elem)){
        var args = prepare(arguments);

        if(!args[1]) return args[0];

        return Query.one.apply(null,args);
    }
    /* create new element */
    var tagName = arguments[0];

    if(!tagName) tagName = 'div';

    return wrapElement(document.createElement(tagName));
}

Elem.all = function(){
    var args = prepare(arguments);
    
    return Query.all.apply(null,args);
}          

function proxy(context,handler){
    var curry = [].slice.call(arguments,2);

    return function(){
        var args = [].slice.call(arguments).concat(curry);

        return handler.apply(context,args);
    }
}

function wrapElement(elem){

    if(!elem) return;

    /* already wrapped */
    if(typeof elem === 'function' && elem.name === 'element')
        return elem;

    var data = getData(elem),
        wrapped = proxy(elem,element,data);

    function element(selector){
        if(selector) 
            return Query.one(wrapped,selector);
        
        /* unwrapped */
        return elem;
    }

    /* todo: refactor these out */
    wrapped.guid = elem[Data.guid];

    wrapped.toString = function(format){
        return elem.outerHTML;
    }

    wrapped.html = function(content){
        if(content !== undefined)
            elem.innerHTML = content;
        else return elem.innerHTML;

        return this;
    }

    wrapped.text = function(content){
        if(content !== undefined)
            elem.innerText = content;
        else return elem.innerText;

        return this;        
    }

    wrapped.data = function(key,val){
        var data = getData(elem);

        if(key === undefined) return data;
        if(val !== undefined) data[key] = val;

        return data[key];
    }

    wrapped.append = function(content){

        if(Array.isArray(content)){
            content.forEach(function(c){
                wrapped.append(c);
            });

            return this;
        }

        var e = createElement(content);
   
        elem.appendChild(e);

        return this;
    }

    wrapped.prepend = function(content){
        
        if(Array.isArray(content)){
            content.forEach(function(c){
                wrapped.prepend(c);
            });

            return this;
        }

        var e = createElement(content);

        elem.insertBefore(e,elem.firstChild);

        return this;
    }

    return wrapped;
}

function createElement(content){
    var elem;

    if(typeof content === 'string'){
        elem = document.createElement('div');
        elem.innerHTML = content;
        elem = elem.firstChild;
    } else if(typeof content === 'function'){
        elem = createElement(content());
    } else elem = content;
    
    return elem;     
}

function extend(elem,obj) {
    for(var o in obj)
        if(!elem[o]) elem[o] = obj[o];
}

function getData(elem){
    var guid = elem[Data.guid];

    if(!guid){
        guid = elem[Data.guid] = Data.guidCounter++;
        Data.store[guid] = {};
    }

    return Data.store[guid];
}

function removedata(elem){
    var guid = elem[Data.guid];

    if(!guid) return;

    delete Data.store[guid];

    try {
        delete elem[Data.guid];
    } catch (e) {
        if(elem.removeAttribute){
            el.removeAttribute(Data.guid);
        }
    }
}   

module.exports = Elem;