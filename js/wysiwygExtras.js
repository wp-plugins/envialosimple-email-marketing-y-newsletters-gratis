(function (e) {
    if (undefined === e.wysiwyg) {
        throw "wysiwyg.colorpicker.js depends on jQuery.wysiwyg"
    }
    if (!e.wysiwyg.controls) {
        e.wysiwyg.controls = {}
    }
    e.wysiwyg.controls.colorpicker = {
        modalOpen: false,
        color: {
            back: {
                prev: "#ffffff",
                palette: []
            },
            fore: {
                prev: "#123456",
                palette: []
            }
        },
        addColorToPalette: function (t, n) {
            if (-1 === e.inArray(n, this.color[t].palette)) {
                this.color[t].palette.push(n)
            } else {
                this.color[t].palette.sort(function (e, t) {
                    if (e === n) {
                        return 1
                    }
                    return 0
                })
            }
        },
        init: function (t, n) {
            if (e.wysiwyg.controls.colorpicker.modalOpen === true) {
                return false
            } else {
                e.wysiwyg.controls.colorpicker.modalOpen = true
            }
            var r = this,
                i, s, o, u, a, f;
            var n = n || "rgb(102, 102, 102)";
            var l = n.replace(/[^,0-9]+/ig, "").split(",");
            var c = l[0] || 0;
            var h = l[1] || 0;
            var p = l[2] || 0;
            var d = CommonFunctions.rgbToHex([c, h, p]);
            u = {
                legend: "Colorpicker",
                color: "Color",
                submit: "Apply",
                reset: "Cancel"
            };
            o = '<form class="wysiwyg"><fieldset style="margin:15px 20px;">' + '<div id="colorPickerContainer">' + '<div id="myPicker"></div>' + "</div>" + '<br clear="all"/><br clear="all"/><div style="display:none;"><input type="submit" class="button" value="{submit}" id="applyColorAction"/> ' + '<input type="reset" value="{reset}"  id="applyColorCancel"/></div></fieldset></form>';
            for (a in u) {
                if (e.wysiwyg.i18n) {
                    f = e.wysiwyg.i18n.t(u[a], "dialogs.colorpicker");
                    if (f === u[a]) {
                        f = e.wysiwyg.i18n.t(u[a], "dialogs")
                    }
                    u[a] = f
                }
                o = o.replace("{" + a + "}", u[a])
            }
            if (e.modal) {
                i = e(o);
                e.modal(i.html(), {
                    appendTo: "#tobWysiwyg",
                    minHeight: "280px",
                    maxWidth: "600px",
                    minWidth: "600px",
                    closeHTML: "<a href='#'>X</a>",
                    overlayClose: true,
                    onShow: function (n) {
                        n.container.hide();
                        e(".simplemodal-container").prepend('<div class="simplemodal-title">' + u.legend + "</div>"), e(".simplemodal-close").show();
                        e(".simplemodal-container").fadeIn("normal");
                        e(".topBlockClonWrapper .comboBlock-container:visible .comboBlock-close").trigger("click");
                        e("input:submit", n.data).click(function (i) {
                            var s = e("input.Hex", n.data).val();
                            r.color.fore.prev = s;
                            r.addColorToPalette("fore", s);
                            if (e.browser.msie) {
                                t.ui.returnRange()
                            }
                            if (e.browser.mozilla) {
                                s = "#" + s
                            }
                            t.editorDoc.execCommand("ForeColor", false, s);
                            e.modal.close();
                            return false
                        });
                        e("input:reset", n.data).click(function (n) {
                            if (e.browser.msie) {
                                t.ui.returnRange()
                            }
                            e.modal.close();
                            return false
                        });
                        e("fieldset", n.data).click(function (e) {
                            e.stopPropagation()
                        });
                        e.fn.jPicker.defaults.images.clientPath = urlImg + "colorpicker/";
                        e("#myPicker").jPicker({
                            color: {
                                alphaSupport: false,
                                active: new e.jPicker.Color({
                                    ahex: d
                                })
                            }
                        }, function (t, r) {
                            var i = t.val("all") || "transparent";
                            e("input:submit", n.data).trigger("click")
                        }, function (e, t) {}, function (t, r) {
                            e("input:reset", n.data).trigger("click")
                        })
                    },
                    onClose: function (t) {
                        e.wysiwyg.controls.colorpicker.modalOpen = false;
                        e.modal.close()
                    }
                })
            } else if (e.fn.dialog) {
                i = e(o);
                s.dialog({
                    appendTo: "#tobWysiwyg",
                    minHeight: "280px",
                    maxWidth: "600px",
                    minWidth: "600px",
                    closeHTML: "<a href='#'>X</a>",
                    modal: true,
                    open: function (n, o) {
                        s.container.hide();
                        e(".simplemodal-container").prepend('<div class="simplemodal-title">' + u.legend + "</div>"), e(".simplemodal-close").show();
                        e(".simplemodal-container").fadeIn("normal");
                        e("input:submit", i).click(function (n) {
                            var i = e("input.Hex").val();
                            r.color.fore.prev = i;
                            r.addColorToPalette("fore", i);
                            if (e.browser.msie) {
                                t.ui.returnRange()
                            }
                            t.editorDoc.execCommand("ForeColor", false, i);
                            e(s).dialog("close");
                            return false
                        });
                        e("input:reset", i).click(function (n) {
                            if (e.browser.msie) {
                                t.ui.returnRange()
                            }
                            e(s).dialog("close");
                            return false
                        });
                        e("fieldset", i).click(function (e) {
                            e.stopPropagation()
                        });
                        e.fn.jPicker.defaults.images.clientPath = urlImg + "colorpicker/";
                        e("#myPicker").jPicker({
                            color: {
                                alphaSupport: false,
                                active: new e.jPicker.Color({
                                    hex: d
                                })
                            }
                        }, function (t, n) {
                            var r = t.val("all") || "transparent";
                            e("input:submit", s.data).trigger("click")
                        }, function (e, t) {}, function (t, n) {
                            e("input:reset", s.data).trigger("click")
                        })
                    },
                    close: function (t, n) {
                        e.wysiwyg.controls.colorpicker.modalOpen = false;
                        s.dialog("destroy");
                        s.remove()
                    }
                })
            } else {}
        }
    }
})(jQuery);
(function (e) {
    if (undefined === e.wysiwyg) {
        throw "wysiwyg.table.js depends on jQuery.wysiwyg"
    }
    if (!e.wysiwyg.controls) {
        e.wysiwyg.controls = {}
    }
    var t = function (e, t, n, r, i, s, o) {
        if (isNaN(t) || isNaN(e) || t === null || e === null) {
            return
        }
        var n = n || 0;
        var r = r || 0;
        var i = i || 0;
        var s = s || "none";
        switch (s) {
            case "none":
                s = "";
                break;
            case "left":
                s = 'style="float:none;" align="left"';
                break;
            case "right":
                s = 'style="float:right;" align="right"';
                break;
            case "center":
                s = 'style="float:none; margin:0 auto;"';
                break
        }
        var u, a, f = ['<table border="' + n + '" cellpadding="' + i + '" cellspacing="' + r + '" ' + s + " ><tbody>"];
        e = parseInt(e, 10);
        t = parseInt(t, 10);
        if (o === null) {
            o = "…"
        }
        o = "<td>" + o + "</td>";
        for (u = t; u > 0; u -= 1) {
            f.push("<tr>");
            for (a = e; a > 0; a -= 1) {
                f.push(o)
            }
            f.push("</tr>")
        }
        f.push("</tbody></table>");
        return f.join("")
    };
    e.wysiwyg.controls.table = function (n) {
        var r, i, s, o, u = "Insertar tabla",
            a = "Cantidad de columnas",
            f = "Cantidad de líneas",
            l = "Border",
            c = "Margen entre celdas",
            h = "Espacio al borde de la celda",
            p = "Alinear tabla",
            d = "Aceptar",
            v = "Cancelar";
        if (e.wysiwyg.i18n) {
            u = e.wysiwyg.i18n.t(u, "dialogs.table");
            a = e.wysiwyg.i18n.t(a, "dialogs.table");
            f = e.wysiwyg.i18n.t(f, "dialogs.table");
            d = e.wysiwyg.i18n.t(d, "dialogs.table");
            v = e.wysiwyg.i18n.t(v, "dialogs");
            l = e.wysiwyg.i18n.t(l, "dialogs");
            c = e.wysiwyg.i18n.t(c, "dialogs");
            h = e.wysiwyg.i18n.t(h, "dialogs");
            p = e.wysiwyg.i18n.t(p, "dialogs")
        }
        var m = ComponenteFormularios.init();
        m.setComponent_useEnvolpe(true);
        m.setComponent_type("inputNumber");
        m.setComponent_name("colCount");
        m.setComponent_label(a);
        m.setComponent_selectedValue("3");
        m.setComponent_customAttributes({
            min: 1,
            max: 50,
            step: 1
        });
        m.setComponent_title("Usar números enteros");
        m.setComponent_customAttributes({
            "data-bootstrap-tooltip": "true"
        });
        m = m.getComponent();
        var g = ComponenteFormularios.init();
        g.setComponent_useEnvolpe(true);
        g.setComponent_type("inputNumber");
        g.setComponent_name("rowCount");
        g.setComponent_label(f);
        g.setComponent_selectedValue("3");
        g.setComponent_customAttributes({
            min: 1,
            max: 50,
            step: 1
        });
        g.setComponent_title("Usar números enteros");
        g.setComponent_customAttributes({
            "data-bootstrap-tooltip": "true"
        });
        g = g.getComponent();
        var y = ComponenteFormularios.init();
        y.setComponent_useEnvolpe(true);
        y.setComponent_type("inputNumber");
        y.setComponent_name("borderSize");
        y.setComponent_label(l);
        y.setComponent_selectedValue("0");
        y.setComponent_customAttributes({
            min: 0,
            max: 10,
            step: 1
        });
        y.setComponent_title("Usar números enteros");
        y.setComponent_customAttributes({
            "data-bootstrap-tooltip": "true"
        });
        y = y.getComponent();
        var b = ComponenteFormularios.init();
        b.setComponent_useEnvolpe(true);
        b.setComponent_type("inputNumber");
        b.setComponent_name("cellspacingSize");
        b.setComponent_label(c);
        b.setComponent_selectedValue("0");
        b.setComponent_customAttributes({
            min: 0,
            max: 50,
            step: 1
        });
        b.setComponent_title("Usar números enteros");
        b.setComponent_customAttributes({
            "data-bootstrap-tooltip": "true"
        });
        b = b.getComponent();
        var w = ComponenteFormularios.init();
        w.setComponent_useEnvolpe(true);
        w.setComponent_type("inputNumber");
        w.setComponent_name("cellpaddingSize");
        w.setComponent_label(h);
        w.setComponent_selectedValue("3");
        w.setComponent_customAttributes({
            min: 0,
            max: 50,
            step: 1
        });
        w.setComponent_title("Usar números enteros");
        w.setComponent_customAttributes({
            "data-bootstrap-tooltip": "true"
        });
        w = w.getComponent();
        var E = ComponenteFormularios.init();
        E.setComponent_useEnvolpe(true);
        E.setComponent_type("select");
        E.setComponent_name("tableAlign");
        E.setComponent_label(p);
        E.setComponent_selectedValue("none");
        ComponenteFormularios.setComponent_optionsList([{
            text: "Predefinido",
            value: "none"
        }, {
            text: "A la izquierda",
            value: "left"
        }, {
            text: "Centrar",
            value: "center"
        }, {
            text: "A la derecha",
            value: "right"
        }]);
        E = E.getComponent();
        var S = ComponenteFormularios.init();
        S.setComponent_useEnvolpe(true);
        S.setComponent_type("buttons");
        S.setComponent_buttonsList([{
            id: "insertTableAction",
            "class": "btn btn-primary",
            title: d,
            text: d
        }, {
            id: "insertTableCancel",
            "class": "btn cancel",
            title: v,
            text: v
        }]);
        S = S.getComponent();
        o = '<form class="wysiwyg form-horizontal insertTableForm" style="margin:20px 0;"><fieldset>' + '<div class="insertTablePreview"></div>' + m + g + y + b + w + E + S + "</fieldset></form>";
        if (!n.insertTable) {
            n.insertTable = t
        }
        if (e.fn.modal) {
            var x = e(o);
            e.modal(x, {
                appendTo: "#tobWysiwyg",
                minHeight: "450px",
                minWidth: "600px",
                closeHTML: "<a href='#'>X</a>",
                onShow: function (t) {
                    e(".simplemodal-container").prepend('<div class="simplemodal-title">Insertar Tabla</div>'), e(".simplemodal-close").show();
                    e(".simplemodal-container").fadeIn("normal");
                    e(".topBlockClonWrapper .comboBlock-container:visible .comboBlock-close").trigger("click");
                    e("#simplemodal-container").css({
                        height: "400px",
                        overflow: "hidden"
                    });
                    e("form.insertTableForm.wysiwyg #insertTableCancel").click(function (t) {
                        t.preventDefault();
                        t.stopPropagation();
                        e.modal.close();
                        return false
                    });
                    setTimeout(function () {
                        e('form.insertTableForm.wysiwyg input[name="colCount"]').trigger("change");
                        e("form.wysiwyg [data-bootstrap-tooltip]").tooltip()
                    }, 100);
                    e("form.insertTableForm.wysiwyg #insertTableAction").click(function (t) {
                        t.preventDefault();
                        t.stopPropagation();
                        var r = e('form.insertTableForm.wysiwyg input[name="colCount"]').val(),
                            i = e('form.insertTableForm.wysiwyg input[name="rowCount"]').val(),
                            s = e('form.insertTableForm.wysiwyg input[name="borderSize"]').val(),
                            o = e('form.insertTableForm.wysiwyg input[name="cellspacingSize"]').val(),
                            u = e('form.insertTableForm.wysiwyg input[name="cellpaddingSize"]').val(),
                            a = e('form.insertTableForm.wysiwyg select[name="tableAlign"]').val();
                        n.insertHtml(n.insertTable(r, i, s, o, u, a, n.defaults.tableFiller));
                        n.saveContent();
                        e.modal.close();
                        return false
                    });
                    e("form.insertTableForm.wysiwyg input, form.insertTableForm.wysiwyg select").change(function (t) {
                        t.preventDefault();
                        t.stopPropagation();
                        var r = e('form.insertTableForm.wysiwyg input[name="colCount"]').val(),
                            i = e('form.insertTableForm.wysiwyg input[name="rowCount"]').val(),
                            s = e('form.insertTableForm.wysiwyg input[name="borderSize"]').val(),
                            o = e('form.insertTableForm.wysiwyg input[name="cellspacingSize"]').val(),
                            u = e('form.insertTableForm.wysiwyg input[name="cellpaddingSize"]').val(),
                            a = e('form.insertTableForm.wysiwyg select[name="tableAlign"]').val();
                        e("form.insertTableForm.wysiwyg .insertTablePreview").html(n.insertTable(r, i, s, o, u, a, n.defaults.tableFiller))
                    })
                },
                overlayClose: true
            })
        }
        e(n.editorDoc).trigger("editorRefresh.wysiwyg")
    };
    e.wysiwyg.insertTable = function (n, r, i, s) {
        return n.each(function () {
            var n = e(this).data("wysiwyg");
            if (!n.insertTable) {
                n.insertTable = t
            }
            if (!n) {
                return this
            }
            n.insertTable(r, i, s);
            e(n.editorDoc).trigger("editorRefresh.wysiwyg");
            return this
        })
    }
})(jQuery);
WizardPersonalizationSocialSharingCombo = {
    init: function () {
        this.adminCustomFields = new Array;
        this.tokenOpen = "<TOKEN>%";
        this.tokenClose = "%</TOKEN>";
        this.loadInteractions();
        this.selectedValue = ""
    },
    loadInteractions: function () {
        var e = this;
        jQuery("body").on("click", ".comboBlock-container[data-name=socialNetworks] .comboBlock-close", function (e) {
            var t = jQuery(this);
            jQuery(t).closest(".comboBlock-container").slideUp("fast");
            jQuery(".comboBlock-container[data-name=socialNetworks] :radio").removeAttr("checked").closest("li").removeClass("active selected")
        }).on("change", ".comboBlock-container[data-name=socialNetworks] ul.comboBlock :radio[name=personalizationWizardSocialSharing]", function (t) {
            e.selectedValue = jQuery(this).closest("li").attr("data-optionValue");
            e.insertTokenIntoEditor();
            jQuery(".comboBlock-container[data-name=socialNetworks] .comboBlock-close").trigger("click")
        }).on("click", ".comboBlock-container[data-name=customFields] ul.comboBlock li", function (t) {
            if (jQuery.browser.msie && parseInt(jQuery.browser.version) <= 7) {
                e.selectedValue = jQuery(this).attr("data-optionValue");
                e.insertTokenIntoEditor();
                jQuery(".comboBlock-container[data-name=socialNetworks] .comboBlock-close").trigger("click")
            }
        })
    },
    insertTokenIntoEditor: function () {
        var e = this;
        var t = jQuery(":radio[name=personalizationWizardSocialSharing]:checked");
        if (jQuery(t).length > 0) {
            var n = jQuery(":radio[name=personalizationWizardSocialSharing]:checked").val();
            switch (n) {
                case "shareFacebook":
                    n = ' <a href="' + e.tokenOpen + "shareFacebook" + e.tokenClose + '" title="' + __("Compartir en") + ' Facebook" ><img alt="Facebook" src="http://v2.email-marketing.adminsimple.com/img/social/facebook_16.png" border="0" data-alignmentType="auto" style=" float: none; margin: 0px; display: inline;"/></a> ';
                    break;
                case "shareTwitter":
                    n = ' <a href="' + e.tokenOpen + "shareTwitter" + e.tokenClose + '" title="' + __("Compartir en") + ' Twitter" ><img src="http://v2.email-marketing.adminsimple.com/img/social/twitter_16.png" alt="Twitter" border="0" data-alignmentType="auto" style=" float: none; margin: 0px; display: inline;"/></a> ';
                    break;
                case "shareMyspace":
                    n = ' <a href="' + e.tokenOpen + "shareMyspace" + e.tokenClose + '" title="' + __("Compartir en") + ' Myspace" ><img src="http://v2.email-marketing.adminsimple.com/img/social/myspace_16.png" alt="Myspace" border="0" data-alignmentType="auto" style=" float: none; margin: 0px; display: inline;"/></a> ';
                    break;
                case "shareGoogle":
                    n = ' <a href="' + e.tokenOpen + "shareGoogle" + e.tokenClose + '" title="' + __("Compartir en") + ' Google Bookmarks" ><img src="http://v2.email-marketing.adminsimple.com/img/social/google_16.png" alt="Google Bookmarks" border="0" data-alignmentType="auto" style=" float: none; margin: 0px; display: inline;"/></a> ';
                    break;
                case "shareLinkedin":
                    n = ' <a href="' + e.tokenOpen + "shareLinkedin" + e.tokenClose + '" title="' + __("Compartir en") + ' Linkedin" ><img src="http://v2.email-marketing.adminsimple.com/img/social/linkedin_16.png" alt="Linkedin" border="0" data-alignmentType="auto" style=" float: none; margin: 0px; display: inline;"/></a> ';
                    break;
                case "shareDelicious":
                    n = ' <a href="' + e.tokenOpen + "shareDelicious" + e.tokenClose + '" title="' + __("Compartir en") + ' Delicious" ><img src="http://v2.email-marketing.adminsimple.com/img/social/delicious_16.png" alt="Delicious" border="0" data-alignmentType="auto" style=" float: none; margin: 0px; display: inline;"/></a> ';
                    break;
                case "ForwardTo":
                    n = ' <a href="' + e.tokenOpen + "ForwardTo" + e.tokenClose + '" title="' + __("Reenvía a un amigo") + '" ><img src="http://v2.email-marketing.adminsimple.com/img/social/email_16.png" alt="' + __("Reenvía a un amigo") + '" border="0" data-alignmentType="auto" style=" float: none; margin: 0px; display: inline;"/></a> ';
                    break;
                case "shareAll":
                    var r = new Array;
                    r.push('<span style="display:inline-block;white-space:nowrap;font-size:10px;">');
                    r.push(" " + __("Compartir en") + ":");
                    r.push("</span>");
                    r.push(' <a href="' + e.tokenOpen + "shareFacebook" + e.tokenClose + '" title="' + __("Compartir en") + ' Facebook" ><img border="0" src="http://v2.email-marketing.adminsimple.com/img/social/facebook_16.png" alt="Facebook" data-alignmentType="auto" style=" float: none; margin: 0px; display: inline;"/></a> ');
                    r.push(' <a href="' + e.tokenOpen + "shareTwitter" + e.tokenClose + '" title="' + __("Compartir en") + ' Twitter" ><img border="0" src="http://v2.email-marketing.adminsimple.com/img/social/twitter_16.png" alt="Twitter" data-alignmentType="auto" style=" float: none; margin: 0px; display: inline;"/></a> ');
                    r.push(' <a href="' + e.tokenOpen + "shareLinkedin" + e.tokenClose + '" title="' + __("Compartir en") + ' Linkedin" ><img border="0" src="http://v2.email-marketing.adminsimple.com/img/social/linkedin_16.png" alt="Linkedin" data-alignmentType="auto" style=" float: none; margin: 0px; display: inline;"/></a> ');
                    r.push(' <a href="' + e.tokenOpen + "shareDelicious" + e.tokenClose + '" title="' + __("Compartir en") + ' Delicious" ><img border="0" src="http://v2.email-marketing.adminsimple.com/img/social/delicious_16.png" alt="Delicious" data-alignmentType="auto" style=" float: none; margin: 0px; display: inline;"/></a> ');
                    r.push(' <a href="' + e.tokenOpen + "ForwardTo" + e.tokenClose + '" title="' + __("Reenvía a un amigo") + '" ><img border="0" src="http://v2.email-marketing.adminsimple.com/img/social/email_16.png" alt="' + __("Reenvía a un amigo") + '" data-alignmentType="auto" style="float: none; margin: 0px; display: inline;"/></a> ');
                    n = r.join("\n");
                    break
            }
            n = n.toString();
            var i = TemplateEditor.getWysiwygObject();
            i.insertHtml(n)
        }
    },
    createCombo: function (e) {
        var t = this;
        if (jQuery(".comboBlock-container.personalizationWizardSocialSharing").length < 1) {
            var n = new Array;
            n.push('<li class="comboBlock-optgroup wizardGroupSocialSharing"><label>' + __("Compartir") + "</label></li>");
            n.push('<li class="comboBlock-item" data-optionValue="shareFacebook"><label>');
            n.push('<input type="radio" value="shareFacebook" name="personalizationWizardSocialSharing"/>');
            n.push('<img border="0" alt="Facebook" src="http://v2.email-marketing.adminsimple.com/img/social/facebook_16.png" /> Facebook');
            n.push("</label></li>");
            n.push('<li class="comboBlock-item" data-optionValue="shareTwitter"><label>');
            n.push('<input type="radio" value="shareTwitter" name="personalizationWizardSocialSharing"/>');
            n.push('<img border="0" alt="Twitter" src="http://v2.email-marketing.adminsimple.com/img/social/twitter_16.png" /> Twitter');
            n.push("</label></li>");
            n.push('<li class="comboBlock-item" data-optionValue="shareLinkedin"><label>');
            n.push('<input type="radio" value="shareLinkedin" name="personalizationWizardSocialSharing"/>');
            n.push('<img border="0" alt="Linkedin" src="http://v2.email-marketing.adminsimple.com/img/social/linkedin_16.png" /> Linkedin');
            n.push("</label></li>");
            n.push('<li class="comboBlock-item" data-optionValue="shareDelicious"><label>');
            n.push('<input type="radio" value="shareDelicious" name="personalizationWizardSocialSharing"/>');
            n.push('<img border="0" alt="Delicious" src="http://v2.email-marketing.adminsimple.com/img/social/delicious_16.png" /> Delicious');
            n.push("</label></li>");
            n.push('<li class="comboBlock-item" data-optionValue="ForwardTo"><label>');
            n.push('<input type="radio" value="ForwardTo" name="personalizationWizardSocialSharing"/>');
            n.push('<img border="0" alt="' + __("Compartir por E-mail") + '" src="http://v2.email-marketing.adminsimple.com/img/social/email_16.png" /> ' + __("Compartir por E-mail") + " (" + __("Reenvía a un amigo") + ")");
            n.push("</label></li>");
            n.push('<li class="comboBlock-item" data-optionValue="shareAll"><label>');
            n.push('<input type="radio" value="shareAll" name="personalizationWizardSocialSharing"/>');
            n.push('<img border="0" alt="' + __("Todos") + '" src="http://v2.email-marketing.adminsimple.com/img/social/todos.png" /> ' + __("Todos"));
            n.push("</label></li>");
            var r = new Array;
            r.push('<div class="comboBlock-container personalizationWizardSocialSharing" data-name="socialNetworks">');
            r.push('<div class="comboBlock-close">x</div>');
            r.push('<div class="comboBlock-title">');
            r.push(__("Redes Sociales") + ":");
            r.push("</div>");
            r.push('<ul class="comboBlock personalizationWizardSocialSharing">');
            r.push(n.join("\n"));
            r.push("</ul>");
            r.push('<div class="comboBlock-tools">');
            r.push('<div class="comboBlock-actionTool applyChanges btn-small btn-primary" data-toolaction="applyChanges">' + __("Aplicar cambios") + "</div>");
            r.push("</div>");
            r.push("</div>");
            jQuery('#tobWysiwyg [data-componentName="wysiwyg-toolbarWrapper"] .toolbarExtraTools').prepend(r.join("\n"))
        }
        jQuery(".comboBlock-container.personalizationWizardSocialSharing").toggle();
        jQuery(".topBlockClonWrapper .comboBlock-container:visible:not(.personalizationWizardSocialSharing) .comboBlock-close, .simplemodal-close").trigger("click")
    }
};
jQuery(function () {
    WizardPersonalizationSocialSharingCombo.init()
});
WizardPersonalizationCombo = {
    init: function () {
        this.adminCustomFields = new Array;
        this.tokenOpen = "<TOKEN>%";
        this.tokenClose = "%</TOKEN>";
        this.loadInteractions();
        this.comboOptionsList = new Array;
        this.selectedValue = ""
    },
    loadInteractions: function () {
        var e = this;
        jQuery("body").on("click", ".comboBlock-container[data-name=customFields] .comboBlock-tools .comboBlock-actionTool", function (t) {
            var n = jQuery(this);
            e.dataToolAction = jQuery(n).attr("data-toolAction");
            switch (e.dataToolAction) {
                case "applyChanges":
                    jQuery(n).closest(".comboBlock-container").slideUp("fast");
                    jQuery(".comboBlock-container[data-name=customFields] .comboBlock-close").trigger("click");
                    break;
                case "cancelChanges":
                    jQuery(n).closest(".comboBlock-container").find(".comboBlock-close").trigger("click");
                    break;
                case "addNewOption":
                    jQuery(n).closest(".comboBlock-tools").find(".actionsList").hide();
                    jQuery(n).closest(".comboBlock-tools").find(".addOption").show().children(":not(.throbberClean)").show();
                    break;
                default:
                    ErrorDisplay.pushError(__("La herramienta seleccionada no possee una acción asociada"));
                    ErrorDisplay.display(false, __("ERROR"), 50);
                    break
            }
        }).on("click", ".comboBlock-container[data-name=customFields] .addOption [data-toolaction=addOptionCancel]", function (e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery(this).closest(".addOption").find("input").val("");
            jQuery(this).closest(".addOption").hide();
            jQuery(this).closest(".comboBlock-tools").find(".actionsList").show();
            return false
        }).on("click", ".comboBlock-container[data-name=customFields] .addOption [data-toolaction=addOptionApply]", function (t) {
            t.preventDefault();
            t.stopPropagation();
            e.saveNewCustomField(jQuery(this));
            return false
        }).on("click", ".comboBlock-container[data-name=customFields] .comboBlock-close", function (e) {
            var t = jQuery(this);
            jQuery(t).closest(".comboBlock-container").slideUp("fast");
            jQuery(".comboBlock-container[data-name=customFields] :radio").removeAttr("checked").closest("li").removeClass("active selected")
        }).on("change", ".comboBlock-container[data-name=customFields] ul.comboBlock :radio[name=personalizationWizard]", function (t) {
            e.selectedValue = jQuery(this).closest("li").attr("data-optionValue");
            e.insertTokenIntoEditor();
            jQuery(".comboBlock-container[data-name=customFields] .comboBlock-close").trigger("click")
        }).on("click", ".comboBlock-container[data-name=customFields] ul.comboBlock li", function (t) {
            if (jQuery.browser.msie && parseInt(jQuery.browser.version) <= 7) {
                e.selectedValue = jQuery(this).attr("data-optionValue");
                e.insertTokenIntoEditor();
                jQuery(".comboBlock-container[data-name=customFields] .comboBlock-close").trigger("click")
            }
        })
    },
    insertTokenIntoEditor: function () {
        var e = this;
        var t = e.selectedValue || "";
        if (t.length > 0) {
            t = e.tokenOpen + t + e.tokenClose;
            jQuery("#tobWysiwygTextArea").wysiwyg("insertHtml", t)
        }
    },
    saveNewCustomField: function (e) {
        var t = this;
        var n = new jQuery.Deferred;
        var r = new Array;
        var i = jQuery(e).parent().find("input[name=addCustomFieldName]").val();
        var s = jQuery(e).parent().find("input[name=addCustomFieldDefaultValue]").val();
        var o = jQuery(e).closest(".comboBlock-container");
        jQuery(o).find(".addCustomFieldInfo").children().hide();
        jQuery(o).find(".throbberClean").show();
        if (!i) {
            r.push(__("El campo Nombre está vacío."))
        }
        if (r.length > 0) {
            jQuery(o).find(".addCustomFieldInfo").children().show();
            jQuery(o).find(".throbberClean").hide();
            ErrorDisplay.pushError(r);
            ErrorDisplay.display(false, __("ERROR"), 50);
            return n.reject()
        }
        n = jQuery.ajax({
            url: "/customfield/add/format/json/",
            type: "POST",
            parseError: true,
            dataType: "json",
            data: {
                Title: i,
                DefaultValue: s
            }
        }).done(function (e) {
            var t = e && e.root && e.root.ajaxResponse;
            if (t && "success" in t && t.success == true) {
                var n = t.customField;
                jQuery(o).find("input[name=addCustomFieldName]").val("");
                jQuery(o).find("input[name=addCustomFieldDefaultValue]").val("");
                jQuery(o).find(".addCustomFieldInfo").slideUp().children().show();
                jQuery(o).find(".throbberClean").hide();
                jQuery("ul.comboBlock.personalizationWizard li").removeClass("active");
                jQuery(":radio[name=personalizationWizard]").removeClass("newAdded");
                var r = ComponenteFormularios.init();
                r.setComponent_type("customCombo");
                r.setComponent_name("personalizationWizard");
                var s = r.customComboCreateOptionLi({
                    value: "Member:CustomField" + n.CustomFieldID,
                    text: i,
                    "class": "newAdded",
                    parentLiCustomAttributes: {
                        "data-customFieldID": n.CustomFieldID
                    }
                });
                jQuery(s.join("\n")).insertAfter("li.comboBlock-optgroup.wizardGroupCustomFields");
                jQuery(":radio.newAdded[name=personalizationWizard]").trigger("click").trigger("change")
            }
        }).fail(function (e, t, n) {
            jQuery(o).find(".addCustomFieldInfo").children().show();
            jQuery(o).find(".throbberClean").hide();
            var r = jQuery.findInCollection(n, function (e) {
                if (e == "errorMsg_upgradeAccount") {
                    return true
                }
            });
            if (r) {
                BackgroundTasksStatusObserver.showOverlay(true)
            } else {
                ErrorDisplay.parseAndPushSystemErrors(n);
                ErrorDisplay.display(false, __("ERROR"), 50)
            }
        });
        return n
    },
    generateComboOptionsList: function () {
        var e = this;
        var t = new Array;
        var n = new Array;
        t.push({
            value: "",
            text: __("Información del suscriptor"),
            parentLiCustomAttributes: {
                isOptionGroup: true,
                "class": "wizardGroupSubscriberInfo"
            }
        });
        t.push({
            value: "Member:Email",
            text: __("Dirección de email"),
            parentLiCustomAttributes: {
                "data-avoidDeletion": true
            }
        });
        t.push({
            value: "",
            text: __("Links"),
            parentLiCustomAttributes: {
                isOptionGroup: true,
                "class": "wizardGroupLinks"
            }
        });
        t.push({
            value: "UnSubscribe",
            text: __("Desuscripción"),
            parentLiCustomAttributes: {
                "data-avoidDeletion": true
            }
        });
        t.push({
            value: "HTMLVersion",
            text: __("Versión HTML"),
            parentLiCustomAttributes: {
                "data-avoidDeletion": true
            }
        });
        t.push({
            value: "ForwardTo",
            text: __("Reenviar a un Amigo"),
            parentLiCustomAttributes: {
                "data-avoidDeletion": true
            }
        });
        t.push({
            value: "IsSpam",
            text: __("Reportar como SPAM"),
            parentLiCustomAttributes: {
                "data-avoidDeletion": true
            }
        });
        t.push({
            value: "",
            text: __("Útiles"),
            parentLiCustomAttributes: {
                isOptionGroup: true,
                "class": "wizardGroupLinks"
            }
        });
        t.push({
            value: "Date",
            text: __("Fecha en formato") + " AAAA-MM-DD HH:MM:SS",
            parentLiCustomAttributes: {
                "data-avoidDeletion": true
            }
        });
        t.push({
            value: "",
            text: __("Campos Personalizados"),
            parentLiCustomAttributes: {
                isOptionGroup: true,
                "class": "wizardGroupCustomFields"
            }
        });
        if (e.adminCustomFields.length > 0) {
            jQuery.each(e.adminCustomFields, function (e, n) {
                t.push({
                    value: "Member:CustomField" + n.CustomFieldID,
                    text: n.Title,
                    parentLiCustomAttributes: {
                        "data-customFieldID": n.CustomFieldID
                    }
                })
            })
        }
        for (var r in t) {
            var i = ComponenteFormularios.init();
            i.setComponent_type("customCombo");
            i.setComponent_name("personalizationWizard");
            var s = i.customComboCreateOptionLi(t[r]);
            n.push(s.join(""))
        }
        return n
    },
    createCombo: function () {
        var e = this;
        if (jQuery(".comboBlock-container[data-name=customFields]").length < 1) {
            var t = e.generateComboOptionsList();
            var n = new Array;
            n.push('<div class="comboBlock-container personalizationWizard" data-optionsAdd="enabled"  data-name="customFields" >');
            n.push('<div class="comboBlock-close">x</div>');
            n.push('<div class="comboBlock-title">');
            n.push(__("Etiquetas personalizadas") + ":");
            n.push("</div>");
            n.push('<ul class="comboBlock personalizationWizard">');
            n.push(t.join("\n"));
            n.push("</ul>");
          
            n.push("</div>");
            jQuery('#tobWysiwyg [data-componentName="wysiwyg-toolbarWrapper"] .toolbarExtraTools').prepend(n.join("\n"))
        }
        jQuery(".comboBlock-container[data-name=customFields]").toggle();
        jQuery(".topBlockClonWrapper .comboBlock-container:visible:not([data-name=customFields]) .comboBlock-close, .simplemodal-close").trigger("click")
    }
};
jQuery(function () {
    WizardPersonalizationCombo.init()
});
(function (e) {
    if (undefined === e.wysiwyg) {
        throw "wysiwyg.image.js depends on jQuery.wysiwyg"
    }
    if (!e.wysiwyg.controls) {
        e.wysiwyg.controls = {}
    }
    e.wysiwyg.controls.link = {
        init: function (t) {
            var n = this,
                r, i, s, o, u, a, f, l, c, h, p, d, v;
            f = "Crear vínculo";
            l = "URL";
            c = "Atributo Title";
            h = "Atributo Target";
            p = "Insertar";
            d = "Cancelar";
            if (e.wysiwyg.i18n) {
                f = e.wysiwyg.i18n.t(f, "dialogs.link");
                l = e.wysiwyg.i18n.t(l, "dialogs.link");
                c = e.wysiwyg.i18n.t(c, "dialogs.link");
                h = e.wysiwyg.i18n.t(h, "dialogs.link");
                p = e.wysiwyg.i18n.t(p, "dialogs.link");
                d = e.wysiwyg.i18n.t(d, "dialogs")
            }
            var m = ComponenteFormularios.init();
            m.setComponent_useEnvolpe(true);
            m.setComponent_type("inputText");
            m.setComponent_name("linkhref");
            m.setComponent_label(l);
            m = m.getComponent();
            var g = ComponenteFormularios.init();
            g.setComponent_useEnvolpe(true);
            g.setComponent_type("inputText");
            g.setComponent_name("linktitle");
            g.setComponent_label(c);
            g = g.getComponent();
            var y = ComponenteFormularios.init();
            y.setComponent_useEnvolpe(true);
            y.setComponent_type("inputText");
            y.setComponent_name("linktarget");
            y.setComponent_label(h);
            y = y.getComponent();
            var b = ComponenteFormularios.init();
            b.setComponent_useEnvolpe(true);
            b.setComponent_type("buttons");
            b.setComponent_buttonsList([{
                id: "insertLinkAction",
                "class": "btn btn-primary",
                title: p,
                text: p
            }, {
                id: "insertLinkCancel",
                "class": "btn cancel",
                title: d,
                text: d
            }]);
            b = b.getComponent();
            a = '<form class="wysiwyg form-horizontal insertLinkForm" style="margin:20px 0;"><fieldset>' + m + y + g + b + "</fieldset></form>";
            o = {
                self: t.dom.getElement("a"),
                href: "http://",
                title: "",
                target: ""
            };
            if (o.self) {
                o.href = o.self.href ? e(o.self).attr("href") : o.href;
                o.title = o.self.title ? o.self.title : "";
                o.target = o.self.target ? o.self.target : ""
            }
            if (e.modal) {
                var r = e(a);
                r.find("input[name=linkhref]").val(o.href);
                r.find("input[name=linktitle]").val(o.title);
                r.find("input[name=linktarget]").val(o.target);
                e.modal(r, {
                    appendTo: "#tobWysiwyg",
                    minHeight: "450px",
                    minWidth: "600px",
                    closeHTML: "<a href='#'>X</a>",
                    onShow: function (n) {
                        e(".simplemodal-container").prepend('<div class="simplemodal-title">Agregar Vínculo</div>'), e(".simplemodal-close").show();
                        e(".simplemodal-container").fadeIn("normal");
                        e(".comboBlock-container .comboBlock-close").trigger("click");
                        e("#simplemodal-container").css({
                            height: "250px",
                            overflow: "hidden"
                        });
                        e("form.insertLinkForm.wysiwyg #insertLinkCancel").click(function (t) {
                            t.preventDefault();
                            t.stopPropagation();
                            e.modal.close();
                            return false
                        });
                        e("form.insertLinkForm.wysiwyg #insertLinkAction").click(function (n) {
                            n.preventDefault();
                            n.stopPropagation();
                            var r = e('form.insertLinkForm.wysiwyg input[name="linkhref"]').val(),
                                i = e('form.insertLinkForm.wysiwyg input[name="linktitle"]').val(),
                                s = e('form.insertLinkForm.wysiwyg input[name="linktarget"]').val(),
                                a;
                            if (t.options.controlLink.forceRelativeUrls) {
                                a = window.location.protocol + "//" + window.location.hostname;
                                if (0 === r.indexOf(a)) {
                                    r = r.substr(a.length)
                                }
                            }
                            if (o.self) {
                                if ("string" === typeof r) {
                                    if (r.length > 0) {
                                        e(o.self).attr("href", r).attr("title", i).attr("target", s)
                                    } else {
                                        e(o.self).replaceWith(o.self.innerHTML)
                                    }
                                }
                            } else {
                                if (e.browser.msie) {
                                    t.ui.returnRange()
                                }
                                u = t.getRangeText();
                                img = t.dom.getElement("img");
                                if (u && u.length > 0 || img) {
                                    if (e.browser.msie) {
                                        t.ui.focus()
                                    }
                                    if ("string" === typeof r) {
                                        if (r.length > 0) {
                                            t.editorDoc.execCommand("createLink", false, r)
                                        } else {
                                            t.editorDoc.execCommand("unlink", false, null)
                                        }
                                    }
                                    o.self = t.dom.getElement("a");
                                    e(o.self).attr("href", r).attr("title", i);
                                    e(o.self).attr("target", s)
                                } else if (t.options.messages.nonSelection) {
                                    window.alert(t.options.messages.nonSelection)
                                }
                            }
                            t.saveContent();
                            e.modal.close();
                            return false
                        })
                    },
                    overlayClose: true
                })
            }
            e(t.editorDoc).trigger("editorRefresh.wysiwyg")
        }
    };
    e.wysiwyg.createLink = function (t, n) {
        return t.each(function () {
            var t = e(this).data("wysiwyg"),
                r;
            if (!t) {
                return this
            }
            if (!n || n.length === 0) {
                return this
            }
            r = t.getRangeText();
            if (r && r.length > 0) {
                if (e.browser.msie) {
                    t.ui.focus()
                }
                t.editorDoc.execCommand("unlink", false, null);
                t.editorDoc.execCommand("createLink", false, n)
            } else if (t.options.messages.nonSelection) {
                window.alert(t.options.messages.nonSelection)
            }
        })
    }
})(jQuery);
WysiwygSelectFontSizeCombo = {
    init: function () {
        this.loadInteractions();
        this.fontsList = {
            1: "8pt",
            2: "10pt",
            3: "12pt",
            4: "14pt",
            5: "18pt",
            6: "24pt",
            7: "36pt"
        };
        this.Wysiwyg = {};
        this.selectedFontSize = ""
    },
    loadInteractions: function () {
        var e = this;
        jQuery("body").on("click", ".comboBlock-container[data-name=fontSizeCombo] .comboBlock-close", function (e) {
            var t = jQuery(this);
            jQuery(t).closest(".comboBlock-container").slideUp("fast");
            jQuery(":radio[name=fontSizeCombo]").removeAttr("checked").closest("li").removeClass("active selected")
        }).on("change", ".comboBlock-container[data-name=fontSizeCombo] ul.comboBlock :radio[name=fontSizeCombo]", function (t) {
            e.selectedFontSize = jQuery(this).closest("li").attr("data-optionValue");
            e.changeFontSize();
            jQuery(".comboBlock-container[data-name=fontSizeCombo] .comboBlock-close").trigger("click")
        }).on("click", ".comboBlock-container[data-name=fontSizeCombo] ul.comboBlock li", function (t) {
            if (jQuery.browser.msie && parseInt(jQuery.browser.version) <= 7) {
                e.selectedFontSize = jQuery(this).attr("data-optionValue");
                e.changeFontSize();
                jQuery(".comboBlock-container[data-name=fontSizeCombo] .comboBlock-close").trigger("click")
            }
        })
    },
    changeFontSize: function () {
        var e = this;
        var t = e.selectedFontSize || "";
        if (t.length > 0) {
            if (jQuery.browser.msie && e.Wysiwyg) {
                e.Wysiwyg.ui.returnRange()
            }
            e.Wysiwyg.editorDoc.execCommand("fontSize", false, t)
        }
    },
    createCombo: function (e) {
        var t = this;
        t.Wysiwyg = e || false;
        if (jQuery(".comboBlock-container[data-name=fontSizeCombo]").length < 1) {
            var n = new Array;
            n.push({
                value: "",
                text: __("Seleccionar Tamaño"),
                parentLiCustomAttributes: {
                    isOptionGroup: true,
                    "class": "fontSizeCombo"
                }
            });
            for (var r in t.fontsList) {
                var i = t.fontsList[r];
                n.push({
                    value: r,
                    text:  t.fontsList[r] ,
                    parentLiCustomAttributes: {
                        style: "font-size:" + t.fontsList[r]
                    }
                })
            }
            var s = new Array;
            for (var r in n) {
                var o = ComponenteFormularios.init();
                o.setComponent_type("customCombo");
                o.setComponent_name("fontSizeCombo");
                var u = o.customComboCreateOptionLi(n[r]);
                s.push(u.join(""))
            }
            var a = new Array;
            a.push('<div class="comboBlock-container fontSizeCombo"   data-name="fontSizeCombo" >');
            a.push('<div class="comboBlock-close">x</div>');
            a.push('<div class="comboBlock-title">');
            a.push(__("Seleccionar Tamaño") + ":");
            a.push("</div>");
            a.push('<ul class="comboBlock fontSizeCombo">');
            a.push(s.join("\n"));
            a.push("</ul>");
            a.push('<div class="comboBlock-tools">');
            a.push('<div class="actionsList">');
            a.push('<div class="comboBlock-actionTool cancelChanges btn btn-small" data-toolaction="cancelChanges">' + __("Cancelar") + "</div>");
            a.push('<div class="comboBlock-actionTool applyChanges btn-small btn-primary" data-toolaction="applyChanges">' + __("Aplicar") + "</div>");            
            a.push("</div>");
            a.push('<div class="addOption">');
            a.push('<span class="button cancel btn btn-small" title="Cancelar" data-toolaction="addOptionCancel">' + __("Cancelar") + "</span>");
            a.push('<span class="button apply btn-small btn-primary" title="Agregar" data-toolaction="addOptionApply">' + __("Agregar") + "</span>");
            a.push('<div class="throbberClean"></div>');
            a.push("</div>");
            a.push("</div>");
            a.push("</div>");
            jQuery('#tobWysiwyg [data-componentName="wysiwyg-toolbarWrapper"] .toolbarExtraTools').prepend(a.join("\n"))
        }
        jQuery(".comboBlock-container[data-name=fontSizeCombo]").toggle();
        jQuery(".topBlockClonWrapper .comboBlock-container:visible:not([data-name=fontSizeCombo]) .comboBlock-close, .simplemodal-close").trigger("click")
    }
};
jQuery(function () {
    WysiwygSelectFontSizeCombo.init()
});
WysiwygSelectFontFaceCombo = {
    init: function () {
        this.loadInteractions();
        this.fontsList = {
            arial: "Arial, Helvetica, sans-serif",
            verdana: "Verdana, Geneva, sans-serif",
            georgia: 'Georgia, "Times New Roman", Times, serif',
            monospace: '"Courier New", Courier, monospace',
            tahoma: "Tahoma, Geneva, sans-serif",
            trebuchet: '"Trebuchet MS", Arial, Helvetica, sans-serif',
            arialBlack: '"Arial Black", Gadget, sans-serif',
            times: '"Times New Roman", Times, serif',
            palatino: '"Palatino Linotype", "Book Antiqua", Palatino, serif',
            lucida: '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
            serif: '"MS Serif", "New York", serif',
            console: '"Lucida Console", Monaco, monospace',
            comic: '"Comic Sans MS", cursive'
        };
        this.Wysiwyg = {};
        this.selectedFontFamily = ""
    },
    loadInteractions: function () {
        var e = this;
        jQuery("body").on("click", ".comboBlock-container[data-name=fontFaceCombo] .comboBlock-close", function (e) {
            var t = jQuery(this);
            jQuery(t).closest(".comboBlock-container").slideUp("fast");
            jQuery(":radio[name=fontFaceCombo]").removeAttr("checked").closest("li").removeClass("active selected")
        }).on("change", ".comboBlock-container[data-name=fontFaceCombo] ul.comboBlock :radio[name=fontFaceCombo]", function (t) {
            e.selectedFontFamily = jQuery(this).closest("li").attr("data-optionValue");
            e.changeFontFace();
            jQuery(".comboBlock-container[data-name=fontFaceCombo] .comboBlock-close").trigger("click")
        }).on("click", ".comboBlock-container[data-name=fontFaceCombo] ul.comboBlock li", function (t) {
            if (jQuery.browser.msie && parseInt(jQuery.browser.version) <= 7) {
                e.selectedFontFamily = jQuery(this).attr("data-optionValue");
                e.changeFontFace();
                jQuery(".comboBlock-container[data-name=fontFaceCombo] .comboBlock-close").trigger("click")
            }
        })
    },
    changeFontFace: function () {
        var e = this;
        var t = e.selectedFontFamily || "";
        if (t.length > 0 && e.fontsList[t] != "undefined") {
            var n = e.fontsList[t];
            if (jQuery.browser.msie && e.Wysiwyg) {
                e.Wysiwyg.ui.returnRange()
            }
            e.Wysiwyg.editorDoc.execCommand("fontname", false, n)
        }
    },
    createCombo: function (e) {
        var t = this;
        t.Wysiwyg = e || false;
        if (jQuery(".comboBlock-container[data-name=fontFaceCombo]").length < 1) {
            var n = new Array;
            n.push({
                value: "",
                text: __("Seleccionar Fuente"),
                parentLiCustomAttributes: {
                    isOptionGroup: true,
                    "class": "fontFaceCombo"
                }
            });
            for (var r in t.fontsList) {
                var i = t.fontsList[r];
                n.push({
                    value: r,
                    text: t.fontsList[r],
                    parentLiCustomAttributes: {
                        style: "font-family:" + i.replace(/"/gi, "'")
                    }
                })
            }
            var s = new Array;
            for (var r in n) {
                var o = ComponenteFormularios.init();
                o.setComponent_type("customCombo");
                o.setComponent_name("fontFaceCombo");
                var u = o.customComboCreateOptionLi(n[r]);
                s.push(u.join(""))
            }
            var a = new Array;
            a.push('<div class="comboBlock-container fontFaceCombo"   data-name="fontFaceCombo" >');
            a.push('<div class="comboBlock-close">x</div>');
            a.push('<div class="comboBlock-title">');
            a.push(__("Seleccionar Fuente") + ":");
            a.push("</div>");
            a.push('<ul class="comboBlock fontFaceCombo">');
            a.push(s.join("\n"));
            a.push("</ul>");
            a.push('<div class="comboBlock-tools">');
            a.push('<div class="actionsList">');
            a.push('<div class="comboBlock-actionTool cancelChanges btn btn-smal" data-toolaction="cancelChanges">' + __("Cancelar") + "</div>");
            a.push('<div class="comboBlock-actionTool applyChanges btn-small btn-primary" data-toolaction="applyChanges">' + __("Aplicar") + "</div>");
            a.push("</div>");
            a.push('<div class="addOption">');
            a.push('<span class="button cancel btn btn-smal" title="' + __("Cancelar") + '" data-toolaction="addOptionCancel">' + __("Cancelar") + "</span>");
            a.push('<span class="button apply btn-small btn-primary" title="' + __("Agregar") + '" data-toolaction="addOptionApply">' + __("Agregar") + "</span>");
            a.push('<div class="throbberClean"></div>');
            a.push("</div>");
            a.push("</div>");
            a.push("</div>");
            jQuery('#tobWysiwyg [data-componentName="wysiwyg-toolbarWrapper"] .toolbarExtraTools').prepend(a.join("\n"))
        }
        jQuery(".comboBlock-container[data-name=fontFaceCombo]").toggle();
        jQuery(".topBlockClonWrapper .comboBlock-container:visible:not([data-name=fontFaceCombo]) .comboBlock-close, .simplemodal-close").trigger("click")
    }
};
jQuery(function () {
    WysiwygSelectFontFaceCombo.init()
})