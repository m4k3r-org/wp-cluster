jQuery(document).ready(function($) {
    var editor = {};
    $("#global-javascript").AceJavascriptEditor({
        setEditorContent: function() {
            var value = this.$elem.val();
            editor = this.editor, this.editor.getSession().setValue(value);
        },
        onInit: function() {
            this.load();
        },
        onLoaded: function() {},
        onDestroy: function() {
            console.log(this.editor);
        }
    }), $("#global-stylesheet").AceStylesheetEditor({
        setEditorContent: function() {
            var value = this.$elem.val();
            editor = this.editor, this.editor.getSession().setValue(value);
        },
        onInit: function() {
            this.load();
        },
        onLoaded: function() {},
        onDestroy: function() {
            console.log(this.editor);
        }
    }), $("#global-javascript-form").submit(function() {
        var js = editor.getValue();
        $("#global-javascript").val(js);
    }), $("#global-stylesheet-form").submit(function() {
        var css = editor.getValue();
        $("#global-stylesheet").val(css);
    });
}), function($) {
    var AceJavascriptEditor = function(config) {
        $.extend(this, config), this.$elem = this.element, this.element = this.$elem.attr("id"), 
        this.$container = this.container ? $(this.container) : this.$elem.parent(), this.contWd = this.$container.width(), 
        this.loaded = !1, this.tinymce = !!window.tinymce, this.onInit && this.onInit.call(this);
    }, AceStylesheetEditor = function(config) {
        $.extend(this, config), this.$elem = this.element, this.element = this.$elem.attr("id"), 
        this.$container = this.container ? $(this.container) : this.$elem.parent(), this.contWd = this.$container.width(), 
        this.loaded = !1, this.tinymce = !!window.tinymce, this.onInit && this.onInit.call(this);
    };
    AceJavascriptEditor.prototype = {
        load: function() {
            if (this.loaded) return !1;
            var self = this;
            this.$elem.hide(), this.insertEditor(), this.editor = ace.edit(this.aceId), this.$editor = $("#" + this.aceId), 
            this.setEditorProps(), this.setEditorContent(), this.containerResizable(), this.editor.on("change", function() {
                self.synchronize.apply(self);
            }), this.editor.resize(!0), this.loaded = !0, this.onLoaded && this.onLoaded.call(this);
        },
        insertEditor: function() {
            $('<div id="' + this.aceId + '"></div>').css({
                left: 0,
                top: 0,
                bottom: 0,
                right: 0,
                zIndex: 1
            }).insertAfter(this.$elem);
        },
        setEditorProps: function() {
            this.editor.setTheme("ace/theme/" + this.theme), this.editor.getSession().setMode("ace/mode/javascript"), 
            this.editor.getSession().setUseWrapMode(!0), this.editor.getSession().setWrapLimitRange();
        },
        setEditorContent: function() {
            this.editor.getSession().setValue(this.$elem.val());
        },
        containerResizable: function() {
            var self = this;
            this.$container.resizable({
                handles: "s"
            }).css({
                position: "relative",
                height: this.defaultHt,
                minHeight: "400px"
            }).on("resize.aceEditorResize", function() {
                self.editor.resize(!0);
            });
        },
        synchronize: function() {
            var val = this.editor.getValue();
            this.$elem.val(val), this.tinymce && tinyMCE.get(this.element) && tinyMCE.get(this.element).setContent(val);
        },
        destroy: function() {
            return this.loaded ? (this.$editor.remove(), this.editor.destroy(), this.$container.resizable("destroy").off("resize.aceEditorResize").css({
                height: ""
            }), this.$elem.show(), this.loaded = !1, void (this.onDestroy && this.onDestroy.apply(this, arguments))) : !1;
        }
    }, AceStylesheetEditor.prototype = {
        load: function() {
            if (this.loaded) return !1;
            var self = this;
            this.$elem.hide(), this.insertEditor(), this.editor = ace.edit(this.aceId), this.$editor = $("#" + this.aceId), 
            this.setEditorProps(), this.setEditorContent(), this.containerResizable(), this.editor.on("change", function() {
                self.synchronize.apply(self);
            }), this.editor.resize(!0), this.loaded = !0, this.onLoaded && this.onLoaded.call(this);
        },
        insertEditor: function() {
            $('<div id="' + this.aceId + '"></div>').css({
                left: 0,
                top: 0,
                bottom: 0,
                right: 0,
                zIndex: 1
            }).insertAfter(this.$elem);
        },
        setEditorProps: function() {
            this.editor.setTheme("ace/theme/" + this.theme), this.editor.getSession().setMode("ace/mode/css"), 
            this.editor.getSession().setUseWrapMode(!0), this.editor.getSession().setWrapLimitRange();
        },
        setEditorContent: function() {
            this.editor.getSession().setValue(this.$elem.val());
        },
        containerResizable: function() {
            var self = this;
            this.$container.resizable({
                handles: "s"
            }).css({
                position: "relative",
                height: this.defaultHt,
                minHeight: "400px"
            }).on("resize.aceEditorResize", function() {
                self.editor.resize(!0);
            });
        },
        synchronize: function() {
            var val = this.editor.getValue();
            this.$elem.val(val), this.tinymce && tinyMCE.get(this.element) && tinyMCE.get(this.element).setContent(val);
        },
        destroy: function() {
            return this.loaded ? (this.$editor.remove(), this.editor.destroy(), this.$container.resizable("destroy").off("resize.aceEditorResize").css({
                height: ""
            }), this.$elem.show(), this.loaded = !1, void (this.onDestroy && this.onDestroy.apply(this, arguments))) : !1;
        }
    }, $.fn.AceJavascriptEditor = function(option, value) {
        var option = option || null, data = $(this).data("AceEditor");
        if (data && "string" == typeof option && data[option]) data[option](value || null); else {
            if (!data) return this.each(function() {
                var config = $.extend({
                    element: $(this),
                    aceId: "ace-editor",
                    theme: "textmate",
                    defaultHt: "600px",
                    container: !1
                }, option);
                $(this).data("AceEditor", new AceJavascriptEditor(config));
            });
            $.error('Method "' + option + '" does not exist on AceEditor!');
        }
    }, $.fn.AceStylesheetEditor = function(option, value) {
        var option = option || null, data = $(this).data("AceEditor");
        if (data && "string" == typeof option && data[option]) data[option](value || null); else {
            if (!data) return this.each(function() {
                var config = $.extend({
                    element: $(this),
                    aceId: "ace-editor",
                    theme: "textmate",
                    defaultHt: "600px",
                    container: !1
                }, option);
                $(this).data("AceEditor", new AceStylesheetEditor(config));
            });
            $.error('Method "' + option + '" does not exist on AceEditor!');
        }
    };
}(jQuery);