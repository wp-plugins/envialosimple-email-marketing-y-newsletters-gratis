
var droped;
var espacioAInsertar;
var formatoSeleccionado;
var colorBg;
var colorBg1;
var colorBg2;
var selectAgregarEmail;
var formatoACopiar;
var imagenModificar;
var mostrarOver = true;

jQuery(document).ready(function() {
 

    inicializar();

    jQuery("#abrir-modal-creditos").click(function(event) {
        event.preventDefault();
        jQuery("#modal-comprar-envios").dialog("open");
    });

    jQuery("#boton-cuenta-nueva").click(function(event) {
        event.preventDefault();
        crearCuentaGratis();
    });

    jQuery("#modal-agregar-email").dialog({
        autoOpen : false,
        height : "auto",
        width : "auto",
        modal : true,
        title : "Agregar Email",
        close : function(event, ui) {
            jQuery("#form-agregar-email").find('input:text').val('')
        }
    });

    jQuery("#modal-insertar-img").dialog({
        autoOpen : false,
        height : "auto",
        width : "auto",
        modal : true,
        title : "Insertar Imagen"
    });

    jQuery("#modal-editar-img").dialog({
        autoOpen : false,
        height : "auto",
        width : "auto",
        modal : true,
        title : "Editar Imagen",
        open : function() {
        },
        close : function() {
            var isIframe = jQuery("input[name=isIframe]").val();
            if (isIframe === "true") {
                jQuery('#editorOverlay').show();
            } else {
                jQuery('#editorOverlay').hide();
            }

        }
    });

    jQuery("#modal-comprar-envios").dialog({
        autoOpen : false,
        height : "auto",
        width : "auto",
        modal : true,
        title : "Comprar Envíos",
        open : function() {
            traerPreciosEnvios()
        }
    });
    
    jQuery("#buscarCampana").click(function(){
        
        if(jQuery(this).val() == "Buscar Newsletters.."){
            jQuery(this).val("");
        }
    });
    
    jQuery("#buscarCampana").blur(function(){
        
        if(jQuery(this).val() == ""){
            jQuery(this).val("Buscar Newsletters.."); 
        }
        
     });

    jQuery("#form-agregar-email").submit(function(event) {
        event.preventDefault();

        if (!validarEmail(jQuery("input[name=emailAdmin]").val())) {
            jQuery("#label-error-mail-admin").hide().removeClass("msjError msjExito").addClass("msjError").html("Ingrese un Email Válido").show(300);
            return false;
        }

        var datos = jQuery("#form-agregar-email").serialize();
        datos += "&accion=agregarEmailAdmin"

        jQuery.post(urlHandler, datos, function(json) {

            if (json.root.ajaxResponse.success) {
                var email = json.root.ajaxResponse.email;
                alert("Email Agregado Correctamente");
                jQuery("#modal-agregar-email").dialog("close");

                if (selectAgregarEmail == "FromID") {
                    jQuery("#FromID").prepend("<option value='" + email.EmailID + "' selected='selected' >" + email.Name + " (" + email.EmailAddress + ")</option>");
                    jQuery("#ReplyToID").prepend("<option value='" + email.EmailID + "' selected='selected' >" + email.Name + " (" + email.EmailAddress + ")</option>");

                } else if (selectAgregarEmail == "ReplyToID") {
                    jQuery("#FromID").prepend("<option value='" + email.EmailID + "'  >" + email.Name + " (" + email.EmailAddress + ")</option>");
                    jQuery("#ReplyToID").prepend("<option value='" + email.EmailID + "' selected='selected' >" + email.Name + " (" + email.EmailAddress + ")</option>");
                }

                jQuery("#label-error-mail-admin").hide().removeClass("msjError msjExito");

            } else {
                alert("Error al Agregar");

            }

        }, "json");

    });

    jQuery(".checkEstadoCampana").click(function() {
        var estado = jQuery(this).attr("name");

        if (estado == "Sending") {
            alert("Antes de Editar un Newsletter que se esta enviando, tiene que pausarlo");
            return false;
        } else if (estado == "Scheduled") {
            alert("Antes de Editar un Newsletter programado para enviar, tiene que pausarlo");
            return false;
        }

    });

    if (jQuery("#responder-check").attr("checked") == "checked") {
        jQuery("#responder-fila").show(100);
    } else {
        jQuery("#ReplyToID").val(jQuery("#FromID").val());
    }

    jQuery("#responder-check").click(function() {

        if (jQuery(this).attr("checked") == "checked") {
            jQuery("#responder-fila").show(100);
        } else {
            jQuery("#responder-fila").hide(100);
            jQuery("#ReplyToID").val(jQuery("#FromID").val());
        }

    });

    if (jQuery("#estado-campana").html() == "Completada") {
        jQuery("input[name=submit]").hide();
    }

    jQuery("#guardar-cambios-bt").click(function(event) {

        event.preventDefault();
   
        var selectOK = true;
        jQuery("select[name=FromID],select[name=ReplyToID]").each(function(){
               
               jQuery(this).css("border","1px solid #ddd");
               
               if(jQuery(this).val() == "agregar"){
                    alert("Por Favor revise todos los campos.")
                    jQuery(this).css("border","1px solid red");
                    selectOK = false;
                    return false;
               } 
                
            });
            
            if(!selectOK){
                return false;
            } 
                 alertarPageLeave = false;
        guardarCambiosCampana(false);
        
        
        
        
    });

    function guardarCambiosCampana(enviar) {

        //cerrar editor
        TemplateEditor.releaseWorkingBlock();

        
            
            
            

            var datos = jQuery("#form-editar-campana").serialize().replace(/%5B%5D/g, '[]');

            datos += "&accion=editarCampana";

            jQuery.post(urlHandler, datos, function(json) {

                if (json.root.ajaxResponse.success) {

                    //exito

                    if (jQuery("#form-editar-campana input[name=CampaignID]").val() == "") {
                        CampaignID = json.root.ajaxResponse.campaign.CampaignID;
                        jQuery("input[name=CampaignID]").first().val(CampaignID);
                    }
                    
                    if(jQuery("#ifr-vacio").length == 0){
                        guardarContenidoHTML(enviar);                        
                    }else{
                        jQuery("#msj-respuesta").hide().show(200).removeClass("msjError").addClass("msjExito").html("Newsletter Guardado Correctamente!");
                        setTimeout(function(){jQuery("#msj-respuesta").hide(300) },4000);
                        jQuery("#cargando").hide();
                    }
                    

                } else if (json.root.ajaxResponse.errors == "errorMsg_invalidCampaignDate") {

                    jQuery("#msj-respuesta").hide().show(200).removeClass("msjExito").addClass("msjError").html("Error al Guardar el Newsletter. La fecha de Programacin es Incorrecta");

                } else {
                    jQuery("#mensaje-campana").show(200).addClass("msjError").html("Error al Guardar el  Newsletter. Error: "+json.root.ajaxResponse.errors);
                }

            }, "json");

        
    }


    jQuery("#editar-programacion-envio").click(function(event) {

        event.preventDefault();

        jQuery(this).parent(".misc-pub-section").hide();

        jQuery("#programacion-envio").show(100);

    })

    jQuery("#seleccionar-plantilla-bt").click(function() {

        //jQuery("#form-editar-campana").submit();
        jQuery("#modal-plantillas").dialog("open");
        return false;

    });

    jQuery("#cargando").ajaxStart(function() {
        
        if(mostrarOver){
            jQuery(this).show();             
        }
        
    }).ajaxStop(function() {
        jQuery(this).hide();
    });

    jQuery(document).on('click', '.ui-widget-overlay', function() {
        jQuery(".ui-dialog-titlebar-close").trigger('click');
    });

    jQuery("#prev-navegador-bt").click(function(event) {

        event.preventDefault();

        var CampaignID = jQuery("input[name=CampaignID]").first().val();
        
        
        jQuery.post(urlHandler, {
            accion : "previsualizar-camp",
            CampaignID : CampaignID
        }, function(json) {

            if (json.root.ajaxResponse.success) {

                jQuery("#prev-navegador-contenedor").html(json.root.ajaxResponse.campaign.HTML);

                jQuery("#modalPrevisualizar").dialog("option", "width", 700);
                jQuery("#modalPrevisualizar").dialog("option", "height", 800);
                jQuery("#modalPrevisualizar").dialog('option', 'position', 'center');
                jQuery("#form-previsualizar-contenedor").hide();

            } else {
                jQuery("#prev-navegador-contenedor").html("Error al Obtener la Previsualización. Intente Nuevamente.");
            }

        }, "json");

    });

    jQuery("#prev-email-bt").click(function(event) {

        event.preventDefault();

        jQuery("#label-error-mail").hide();
        var CampaignID = jQuery("input[name=CampaignID]").first().val();
        var email = jQuery("input[name=input-email]").val();
        
            
        if (!validarEmail(email)) {
            jQuery("#label-error-mail").show(300);
            return false;
        }

        jQuery.post(urlHandler, {
            accion : "previsualizar-camp",
            Email : email,
            CampaignID : CampaignID
        }, function(json) {

             if (json.root.ajaxResponse.success) {
                alert("Previsualización Enviada Correctamente!");
                jQuery("#modalPrevisualizar").dialog("close");
            } else {
                alert("Error al Enviar la Previsualización, por favor Intente Nuevamente.");
            }

        }, "json");

    });

    jQuery(".prev-cancelar").click(function() {
        jQuery("#modalPrevisualizar").dialog("close");
    });

    jQuery("#MailListsIds").chosen();

    jQuery("#enviar-campana-bt").click(function() {

        if (jQuery("#ifr-vacio").length == 1) {

            alert("Aún no Generaste el Contenido del Newsletter.")
            return false;

        }
        
        if(!validarForm(jQuery("#form-editar-campana"))){
            alert("Por Favor Verifica todos los Campos antes de Enviar tu Newsletter.")
           return false; 
        }
        

        if (jQuery(this).html() == "Enviar!") {
            var msj = "El Newsletter será Enviado a las Listas de Contactos seleccionadas. Está seguro que desea realizar esta operación?";
        } else {
            var msj = "El Newsletter será Enviado a las Listas de Contactos seleccionadas, el día " + jQuery("input[name=SchedulingDate]").val() + " a las " + jQuery("#input-hora").val() + ":" + jQuery("#input-minuto").val() + ". Está seguro que desa realizar esta operación?";
        }

        if (confirm(msj)) {
            
            alertarPageLeave = false;
            guardarCambiosCampana(true);

            inicializar();

        }

    })

    jQuery(document).on("click",".pausar-campana-bt",function() {

        CampaignID = jQuery(this).attr("name");

        if (confirm("Seguro que desea Pausar el Newsletter?")) {

            jQuery.post(urlHandler, {
                accion : "pausarCampana",
                CampaignID : CampaignID
            }, function(json) {

                if (json.root.ajaxResponse.success) {                    
                    refrescarNewsletters();
                } else {
                    alert("Error al Pausar");                   
                }

            }, "json");

        }
        return false;

    });

    jQuery("#modal-editar-img-aceptar").click(function(event) {
        event.preventDefault();
        var alto = parseInt(jQuery(".editar-img-input[name=alto]").val());
        var ancho = parseInt(jQuery(".editar-img-input[name='ancho']").val());
        var isIframe = jQuery("input[name=isIframe]").val();
        var url = jQuery("#urlImagen").html();
        var align = jQuery("input[name=align]:checked").val();
        var enlace = jQuery(".editar-img-campos [name=enlaceImagen]").val()
        var altImagen = jQuery(".editar-img-campos [name=altImagen]").val()
        
        switch (align) {
            case "der":
                imagenModificar.css("float", "right");
                break;
            case "izq":
                imagenModificar.css("float", "left");
                break;
            case "cen":
                imagenModificar.css("margin", "0 auto, 0, auto").css("float", "none");
                break;
            case"none":
                magenModificar.css("margin", "0").css("float", "none");
                break;    

        }
        
       

        imagenModificar.attr("height", alto).attr("width", ancho).attr("src", url).attr("alt", altImagen);
        imagenModificar.css("height", alto).css("width", ancho);
        
        if(enlace != ""){            
            if(imagenModificar.parent("a").length == 0){
                imagenModificar.wrap("<a href='"+enlace+"'/>");    
            }else{
                imagenModificar.parent("a").attr("href",enlace);
            }            
        }else{
            if(imagenModificar.parent("a").length > 0){
                imagenModificar.unwrap("a");
            }
        }
        
        jQuery("#modal-editar-img").dialog("close");

        if (isIframe === "true") {
            jQuery('#editorOverlay').show();
        } else {
            jQuery('#editorOverlay').hide();
        }

    });

    jQuery(".editar-img-input").keypress(function(event){
                        
        if(event.keyCode == 13){
            resizeImgInput(jQuery(this).attr("name"))
        }      
    });
    
    jQuery(".editar-img-input").focusout(function(event){
            resizeImgInput(jQuery(this).attr("name"))
    });
    

    jQuery("#editar-img-cambiar").click(function(){
        
        
        jQuery("#modal-editar-img").dialog("close");
        
        
        jQuery("#modal-insertar-img").dialog("open");
        jQuery("#contenedor-wp-media").attr("src", urlAdmin + "media-upload.php?type=image&amp;TB_iframe=true");
        jQuery("#contenedor-wp-media").contents().find("body").append('<script>window.send_to_editor = function(html){insertarImagenBloque(html)}</script>');
        
        
        
        
    });


    jQuery(document).on("click",".reanudar-campana-bt",function() {

        CampaignID = jQuery(this).attr("name");

        if (confirm("Seguro que desea Reanudar el Newsletter?")) {

            jQuery.post(urlHandler, {
                accion : "enviarCampana",
                CampaignID : CampaignID
            }, function(json) {

                if (json.root.ajaxResponse.success) {
                    refrescarNewsletters()
                } else {
                    alert("Error al Reanudar");
                   
                }

            }, "json");

        }
        return false;

    })
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;

    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    today = dd + '/' + mm + '/' + yyyy;

    jQuery("#input-fecha").datepicker({
        dateFormat : "dd/mm/yy",
        minDate : today
    });

    jQuery("#opciones-avanzadas-bt").click(function() {

        jQuery("#opciones-avanzadas").toggle(100);
        return false;
    });

    jQuery("#contenedor-plantillas").on("click", ".plantilla-click", function(event) {

        event.preventDefault();

        if (!confirm("Al Cambiar de Plantilla perderá todo su contenido cargado. Está Seguro?")) {
            return false;
        } else {
            alertarPageLeave = false;
            jQuery("#form-editar-campana").append('<input type="hidden" name="idPlantilla" value="' + jQuery(this).attr("name") + '"/>');
            jQuery("#form-editar-campana").submit();
            return false;
        }
    })

    jQuery("#programacion-envio-select").change(function() {

        if (jQuery(this).val() == "1") {
            jQuery("#programacion-envio-alta").show(200);
            jQuery("#enviar-campana-bt").html("Programar Envío").css("width", "100px")
        } else {
            jQuery("#programacion-envio-alta").hide(200);
            jQuery("#enviar-campana-bt").html("Enviar!").css("width", "50px")
        }
    });

    jQuery(".previsualizar-news").click(function(event) {
        
        if(jQuery(this).attr("name") == ""){
            jQuery("#input-campana-id").val(jQuery("input[name=CampaignID]").first().val());    
        }else{
            jQuery("#input-campana-id").val(jQuery(this).attr("name"));    
        }
        
         
         
         
        if(jQuery("#input-campana-id").val() == "" ){
            alert("Antes de Previsualizar, debe Guardar los Cambios.");
            return false;
        }    
        
        jQuery("#modalPrevisualizar").dialog("open");

        
        return false;

    });

    jQuery("#FromID").change(function() {

        if (jQuery(this).val() == "agregar") {
            jQuery("#modal-agregar-email").dialog("open");

            selectAgregarEmail = "FromID";
        } else if (jQuery("#responder-check").attr("checked") != "checked") {
            jQuery("#ReplyToID").val(jQuery("#FromID").val());
        }
    });

    jQuery("#ReplyToID").change(function() {

        if (jQuery(this).val() == "agregar") {
            jQuery("#modal-agregar-email").dialog("open");

            selectAgregarEmail = "ReplyToID";
        }

    });

    jQuery("#contenedor-plantillas").on("change", ".select-categorias", function() {

        var idCampana = CampaignID;
        var categoria = [];
        categoria[0] = jQuery(this).val();

        jQuery.post(urlHandler, {
            accion : "traerPlantillas",
            filterListByCategory : categoria,
            offset : 0,
            limit : 9
        }, function(contenido) {

            jQuery("#tabla-plantillas").remove();

            jQuery("#contenedor-plantillas").html(contenido);

        }, "html")

    });

    jQuery("#contenedor-plantillas").on("click", ".pag-plantilla", function() {

        var idCampana = CampaignID;
        var categoria = [];
        var categoria2 = [];
        categoria[0] = jQuery(".select-categorias").first().val();
        categoria2[0] = jQuery(".select-categorias").last().val()
        
        off = parseInt(jQuery(this).attr("name")) * 9;

        jQuery.post(urlHandler, {
            accion : "traerPlantillas",
            filterListByCategory : categoria,
            filterListByCategory2 : categoria2,            
            offset : off,
            limit : 9
        }, function(contenido) {

            jQuery("#tabla-plantillas").remove();

            jQuery("#contenedor-plantillas").html(contenido);

        }, "html")

    })

    jQuery("#acordeon").accordion({
        heightStyle : "content",
        collapsible : true
    });
    jQuery("#contenedor-wp").css("height", "auto");
    jQuery("#contenedor-estatico").css("height", "auto");

    jQuery("#modal-agregar-ok").click(function(event) {

        event.preventDefault();

        agregarContenidoWP();

    });

    jQuery("#modal-plantillas").dialog({
        autoOpen : false,
        height : "auto",
        width : "auto",
        modal : true,
        title : "Seleccione una Plantilla"

    });

    jQuery(".abrir-modal-plantillas").click(function() {

        jQuery("#modal-plantillas").dialog("open");

    });

    jQuery("#contenedor-plantillas").on("click", "#cerrar-modal-plantillas", function() {

        jQuery("#modal-plantillas").dialog("close");
    });

    jQuery("#modal-agregar-contenido").dialog({
        autoOpen : false,
        height : "auto",
        width : 680,
        modal : true,
        open : function() {

            jQuery(".contenedor-checkbox-post input[type=checkbox]").attr("checked", false);
            jQuery.post(urlHandler, {
                accion : "mostrarPostsWP",
                category : "",
                numberposts : 10,
                offset : 0
            }, function(html) {

                jQuery("#contenedor-posts").html(html);
                jQuery("#modal-agregar-contenido").dialog().scrollTop(jQuery("#contenedor-posts").offset().top);

            }, "html");

        },
        title : "Agregar Contenido Desde Wordpress"

    });

    jQuery("#modal-agregar-cancelar").click(function() {

        jQuery("#modal-agregar-contenido").dialog("close");

    })

    jQuery(".boton-modal.cancelar").click(function() {

        jQuery("#modal-agregar-contenido").dialog("close");

    });

    jQuery("#select-categoria-post").change(function(event) {

        event.preventDefault();

        categoria = jQuery(this).attr("value");

        jQuery.post(urlHandler, {
            accion : "mostrarPostsWP",
            category : categoria,
            numberposts : 10,
            offset : 0
        }, function(html) {

            jQuery("#contenedor-posts").html(html);
            jQuery("#modal-agregar-contenido").dialog().scrollTop(jQuery("#contenedor-posts").offset().top);

        }, "html");

    });

    jQuery("#modalPrevisualizar").dialog({
        autoOpen : false,
        height : "auto",
        width : "auto",
        modal : true,
        title : "Previsualizar Newsletter",
        open: function(event,ui){
            jQuery("body").unbind();
        },
        close : function(event, ui) {
            jQuery("#prev-navegador-contenedor").html("");
            jQuery("#modalPrevisualizar").dialog("option", "width", "auto");
            jQuery("#modalPrevisualizar").dialog("option", "height", "auto");
            jQuery("#modalPrevisualizar").dialog('option', 'position', 'center');
            jQuery("#form-previsualizar-contenedor").show();
            TemplateEditor.init();

        }
    });

    jQuery(document).on("click", ".contenido-post", function() {

        var check = jQuery(this).parents().find(".contenedor-checkbox-post input[type=checkbox]").first().attr("checked");

        if (check != "checked") {
            jQuery(this).parents().find(".contenedor-checkbox-post input[type=checkbox]").first().attr("checked", "checked");

        } else {
            jQuery(this).parents().find(".contenedor-checkbox-post input[type=checkbox]").first().attr("checked", false);

        }

    })
    
    //form suscripcion
    

    
    
});

function cambiarUrlImagen(url) {    
    jQuery("#vista-previa-img img").attr("src", url);
}

function abrirModalInsertarImg() {
    
    alertarPageLeave = true;
    
    jQuery("#modal-insertar-img").dialog("open");
    jQuery("#contenedor-wp-media").attr("src", urlAdmin + "media-upload.php?type=image&amp;TB_iframe=true");
    jQuery("#contenedor-wp-media").contents().find("body").append('<script>window.send_to_editor = function(html){insertarImagenEditor(html)}</script>');

}

function insertarImagenEditor(html) {
    

    var img = jQuery(html);
    TemplateEditor.getWysiwygObject().insertHtml(img.html());
    jQuery("#modal-insertar-img").dialog("close");
}

function insertarImagenBloque(html){
    imagenModificar.replaceWith(html);
    jQuery("#modal-insertar-img").dialog("close");
}

function abrirModalEditarImg(imgMaxWidth, currentEditableBlock, sourceImg, imglink, isIframe) {

    alertarPageLeave = true;
    
    if (isIframe) {
        jQuery("input[name=isIframe]").val(true)
    } else {
        jQuery("input[name=isIframe]").val(false)
    }

    jQuery("#modal-editar-img").dialog("open");
    jQuery("#vista-previa-img").html("");

    jQuery("input[name=alto]").val(sourceImg.css("height").replace("px","")).data("prevVal",sourceImg.css("height").replace("px",""));
    jQuery("input[name=ancho]").val(sourceImg.css("width").replace("px","")).data("prevVal",sourceImg.css("width").replace("px",""));
    
    jQuery(".editar-img-campos [name=enlaceImagen]").val(sourceImg.parent().attr("href"));
    jQuery(".editar-img-campos [name=altImagen]").val(sourceImg.attr("alt"));
    

    jQuery("#urlImagen").html(sourceImg.attr("src"));

    jQuery("#vista-previa-img").append(sourceImg.clone());

    //jQuery("#vista-previa-img img").css("border", "2px dashed #807B7B")

    bindResizable(imgMaxWidth);

    imagenModificar = "";
    imagenModificar = sourceImg;
}

function bindResizable(imgMaxWidth){
    if (imgMaxWidth) {
        jQuery("#vista-previa-img img").resizable({
            maxWidth : imgMaxWidth,
            helper : "ui-resizable-helper",
            resize : function(event, ui) {
                jQuery(".editar-img-input[name=alto]").val(ui.size.height).data("prevVal",ui.size.height);
                jQuery(".editar-img-input[name=ancho]").val(ui.size.width).data("prevVal",ui.size.width );

            }
        });
    } else {
        jQuery("#vista-previa-img img").resizable({

            helper : "ui-resizable-helper",
            resize : function(event, ui) {

                jQuery(".editar-img-input[name=alto]").val(ui.size.height).data("prevVal",ui.size.height);
                jQuery(".editar-img-input[name=ancho]").val(ui.size.width).data("prevVal",ui.size.width);
            }
        });
    }
}

function resizeImgInput(nombreCampo){
         if(jQuery("input[name=editar-img-proporcion]:checked").length == 1){
                     
                previousImageWidth = jQuery('input[name="ancho"]').data('prevVal') ;
                previousImageHeight = jQuery('input[name="alto"]').data('prevVal') ;
        
                if (previousImageWidth > 0 && previousImageHeight > 0) {
           
                    switch(nombreCampo) {
                        case'ancho':
                            proportionsRate = previousImageHeight / previousImageWidth;
                            newVal = jQuery('input[name="ancho"]').val() * proportionsRate;
                            
                            if(newVal < 1){
                                jQuery('input[name="ancho"]').val(parseInt(previousImageWidth,10));
                                return;
                            }
                            
                            jQuery('input[name="alto"]').val(Math.round(newVal));
                            jQuery('input[name="alto"]').data('prevVal',parseInt(newVal,10));
                            jQuery('input[name="ancho"]').data('prevVal',parseInt(jQuery('input[name="ancho"]').val(),10));
                            
                            jQuery("#vista-previa-img img").resizable("destroy").css("height",jQuery('input[name="alto"]').val())
                            .css("width",jQuery('input[name="ancho"]').val());
                            
                          
                            break;
                        case'alto':
                            proportionsRate = previousImageWidth / previousImageHeight;
                            newVal = jQuery('input[name="alto"]').val() *  proportionsRate;
                            
                            if(newVal < 1){
                                jQuery('input[name="alto"]').val(parseInt(previousImageHeight,10));
                                return;
                            }
                            
                            jQuery('input[name="ancho"]').val(Math.round(newVal));
                            jQuery('input[name="ancho"]').data('prevVal',parseInt(newVal,10));
                            jQuery('input[name="alto"]').data('prevVal',parseInt(jQuery('input[name="alto"]').val(),10));
                            
                            jQuery("#vista-previa-img img").resizable("destroy").css("height",jQuery('input[name="alto"]').val())
                            .css("width",jQuery('input[name="ancho"]').val());
                            
                            
                            break;
                    }
                } else {
                    switch(jQuery(this).attr("name")) {
                        case'ancho':
                            jQuery('input[name="ancho"]').val(parseInt(previousImageWidth,10));
                            break;
                        case'alto':
                            jQuery('input[name="alto"]').val(parseInt(previousImageHeight,10));
                            break;
                    }   
                    jQuery("#vista-previa-img img").resizable("destroy").css("height",jQuery('input[name="alto"]').val())
                            .css("width",jQuery('input[name="ancho"]').val());
                            
                }     
            
            
            
        }else{
            jQuery("#vista-previa-img img").resizable("destroy").css("height",jQuery('input[name="alto"]').val())
                            .css("width",jQuery('input[name="ancho"]').val());
        }
        
        
        
      bindResizable(false)
    }
    
function traerPreciosEnvios() {

    jQuery.post(urlHandler, {
        accion : "traerPreciosEnvios"
    }, function(html) {

        jQuery("#contenedor-precios").html(html);

        jQuery("#form-comprar-envios").submit(function(event) {
            event.preventDefault();
            if (jQuery("input[name=plan]:checked").length < 1) {
                alert("Debe Seleccionar Algna Opcion para Continuar");
                return false;
            }

            var plan = jQuery("input[name=plan]:checked").val();
            var codigoPais = jQuery("input[name=codigoPais]").val();

            jQuery.ajax({
                url : 'https://dattatec.com/ajax-add-carrito-bulk.php?jsoncallback=?',
                dataType : 'json',
                type : 'POST',
                data : {
                    pais : codigoPais, // services requires string
                    origen : 'wp-plugin-envialosimple', // services requires string
                    periodo : [12000], // service requires array
                    plan : [plan],
                    cantidad : [1] // service requires array
                    // service requires string
                }
            }).done(function(data) {
                var response = data && data.root && data.root.site && data.root.site.ok, a, e;

                if (response) {

                    window.location.href = "https://dattatec.com/site/mis-compras";

                } else {
                    alert("Se ha producido un Error");
                }

            }).fail(function(jqXHR, status, errorMSG) {
                alert("Se ha producido un Error");
            });
        });

    }, "html")
}

function crearCuentaGratis() {
    jQuery("#cargando").show();

    jQuery.ajax({
        url : 'https://dattatec.com/ajax-add-carrito-bulk.php?jsoncallback=?',
        dataType : 'json',
        type : 'POST',
        data : {
            origen : 'wp-plugin-envialosimple', // services requires string
            periodo : [12000], // service requires array
            plan : ["envialosimple_500"],
            cantidad : [1], // service requires array
            comentario : [dominio]
            // service requires string
        }
    }).done(function(data) {
        var response = data && data.root && data.root.site && data.root.site.ok, a, e;

        jQuery("#cargando").hide();
        if (response) {
                
            window.location.href = "https://dattatec.com/site/sp/mis-compras#continuar";
        } else {
            alert("Se ha producido un Error");
        }

    }).fail(function(jqXHR, status, errorMSG) {
        alert("Se ha producido un Error");
    });

}

jQuery(function(jQuery) {
    jQuery.datepicker.regional['es'] = {
        closeText : 'Cerrar',
        prevText : '&#x3c;Ant',
        nextText : 'Sig&#x3e;',
        currentText : 'Hoy',
        monthNames : ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort : ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        dayNames : ['Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado'],
        dayNamesShort : ['Dom', 'Lun', 'Mar', 'Mi&eacute;', 'Juv', 'Vie', 'S&aacute;b'],
        dayNamesMin : ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'S&aacute;'],
        dateFormat : 'dd/mm/yy',
        firstDay : 0,
        isRTL : false
    };
    jQuery.datepicker.setDefaults(jQuery.datepicker.regional['es']);
});

function validarForm(form, msjForm) {

    var valido = true;
    form.find("input.validar,select.validar").each(function() {

        jQuery(this).css("border", "1px solid #DFDFDF");
        jQuery(".chzn-choices").css("border", "1px solid #DFDFDF");

        if (jQuery.trim(jQuery(this).val()).length < 1) {

            jQuery(this).css("border", "1px solid red");

            if (jQuery(this).hasClass("chzn-done")) {
                jQuery(".chzn-choices").css("border", "1px solid red");
            }
            valido = false;
        }

    });

    return valido;
}

function validarEmail(email) {
    return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(email);
}

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
        vars[key] = value;
    });
    return vars;
}

function guardarContenidoHTML(enviar) {

    var enviarCampana = enviar;
    jQuery(".preventMouseActionsOverlay").hide();

    if (jQuery("#form-editar-campana input[name=CampaignID]").val() != "") {

        var idCampana = jQuery("#form-editar-campana input[name=CampaignID]").val();

        var saveRestorePoint = saveRestorePoint || false;

        jQuery(".dropeable").not(":last").remove();

        var content = jQuery('[data-containerName=editorBlocksContainer] [data-containerName=templateEditorBody]').html();
        var plainText = jQuery('[data-containerName=editorBlocksContainer][data-containerName=plainTextVersionContent]').val();

        var advanceEditable = TemplateEditor.advanceEditable ? 1 : 0;
        var autoContentAlternate = TemplateEditor.autoContentAlternate ? 1 : 0;
        var remoteTemplateUrl = TemplateEditor.remoteTemplateUrl || '';
        var plainTextContent = TemplateEditor.assignPlainTextContent(plainText, content, self.autoContentAlternate);
        var sourceCodeContent = TemplateEditor.assignSourceCodeContent(jQuery('[data-containerName=editorBlocksContainer] [data-containerName=templateEditorBody] > .templateBoundary').html());

        var contentCopy = content;
        var remoteUnsubscribeBlock = '';

        if (remoteTemplateUrl.length > 0) {
            var node = jQuery('<div>').append(content);
            var hasUnsubscribeLink = content.match(/%UnSubscribe%/g);

            if (!hasUnsubscribeLink) {
                node.append(TemplateEditor.unsubscribeBlock);
            }

            var unsuscribe = jQuery(node).find('[id="unsubscribeBlock"]:last');
            remoteUnsubscribeBlock = unsuscribe.outerHtml();
            content = '';
            advanceEditable = 0;
        }
        jQuery("#cargando").show()

        jQuery.ajax({
            data : {
                accion : "guardarContenidoHTML",
                HTML : content,
                AdvanceEditable : advanceEditable,
                URL : remoteTemplateUrl,
                PlainText : plainTextContent,
                CampaignID : idCampana,
                RemoteUnsubscribeBlock : remoteUnsubscribeBlock,
                AutoContentAlternate : autoContentAlternate
            },
            type : "POST",
            url : urlHandler,
            timeout : 20000,

            contentType : 'application/x-www-form-urlencoded; charset=UTF-8',
            dataType : 'json',
            success : function(json) {
              
                if (json.root.ajaxResponse.success) {
                    
                    if (enviarCampana === true) {

                       
                        jQuery.post(urlHandler, {
                            accion : "enviarCampana",
                            CampaignID : idCampana
                        }, function(json) {

                            if (json.root.ajaxResponse.success) {
                                window.location = urlAdmin + "admin.php?page=envialo-simple&camp-enviada=1";
                            } else {
                                alert("Error al enviar");
                            }

                        }, "json");

                    } else {
                        jQuery("#msj-respuesta").hide().show(200).removeClass("msjError").addClass("msjExito").html("Newsletter Guardado Correctamente!");
                        setTimeout(function(){jQuery("#msj-respuesta").hide(300) },4000);
                        jQuery("#cargando").hide();
                        return true;
                    }
                }
            },
            error : function() {
                jQuery("#cargando").hide();
                return false;
            }
        });

    }

    inicializar();

}

function inicializar() {

    jQuery(".dropeable").not(":last").remove()

    //inserto zona dropeable
    jQuery(".dropeable:first").clone().insertBefore(jQuery('[data-containername="htmlEditorContainer"]').contents().find(".tobBlock.tobClonable:eq(0)"));    
    jQuery(".dropeable:first").clone().insertAfter(jQuery('[data-containername="htmlEditorContainer"]').contents().find(".tobBlock.tobClonable"));

    //drag&drop
    jQuery(".drag-contenido").draggable({
        tolerance : "touch",
        helper : "clone",
        revert : "invalid",
        iframeFix : true,

        start : function(event, ui) {

        },
        stop : function(event, ui) {

        }
    });

    bindDropeable();

    //obtengo color de bg
/*
    if ( typeof jQuery(".tobBlock:eq(1)").css("background-color") != "undefined" && jQuery(".tobBlock:eq(1)").css("background-color") != "rgba(0, 0, 0, 0)") {
        colorBg = rgb2hex(jQuery(".tobBlock:eq(1)").css("background-color"));
    }

    if ( typeof jQuery(".tobBlock:eq(2)").css("background-color") != "undefined" && jQuery(".tobBlock:eq(2)").css("background-color") != "rgba(0, 0, 0, 0)") {
        colorBg1 = rgb2hex(jQuery(".tobBlock:eq(2)").css("background-color"));
    }

    if ( typeof jQuery(".templateBoundary td").css("background-color") != "undefined" && jQuery(".templateBoundary td").css("background-color") != "rgba(0, 0, 0, 0)") {
        colorBg2 = rgb2hex(jQuery(".templateBoundary td").css("background-color"));
    }
    
    */

}

function dropContenido(idElemento, espacioDrop) {

    switch(idElemento) {

        case "cont1":
            agregarContenidoEstatico(jQuery("#contenido-1:first"), espacioDrop);
            break;

        case "cont2":
            agregarContenidoEstatico(jQuery("#contenido-2:first"), espacioDrop);
            break;

        case "cont3":
            agregarContenidoEstatico(jQuery("#contenido-3:first"), espacioDrop);
            break;

        case "cont4":
            agregarContenidoEstatico(jQuery("#contenido-4:first"), espacioDrop);
            break;

        case "cont5":
            agregarContenidoEstatico(jQuery("#contenido-5:first"), espacioDrop);

            break;

        case "cont6":
            agregarContenidoEstatico(jQuery("#contenido-6:first"), espacioDrop);

            break;
        case "cont7":
            agregarContenidoEstatico(jQuery("#contenido-7:first"), espacioDrop);

            break;
        case "cont8":
            agregarContenidoEstatico(jQuery("#contenido-8:first"), espacioDrop);

            break;

        case "cont1wp":
           
            formatoACopiar = jQuery("#contenido-1");
            espacioAInsertar = espacioDrop;
            jQuery("#modal-agregar-contenido").dialog("open");

            break;
        case "cont2wp":
            formatoACopiar = jQuery("#contenido-2");
            espacioAInsertar = espacioDrop;
            jQuery("#modal-agregar-contenido").dialog("open");

            break;

        case "cont3wp":
            formatoACopiar = jQuery("#contenido-3");
            espacioAInsertar = espacioDrop;
            jQuery("#modal-agregar-contenido").dialog("open");
            break;

        case "cont4wp":
            formatoACopiar = jQuery("#contenido-4");
            espacioAInsertar = espacioDrop;
            jQuery("#modal-agregar-contenido").dialog("open");
            break;

        default:

    }

    bindDropeable();
}

function agregarContenidoEstatico(contenido, espacioDrop) {

    alertarPageLeave = true;
    
    var div = jQuery(contenido.html());
    espacioDrop.before(div);

    div.before(jQuery("<div class='dropeable'/>"))
    //div.find(".tobBlock").css("background", colorBg);
}

function agregarContenidoWP() {
    
    alertarPageLeave = true;

    if (jQuery(".checkbox-post:checked").length == 0) {
        alert("Debe Seleccionar Algun Post Para Continuar");
        return false;
    }

    var posts = jQuery(".checkbox-post:checked").parents(".post");

    posts.each(function() {
        var resultado = "";

        var html = formatoACopiar.clone();

        html.find(".titulo-wp").html(jQuery(this).find(".titulo-post").html());
        html.find(".contenido-wp").html(jQuery(this).find(".resumen-post").html());
        html.find(".imagen-wp").attr("src", jQuery(this).find(".slides_control > div :visible").attr("name"));
        
        
        html.find(".imagen-wp").wrap("<a href='"+jQuery(this).find(".ver-mas-post").attr("href")+"' />");
        
        resultado += html.html();

        var div = jQuery(resultado);
        espacioAInsertar.after(div);

        div.after(jQuery("<div class='dropeable'/>"))
        //div.find(".tobBlock").css("background", colorBg);

    });

    bindDropeable();

  
    jQuery("#modal-agregar-contenido").dialog("close");

}

function bindDropeable() {
    jQuery(".dropeable").droppable({

        drop : function(event, ui) {

            dropContenido(jQuery(ui.draggable).attr("id"), jQuery(this));

        },
        accept : ".drag-contenido",
        hoverClass : "dropHover",
        activeClass : "dropActivo",
        tolerance : "touch"
    });
}

function rgb2hex(color) {
    if (color.substr(0, 1) === '#') {
        return color;
    }
    var digits = /(.*?)rgb\((\d+), (\d+), (\d+)\)/.exec(color);

    var red = parseInt(digits[2]);
    var green = parseInt(digits[3]);
    var blue = parseInt(digits[4]);

    var rgb = blue | (green << 8) | (red << 16);
    return digits[1] + '#' + rgb.toString(16);
};
