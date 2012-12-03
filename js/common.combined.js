var KeyboardKey = {
    'A':65,
    'B':66,
    'C':67,
    'D':68,
    'E':69,
    'F':70,
    'G':71,
    'H':72,
    'I':73,
    'J':74,
    'K':75,
    'L':76,
    'M':77,
    'N':78,
    'O':79,
    'P':80,
    'Q':81,
    'R':82,
    'S':83,
    'T':84,
    'U':85,
    'V':86,
    'W':87,
    'X':88,
    'Y':89,
    'Z':90,
    'Minus':107,
    'Plus':109,
    '0':48,
    '1':49,
    '2':50,
    '3':51,
    '4':52,
    '5':53,
    '6':54,
    '7':55,
    '8':56,
    '9':57,
    'Enter':13,
    'PageUp':33,
    'PageDown':34,
    'End':35,
    'Insert':45,
    'Shift':16,
    'Delete':46,
    'Ctrl':17,
    'Esc':27,
    'Space':32,
    'Alt':18,
    'Backspace':8,
    'Tab':9,
    'ArrowLeft':37,
    'ArrowRight':39,
    'ArrowDown':40,
    'ArrowUp':38,
    'F1':112,
    'F2':113,
    'F3':114,
    'F4':115,
    'F5':116,
    'F6':117,
    'F7':118,
    'F8':119,
    'F9':120,
    'F10':121,
    'F11':122,
    'F12':123
};

(function (e) {
    e.fn.slides = function (t) {
        t = e.extend({}, e.fn.slides.option, t);
        return this.each(function () {
            function S(o, u, a) {
                if (!v && d) {
                    v = true;
                    t.animationStart(p + 1);
                    switch (o) {
                        case "next":
                            c = p;
                            l = p + 1;
                            l = i === l ? 0 : l;
                            g = s * 2;
                            o = -s * 2;
                            p = l;
                            break;
                        case "prev":
                            c = p;
                            l = p - 1;
                            l = l === -1 ? i - 1 : l;
                            g = 0;
                            o = 0;
                            p = l;
                            break;
                        case "pagination":
                            l = parseInt(a, 10);
                            c = e("." + t.paginationClass + " li." + t.currentClass + " a", n).attr("href").match("[^#/]+$");
                            if (l > c) {
                                g = s * 2;
                                o = -s * 2
                            } else {
                                g = 0;
                                o = 0
                            }
                            p = l;
                            break
                    }
                    if (u === "fade") {
                        if (t.crossfade) {
                            r.children(":eq(" + l + ")", n).css({
                                zIndex: 10
                            }).fadeIn(t.fadeSpeed, t.fadeEasing, function () {
                                if (t.autoHeight) {
                                    r.animate({
                                        height: r.children(":eq(" + l + ")", n).outerHeight()
                                    }, t.autoHeightSpeed, function () {
                                        r.children(":eq(" + c + ")", n).css({
                                            display: "none",
                                            zIndex: 0
                                        });
                                        r.children(":eq(" + l + ")", n).css({
                                            zIndex: 0
                                        });
                                        t.animationComplete(l + 1);
                                        v = false
                                    })
                                } else {
                                    r.children(":eq(" + c + ")", n).css({
                                        display: "none",
                                        zIndex: 0
                                    });
                                    r.children(":eq(" + l + ")", n).css({
                                        zIndex: 0
                                    });
                                    t.animationComplete(l + 1);
                                    v = false
                                }
                            })
                        } else {
                            r.children(":eq(" + c + ")", n).fadeOut(t.fadeSpeed, t.fadeEasing, function () {
                                if (t.autoHeight) {
                                    r.animate({
                                        height: r.children(":eq(" + l + ")", n).outerHeight()
                                    }, t.autoHeightSpeed, function () {
                                        r.children(":eq(" + l + ")", n).fadeIn(t.fadeSpeed, t.fadeEasing)
                                    })
                                } else {
                                    r.children(":eq(" + l + ")", n).fadeIn(t.fadeSpeed, t.fadeEasing, function () {
                                        if (e.browser.msie) {
                                            e(this).get(0).style.removeAttribute("filter")
                                        }
                                    })
                                }
                                t.animationComplete(l + 1);
                                v = false
                            })
                        }
                    } else {
                        r.children(":eq(" + l + ")").css({
                            left: g,
                            display: "block"
                        });
                        if (t.autoHeight) {
                            r.animate({
                                left: o,
                                height: r.children(":eq(" + l + ")").outerHeight()
                            }, t.slideSpeed, t.slideEasing, function () {
                                r.css({
                                    left: -s
                                });
                                r.children(":eq(" + l + ")").css({
                                    left: s,
                                    zIndex: 5
                                });
                                r.children(":eq(" + c + ")").css({
                                    left: s,
                                    display: "none",
                                    zIndex: 0
                                });
                                t.animationComplete(l + 1);
                                v = false
                            })
                        } else {
                            r.animate({
                                left: o
                            }, t.slideSpeed, t.slideEasing, function () {
                                r.css({
                                    left: -s
                                });
                                r.children(":eq(" + l + ")").css({
                                    left: s,
                                    zIndex: 5
                                });
                                r.children(":eq(" + c + ")").css({
                                    left: s,
                                    display: "none",
                                    zIndex: 0
                                });
                                t.animationComplete(l + 1);
                                v = false
                            })
                        }
                    }
                    if (t.pagination) {
                        e("." + t.paginationClass + " li." + t.currentClass, n).removeClass(t.currentClass);
                        e("." + t.paginationClass + " li:eq(" + l + ")", n).addClass(t.currentClass)
                    }
                }
            }
            function x() {
                clearInterval(n.data("interval"))
            }
            function T() {
                if (t.pause) {
                    clearTimeout(n.data("pause"));
                    clearInterval(n.data("interval"));
                    w = setTimeout(function () {
                        clearTimeout(n.data("pause"));
                        E = setInterval(function () {
                            S("next", a)
                        }, t.play);
                        n.data("interval", E)
                    }, t.pause);
                    n.data("pause", w)
                } else {
                    x()
                }
            }
            e("." + t.container, e(this)).children().wrapAll('<div class="slides_control"/>');
            var n = e(this),
                r = e(".slides_control", n),
                i = r.children().size(),
                s = r.children().outerWidth(),
                o = r.children().outerHeight(),
                u = t.start - 1,
                a = t.effect.indexOf(",") < 0 ? t.effect : t.effect.replace(" ", "").split(",")[0],
                f = t.effect.indexOf(",") < 0 ? a : t.effect.replace(" ", "").split(",")[1],
                l = 0,
                c = 0,
                h = 0,
                p = 0,
                d, v, m, g, y, b, w, E;
            if (i < 2) {
                e("." + t.container, e(this)).fadeIn(t.fadeSpeed, t.fadeEasing, function () {
                    d = true;
                    t.slidesLoaded()
                });
                e("." + t.next + ", ." + t.prev).fadeOut(0);
                return false
            }
            if (i < 2) {
                return
            }
            if (u < 0) {
                u = 0
            }
            if (u > i) {
                u = i - 1
            }
            if (t.start) {
                p = u
            }
            if (t.randomize) {
                r.randomize()
            }
            e("." + t.container, n).css({
                overflow: "hidden",
                position: "relative"
            });
            r.children().css({
                position: "absolute",
                top: 0,
                left: r.children().outerWidth(),
                zIndex: 0,
                display: "none"
            });
            r.css({
                position: "relative",
                width: s * 3,
                height: o,
                left: -s
            });
            e("." + t.container, n).css({
                display: "block"
            });
            if (t.autoHeight) {
                r.children().css({
                    height: "auto"
                });
                r.animate({
                    height: r.children(":eq(" + u + ")").outerHeight()
                }, t.autoHeightSpeed)
            }
            if (t.preload && r.find("img:eq(" + u + ")").length) {
                e("." + t.container, n).css({
                    background: "url(" + t.preloadImage + ") no-repeat 50% 50%"
                });
                var N = r.find("img:eq(" + u + ")").attr("src") + "?" + (new Date).getTime();
                if (e("img", n).parent().attr("class") != "slides_control") {
                    b = r.children(":eq(0)")[0].tagName.toLowerCase()
                } else {
                    b = r.find("img:eq(" + u + ")")
                }
                r.find("img:eq(" + u + ")").attr("src", N).load(function () {
                    r.find(b + ":eq(" + u + ")").fadeIn(t.fadeSpeed, t.fadeEasing, function () {
                        e(this).css({
                            zIndex: 5
                        });
                        e("." + t.container, n).css({
                            background: ""
                        });
                        d = true;
                        t.slidesLoaded()
                    })
                })
            } else {
                r.children(":eq(" + u + ")").fadeIn(t.fadeSpeed, t.fadeEasing, function () {
                    d = true;
                    t.slidesLoaded()
                })
            }
            if (t.bigTarget) {
                r.children().css({
                    cursor: "pointer"
                });
                r.children().click(function () {
                    S("next", a);
                    return false
                })
            }
            if (t.hoverPause && t.play) {
                r.bind("mouseover", function () {
                    x()
                });
                r.bind("mouseleave", function () {
                    T()
                })
            }
            if (t.generateNextPrev) {
                e("." + t.container, n).after('<a href="#" class="' + t.prev + '">Prev</a>');
                e("." + t.prev, n).after('<a href="#" class="' + t.next + '">Next</a>')
            }
            e("." + t.next, n).click(function (e) {
                e.preventDefault();
                if (t.play) {
                    T()
                }
                S("next", a)
            });
            e("." + t.prev, n).click(function (e) {
                e.preventDefault();
                if (t.play) {
                    T()
                }
                S("prev", a)
            });
            if (t.generatePagination) {
                if (t.prependPagination) {
                    n.prepend("<ul class=" + t.paginationClass + "></ul>")
                } else {
                    n.append("<ul class=" + t.paginationClass + "></ul>")
                }
                r.children().each(function () {
                    e("." + t.paginationClass, n).append('<li><a href="#' + h + '">' + (h + 1) + "</a></li>");
                    h++
                })
            } else {
                e("." + t.paginationClass + " li a", n).each(function () {
                    e(this).attr("href", "#" + h);
                    h++
                })
            }
            e("." + t.paginationClass + " li:eq(" + u + ")", n).addClass(t.currentClass);
            e("." + t.paginationClass + " li a", n).click(function () {
                if (t.play) {
                    T()
                }
                m = e(this).attr("href").match("[^#/]+$");
                if (p != m) {
                    S("pagination", f, m)
                }
                return false
            });
            e("a.link", n).click(function () {
                if (t.play) {
                    T()
                }
                m = e(this).attr("href").match("[^#/]+$") - 1;
                if (p != m) {
                    S("pagination", f, m)
                }
                return false
            });
            if (t.play) {
                E = setInterval(function () {
                    S("next", a)
                }, t.play);
                n.data("interval", E)
            }
        })
    };
    e.fn.slides.option = {
        preload: false,
        preloadImage: "/img/loading.gif",
        container: "slides_container",
        generateNextPrev: false,
        next: "next",
        prev: "prev",
        pagination: true,
        generatePagination: true,
        prependPagination: false,
        paginationClass: "pagination",
        currentClass: "sliderActual",
        fadeSpeed: 350,
        fadeEasing: "",
        slideSpeed: 350,
        slideEasing: "",
        start: 1,
        effect: "slide",
        crossfade: false,
        randomize: false,
        play: 0,
        pause: 0,
        hoverPause: false,
        autoHeight: false,
        autoHeightSpeed: 350,
        bigTarget: false,
        animationStart: function () {},
        animationComplete: function () {},
        slidesLoaded: function () {}
    };
    e.fn.randomize = function (t) {
        function n() {
            return Math.round(Math.random()) - .5
        }
        return e(this).each(function () {
            var r = e(this);
            var s = r.children();
            var o = s.length;
            if (o > 1) {
                s.hide();
                var u = [];
                for (i = 0; i < o; i++) {
                    u[u.length] = i
                }
                u = u.sort(n);
                e.each(u, function (e, n) {
                    var i = s.eq(n);
                    var o = i.clone(true);
                    o.show().appendTo(r);
                    if (t !== undefined) {
                        t(i, o)
                    }
                    i.remove()
                })
            }
        })
    }
})(jQuery);
jQuery.extend(jQuery.expr[":"], {
    hasBgColor: function (e) {
        var t = jQuery(e).css("background-color");
        return t != "" && t != "transparent" && t != "rgba(0, 0, 0, 0)"
    }
});
var CommonFunctions = {
    init: function () {},
    convertRgbStringToRgbArray: function (e) {
        e = e || false;
        var t = this,
            n = [0, 0, 0, 0],
            r = /rgba*\(\s*\d{1,3},\s*\d{1,3},\s*\d{1,3}(,\s*\d?.?\d{1,2}\s*)?\)/i,
            i;
        if (!e) {
            return n
        }
        if (e.match(/#[a-z0-9]{3,6}/gi)) {
            return t.hexToRgb(e)
        }
        if (e.match(r)) {
            i = e.replace(/[^,.0-9]+/ig, "").split(",");
            if (i.length > 2) {
                n = i
            }
        }
        return n
    },
    rgbToHex: function (e) {
        e = e || [0, 0, 0];
        var t = this,
            n = t.intToHex(e[0]),
            r = t.intToHex(e[1]),
            i = t.intToHex(e[2]),
            s = n + r + i;
        return s
    },
    intToHex: function (e) {
        e = parseInt(e, 10);
        if (isNaN(e)) {
            return "00"
        }
        e = Math.max(0, Math.min(e, 255));
        return "0123456789ABCDEF".charAt((e - e % 16) / 16) + "0123456789ABCDEF".charAt(e % 16)
    },
    getContrastYIQFromHex: function (e) {
        var t = this,
            n = t.hexToRgb(e);
        return t.getContrastYIQFromRGB(n)
    },
    getContrastYIQFromRGB: function (e) {
        e = e || [];
        var t;
        if (Object.prototype.toString.apply(e) !== "[object Array]") {
            e = e.replace(/[^,0-9]+/ig, "").split(",")
        }
        if (e.length < 3) {
            e = [0, 0, 0]
        }
        t = (e[0] * 299 + e[1] * 587 + e[2] * 114) / 1e3;
        return t >= 170 ? "#333333" : "#FFFFFF"
    },
    throttle: function (e, t) {
        var n = null;
        return function () {
            var r = this,
                i = arguments;
            clearTimeout(n);
            n = setTimeout(function () {
                e.apply(r, i)
            }, t)
        }
    }
};
var ComponenteFormularios = {
    init: function () {
        this.component_useEnvolpe = true;
        this.component_type = false;
        this.component_name = false;
        this.component_id = false;
        this.component_selectedValue = false;
        this.component_title = false;
        this.component_label = false;
        this.component_labelExplanation = false;
        this.component_controlsGroup_cssClass = false;
        this.component_cssClass = false;
        this.component_cssStyle = false;
        this.component_cssPortaAddClass = false;
        this.component_inputPrepend = false;
        this.component_inputAppend = false;
        this.controlGroup_customAttributes = {};
        this.component_customAttributes = {};
        this.component_optionsList = [];
        this.component_buttonsList = [];
        this.component_extraContent = false;
        this.component_customCombo_extraContent = false;
        this.component_customCombo_addClass = false;
        this.component_customCombo_optionsMulti = "disabled";
        this.component_customCombo_optionsAdd = "disabled";
        this.component_customCombo_optionsDelete = "disabled";
        this.component_customCombo_optionsEdit = "disabled";
        this.component_customCombo_optionsMove = "disabled";
        this.component_customCombo_confirmBlock = "disabled";
        this.component_customCombo_addOption_inputList = [];
        this.component_customCombo_customActionTools = [];
        if (typeof this.component_termTranslations === "undefined") {
            this.component_termTranslations = {
                select: __("Seleccionar"),
                editionInCourse: __("Edición en curso"),
                applyChanges: __("Aplicar"),
                cancelChanges: __("Cancelar"),
                addNewOption: __("Crear Nuevo"),
                addOptionApply: __("Agregar"),
                addOptionCancel: __("Cancelar")
            }
        }
        return this
    },
    setComponent_termTranslation: function (e, t) {
        var n = this;
        e = e || false;
        t = t || false;
        if (e && t && n.component_termTranslations.hasOwnProperty(e)) {
            n.component_termTranslations[e] = t
        }
        return n
    },
    setComponent_useEnvolpe: function (e) {
        var t = this;
        t.component_useEnvolpe = e === false ? false : true;
        return t
    },
    setComponent_type: function (e) {
        var t = this;
        t.component_type = e || false;
        return t
    },
    setComponent_name: function (e) {
        var t = this;
        t.component_name = e || false;
        return t
    },
    setComponent_id: function (e) {
        var t = this;
        t.component_id = e || false;
        return t
    },
    setComponent_selectedValue: function (e) {
        var t = this;
        t.component_selectedValue = e || false;
        return t
    },
    setComponent_title: function (e) {
        var t = this;
        t.component_title = e || false;
        return t
    },
    setComponent_label: function (e) {
        var t = this;
        t.component_label = e || false;
        return t
    },
    setComponent_labelExplanation: function (e) {
        var t = this;
        t.component_labelExplanation = e || false;
        return t
    },
    setComponent_controlsGroup_cssClass: function (e) {
        var t = this;
        t.component_controlsGroup_cssClass = e || false;
        return t
    },
    setComponent_cssClass: function (e) {
        var t = this;
        t.component_cssClass = e || false;
        return t
    },
    setComponent_cssStyle: function (e) {
        var t = this;
        t.component_cssStyle = e || false;
        return t
    },
    addComponentPortaCssClass: function (e) {
        var t = this;
        t.component_cssPortaAddClass = e || false;
        return t
    },
    addComponentInputPrepend: function (e) {
        var t = this;
        t.component_inputPrepend = e || false;
        return t
    },
    addComponentInputAppend: function (e) {
        var t = this;
        t.component_inputAppend = e || false;
        return t
    },
    setComponent_controlGroup_customAttributes: function (e) {
        var t = this;
        t.component_controlGroup_customAttributes = e || {};
        return t
    },
    setComponent_customAttributes: function (e) {
        var t = this;
        t.component_customAttributes = e || {};
        return t
    },
    setComponent_optionsList: function (e) {
        var t = this;
        t.component_optionsList = e || [];
        return t
    },
    setComponent_buttonsList: function (e) {
        var t = this;
        t.component_buttonsList = e || [];
        return t
    },
    setComponent_extraContent: function (e) {
        var t = this;
        t.component_extraContent = e || false;
        return t
    },
    setComponent_customCombo_extraContent: function (e) {
        var t = this;
        t.component_customCombo_extraContent = e || false;
        return t
    },
    setComponent_customComboCssClass: function (e) {
        var t = this;
        t.component_customCombo_addClass = e || false;
        return t
    },
    setComponent_customCombo_optionsMulti: function (e) {
        var t = this;
        t.component_customCombo_optionsMulti = e && e === "enabled" ? "enabled" : "disabled";
        if (t.component_customCombo_optionsMulti === "enabled") {
            t.component_customCombo_confirmBlock = "enabled"
        }
        return t
    },
    setComponent_customCombo_optionsAdd: function (e) {
        var t = this;
        t.component_customCombo_optionsAdd = e && e === "enabled" ? "enabled" : "disabled";
        return t
    },
    setComponent_customCombo_optionsDelete: function (e) {
        var t = this;
        t.component_customCombo_optionsDelete = e && e === "enabled" ? "enabled" : "disabled";
        return t
    },
    setComponent_customCombo_optionsEdit: function (e) {
        var t = this;
        t.component_customCombo_optionsEdit = e && e === "enabled" ? "enabled" : "disabled";
        if (t.component_customCombo_optionsEdit === "enabled") {
            t.component_customCombo_confirmBlock = "enabled"
        }
        return t
    },
    setComponent_customCombo_optionsMove: function (e) {
        var t = this;
        t.component_customCombo_optionsMove = e && e === "enabled" ? "enabled" : "disabled";
        return t
    },
    setComponent_customCombo_confirmBlock: function (e) {
        var t = this;
        t.component_customCombo_confirmBlock = e && e === "enabled" ? "enabled" : "disabled";
        return t
    },
    setComponent_customCombo_addOption_inputList: function (e) {
        var t = this;
        t.component_customCombo_addOption_inputList = e || [];
        return t
    },
    setComponent_customCombo_customActionTools: function (e) {
        var t = this;
        t.component_customCombo_customActionTools = e || [];
        return t
    },
    getComponent: function () {
        var e = this,
            t = [],
            n = e.component_cssPortaAddClass || "",
            r = e.component_controlsGroup_cssClass || "",
            i = [],
            s, o = {}, u = [];
        if (typeof e.component_type === "undefined" || e.component_type === false) {
            return false
        }
        if (e.component_useEnvolpe) {
            i = [];
            if (typeof e.component_controlGroup_customAttributes !== "undefined" && !jQuery.isEmptyObject(e.component_controlGroup_customAttributes)) {
                for (s in e.component_controlGroup_customAttributes) {
                    if (e.component_controlGroup_customAttributes.hasOwnProperty(s)) {
                        i.push(" " + s + '="' + e.component_controlGroup_customAttributes[s] + '"')
                    }
                }
            }
            t.push('<div class="' + (e.component_type === "buttons" ? "form-actions" : "control-group") + " component-type-" + e.component_type + " " + r + '" data-componentType="' + e.component_type + '"  data-componentName="' + e.component_name + '" ' + i.join("") + ">");
            if (e.component_label) {
                t.push('<label class="control-label">');
                t.push(e.component_label);
                t.push("</label>")
            }
        }
        u = [];
        if (typeof e.component_customAttributes !== "undefined" && !jQuery.isEmptyObject(e.component_customAttributes)) {
            for (s in e.component_customAttributes) {
                if (e.component_customAttributes.hasOwnProperty(s)) {
                    u.push(" " + s + '="' + e.component_customAttributes[s] + '"')
                }
            }
        }
        o = {
            customAttributes: u.join(""),
            cssClass: e.component_cssClass ? ' class="' + e.component_cssClass + '"' : "",
            cssStyle: e.component_cssStyle ? ' style="' + e.component_cssStyle + '"' : "",
            name: e.component_name ? ' name="' + e.component_name + '"' : "",
            dataName: e.component_name ? ' data-name="' + e.component_name + '"' : "",
            id: e.component_id ? ' id="' + e.component_id + '"' : "",
            title: e.component_title ? ' title="' + e.component_title + '"' : "",
            multipleSelectOptions: e.component_type === "selectMultiple" ? ' multiple data-multipleSelection="enabled" ' : "",
            customCombo_addClass: e.component_customCombo_addClass || "",
            customCombo_optionsMulti: e.component_customCombo_optionsMulti === "enabled" ? ' data-optionsmulti="enabled"' : "",
            customCombo_optionsAdd: e.component_customCombo_optionsAdd === "enabled" ? ' data-optionsadd="enabled"' : "",
            customCombo_optionsDelete: e.component_customCombo_optionsDelete === "enabled" ? ' data-optionsdelete="enabled"' : "",
            customCombo_confirmBlock: e.component_customCombo_confirmBlock === "enabled" ? ' data-confirmblock="enabled"' : "",
            customCombo_dropDownLabel: e.component_title || e.component_termTranslations.select
        };
        if (e.component_type === "buttons") {
            r = ""
        } else if (e.component_type === "checkboxSet" || e.component_type === "radioSet") {
            r = "controls porta_checkbox_radio " + r
        } else {
            r = "controls " + r
        }
        t.push('<div class="' + r + '">');
        switch (e.component_type) {
            case "customCombo":
                jQuery.merge(t, e.prepareContent_customCombo(o));
                break;
            case "select":
            case "selectMultiple":
                jQuery.merge(t, e.prepareContent_select(o));
                break;
            case "checkboxSet":
            case "radioSet":
                o.cssClass = e.component_cssClass ? ' class="checkboxRadioContainer ' + e.component_cssClass + '"' : ' class="checkboxRadioContainer"';
                jQuery.merge(t, e.prepareContent_checkboxRadioList(o));
                break;
            case "buttons-checkbox":
            case "buttons-radio":
                o.cssClass = e.component_cssClass ? ' class="btn-group' + e.component_cssClass + '"' : ' class="btn-group"';
                jQuery.merge(t, e.prepareContent_checkboxRadioButtons(o));
                break;
            case "inputText":
            case "inputNumber":
            case "inputEmail":
                jQuery.merge(t, e.prepareContent_inputText(o));
                break;
            case "inputPassword":
                jQuery.merge(t, e.prepareContent_inputPassword(o));
                break;
            case "textarea":
                jQuery.merge(t, e.prepareContent_textarea(o));
                break;
            case "buttons":
                jQuery.merge(t, e.prepareContent_buttons(o));
                break;
            default:
                break
        }
        if (e.component_labelExplanation) {
            t.push('<span class="help-block">');
            t.push(e.component_labelExplanation);
            t.push("</span>")
        }
        t.push("</div>");
        if (e.component_useEnvolpe) {
            t.push('<div class="comboBlock-customContent">');
            if (e.component_extraContent) {
                t.push(e.component_extraContent)
            }
            t.push("</div>");
            t.push("</div>")
        }
        e.reset();
        return t.join("")
    },
    prepareContent_customCombo: function (e) {
        e = e || {};
        var t = this,
            n = [],
            r, i, s, o, u, a = [],
            f = "",
            l = "",
            c, h = [],
            p = "",
            d = e.customAttributes || "",
            v = e.dataName || "",
            m = e.customCombo_addClass || "",
            g = e.customCombo_dropDownLabel || "",
            y = e.customCombo_optionsMulti || "",
            b = e.customCombo_optionsAdd || "",
            w = e.customCombo_optionsDelete || "",
            E = e.customCombo_confirmBlock || "";
        n.push('<div class="customComboComponent"' + v + ">");
        n.push('<div class="dropDownBlock"' + v + ">");
        n.push('<span class="dropDownLabel" data-defaulttext="' + g + '">' + g + "</span>");
        n.push('<span class="dropDownIcon"></span>');
        n.push('<div class="dropDownSpinner"></div>');
        n.push("</div>");
        n.push('<div class="comboBlock-container ' + m + '"' + v + y + b + w + E + d + ">");
        n.push('<div class="comboBlock-close">x</div>');
        n.push('<div class="comboBlock-title">' + g + "</div>");
        n.push('<ul class="comboBlock">');
        for (i in t.component_optionsList) {
            if (t.component_optionsList.hasOwnProperty(i)) {
                s = t.component_optionsList[i];
                o = t.customComboCreateOptionLi(s);
                n.push(o.join("\n"))
            }
        }
        n.push("</ul>");
        n.push('<div class="comboBlock-tools">');
        n.push('<div class="actionsList">');
        n.push('<div class="comboBlock-actionTool cancelChanges btn btn-small" data-toolaction="cancelChanges">' + t.component_termTranslations.cancelChanges + "</div>");
        n.push('<div class="comboBlock-actionTool applyChanges btn btn-small btn-primary" data-toolaction="applyChanges">' + t.component_termTranslations.applyChanges + "</div>");
        n.push('<div class="comboBlock-actionTool addNewOption btn btn-small" data-toolAction="addNewOption">' + t.component_termTranslations.addNewOption + "</div>");
        for (i in t.component_customCombo_customActionTools) {
            if (t.component_customCombo_customActionTools.hasOwnProperty(i)) {
                u = t.component_customCombo_customActionTools[i];
                a = [];
                f = "";
                l = "comboBlock-actionTool customTool";
                for (r in u) {
                    if (u.hasOwnProperty(r)) {
                        if (r === "text") {
                            f = u[r]
                        } else if (r === "class") {
                            l = "comboBlock-actionTool customTool " + u[r]
                        } else {
                            a.push(" " + r + '="' + u[r] + '"')
                        }
                    }
                }
                n.push('<div class="' + l + '" ' + a.join("") + ">" + f + "</div>")
            }
        }
        n.push("</div>");
        n.push('<div class="addOption">');
        for (i in t.component_customCombo_addOption_inputList) {
            if (t.component_customCombo_addOption_inputList.hasOwnProperty(i)) {
                c = t.component_customCombo_addOption_inputList[i];
                h = [];
                p = "";
                n.push('<span class="inputContainer">');
                for (r in c) {
                    if (c.hasOwnProperty(r)) {
                        if (r === "text") {
                            p = c[r]
                        } else {
                            h.push(" " + r + '="' + c[r] + '"')
                        }
                    }
                }
                n.push("<label>" + p + "</label>");
                n.push("<input " + h.join("") + ' type="text" />');
                n.push("</span>")
            }
        }
        n.push('<span class="button cancel btn btn-small"  data-toolaction="addOptionCancel">' + t.component_termTranslations.addOptionCancel + "</span>");
        n.push('<span class="button apply btn-small btn-primary"  data-toolaction="addOptionApply">' + t.component_termTranslations.addOptionApply + "</span>");
        n.push('<div class="throbberClean"></div>');
        n.push("</div>");
        n.push('<div class="comboBlock-customContent comboBlock-customComboExtraContent">');
        if (t.component_customCombo_extraContent) {
            n.push(t.component_customCombo_extraContent)
        }
        n.push("</div>");
        n.push("</div>");
        n.push("</div>");
        n.push("</div>");
        return n
    },
    prepareContent_select: function (e) {
        e = e || {};
        var t = this,
            n = [],
            r, i, s, o = "",
            u = [],
            a = e.customAttributes || "",
            f = e.cssClass || "",
            l = e.cssStyle || "",
            c = e.name || "",
            h = e.id || "",
            p = e.title || "",
            d = e.multipleSelectOptions || "";
        n.push("<select " + c + h + p + f + l + a + d + ">");
        if (typeof t.component_optionsList !== "undefined" && Object.prototype.toString.apply(t.component_optionsList) === "[object Array]") {
            for (i in t.component_optionsList) {
                if (t.component_optionsList.hasOwnProperty(i)) {
                    s = t.component_optionsList[i];
                    o = "";
                    u = [];
                    for (r in s) {
                        if (s.hasOwnProperty(r) && r !== "text" && r !== "parentLiCustomAttributes") {
                            if (r === "value" && t.isSelectedValue(s[r])) {
                                o = ' selected="selected"'
                            }
                            u.push(" " + r + '="' + s[r] + '"')
                        }
                    }
                    n.push("<option " + u.join("") + o + ">");
                    n.push(s.text);
                    n.push("</option>")
                }
            }
        }
        n.push("</select>");
        return n
    },
    prepareContent_checkboxRadioList: function (e) {
        e = e || {};
        var t = this,
            n = [],
            r, i, s, o = "",
            u = [],
            a = e.customAttributes || "",
            f = e.cssClass || "",
            l = e.cssStyle || "",
            c = e.name || "",
            h = e.id || "",
            p = e.title || "";
        n.push("<span " + c + h + p + f + l + a + ">");
        if (typeof t.component_optionsList !== "undefined" && Object.prototype.toString.apply(t.component_optionsList) === "[object Array]") {
            for (i in t.component_optionsList) {
                if (t.component_optionsList.hasOwnProperty(i)) {
                    s = t.component_optionsList[i];
                    o = "";
                    u = [];
                    for (r in s) {
                        if (s.hasOwnProperty(r) && r !== "text" && r !== "parentLiCustomAttributes") {
                            if (r === "value" && t.isSelectedValue(s[r])) {
                                o = ' checked="checked" '
                            }
                            u.push(" " + r + '="' + s[r] + '"')
                        }
                    }
                    u.push(' type="' + (t.component_type === "checkboxSet" ? "checkbox" : "radio") + '" ');
                    u.push(' name="' + (t.component_type === "checkboxSet" ? t.component_name + "[]" : t.component_name) + '" ');
                    u.push(' data-name="' + t.component_name + '"');
                    n.push('<label class="' + (t.component_type === "checkboxSet" ? "checkbox" : "radio") + '" >');
                    n.push("<input " + u.join("") + o + "/>");
                    n.push("<span>" + s.text + "</span>");
                    n.push("</label>")
                }
            }
        }
        n.push("</span>");
        return n
    },
    prepareContent_checkboxRadioButtons: function (e) {
        e = e || {};
        var t = this,
            n = [],
            r, i, s, o = "",
            u = [],
            a = e.customAttributes || "",
            f = e.cssClass || "",
            l = e.cssStyle || "",
            c = e.name || "",
            h = e.id || "",
            p = e.title || "";
        n.push("<div " + c + h + p + f + l + a + ' data-toggle="' + t.component_type + '">');
        if (typeof t.component_optionsList !== "undefined" && Object.prototype.toString.apply(t.component_optionsList) === "[object Array]") {
            for (i in t.component_optionsList) {
                if (t.component_optionsList.hasOwnProperty(i)) {
                    s = t.component_optionsList[i];
                    o = false;
                    u = [];
                    for (r in s) {
                        if (s.hasOwnProperty(r) && r !== "text" && r !== "parentLiCustomAttributes" && r !== "class") {
                            if (r === "data-value" && t.isSelectedValue(s[r])) {
                                o = true
                            }
                            u.push(" " + r + '="' + s[r] + '"')
                        }
                    }
                    u.push(' data-name="' + t.component_name + '"');
                    n.push('<button type="button" class="' + (o ? "btn active" : "btn") + '" ' + u.join("") + ">");
                    n.push(s.text);
                    n.push("</button>")
                }
            }
        }
        n.push("</div>");
        return n
    },
    prepareContent_inputText: function (e) {
        e = e || {};
        var t = this,
            n = [],
            r, i = e.customAttributes || "",
            s = e.cssClass || "",
            o = e.cssStyle || "",
            u = e.name || "",
            a = e.id || "",
            f = e.title || "";
        switch (t.component_type) {
            case "inputNumber":
                r = "number";
                break;
            case "inputEmail":
                r = "email";
                break;
            default:
                r = "text";
                break
        }
        if (t.component_inputPrepend && t.component_inputAppend) {
            n.push('<div class="input-prepend input-append">');
            n.push(t.component_inputPrepend)
        } else if (t.component_inputPrepend) {
            n.push('<div class="input-prepend">');
            n.push(t.component_inputPrepend)
        } else if (t.component_inputAppend) {
            n.push('<div class="input-append">')
        } else {
            n.push("<div>")
        }
        n.push('<input type="' + r + '" value="' + (t.component_selectedValue !== false ? t.component_selectedValue : "") + '" ' + u + a + f + s + o + i + "/>");
        if (t.component_inputAppend) {
            n.push(t.component_inputAppend)
        }
        n.push("</div>");
        return n
    },
    prepareContent_inputPassword: function (e) {
        e = e || {};
        var t = this,
            n = [],
            r = e.customAttributes || "",
            i = e.cssClass || "",
            s = e.cssStyle || "",
            o = e.name || "",
            u = e.id || "",
            a = e.title || "";
        n.push('<input type="password" value="' + (t.component_selectedValue !== false ? t.component_selectedValue : "") + '" ' + o + u + a + i + s + r + "/>");
        return n
    },
    prepareContent_textarea: function (e) {
        e = e || {};
        var t = this,
            n = [],
            r = e.customAttributes || "",
            i = e.cssClass || "",
            s = e.cssStyle || "",
            o = e.name || "",
            u = e.id || "",
            a = e.title || "";
        n.push("<textarea " + o + u + a + i + s + r + ">");
        n.push(t.component_selectedValue !== false ? t.component_selectedValue : "");
        n.push("</textarea>");
        return n
    },
    prepareContent_buttons: function (e) {
        e = e || {};
        var t = this,
            n = [],
            r, i, s, o = [];
        if (typeof t.component_buttonsList !== "undefined" && Object.prototype.toString.apply(t.component_buttonsList) === "[object Array]") {
            for (i in t.component_buttonsList) {
                if (t.component_buttonsList.hasOwnProperty(i)) {
                    s = t.component_buttonsList[i];
                    o = [];
                    for (r in s) {
                        if (s.hasOwnProperty(r) && r !== "text") {
                            o.push(" " + r + '="' + s[r] + '"')
                        }
                    }
                    o = o.join("");
                    n.push("<button " + o + ">");
                    n.push(s.text);
                    n.push("</button>")
                }
            }
        }
        return n
    },
    customComboCreateOptionLi: function (e, t) {
        e = e || false;
        t = t || false;
        var n = this,
            r = [],
            i, s = "",
            o = false,
            u = "",
            a = false,
            f = "comboBlock-item",
            l, c, h = [],
            p = "",
            d = [],
            v, m, g = false,
            y = "";
        if (e) {
            i = e;
            l = n.component_customCombo_optionsMulti === "enabled" ? "checkbox" : "radio";
            c = n.component_customCombo_optionsMulti === "enabled" ? n.component_name + "[]" : n.component_name;
            for (v in i) {
                if (i.hasOwnProperty(v) && v !== "text") {
                    if (v === "value") {
                        if (n.isSelectedValue(i[v])) {
                            s = ' checked="checked" ';
                            f = "comboBlock-item selected"
                        }
                        p = i[v]
                    }
                    if (v === "parentLiCustomAttributes") {
                        for (m in i[v]) {
                            if (i[v].hasOwnProperty(m)) {
                                if (m === "class") {
                                    u = i[v][m];
                                    f += " " + i[v][m]
                                } else if (m === "isOptionGroup") {
                                    o = true
                                } else {
                                    h.push(m + '="' + i[v][m] + '"')
                                }
                            }
                        }
                    } else if (v === "hasIcon") {
                        a = i[v];
                        d.push(' data-hasIcon="true"');
                        h.push(' data-hasIcon="true"')
                    } else if (v === "name") {
                        c = i[v]
                    } else {
                        d.push(" " + v + '="' + i[v] + '"')
                    }
                }
            }
            if (o) {
                f = "comboBlock-optgroup " + u
            }
            r.push('<li class="' + f + '" ' + h.join(" ") + ' data-optionValue="' + p + '">');
            r.push('<span class="editionActiveWarning">' + n.component_termTranslations.editionInCourse + "</span>");
            r.push('<span class="move"></span>');
            r.push("<label>");
            g = false;
            y = "";
            if (a) {
                g = a.url || false;
                y = a["class"] || ""
            }
            r.push('<span class="optionIcon ' + y + '">');
            if (g) {
                r.push('<img src="' + g + '" alt="' + i.text + '"/>')
            }
            r.push("</span>");
            if (!o) {
                r.push('<input type="' + l + '" name="' + c + '" ' + s + d.join("") + "/>")
            }
            r.push("<span>" + i.text + "</span>");
            r.push("</label>");
            if (t) {
                r.push(t)
            }
            r.push('<span class="optionEdit"></span>');
            r.push('<span class="trashCan" title="' + __("Eliminar") + '"></span>');
            r.push("</li>")
        }
        return r
    },
    isSelectedValue: function (e) {
        e = e || false;
        if (!e) {
            return false
        }
        var t = this,
            n = e.toString(),
            r = [],
            i, s = false;
        if (Object.prototype.toString.apply(t.component_selectedValue) === "[object Array]") {
            for (i in t.component_selectedValue) {
                if (t.component_selectedValue.hasOwnProperty(i)) {
                    r.push(t.component_selectedValue[i].toString())
                }
            }
            if (jQuery.inArray(e, t.component_selectedValue) > -1 || jQuery.inArray(e, r) > -1 || jQuery.inArray(n, t.component_selectedValue) > -1 || jQuery.inArray(n, r) > -1) {
                s = true
            }
            if (s) {
                return true
            }
        } else if (e === t.component_selectedValue || n === t.component_selectedValue) {
            return true
        }
        return false
    },
    reset: function () {
        var e = this;
        e.init()
    }
};
jQuery(function () {
    ComponenteFormularios.init()
})