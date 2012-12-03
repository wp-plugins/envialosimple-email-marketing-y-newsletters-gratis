! function (e) {
    "use strict";
    var t = function (e, t) {
        this.init("tooltip", e, t)
    };
    t.prototype = {
        constructor: t,
        init: function (t, n, r) {
            var i, s;
            this.type = t;
            this.jQueryelement = e(n);
            this.options = this.getOptions(r);
            this.enabled = true;
            if (this.options.trigger == "click") {
                this.jQueryelement.on("click." + this.type, this.options.selector, e.proxy(this.toggle, this))
            } else if (this.options.trigger != "manual") {
                i = this.options.trigger == "hover" ? "mouseenter" : "focus";
                s = this.options.trigger == "hover" ? "mouseleave" : "blur";
                this.jQueryelement.on(i + "." + this.type, this.options.selector, e.proxy(this.enter, this));
                this.jQueryelement.on(s + "." + this.type, this.options.selector, e.proxy(this.leave, this))
            }
            this.options.selector ? this._options = e.extend({}, this.options, {
                trigger: "manual",
                selector: ""
            }) : this.fixTitle()
        },
        getOptions: function (t) {
            t = e.extend({}, e.fn[this.type].defaults, t, this.jQueryelement.data());
            if (t.delay && typeof t.delay == "number") {
                t.delay = {
                    show: t.delay,
                    hide: t.delay
                }
            }
            return t
        },
        enter: function (t) {
            var n = e(t.currentTarget)[this.type](this._options).data(this.type);
            if (!n.options.delay || !n.options.delay.show) return n.show();
            clearTimeout(this.timeout);
            n.hoverState = "in";
            this.timeout = setTimeout(function () {
                if (n.hoverState == "in") n.show()
            }, n.options.delay.show)
        },
        leave: function (t) {
            var n = e(t.currentTarget)[this.type](this._options).data(this.type);
            if (this.timeout) clearTimeout(this.timeout);
            if (!n.options.delay || !n.options.delay.hide) return n.hide();
            n.hoverState = "out";
            this.timeout = setTimeout(function () {
                if (n.hoverState == "out") n.hide()
            }, n.options.delay.hide)
        },
        show: function () {
            var e, t, n, r, i, s, o;
            if (this.hasContent() && this.enabled) {
                e = this.tip();
                this.setContent();
                if (this.options.animation) {
                    e.addClass("fade")
                }
                s = typeof this.options.placement == "function" ? this.options.placement.call(this, e[0], this.jQueryelement[0]) : this.options.placement;
                t = /in/.test(s);
                e.remove().css({
                    top: 0,
                    left: 0,
                    display: "block"
                }).appendTo(t ? this.jQueryelement : document.body);
                n = this.getPosition(t);
                r = e[0].offsetWidth;
                i = e[0].offsetHeight;
                switch (t ? s.split(" ")[1] : s) {
                    case "bottom":
                        o = {
                            top: n.top + n.height,
                            left: n.left + n.width / 2 - r / 2
                        };
                        break;
                    case "top":
                        o = {
                            top: n.top - i,
                            left: n.left + n.width / 2 - r / 2
                        };
                        break;
                    case "left":
                        o = {
                            top: n.top + n.height / 2 - i / 2,
                            left: n.left - r
                        };
                        break;
                    case "right":
                        o = {
                            top: n.top + n.height / 2 - i / 2,
                            left: n.left + n.width
                        };
                        break
                }
                e.css(o).addClass(s).addClass("in")
            }
        },
        setContent: function () {
            var e = this.tip(),
                t = this.getTitle();
            e.find(".tooltip-inner")[this.options.html ? "html" : "text"](t);
            e.removeClass("fade in top bottom left right")
        },
        hide: function () {
            function r() {
                var t = setTimeout(function () {
                    n.off(e.support.transition.end).remove()
                }, 500);
                n.one(e.support.transition.end, function () {
                    clearTimeout(t);
                    n.remove()
                })
            }
            var t = this,
                n = this.tip();
            n.removeClass("in");
            e.support.transition && this.jQuerytip.hasClass("fade") ? r() : n.remove();
            return this
        },
        fixTitle: function () {
            var e = this.jQueryelement;
            if (e.attr("title") || typeof e.attr("data-original-title") != "string") {
                e.attr("data-original-title", e.attr("title") || "").removeAttr("title")
            }
        },
        hasContent: function () {
            return this.getTitle()
        },
        getPosition: function (t) {
            return e.extend({}, t ? {
                top: 0,
                left: 0
            } : this.jQueryelement.offset(), {
                width: this.jQueryelement[0].offsetWidth,
                height: this.jQueryelement[0].offsetHeight
            })
        },
        getTitle: function () {
            var e, t = this.jQueryelement,
                n = this.options;
            e = t.attr("data-original-title") || (typeof n.title == "function" ? n.title.call(t[0]) : n.title);
            return e
        },
        tip: function () {
            return this.jQuerytip = this.jQuerytip || e(this.options.template)
        },
        validate: function () {
            if (!this.jQueryelement[0].parentNode) {
                this.hide();
                this.jQueryelement = null;
                this.options = null
            }
        },
        enable: function () {
            this.enabled = true
        },
        disable: function () {
            this.enabled = false
        },
        toggleEnabled: function () {
            this.enabled = !this.enabled
        },
        toggle: function () {
            this[this.tip().hasClass("in") ? "hide" : "show"]()
        },
        destroy: function () {
            this.hide().jQueryelement.off("." + this.type).removeData(this.type)
        }
    };
    e.fn.tooltip = function (n) {
        return this.each(function () {
            var r = e(this),
                i = r.data("tooltip"),
                s = typeof n == "object" && n;
            if (!i) r.data("tooltip", i = new t(this, s));
            if (typeof n == "string") i[n]()
        })
    };
    e.fn.tooltip.Constructor = t;
    e.fn.tooltip.defaults = {
        animation: true,
        placement: "top",
        selector: false,
        template: '<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
        trigger: "hover",
        title: "",
        delay: 0,
        html: true
    }
}(window.jQuery);
! function (e) {
    "use strict";
    var t = function (e, t) {
        this.init("popover", e, t)
    };
    t.prototype = e.extend({}, e.fn.tooltip.Constructor.prototype, {
        constructor: t,
        setContent: function () {
            var e = this.tip(),
                t = this.getTitle(),
                n = this.getContent();
            e.find(".popover-title")[this.options.html ? "html" : "text"](t);
            e.find(".popover-content > *")[this.options.html ? "html" : "text"](n);
            e.removeClass("fade top bottom left right in")
        },
        hasContent: function () {
            return this.getTitle() || this.getContent()
        },
        getContent: function () {
            var e, t = this.jQueryelement,
                n = this.options;
            e = t.attr("data-content") || (typeof n.content == "function" ? n.content.call(t[0]) : n.content);
            return e
        },
        tip: function () {
            if (!this.jQuerytip) {
                this.jQuerytip = e(this.options.template)
            }
            return this.jQuerytip
        },
        destroy: function () {
            this.hide().jQueryelement.off("." + this.type).removeData(this.type)
        }
    });
    e.fn.popover = function (n) {
        return this.each(function () {
            var r = e(this),
                i = r.data("popover"),
                s = typeof n == "object" && n;
            if (!i) r.data("popover", i = new t(this, s));
            if (typeof n == "string") i[n]()
        })
    };
    e.fn.popover.Constructor = t;
    e.fn.popover.defaults = e.extend({}, e.fn.tooltip.defaults, {
        placement: "right",
        trigger: "click",
        content: "",
        template: '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
    })
}(window.jQuery)