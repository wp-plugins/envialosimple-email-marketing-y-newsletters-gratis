(function (e) {
    "use strict";

    function r() {
        this.controls = {
            bold: {
                groupIndex: 0,
                visible: true,
                tags: ["b", "strong"],
                css: {
                    fontWeight: "bold"
                },
                tooltip: __("Negrita"),
                hotkey: {
                    ctrl: 1,
                    key: 66
                }
            },
            copy: {
                groupIndex: 8,
                visible: false,
                tooltip: __("Copiar")
            },
            createLink: {
                groupIndex: 6,
                visible: true,
                exec: function () {
                    this.savedRange = this.getInternalRange();
                    var n = this;
                    if (e.wysiwyg.controls && e.wysiwyg.controls.link) {
                        e.wysiwyg.controls.link.init(this)
                    } else if (e.wysiwyg.autoload) {
                        e.wysiwyg.autoload.control("wysiwyg.link.js", function () {
                            n.controls.createLink.exec.apply(n)
                        })
                    } else {
                        t.error("$.wysiwyg.controls.link not defined. You need to include wysiwyg.link.js file")
                    }
                },
                tags: ["a"],
                tooltip: __("Crear vínculo")
            },
            cut: {
                groupIndex: 8,
                visible: false,
                tooltip: __("Cortar")
            },
            decreaseFontSize: {
                groupIndex: 0,
                visible: false,
                tags: ["small"],
                tooltip: __("Achicar tamaño letra"),
                exec: function () {
                    this.decreaseFontSize()
                }
            },
            h1: {
                groupIndex: 7,
                visible: false,
                className: "h1",
                command: e.browser.msie || e.browser.safari ? "FormatBlock" : "heading",
                arguments: e.browser.msie || e.browser.safari ? "<h1>" : "h1",
                tags: ["h1"],
                tooltip: "Header 1"
            },
            h2: {
                groupIndex: 7,
                visible: false,
                className: "h2",
                command: e.browser.msie || e.browser.safari ? "FormatBlock" : "heading",
                arguments: e.browser.msie || e.browser.safari ? "<h2>" : "h2",
                tags: ["h2"],
                tooltip: "Header 2"
            },
            h3: {
                groupIndex: 7,
                visible: false,
                className: "h3",
                command: e.browser.msie || e.browser.safari ? "FormatBlock" : "heading",
                arguments: e.browser.msie || e.browser.safari ? "<h3>" : "h3",
                tags: ["h3"],
                tooltip: "Header 3"
            },
            highlight: {
                tooltip: __("Resaltar"),
                className: "highlight",
                groupIndex: 1,
                visible: false,
                css: {
                    backgroundColor: "rgb(255, 255, 102)"
                },
                exec: function () {
                    var t, n, r, i;
                    if (e.browser.msie || e.browser.safari) {
                        t = "backcolor"
                    } else {
                        t = "hilitecolor"
                    }
                    if (e.browser.msie) {
                        n = this.getInternalRange().parentElement()
                    } else {
                        r = this.getInternalSelection();
                        n = r.extentNode || r.focusNode;
                        while (n.style === undefined) {
                            n = n.parentNode;
                            if (n.tagName && n.tagName.toLowerCase() === "body") {
                                return
                            }
                        }
                    }
                    if (n.style.backgroundColor === "rgb(255, 255, 102)" || n.style.backgroundColor === "#ffff66") {
                        i = "#ffffff"
                    } else {
                        i = "#ffff66"
                    }
                    this.editorDoc.execCommand(t, false, i)
                }
            },
            html: {
                groupIndex: 10,
                visible: true,
                exec: function () {
                    var t;
                    if (this.options.resizeOptions && e.fn.resizable) {
                        t = this.element.height()
                    }
                    if (this.viewHTML) {
                        this.setContent(this.original.value);
                        e(this.original).hide();
                        this.editor.show();
                        if (this.options.resizeOptions && e.fn.resizable) {
                            if (t === this.element.height()) {
                                this.element.height(t + this.editor.height())
                            }
                            this.element.resizable(e.extend(true, {
                                alsoResize: this.editor
                            }, this.options.resizeOptions))
                        }
                        this.ui.toolbar.find("li").each(function () {
                            var t = e(this);
                            if (t.is(".save.btn") || t.is(".cancel.btn")) {
                                return false
                            }
                            if (t.hasClass("html")) {
                                t.removeClass("active")
                            } else {
                                t.removeClass("disabled")
                            }
                        })
                    } else {
                        this.saveContent();
                        e(this.original).css({
                            width: this.element.outerWidth() - 6,
                            height: this.element.height() - this.ui.toolbar.height() - 6,
                            resize: "none"
                        }).show();
                        this.editor.hide();
                        if (this.options.resizeOptions && e.fn.resizable) {
                            if (t === this.element.height()) {
                                this.element.height(this.ui.toolbar.height())
                            }
                            this.element.resizable("destroy")
                        }
                        this.ui.toolbar.find("li").each(function () {
                            var t = e(this);
                            if (t.is(".save.btn") || t.is(".cancel.btn")) {
                                return false
                            }
                            if (t.hasClass("html")) {
                                t.addClass("active")
                            } else {
                                if (false === t.hasClass("fullscreen")) {
                                    t.removeClass("active").addClass("disabled")
                                }
                            }
                        })
                    }
                    this.viewHTML = !this.viewHTML
                },
                tooltip: __("Ver HTML")
            },
            increaseFontSize: {
                groupIndex: 0,
                visible: false,
                tags: ["big"],
                tooltip: __("Aumentar tamaño letra"),
                exec: function () {
                    this.increaseFontSize()
                }
            },
            indent: {
                groupIndex: 2,
                visible: false,
                tooltip: __("Indentar")
            },
            insertHorizontalRule: {
                groupIndex: 6,
                visible: false,
                tags: ["hr"],
                tooltip: __("Línea horizontal")
            },
            insertImage: {
                groupIndex: 6,
                visible: true,
                exec: function () {
                    abrirModalInsertarImg()
                },
                tags: ["img"],
                tooltip: __("Insertar imagen")
            },
            insertOrderedList: {
                groupIndex: 6,
                visible: true,
                tags: ["ol"],
                tooltip: __("Lista numerada")
            },
            insertTable: {
                groupIndex: 6,
                visible: false,
                exec: function () {
                    var n = this;
                    this.savedRange = this.getInternalRange();
                    if (e.wysiwyg.controls && e.wysiwyg.controls.table) {
                        e.wysiwyg.controls.table(this)
                    } else if (e.wysiwyg.autoload) {
                        e.wysiwyg.autoload.control("wysiwyg.table.js", function () {
                            n.controls.insertTable.exec.apply(n)
                        })
                    } else {
                        t.error("$.wysiwyg.controls.table not defined. You need to include wysiwyg.table.js file")
                    }
                },
                tags: ["table"],
                tooltip: __("Insertar tabla")
            },
            insertUnorderedList: {
                groupIndex: 5,
                visible: true,
                tags: ["ul"],
                tooltip: __("Lista punteada")
            },
            italic: {
                groupIndex: 0,
                visible: true,
                tags: ["i", "em"],
                css: {
                    fontStyle: "italic"
                },
                tooltip: __("Cursiva"),
                hotkey: {
                    ctrl: 1,
                    key: 73
                }
            },
            justifyCenter: {
                groupIndex: 1,
                visible: true,
                tags: ["center"],
                css: {
                    textAlign: "center"
                },
                tooltip: __("Centrar")
            },
            justifyFull: {
                groupIndex: 1,
                visible: true,
                css: {
                    textAlign: "justify"
                },
                tooltip: __("Justificar")
            },
            justifyLeft: {
                visible: true,
                groupIndex: 1,
                css: {
                    textAlign: "left"
                },
                tooltip: __("Izquierda")
            },
            justifyRight: {
                groupIndex: 1,
                visible: true,
                css: {
                    textAlign: "right"
                },
                tooltip: __("Derecha")
            },
            ltr: {
                groupIndex: 10,
                visible: false,
                exec: function () {
                    var t = this.dom.getElement("p");
                    if (!t) {
                        return false
                    }
                    e(t).attr("dir", "ltr");
                    return true
                },
                tooltip: __("de izquierda a derecha")
            },
            outdent: {
                groupIndex: 2,
                visible: false,
                tooltip: __("Des-indentar")
            },
            paragraph: {
                groupIndex: 7,
                visible: false,
                className: "paragraph",
                command: "FormatBlock",
                arguments: e.browser.msie || e.browser.safari ? "<p>" : "p",
                tags: ["p"],
                tooltip: __("Parágrafo")
            },
            paste: {
                groupIndex: 8,
                visible: false,
                tooltip: __("Pegar")
            },
            redo: {
                groupIndex: 4,
                visible: false,
                tooltip: __("Rehacer")
            },
            removeFormat: {
                groupIndex: 10,
                visible: true,
                exec: function () {
                    this.removeFormat()
                },
                tooltip: __("Remover formato")
            },
            rtl: {
                groupIndex: 10,
                visible: false,
                exec: function () {
                    var t = this.dom.getElement("p");
                    if (!t) {
                        return false
                    }
                    e(t).attr("dir", "rtl");
                    return true
                },
                tooltip: __("de derecha a izquierda")
            },
            strikeThrough: {
                groupIndex: 0,
                visible: true,
                tags: ["s", "strike"],
                css: {
                    textDecoration: "line-through"
                },
                tooltip: __("Tachado")
            },
            subscript: {
                groupIndex: 3,
                visible: false,
                tags: ["sub"],
                tooltip: __("Subíndice")
            },
            superscript: {
                groupIndex: 3,
                visible: false,
                tags: ["sup"],
                tooltip: __("Superíndice")
            },
            underline: {
                groupIndex: 0,
                visible: true,
                tags: ["u"],
                css: {
                    textDecoration: "underline"
                },
                tooltip: __("Subrayado"),
                hotkey: {
                    ctrl: 1,
                    key: 85
                }
            },
            undo: {
                groupIndex: 4,
                visible: false,
                tooltip: __("Deshacer")
            },
            code: {
                visible: false,
                groupIndex: 6,
                tooltip: __("Fragmento de código"),
                exec: function () {
                    var t = this.getInternalRange(),
                        n = e(t.commonAncestorContainer),
                        r = t.commonAncestorContainer.nodeName.toLowerCase();
                    if (n.parent("code").length) {
                        n.unwrap()
                    } else {
                        if (r !== "body") {
                            n.wrap("<code/>")
                        }
                    }
                }
            },
            changeFont: {
                visible: true,
                groupIndex: 0,
                tooltip: __("Cambiar tipo de letra"),
                exec: function () {
                    this.savedRange = this.getInternalRange();
                    WysiwygSelectFontFaceCombo.createCombo(this)
                }
            },
            changeFontSize: {
                visible: true,
                groupIndex: 0,
                tooltip: __("Cambiar tamaño de letra"),
                exec: function () {
                    this.savedRange = this.getInternalRange();
                    WysiwygSelectFontSizeCombo.createCombo(this)
                }
            },
            wizardPersonalizationCombo: {
                visible: true,
                groupIndex: 11,
                tooltip: __("Personalizar"),
                exec: function () {
                    this.savedRange = this.getInternalRange();
                    WizardPersonalizationCombo.createCombo()
                }
            },
            wizardPersonalizationSocialSharingCombo: {
                visible: true,
                groupIndex: 12,
                tooltip: __("Compartir"),
                exec: function () {
                    this.savedRange = this.getInternalRange();
                    WizardPersonalizationSocialSharingCombo.createCombo()
                }
            },
            save: {
                visible: false,
                exec: function () {
                    var e = this;
                    if (this.viewHTML) {
                        e.controls.html.exec.apply(e)
                    }
                    var t = e.getContent();
                    TemplateEditor.saveFromWysiwyg(t)
                },
                tooltip: __("aplicar"),
                hotkey: {
                    ctrl: 1,
                    key: 83
                }
            },
            cancel: {
                visible: false,
                exec: function () {
                    TemplateEditor.cancelFromWysiwyg()
                },
                tooltip: __("cancelar")
            }
        };
        this.defaults = {
            html: '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><link type="text/css" href="' + urlCss + 'wysiwyg_helper.css" rel="stylesheet" /></head><body style="margin: 3px;" class="restoreNormalCss" id="wysiwyg-iFrame">INITIAL_CONTENT</body></html>',
            debug: false,
            controls: {},
            css: {},
            events: {},
            autoGrow: true,
            autoSave: true,
            brIE: true,
            formHeight: 270,
            formWidth: 440,
            iFrameClass: null,
            initialContent: "<p>Initial content</p>",
            maxHeight: 1e4,
            maxLength: 0,
            messages: {
                nonSelection: __("Selecciona el texto que deseas enlazar")
            },
            toolbarHtml: '<ul role="menu" class="toolbar" style="width:650px;" id="wysiwygToolbar"></ul>',
            removeHeadings: false,
            replaceDivWithP: false,
            resizeOptions: false,
            rmUnusedControls: false,
            rmUnwantedBr: true,
            tableFiller: "Lorem ipsum",
            initialMinHeight: null,
            controlImage: {
                forceRelativeUrls: false
            },
            controlLink: {
                forceRelativeUrls: false
            },
            plugins: {
                autoload: false,
                i18n: false,
                rmFormat: {
                    rmMsWordMarkup: false
                }
            }
        };
        this.availableControlProperties = ["arguments", "callback", "className", "command", "css", "custom", "exec", "groupIndex", "hotkey", "icon", "tags", "tooltip", "visible"];
        this.editor = null;
        this.editorDoc = null;
        this.element = null;
        this.options = {};
        this.original = null;
        this.savedRange = null;
        this.timers = [];
        this.validKeyCodes = [8, 9, 13, 16, 17, 18, 19, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46];
        this.isDestroyed = false;
        this.dom = {
            ie: {
                parent: null
            },
            w3c: {
                parent: null
            }
        };
        this.dom.parent = this;
        this.dom.ie.parent = this.dom;
        this.dom.w3c.parent = this.dom;
        this.ui = {};
        this.ui.self = this;
        this.ui.toolbar = null;
        this.ui.initialHeight = null;
        this.dom.getAncestor = function (e, t) {
            t = t.toLowerCase();
            while (e && "body" !== e.tagName.toLowerCase()) {
                if (t === e.tagName.toLowerCase()) {
                    return e
                }
                e = e.parentNode
            }
            return null
        };
        this.dom.getElement = function (e) {
            var t = this;
            if (window.getSelection) {
                return t.w3c.getElement(e)
            } else {
                return t.ie.getElement(e)
            }
        };
        this.dom.ie.getElement = function (e) {
            var t = this.parent,
                n = t.parent.getInternalSelection(),
                r = n.createRange(),
                i;
            if ("Control" === n.type) {
                if (1 === r.length) {
                    i = r.item(0)
                } else {
                    return null
                }
            } else {
                i = r.parentElement()
            }
            return t.getAncestor(i, e)
        };
        this.dom.w3c.getElement = function (e) {
            var t = this.parent,
                n = t.parent.getInternalRange(),
                r;
            if (!n) {
                return null
            }
            r = n.commonAncestorContainer;
            if (3 === r.nodeType) {
                r = r.parentNode
            }
            if (r === n.startContainer) {
                r = r.childNodes[n.startOffset]
            }
            return t.getAncestor(r, e)
        };
        this.ui.addHoverClass = function () {
            e(this).addClass("wysiwyg-button-hover")
        };
        this.ui.appendControls = function () {
            var t = this,
                n = this.self,
                r = n.parseControls(),
                i = true,
                s = [],
                o = {}, u, a, f = function (e, n) {
                    if (n.groupIndex && a !== n.groupIndex) {
                        a = n.groupIndex;
                        i = false
                    }
                    if (!n.visible) {
                        return
                    }
                    if (!i) {
                        t.appendItemSeparator();
                        i = true
                    }
                    if (n.custom) {
                        t.appendItemCustom(e, n)
                    } else {
                        t.appendItem(e, n)
                    }
                };
            e.each(r, function (e, t) {
                var n = "empty";
                if (undefined !== t.groupIndex) {
                    if ("" === t.groupIndex) {
                        n = "empty"
                    } else {
                        n = t.groupIndex
                    }
                }
                if (undefined === o[n]) {
                    s.push(n);
                    o[n] = {}
                }
                o[n][e] = t
            });
            s.sort(function (e, t) {
                if ("number" === typeof e && typeof e === typeof t) {
                    return e - t
                } else {
                    e = e.toString();
                    t = t.toString();
                    if (e > t) {
                        return 1
                    }
                    if (e === t) {
                        return 0
                    }
                    return -1
                }
            });
            if (0 < s.length) {
                a = s[0]
            }
            for (u = 0; u < s.length; u += 1) {
                e.each(o[s[u]], f)
            }
        };
        this.ui.appendItem = function (t, n) {
            var r = this.self,
                i = n.className || n.command || t || "empty",
                s = n.tooltip || n.command || t || "";
            if (t == "save") {
                i = i + " btn btn-primary"
            } else if (t == "cancel") {
                i = i + " btn"
            }
            return e('<li role="menuitem" unselectable="on"><span>' + s + "</span></li>").addClass(i).attr("title", s).hover(this.addHoverClass, this.removeHoverClass).click(function () {
                if (e(this).hasClass("disabled")) {
                    return false
                }
                r.triggerControl.apply(r, [t, n]);
                this.blur();
                r.ui.returnRange();
                r.ui.focus();
                return true
            }).appendTo(r.ui.toolbar)
        };
        this.ui.appendItemCustom = function (t, n) {
            var r = this.self,
                i = n.tooltip || n.command || t || "";
            if (n.callback) {
                e(window).bind("trigger-" + t + ".wysiwyg", n.callback)
            }
            return e('<li role="menuitem" unselectable="on" style="background: url(\'' + n.icon + "') no-repeat;\"></li>").addClass("custom-command-" + t).addClass("wysiwyg-custom-command").addClass(t).attr("title", i).hover(this.addHoverClass, this.removeHoverClass).click(function () {
                if (e(this).hasClass("disabled")) {
                    return false
                }
                r.triggerControl.apply(r, [t, n]);
                this.blur();
                r.ui.returnRange();
                r.ui.focus();
                r.triggerControlCallback(t);
                return true
            }).appendTo(r.ui.toolbar)
        };
        this.ui.appendItemSeparator = function () {
            var t = this.self;
            return e('<li role="separator" class="separator"></li>').appendTo(t.ui.toolbar)
        };
        this.autoSaveFunction = function () {
            this.saveContent()
        };
        this.SelectText = function (t) {
            var n = this.editorDoc;
            var r = e(t)[0];
            if (n.body.createTextRange) {
                var i = n.body.createTextRange();
                i.moveToElementText(r);
                i.select()
            } else if (n.getSelection) {
                var s = n.getSelection();
                if (s.setBaseAndExtent) {
                    s.setBaseAndExtent(r, 0, r, 1)
                } else {
                    var i = n.createRange();
                    i.selectNodeContents(r);
                    s.removeAllRanges();
                    s.addRange(i)
                }
            }
        };
        this.ui.checkTargets = function (t) {
            var n = this.self;
            e.each(n.options.controls, function (r, i) {
                var s = i.className || i.command || r || "empty",
                    o, u, a, f, l = function (e, t) {
                        var r;
                        if ("function" === typeof t) {
                            r = t;
                            if (r(f.css(e).toString().toLowerCase(), n)) {
                                n.ui.toolbar.find("." + s).addClass("active")
                            }
                        } else {
                            if (f.css(e).toString().toLowerCase() === t) {
                                n.ui.toolbar.find("." + s).addClass("active")
                            }
                        }
                    };
                if ("fullscreen" !== s) {
                    n.ui.toolbar.find("." + s).removeClass("active")
                }
                if (i.tags || i.options && i.options.tags) {
                    o = i.tags || i.options && i.options.tags;
                    u = t;
                    while (u) {
                        if (u.nodeType !== 1) {
                            break
                        }
                        if (e.inArray(u.tagName.toLowerCase(), o) !== -1) {
                            n.ui.toolbar.find("." + s).addClass("active")
                        }
                        u = u.parentNode
                    }
                }
                if (i.css || i.options && i.options.css) {
                    a = i.css || i.options && i.options.css;
                    f = e(t);
                    while (f) {
                        if (!f[0] || f[0].nodeType !== 1) {
                            break
                        }
                        e.each(a, l);
                        f = f.parent()
                    }
                }
            })
        };
        this.ui.designMode = function () {
            var e = 3,
                t = this.self,
                n;
            n = function (e) {
                if ("on" === t.editorDoc.designMode) {
                    if (t.timers.designMode) {
                        window.clearTimeout(t.timers.designMode)
                    }
                    if (t.innerDocument() !== t.editorDoc) {
                        t.ui.initFrame()
                    }
                    return
                }
                try {
                    t.editorDoc.designMode = "on"
                } catch (r) {}
                e -= 1;
                if (e > 0) {
                    t.timers.designMode = window.setTimeout(function () {
                        n(e)
                    }, 100)
                }
            };
            n(e)
        };
        this.destroy = function () {
            this.isDestroyed = true;
            var t, n = this.element.closest("form");
            for (t = 0; t < this.timers.length; t += 1) {
                window.clearTimeout(this.timers[t])
            }
            n.unbind(".wysiwyg");
            this.element.remove();
            e.removeData(this.original, "wysiwyg");
            e(this.original).show();
            return this
        };
        this.getRangeText = function () {
            var e = this.getInternalRange();
            if (e.toString) {
                e = e.toString()
            } else if (e.text) {
                e = e.text
            }
            return e
        };
        this.getRangeHtml = function () {
            var t = this.getInternalSelection();
            if (t.anchorNode && t.getRangeAt) {
                var n = t.getRangeAt(0);
                var r = e(n.cloneContents().childNodes);
                var i = e(n.commonAncestorContainer).closest("*");
                return {
                    rangeAtZero: n,
                    rangeContents: r,
                    rangeClosestContainer: i
                }
            }
            return false
        };
        this.execute = function (e, t) {
            if (typeof t === "undefined") {
                t = null
            }
            this.editorDoc.execCommand(e, false, t)
        };
        this.extendOptions = function (t) {
            var n = {};
            if ("object" === typeof t.controls) {
                n = t.controls;
                delete t.controls
            }
            t = e.extend(true, {}, this.defaults, t);
            t.controls = e.extend(true, {}, n, this.controls, n);
            if (t.rmUnusedControls) {
                e.each(t.controls, function (e) {
                    if (!n[e]) {
                        delete t.controls[e]
                    }
                })
            }
            return t
        };
        this.ui.focus = function () {
            var e = this.self;
            e.editor.get(0).contentWindow.focus();
            return e
        };
        this.ui.returnRange = function () {
            var e = this.self,
                n;
            if (e.savedRange !== null) {
                if (window.getSelection) {
                    n = window.getSelection();
                    if (n.rangeCount > 0) {
                        n.removeAllRanges()
                    }
                    try {
                        n.addRange(e.savedRange)
                    } catch (r) {
                        t.error(r)
                    }
                } else if (window.document.createRange) {
                    window.getSelection().addRange(e.savedRange)
                } else if (window.document.selection) {
                    e.savedRange.select()
                }
                e.savedRange = null
            }
        };
        this.increaseFontSize = function () {
            var t = this.getRangeHtml();
            if (t && t.rangeContents) {
                var n = e(t.rangeClosestContainer).css("font-size");
                var r = parseInt(n) + 1;
                var i = false;
                var s = false;
                e(t.rangeClosestContainer).find('[style*="font-size"], [style*="FONT-SIZE"]').each(function () {
                    if (e(this)[0] === e(t.rangeAtZero.endContainer.parentNode).next()[0]) {
                        return false
                    }
                    if (e(this)[0] === e(t.rangeAtZero.startContainer.parentNode)[0]) {
                        s = true
                    }
                    if (s) {
                        var n = e(this).css("font-size");
                        var r = parseInt(n) + 1;
                        e(this).css("font-size", r);
                        i = true
                    }
                });
                if (!i) {
                    e(t.rangeClosestContainer).css("font-size", r)
                }
            }
        };
        this.decreaseFontSize = function () {
            var t = this.getRangeHtml();
            if (t && t.rangeContents) {
                var n = e(t.rangeClosestContainer).css("font-size");
                var r = parseInt(n) - 1;
                var i = false;
                var s = false;
                e(t.rangeClosestContainer).find('[style*="font-size"], [style*="FONT-SIZE"]').each(function () {
                    if (e(this)[0] === e(t.rangeAtZero.endContainer.parentNode).next()[0]) {
                        return false
                    }
                    if (e(this)[0] === e(t.rangeAtZero.startContainer.parentNode)[0]) {
                        s = true
                    }
                    if (s) {
                        var n = e(this).css("font-size");
                        var r = parseInt(n) - 1;
                        e(this).css("font-size", r);
                        i = true
                    }
                });
                if (!i) {
                    e(t.rangeClosestContainer).css("font-size", r)
                }
            }
        };
        this.getContent = function () {
            return this.events.filter("getContent", TemplateEditor.stripNastyTagsFromString(this.editorDoc.body.innerHTML))
        };
        this.events = {
            _events: {},
            bind: function (e, t) {
                if (typeof this._events.eventName !== "object") {
                    this._events[e] = []
                }
                this._events[e].push(t)
            },
            trigger: function (t, n) {
                if (typeof this._events.eventName === "object") {
                    var r = this.editor;
                    e.each(this._events[t], function (e, t) {
                        if (typeof t === "function") {
                            t.apply(r, n)
                        }
                    })
                }
            },
            filter: function (t, n) {
                if (typeof this._events[t] === "object") {
                    var r = this.editor,
                        i = Array.prototype.slice.call(arguments, 1);
                    e.each(this._events[t], function (e, t) {
                        if (typeof t === "function") {
                            n = t.apply(r, i)
                        }
                    })
                }
                return n
            }
        };
        this.getElementByAttributeValue = function (t, n, r) {
            var i, s, o = this.editorDoc.getElementsByTagName(t);
            for (i = 0; i < o.length; i += 1) {
                s = o[i].getAttribute(n);
                if (e.browser.msie) {
                    s = s.substr(s.length - r.length)
                }
                if (s === r) {
                    return o[i]
                }
            }
            return false
        };
        this.getInternalRange = function () {
            var e = this.getInternalSelection();
            if (!e) {
                return null
            }
            if (e.rangeCount && e.rangeCount > 0) {
                return e.getRangeAt(0)
            } else if (e.createRange) {
                return e.createRange()
            }
            return null
        };
        this.getInternalSelection = function () {
            if (this.editor.get(0).contentWindow) {
                if (this.editor.get(0).contentWindow.getSelection) {
                    return this.editor.get(0).contentWindow.getSelection()
                }
                if (this.editor.get(0).contentWindow.selection) {
                    return this.editor.get(0).contentWindow.selection
                }
            }
            if (this.editorDoc.getSelection) {
                return this.editorDoc.getSelection()
            }
            if (this.editorDoc.selection) {
                return this.editorDoc.selection
            }
            return null
        };
        this.getRange = function () {
            var e = this.getSelection();
            if (!e) {
                return null
            }
            if (e.rangeCount && e.rangeCount > 0) {
                e.getRangeAt(0)
            } else if (e.createRange) {
                return e.createRange()
            }
            return null
        };
        this.getSelection = function () {
            return window.getSelection ? window.getSelection() : window.document.selection
        };
        this.ui.grow = function () {
            var t = this.self,
                n = e(t.editorDoc.body),
                r = e.browser.msie ? n[0].scrollHeight : n.height() + 2 + 20,
                i = t.ui.initialHeight,
                s = Math.max(r, i);
            s = Math.min(s, t.options.maxHeight);
            t.editor.attr("scrolling", s < t.options.maxHeight ? "no" : "auto");
            n.css("overflow", s < t.options.maxHeight ? "hidden" : "");
            t.editor.get(0).height = s;
            return t
        };
        this.init = function (t, n) {
            var r = this,
                i = e(t).closest("form"),
                s = t.width || t.clientWidth || 0,
                o = t.height || t.clientHeight || 0;
            this.options = this.extendOptions(n);
            this.original = t;
            this.ui.toolbar = e(this.options.toolbarHtml);
            if (e.browser.msie && parseInt(e.browser.version, 10) < 8) {
                this.options.autoGrow = false
            }
            if (s === 0 && t.cols) {
                s = t.cols * 8 + 21
            }
            if (o === 0 && t.rows) {
                o = t.rows * 16 + 16
            }
            this.editor = e(window.location.protocol === "https:" ? '<iframe src="javascript:false;"></iframe>' : "<iframe></iframe>").attr("frameborder", "0");
            if (this.options.iFrameClass) {
                this.editor.addClass(this.options.iFrameClass)
            } else {
                this.editor.css({
                    minHeight: (o - 6).toString() + "px",
                    width: s > 50 ? (s - 8).toString() + "px" : ""
                });
                if (e.browser.msie && parseInt(e.browser.version, 10) < 7) {
                    this.editor.css("height", o.toString() + "px")
                }
            }
            this.editor.attr("tabindex", e(t).attr("tabindex"));
            this.element = e('<div id="wysiwygContainer"/>').addClass("wysiwyg");
            if (!this.options.iFrameClass) {
                this.element.css({
                    width: s > 0 ? s.toString() + "px" : "100%"
                })
            }
            e(t).hide().before(this.element);
            this.viewHTML = false;
            this.initialContent = e(t).val();
            this.ui.initFrame();
            if (this.options.resizeOptions && e.fn.resizable) {
                this.element.resizable(e.extend(true, {
                    alsoResize: this.editor
                }, this.options.resizeOptions))
            }
            if (this.options.autoSave) {
                i.bind("submit.wysiwyg", function () {
                    r.autoSaveFunction()
                })
            }
            i.bind("reset.wysiwyg", function () {
                r.resetFunction()
            })
        };
        this.ui.initFrame = function () {
            var t = this.self,
                n, r, i;
            var s = e('<div data-componentName="wysiwyg-toolbarWrapper" class="wysiwyg-toolbarWrapper"><div class="toolbarExtraTools"></div>');
            s.prepend(t.ui.toolbar);
            t.ui.appendControls();
            t.ui.toolbar.append('<br clear="all"/>');
            t.element.append(s).append(e("<div><!-- --></div>").css({
                clear: "both"
            })).append(t.editor);
            t.editorDoc = t.innerDocument();
            if (t.isDestroyed) {
                return null
            }
            t.ui.designMode();
            t.editorDoc.open();
            t.ui.toolbar.animate({
                top: "0"
            }, 300);
            t.editorDoc.write(t.options.html.replace(/INITIAL_CONTENT/, function () {
                return t.wrapInitialContent()
            }));
            t.editorDoc.close();
            e.wysiwyg.plugin.bind(t);
            e(t.editorDoc).trigger("initFrame.wysiwyg");
            e(t.editorDoc).bind("click.wysiwyg", function (e) {
                t.ui.checkTargets(e.target ? e.target : e.srcElement)
            });
            e(t.editorDoc).on("click", "a[data-isAttachment=1]", function (t) {
                e(this).trigger("dblclick")
            }).on("dblclick", "a[data-isAttachment=1]", function (n) {
                var r = this;
                e.wysiwyg.launchAttachmentSelector(t, r)
            }).on("mouseenter", "a[data-isAttachment=1]", function (n) {
                var r = e(this);
                var i = e(r).data("relLinkToolsWrapper") || false;
                e(t.editorDoc).find("a[data-isAttachment=1]").not(e(r)).each(function (t, n) {
                    e(n).data("relLinkToolsWrapper", null)
                });
                e(t.editorDoc).find("img").each(function (t, n) {
                    e(n).data("relImgToolsWrapper", null)
                });
                e(t.editorDoc).find(".wysiwyg-imgWrapper").trigger("mouseleave");
                if (i != false) {
                    return true
                }
                e(t.editorDoc).find(".wysiwyg-attachmentWrapper").not(i).trigger("mouseleave");
                if (e.browser.mozilla) {
                    t.editorDoc.designMode = "on";
                    t.editorDoc.execCommand("enableObjectResizing", false, false)
                }
                if (e(r).closest(".tobAttachmentItem").length < 1) {
                    e(r).wrap('<span style="white-space: nowrap;color: rgb(102, 102, 102); font-family: Arial, Helvetica, sans-serif; font-size: 12px;text-align:left; vertical-align:middle;letter-spacing:normal;" class="tobAttachmentItem">')
                }
                if (e(r).children("font").length) {
                    e(r).css("color", e(r).children("font").css("color"));
                    e(r).html(e(r).children("font").html())
                }
                var s = e(r).width();
                var o = e(r).height();
                var u = parseInt(e(r).css("font-size")) || 0;
                var a = e(r).offset();
                var f = e('<div class="wysiwyg-attachmentWrapper" style="top:' + a.top + "px;left:" + a.left + "px;width:" + s + "px;height:" + o + "px;line-height:normal;" + (u && u > 0 ? "font-size:" + u + "px;" : "") + '"><div class="attachmentToolContainer"><div class="attachmentTool changeColor" title="' + __("Modificar color del texto") + '"></div><div class="attachmentTool delete"></div><div class="attachmentTool edit"></div></div></div>');
                e(r).closest(".tobAttachmentItem").after(e(f));
                e(f).find(".attachmentToolContainer").css({
                    top: e(f).height() - 10,
                    left: e(f).width() / 2 - 44,
                    "z-index": 1e5
                }).show();
                e(r).data("relLinkToolsWrapper", e(f));
                e(f).data("relLink", e(r))
            }).on("mouseleave", ".wysiwyg-attachmentWrapper", function (t) {
                var n = e(this).data("relLink");
                if (n) {
                    e(n).data("relLinkToolsWrapper", null)
                }
                e(this).remove()
            }).on("click", ".wysiwyg-attachmentWrapper .attachmentTool.changeColor", function (n) {
                n.stopPropagation();
                var r = e(this).closest(".wysiwyg-attachmentWrapper").data("relLink");
                if (r) {
                    var i = e(r).css("color") || "rgb(34, 0, 193)";
                    e(r).trigger("mouseleave");
                    t.SelectText(e(r));
                    if (e.wysiwyg.controls.colorpicker) {
                        e.wysiwyg.controls.colorpicker.init(t, i)
                    }
                }
            }).on("click", ".wysiwyg-attachmentWrapper", function (t) {
                t.stopPropagation();
                e(this).find(".attachmentTool.edit").trigger("click")
            }).on("click", ".wysiwyg-attachmentWrapper .attachmentTool.edit", function (t) {
                t.stopPropagation();
                var n = e(this).closest(".wysiwyg-attachmentWrapper").data("relLink");
                if (n) {
                    e(n).trigger("click")
                }
            }).on("click", ".wysiwyg-attachmentWrapper .attachmentTool.delete", function (t) {
                t.stopPropagation();
                var n = e(this).closest(".wysiwyg-attachmentWrapper").data("relLink");
                if (n) {
                    e(n).closest(".tobAttachmentItem").remove()
                }
                e(this).closest(".wysiwyg-attachmentWrapper").remove()
            }).on("click", "img", function (n) {
                var r = this;
                e(r).data("relImgToolsWrapper", null);
                e(t.editorDoc).find(".wysiwyg-imgWrapper").trigger("mouseleave");
                e(r).trigger("dblclick")
            }).on("dblclick", "img", function (n) {
                var r = this;
                var i = "";
                if (e(r).parent()) {
                    if (e(r).parent().is("a")) {
                        i = e(r).parent().attr("href")
                    }
                }
                e(r).data("relImgToolsWrapper", null);
                e(t.editorDoc).find(".wysiwyg-imgWrapper").trigger("mouseleave");
                abrirModalEditarImg("", "", e(r), i, true)
            }).on("mouseenter", "img", function (n) {
                var r = e(this);
                var i = e(r).data("relImgToolsWrapper") || false;
                e(t.editorDoc).find("img").not(e(r)).each(function (t, n) {
                    e(n).data("relImgToolsWrapper", null)
                });
                e(t.editorDoc).find("a[data-isAttachment=1]").each(function (t, n) {
                    e(n).data("relLinkToolsWrapper", null)
                });
                e(t.editorDoc).find(".wysiwyg-attachmentWrapper").trigger("mouseleave");
                if (i != false) {
                    return true
                }
                e(t.editorDoc).find(".wysiwyg-imgWrapper").not(i).trigger("mouseleave");
                if (e.browser.mozilla) {
                    t.editorDoc.designMode = "on";
                    t.editorDoc.execCommand("enableObjectResizing", false, false)
                }
                var s = e(r).width();
                var o = e(r).height();
                var u = e(r).attr("data-alignmentType");
                var a = e(r).css("float");
                var f = e(r).css("marginLeft");
                var l = e(r).css("marginRight");
                var c = e(r).position();
                var h = e(r).offset();
                if (u == "left-block" || u == "center" || u == "right-block") {
                    switch (u) {
                        case "left-block":
                            l = "auto";
                            break;
                        case "right-block":
                            f = "auto";
                            break;
                        case "center":
                            l = f = "auto";
                            break
                    }
                }
                var p = '<div class="wysiwyg-imgWrapper" style="top:' + h.top + "px;left:" + h.left + "px;width:" + s + "px;height:" + o + "px;float:" + a + ";margin-left:" + f + ";margin-right:" + l + '">\n<div class="imgToolContainer">\n<div class="imgTool delete"></div><div class="imgTool edit"></div>\n</div>\n</div>';
                var d = e(p);
                e(r).after(e(d));
                e(d).find(".imgToolContainer").css({
                    top: o - (e.browser.msie ? 28 : 18),
                    left: s / 2 - 30,
                    "z-index": 1e5
                }).show();
                e(r).data("relImgToolsWrapper", e(d));
                e(d).data("relImg", e(r))
            }).on("mouseleave", ".wysiwyg-imgWrapper", function (t) {
                var n = e(this).data("relImg");
                if (n) {
                    e(n).data("relImgToolsWrapper", null)
                }
                e(this).remove()
            }).on("click", ".wysiwyg-imgWrapper", function (t) {
                t.stopPropagation();
                e(this).find(".imgTool.edit").trigger("click")
            }).on("click", ".wysiwyg-imgWrapper .imgTool.edit", function (t) {
                t.stopPropagation();
                var n = e(this).closest(".wysiwyg-imgWrapper").data("relImg");
                e(n).data("relImgToolsWrapper", null);
                e(this).closest(".wysiwyg-imgWrapper").remove();
                setTimeout(function () {
                    if (n) {
                        e(n).trigger("click")
                    }
                }, 50)
            }).on("click", ".wysiwyg-imgWrapper .imgTool.delete", function (t) {
                t.stopPropagation();
                var n = e(this).closest(".wysiwyg-imgWrapper").data("relImg");
                if (n) {
                    e(n).remove()
                }
                e(this).closest(".wysiwyg-imgWrapper").remove()
            });
            e("[role=menuitem]").on("mouseenter", function (n) {
                e(t.editorDoc).find(".wysiwyg-imgWrapper").trigger("mouseleave");
                e(t.editorDoc).find(".wysiwyg-attachmentWrapper").trigger("mouseleave")
            });
            e(t.original).focus(function () {
                if (e(this).filter(":visible")) {
                    return
                }
                t.ui.focus()
            });
            e(t.editorDoc).keydown(function (e) {
                var n;
                if (e.keyCode === 8) {
                    n = /^<([\w]+)[^>]*>(<br\/?>)?<\/\1>$/;
                    if (n.test(t.getContent())) {
                        e.stopPropagation();
                        return false
                    }
                }
                return true
            });
            if (true || !e.browser.msie) {
                e(t.editorDoc).keydown(function (e) {
                    var n;
                    if (e.ctrlKey || e.metaKey) {
                        for (n in t.controls) {
                            if (t.controls[n].hotkey && t.controls[n].hotkey.ctrl) {
                                if (e.keyCode === t.controls[n].hotkey.key) {
                                    t.triggerControl.apply(t, [n, t.controls[n]]);
                                    return false
                                }
                            }
                        }
                    }
                    return true
                })
            } else if (t.options.brIE) {
                e(t.editorDoc).keydown(function (e) {
                    if (e.keyCode === 13) {
                        var n = t.getRange();
                        n.pasteHTML("<br/>");
                        n.collapse(false);
                        n.select();
                        return false
                    }
                    return true
                })
            }
            if (t.options.plugins.rmFormat.rmMsWordMarkup) {
                e(t.editorDoc).bind("keyup.wysiwyg", function (n) {
                    if (n.ctrlKey || n.metaKey) {
                        if (86 === n.keyCode) {
                            if (e.wysiwyg.rmFormat) {
                                if ("object" === typeof t.options.plugins.rmFormat.rmMsWordMarkup) {
                                    e.wysiwyg.rmFormat.run(t, {
                                        rules: {
                                            msWordMarkup: t.options.plugins.rmFormat.rmMsWordMarkup
                                        }
                                    })
                                } else {
                                    e.wysiwyg.rmFormat.run(t, {
                                        rules: {
                                            msWordMarkup: {
                                                enabled: true
                                            }
                                        }
                                    })
                                }
                            }
                        }
                    }
                })
            }
            if (t.options.autoSave) {
                e(t.editorDoc).keydown(function () {
                    t.autoSaveFunction()
                }).keyup(function () {
                    t.autoSaveFunction()
                }).mousedown(function () {
                    t.autoSaveFunction()
                }).bind(e.support.noCloneEvent ? "input.wysiwyg" : "paste.wysiwyg", function () {
                    t.autoSaveFunction()
                })
            }
            if (t.options.autoGrow) {
                if (t.options.initialMinHeight !== null) {
                    t.ui.initialHeight = t.options.initialMinHeight
                } else {
                    t.ui.initialHeight = e(t.editorDoc).height()
                }
                e(t.editorDoc.body).css("border", "1px solid white");
                r = function () {
                    t.ui.grow()
                };
                e(t.editorDoc).keyup(r);
                e(t.editorDoc).bind("editorRefresh.wysiwyg", r);
                t.ui.grow()
            }
            if (t.options.css) {
                if (String === t.options.css.constructor) {
                    if (e.browser.msie) {
                        n = t.editorDoc.createStyleSheet(t.options.css);
                        e(n).attr({
                            media: "all"
                        })
                    } else {
                        n = e("<link/>").attr({
                            href: t.options.css,
                            media: "all",
                            rel: "stylesheet",
                            type: "text/css"
                        });
                        e(t.editorDoc).find("head").append(n)
                    }
                } else {
                    t.timers.initFrame_Css = window.setTimeout(function () {
                        e(t.editorDoc.body).css(t.options.css)
                    }, 0)
                }
            }
            if (t.initialContent.length === 0) {
                if ("function" === typeof t.options.initialContent) {
                    t.setContent(t.options.initialContent())
                } else {
                    t.setContent(t.options.initialContent)
                }
            }
            if (t.options.maxLength > 0) {
                e(t.editorDoc).keydown(function (n) {
                    if (e(t.editorDoc).text().length >= t.options.maxLength && e.inArray(n.which, t.validKeyCodes) === -1) {
                        n.preventDefault()
                    }
                })
            }
            e.each(t.options.events, function (n, r) {
                e(t.editorDoc).bind(n + ".wysiwyg", function (e) {
                    r.apply(t.editorDoc, [e, t])
                })
            });
            if (e.browser.msie) {
                e(t.editorDoc).bind("beforedeactivate.wysiwyg", function () {
                    t.savedRange = t.getInternalRange()
                })
            } else {
                e(t.editorDoc).bind("blur.wysiwyg", function () {
                    t.savedRange = t.getInternalRange()
                })
            }
            e(t.editorDoc.body).addClass("wysiwyg");
            if (t.options.events && t.options.events.save) {
                i = t.options.events.save;
                e(t.editorDoc).bind("keyup.wysiwyg", i);
                e(t.editorDoc).bind("change.wysiwyg", i);
                if (e.support.noCloneEvent) {
                    e(t.editorDoc).bind("input.wysiwyg", i)
                } else {
                    e(t.editorDoc).bind("paste.wysiwyg", i);
                    e(t.editorDoc).bind("cut.wysiwyg", i)
                }
            }
            if (t.options.xhtml5 && t.options.unicode) {
                var o = {
                    ne: 8800,
                    le: 8804,
                    para: 182,
                    xi: 958,
                    darr: 8595,
                    nu: 957,
                    oacute: 243,
                    Uacute: 218,
                    omega: 969,
                    prime: 8242,
                    pound: 163,
                    igrave: 236,
                    thorn: 254,
                    forall: 8704,
                    emsp: 8195,
                    lowast: 8727,
                    brvbar: 166,
                    alefsym: 8501,
                    nbsp: 160,
                    delta: 948,
                    clubs: 9827,
                    lArr: 8656,
                    Omega: 937,
                    Auml: 196,
                    cedil: 184,
                    and: 8743,
                    plusmn: 177,
                    ge: 8805,
                    raquo: 187,
                    uml: 168,
                    equiv: 8801,
                    laquo: 171,
                    rdquo: 8221,
                    Epsilon: 917,
                    divide: 247,
                    fnof: 402,
                    chi: 967,
                    Dagger: 8225,
                    iacute: 237,
                    rceil: 8969,
                    sigma: 963,
                    Oslash: 216,
                    acute: 180,
                    frac34: 190,
                    lrm: 8206,
                    upsih: 978,
                    Scaron: 352,
                    part: 8706,
                    exist: 8707,
                    nabla: 8711,
                    image: 8465,
                    prop: 8733,
                    zwj: 8205,
                    omicron: 959,
                    aacute: 225,
                    Yuml: 376,
                    Yacute: 221,
                    weierp: 8472,
                    rsquo: 8217,
                    otimes: 8855,
                    kappa: 954,
                    thetasym: 977,
                    harr: 8596,
                    Ouml: 214,
                    Iota: 921,
                    ograve: 242,
                    sdot: 8901,
                    copy: 169,
                    oplus: 8853,
                    acirc: 226,
                    sup: 8835,
                    zeta: 950,
                    Iacute: 205,
                    Oacute: 211,
                    crarr: 8629,
                    Nu: 925,
                    bdquo: 8222,
                    lsquo: 8216,
                    apos: 39,
                    Beta: 914,
                    eacute: 233,
                    egrave: 232,
                    lceil: 8968,
                    Kappa: 922,
                    piv: 982,
                    Ccedil: 199,
                    ldquo: 8220,
                    Xi: 926,
                    cent: 162,
                    uarr: 8593,
                    hellip: 8230,
                    Aacute: 193,
                    ensp: 8194,
                    sect: 167,
                    Ugrave: 217,
                    aelig: 230,
                    ordf: 170,
                    curren: 164,
                    sbquo: 8218,
                    macr: 175,
                    Phi: 934,
                    Eta: 919,
                    rho: 961,
                    Omicron: 927,
                    sup2: 178,
                    euro: 8364,
                    aring: 229,
                    Theta: 920,
                    mdash: 8212,
                    uuml: 252,
                    otilde: 245,
                    eta: 951,
                    uacute: 250,
                    rArr: 8658,
                    nsub: 8836,
                    agrave: 224,
                    notin: 8713,
                    ndash: 8211,
                    Psi: 936,
                    Ocirc: 212,
                    sube: 8838,
                    szlig: 223,
                    micro: 181,
                    not: 172,
                    sup1: 185,
                    middot: 183,
                    iota: 953,
                    ecirc: 234,
                    lsaquo: 8249,
                    thinsp: 8201,
                    sum: 8721,
                    ntilde: 241,
                    scaron: 353,
                    cap: 8745,
                    atilde: 227,
                    lang: 10216,
                    __replacement: 65533,
                    isin: 8712,
                    gamma: 947,
                    Euml: 203,
                    ang: 8736,
                    upsilon: 965,
                    Ntilde: 209,
                    hearts: 9829,
                    Alpha: 913,
                    Tau: 932,
                    spades: 9824,
                    dagger: 8224,
                    THORN: 222,
                    "int": 8747,
                    lambda: 955,
                    Eacute: 201,
                    Uuml: 220,
                    infin: 8734,
                    rlm: 8207,
                    Aring: 197,
                    ugrave: 249,
                    Egrave: 200,
                    Acirc: 194,
                    rsaquo: 8250,
                    ETH: 208,
                    oslash: 248,
                    alpha: 945,
                    Ograve: 210,
                    Prime: 8243,
                    mu: 956,
                    ni: 8715,
                    real: 8476,
                    bull: 8226,
                    beta: 946,
                    icirc: 238,
                    eth: 240,
                    prod: 8719,
                    larr: 8592,
                    ordm: 186,
                    perp: 8869,
                    Gamma: 915,
                    reg: 174,
                    ucirc: 251,
                    Pi: 928,
                    psi: 968,
                    tilde: 732,
                    asymp: 8776,
                    zwnj: 8204,
                    Agrave: 192,
                    deg: 176,
                    AElig: 198,
                    times: 215,
                    Delta: 916,
                    sim: 8764,
                    Otilde: 213,
                    Mu: 924,
                    uArr: 8657,
                    circ: 710,
                    theta: 952,
                    Rho: 929,
                    sup3: 179,
                    diams: 9830,
                    tau: 964,
                    Chi: 935,
                    frac14: 188,
                    oelig: 339,
                    shy: 173,
                    or: 8744,
                    dArr: 8659,
                    phi: 966,
                    iuml: 239,
                    Lambda: 923,
                    rfloor: 8971,
                    iexcl: 161,
                    cong: 8773,
                    ccedil: 231,
                    Icirc: 206,
                    frac12: 189,
                    loz: 9674,
                    rarr: 8594,
                    cup: 8746,
                    radic: 8730,
                    frasl: 8260,
                    euml: 235,
                    OElig: 338,
                    hArr: 8660,
                    Atilde: 195,
                    Upsilon: 933,
                    there4: 8756,
                    ouml: 246,
                    oline: 8254,
                    Ecirc: 202,
                    yacute: 253,
                    auml: 228,
                    permil: 8240,
                    sigmaf: 962,
                    iquest: 191,
                    empty: 8709,
                    pi: 960,
                    Ucirc: 219,
                    supe: 8839,
                    Igrave: 204,
                    yen: 165,
                    rang: 10217,
                    trade: 8482,
                    lfloor: 8970,
                    minus: 8722,
                    Zeta: 918,
                    sub: 8834,
                    epsilon: 949,
                    yuml: 255,
                    Sigma: 931,
                    Iuml: 207,
                    ocirc: 244
                };
                t.events.bind("getContent", function (e) {
                    return e.replace(/&(?:amp;)?(?!amp|lt|gt|quot)([a-z][a-z0-9]*);/gi, function (e, t) {
                        if (!o[t]) {
                            t = t.toLowerCase();
                            if (!o[t]) {
                                t = "__replacement"
                            }
                        }
                        var n = o[t];
                        return String.fromCharCode(n)
                    })
                })
            }
        };
        this.innerDocument = function () {
            var e = this.editor.get(0);
            if (e.nodeName.toLowerCase() === "iframe") {
                if (e.contentDocument) {
                    return e.contentDocument
                } else if (e.contentWindow) {
                    return e.contentWindow.document
                }
                if (this.isDestroyed) {
                    return null
                }
                t.error("Unexpected error in innerDocument")
            }
            return e
        };
        this.insertHtml = function (t) {
            var n, r;
            if (!t || t.length === 0) {
                return this
            }
            t = t.replace(/<\/*TOKEN>/ig, "");
            if (e.browser.msie) {
                this.ui.focus();
                this.ui.returnRange();
                this.editorDoc.execCommand("insertImage", false, "#jwysiwyg#");
                n = this.getElementByAttributeValue("img", "src", "#jwysiwyg#");
                if (n) {
                    e(n).replaceWith(t)
                }
            } else {
                if (!this.editorDoc.execCommand("insertHTML", false, t)) {
                    this.editor.focus();
                    this.editorDoc.execCommand("insertHTML", false, t)
                }
            }
            this.saveContent();
            return this
        };
        this.execChangeFontFace = function (e) {
            if (!e || e.length === 0) {
                return this
            }
            this.editorDoc.execCommand("fontname", false, e)
        };
        this.parseControls = function () {
            var t = this;
            e.each(this.options.controls, function (n, r) {
                e.each(r, function (r) {
                    if (-1 === e.inArray(r, t.availableControlProperties)) {
                        throw n + '["' + r + '"]: property "' + r + '" not exists in Wysiwyg.availableControlProperties'
                    }
                })
            });
            if (this.options.parseControls) {
                return this.options.parseControls.call(this)
            }
            return this.options.controls
        };
        this.removeFormat = function () {
            if (e.browser.msie) {
                this.ui.focus()
            }
            if (this.options.removeHeadings) {
                this.editorDoc.execCommand("formatBlock", false, "<p>")
            }
            this.editorDoc.execCommand("removeFormat", false, null);
            this.editorDoc.execCommand("unlink", false, null);
            if (e.wysiwyg.rmFormat && e.wysiwyg.rmFormat.enabled) {
                if ("object" === typeof this.options.plugins.rmFormat.rmMsWordMarkup) {
                    e.wysiwyg.rmFormat.run(this, {
                        rules: {
                            msWordMarkup: this.options.plugins.rmFormat.rmMsWordMarkup
                        }
                    })
                } else {
                    e.wysiwyg.rmFormat.run(this, {
                        rules: {
                            msWordMarkup: {
                                enabled: true
                            }
                        }
                    })
                }
            }
            e(this.editorDoc.body).css(this.options.css);
            return this
        };
        this.ui.removeHoverClass = function () {
            e(this).removeClass("wysiwyg-button-hover")
        };
        this.resetFunction = function () {
            this.setContent(this.initialContent)
        };
        this.saveContent = function () {
            if (this.original) {
                var t, n;
                t = this.getContent();
                if (this.options.rmUnwantedBr) {
                    t = t.replace(/<br\/?>$/, "")
                }
                t = TemplateEditor.stripNastyTagsFromString(t);
                if (this.options.replaceDivWithP) {
                    n = e("<div/>").addClass("temp").append(t);
                    n.children("div").each(function () {
                        var t = e(this),
                            n = t.find("p"),
                            r;
                        if (0 === n.length) {
                            n = e("<p></p>");
                            if (this.attributes.length > 0) {
                                for (r = 0; r < this.attributes.length; r += 1) {
                                    n.attr(this.attributes[r].name, t.attr(this.attributes[r].name))
                                }
                            }
                            n.append(t.html());
                            t.replaceWith(n)
                        }
                    });
                    t = n.html()
                }
                var r = false;
                if (true) {
                    n = e("<div/>").addClass("temp").append(t);
                    n.find("p, span, div, img").each(function () {
                        var t = e(this);
                        var n = e(t).css("align") || e(t).attr("align") || false;
                        var i = e(t).css("textAlign") || false;
                        var s = e(t).css("float") || false;
                        if (i && n) {
                            e(t).css({
                                textAlign: n,
                                align: ""
                            }).removeAttr("align");
                            r = true
                        }
                    });
                    t = n.html()
                }
                e(this.original).val(t);
                if (r) {
                    this.editorDoc.body.innerHTML = t
                }
                if (this.options.events && this.options.events.save) {
                    this.options.events.save.call(this)
                }
                e(this.editorDoc).trigger("editorRefresh.wysiwyg")
            }
            return this
        };
        this.setContent = function (e) {
            this.editorDoc.body.innerHTML = e;
            this.saveContent();
            return this
        };
        this.triggerControl = function (e, n) {
            var r = n.command || e,
                i = n["arguments"] || [];
            if (n.exec) {
                n.exec.apply(this)
            } else {
                this.ui.focus();
                this.ui.withoutCss();
                try {
                    this.editorDoc.execCommand(r, false, i)
                } catch (s) {
                    t.error(s)
                }
            }
            if (this.options.autoSave) {
                this.autoSaveFunction()
            }
        };
        this.triggerControlCallback = function (t) {
            e(window).trigger("trigger-" + t + ".wysiwyg", [this])
        };
        this.ui.withoutCss = function () {
            var t = this.self;
            if (e.browser.mozilla) {
                try {
                    t.editorDoc.execCommand("styleWithCSS", false, false)
                } catch (n) {
                    try {
                        t.editorDoc.execCommand("useCSS", false, true)
                    } catch (r) {}
                }
            }
            return t
        };
        this.wrapInitialContent = function () {
            var e = this.initialContent,
                t = e.match(/<\/?p>/gi);
            if (!t) {
                return "<p>" + e + "</p>"
            } else {}
            return e
        }
    }
    var t = window.console ? window.console : {
        log: e.noop,
        error: function (t) {
            e.error(t)
        }
    };
    var n = "prop" in e.fn && "removeProp" in e.fn;
    e.wysiwyg = {
        messages: {
            noObject: "Something goes wrong, check object"
        },
        addControl: function (t, n, r) {
            return t.each(function () {
                var t = e(this).data("wysiwyg"),
                    i = {}, s;
                if (!t) {
                    return this
                }
                i[n] = e.extend(true, {
                    visible: true,
                    custom: true
                }, r);
                e.extend(true, t.options.controls, i);
                s = e(t.options.toolbarHtml);
                t.ui.toolbar.replaceWith(s);
                t.ui.toolbar = s;
                t.ui.appendControls()
            })
        },
        clear: function (t) {
            return t.each(function () {
                var t = e(this).data("wysiwyg");
                if (!t) {
                    return this
                }
                t.setContent("")
            })
        },
        console: t,
        destroy: function (t) {
            return t.each(function () {
                var t = e(this).data("wysiwyg");
                if (!t) {
                    return this
                }
                t.destroy()
            })
        },
        preventDefault: function () {
            return false
        },
        launchImageSelector: function (t, n, r) {
            var i = t;
            var r = r || "";
            var s = e("#wysiwygContainer").css("width").replace(/[^0-9]+/g, "") - 50;
            if (r) {
                InsertImage.init(i, n, s, false, r)
            } else {
                InsertImage.init(i, n, s)
            }
        },
        launchAttachmentSelector: function (e, t) {
            var n = e;
            InsertAttachment.init(n, t)
        },
        document: function (t) {
            var n = t.data("wysiwyg");
            if (!n) {
                return undefined
            }
            return e(n.editorDoc)
        },
        getContent: function (e) {
            var t = e.data("wysiwyg");
            if (!t) {
                return undefined
            }
            return t.getContent()
        },
        init: function (t, n) {
            return t.each(function () {
                var t = e.extend(true, {}, n),
                    i;
                if ("textarea" !== this.nodeName.toLowerCase() || e(this).data("wysiwyg")) {
                    return
                }
                i = new r;
                i.init(this, t);
                e.data(this, "wysiwyg", i);
                e(i.editorDoc).trigger("afterInit.wysiwyg")
            })
        },
        insertHtml: function (t, n) {
            return t.each(function () {
                var t = e(this).data("wysiwyg");
                if (!t) {
                    return this
                }
                t.insertHtml(n)
            })
        },
        changeFontFace: function (t, n) {
            return t.each(function () {
                var t = e(this).data("wysiwyg");
                if (!t) {
                    return this
                }
                t.execChangeFontFace(n)
            })
        },
        plugin: {
            listeners: {},
            bind: function (t) {
                var n = this;
                e.each(this.listeners, function (r, i) {
                    var s, o;
                    for (s = 0; s < i.length; s += 1) {
                        o = n.parseName(i[s]);
                        e(t.editorDoc).bind(r + ".wysiwyg", {
                            plugin: o
                        }, function (n) {
                            e.wysiwyg[n.data.plugin.name][n.data.plugin.method].apply(e.wysiwyg[n.data.plugin.name], [t])
                        })
                    }
                })
            },
            exists: function (t) {
                var n;
                if ("string" !== typeof t) {
                    return false
                }
                n = this.parseName(t);
                if (!e.wysiwyg[n.name] || !e.wysiwyg[n.name][n.method]) {
                    return false
                }
                return true
            },
            listen: function (t, n) {
                var r;
                r = this.parseName(n);
                if (!e.wysiwyg[r.name] || !e.wysiwyg[r.name][r.method]) {
                    return false
                }
                if (!this.listeners[t]) {
                    this.listeners[t] = []
                }
                this.listeners[t].push(n);
                return true
            },
            parseName: function (e) {
                var t;
                if ("string" !== typeof e) {
                    return false
                }
                t = e.split(".");
                if (2 > t.length) {
                    return false
                }
                return {
                    name: t[0],
                    method: t[1]
                }
            },
            register: function (n) {
                if (!n.name) {
                    t.error("Plugin name missing")
                }
                e.each(e.wysiwyg, function (e) {
                    if (e === n.name) {
                        t.error("Plugin with name '" + n.name + "' was already registered")
                    }
                });
                e.wysiwyg[n.name] = n;
                return true
            }
        },
        removeFormat: function (t) {
            return t.each(function () {
                var t = e(this).data("wysiwyg");
                if (!t) {
                    return this
                }
                t.removeFormat()
            })
        },
        save: function (t) {
            return t.each(function () {
                var t = e(this).data("wysiwyg");
                if (!t) {
                    return this
                }
                t.saveContent()
            })
        },
        selectAll: function (e) {
            var t = e.data("wysiwyg"),
                n, r, i;
            if (!t) {
                return this
            }
            n = t.editorDoc.body;
            if (window.getSelection) {
                i = t.getInternalSelection();
                i.selectAllChildren(n)
            } else {
                r = n.createTextRange();
                r.moveToElementText(n);
                r.select()
            }
        },
        setContent: function (t, n) {
            return t.each(function () {
                var t = e(this).data("wysiwyg");
                if (!t) {
                    return this
                }
                t.setContent(n)
            })
        },
        triggerControl: function (n, r) {
            return n.each(function () {
                var n = e(this).data("wysiwyg");
                if (!n) {
                    return this
                }
                if (!n.controls[r]) {
                    t.error("Control '" + r + "' not exists")
                }
                n.triggerControl.apply(n, [r, n.controls[r]])
            })
        },
        support: {
            prop: n
        },
        utils: {
            extraSafeEntities: [
                ["<", ">", "'", '"', " "],
                [32]
            ],
            encodeEntities: function (t) {
                var n = this,
                    r, i = [];
                if (this.extraSafeEntities[1].length === 0) {
                    e.each(this.extraSafeEntities[0], function (e, t) {
                        n.extraSafeEntities[1].push(t.charCodeAt(0))
                    })
                }
                r = t.split("");
                e.each(r, function (t) {
                    var s = r[t].charCodeAt(0);
                    if (e.inArray(s, n.extraSafeEntities[1]) && (s < 65 || s > 127 || s > 90 && s < 97)) {
                        i.push("&#" + s + ";")
                    } else {
                        i.push(r[t])
                    }
                });
                return i.join("")
            }
        }
    };
    e.fn.wysiwyg = function (n) {
        var r = arguments,
            i;
        if ("undefined" !== typeof e.wysiwyg[n]) {
            r = Array.prototype.concat.call([r[0]], [this], Array.prototype.slice.call(r, 1));
            return e.wysiwyg[n].apply(e.wysiwyg, Array.prototype.slice.call(r, 1))
        } else if ("object" === typeof n || !n) {
            Array.prototype.unshift.call(r, this);
            return e.wysiwyg.init.apply(e.wysiwyg, r)
        } else if (e.wysiwyg.plugin.exists(n)) {
            i = e.wysiwyg.plugin.parseName(n);
            r = Array.prototype.concat.call([r[0]], [this], Array.prototype.slice.call(r, 1));
            return e.wysiwyg[i.name][i.method].apply(e.wysiwyg[i.name], Array.prototype.slice.call(r, 1))
        } else {
            t.error("Method '" + n + "' does not exist on jQuery.wysiwyg.\nTry to include some extra controls or plugins")
        }
    };
    e.fn.getWysiwyg = function () {
        return e(this).data("wysiwyg")
    }
})(jQuery)