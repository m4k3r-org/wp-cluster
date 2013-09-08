var Emitter = require('emitter'),
    Elem = require('elem');

// Event /////////////////////////////////////////////////////////////////////////////

var Event = {
    emitter: new Emitter(),
    normalize: function(event) {
        // normalize 'inspired' from Secrets of the Javascript Ninja by John Resig 
        // Reference http://www.quirksmode.org/dom/events/ 
        function returnTrue() { return true; } 
        function returnFalse() { return false; }

        if (!event || !event.stopPropagation) { 
            // Clone the old object so that we can modify the values 
            event = Event.clone(event || window.event);

            // The event occurred on this element 
            if (!event.target) {
                event.target = event.srcElement || document;
            }
            // Handle which other element the event is related to 
            event.relatedTarget = event.fromElement === event.target ? event.toElement : event.fromElement;
            // Stop the default browser action 
            event.preventDefault = function () {
                event.returnValue = false; 
                event.isDefaultPrevented = returnTrue;
            }; 
            event.isDefaultPrevented = returnFalse;
            // Stop the event from bubbling 
            event.stopPropagation = function () {
                event.cancelBubble = true; 
                event.isPropagationStopped = returnTrue;
            }; 
            event.isPropagationStopped = returnFalse;
            // Stop the event from bubbling and executing other handlers 
            event.stopImmediatePropagation = function () {
                this.isImmediatePropagationStopped = returnTrue; 
                this.stopPropagation();
            }; 
            event.isImmediatePropagationStopped = returnFalse;
            // Handle mouse position 
            if (event.clientX != null) {
                var doc = document.documentElement, 
                    body = document.body;

                event.pageX = event.clientX + (doc && 
                        doc.scrollLeft || body && 
                        body.scrollLeft || 0) - (doc && 
                        doc.clientLeft || body && 
                        body.clientLeft || 0);

                event.pageY = event.clientY + (doc && 
                        doc.scrollTop || body && 
                        body.scrollTop || 0) - (doc && 
                        doc.clientTop || body && 
                        body.clientTop || 0);
            }
            // Handle key presses 
            event.which = event.charCode || event.keyCode;
            // Fix button for mouse clicks: // 0 == left; 1 == middle; 2 == right
            if (event.button != null) {
                event.keyCode;
                event.button = (event.button & 1 ? 0 : (event.button & 4 ? 1 : (event.button & 2 ? 2 : 0)));
            }
            // mouse scroll
            event.wheelDelta = event.wheelDelta || -event.Detail * 40; 
        }    

        return Event.extend(event,Event.methods);
    },
    methods: {

    },
    extend: function(event,obj) {
        for(var o in obj) {
            if(!event[o]) event[o] = obj[o];
        }

        return event;
    },
    clone: function(event,obj) {
        obj = obj ? obj : {};

        for (var p in event) { 
            obj[p] = event[p];
        }
        return obj;
    },
    bind: function(el,ev,fn,cap){
        if(el.addEventListener){
            el.addEventListener(ev, fn, !!cap);
        } else if (elm.attachEvent){
            el.attachEvent('on' + ev, fn);
        }  else el['on' + ev] = fn;

        return el;
    },
    unbind: function(el,ev,fn){
        if(el.removeEventListener){
            el.removeEventListener(ev, fn, false);
        } else if (el.detachEvent){
            el.detachEvent('on' + ev, fn);
        } else el['on' + ev] = null;

        return el;
    },
    add: function(el,ev,fn){
        ev = ev.split(' ');
        
        var i = ev.length;
        
        while(1 < i--) Event.add(el,ev[i],fn);
        
        ev = ev[0];

        var data = Elem(el).data();

        if(!data.__emitter__) {
            data.__emitter__ = new Emitter();
        }    
        
        Event.bind(el,ev,onEvent);

        data.__emitter__.on(ev,fn);

        return data.__emitter__;
    }, 
    remove: function(el,ev,fn){
        ev = ev.split(' ');

        var i = ev.length;
        
        while(1 < i--) Event.remove(el,ev[i],fn);
        
        ev = ev[0];

        var data = Elem(el).data();

        if(data.__emitter__) {
            data.__emitter__.off(ev,fn);
            if(!data.__emitter__.hasListeners(ev))
                Event.unbind(el,ev,onEvent);
        }

        return data.__emitter__; 
    }, 
    delegate: function(el,ev,fn){

        Event.bind(document,ev,onDelegate,true);

        var guid = Elem(el).guid;

        Event.emitter.on(ev+'>'+guid,fn);

        return el;
    },
    undelegate: function(el,ev,fn){
        var guid = Elem(el).guid;

        if(guid) {
            Event.emitter.off(ev+'>'+guid,fn);
        }

        return el;
    }
}

function onEvent(event) {
    event = Event.normalize(event);

    var data = Elem(event.target).data();

    if(!data.__emitter__) throw "event has no emitter";

    data.__emitter__.emit(event.type,event);
} 

function onDelegate(event) {
    var guid = Elem(event.target).guid;
    
    Event.emitter.emit(event.type+'>'+guid,event);
}

module.exports = Event; 
