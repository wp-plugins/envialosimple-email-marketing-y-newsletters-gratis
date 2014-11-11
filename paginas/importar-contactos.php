<?php

include_once(ENVIALO_DIR."/clases/EnvialoSimple.php");
include_once(ENVIALO_DIR."/clases/Contactos.php");

$ev = new EnvialoSimple();
$ev->checkSetup();

$co = new Contactos();

if(isset($_GET["MailListsIds"])){
    $MailListsIds = filter_var($_GET["MailListsIds"], FILTER_SANITIZE_NUMBER_INT);
}

$listas = $co->listarListasContactos(-1);

$htmlListas ="";

foreach($listas[0]['item'] as $l){
    $selected = "";

    if($MailListsIds == $l['MailListID']){
        $selected = "selected='selected'";
    }
    
    $htmlListas .= "<option {$selected} value='{$l['MailListID']}' >{$l['Name']} ({$l['MemberCount']} Destinatarios)</option>";
}

?>
<link rel="stylesheet" href="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/css/fineuploader.css"); ?>" type="text/css" media="all"/>
<script type="text/javascript" src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/jquery.fineuploader-3.0.min.js"); ?>"></script>
<div class="wrap">
    <div id="icon-users" class="icon32">
        <br/>
    </div>
    <h2><?php _e('Importar Contactos a la Lista', 'envialo-simple');?></h2> 
    
    <p><?php _e('Desde aquí puedes importar contactos a la lista seleccionada, utilizando los siguentes métodos:', 'envialo-simple');?></p>

    <div class="tool-box" id="contenedor-1">
        <?php if(!$co->importarSelectSource($MailListsIds)):?>
            <div class='mensaje msjError' style='display:block'><?php _e('Ha Ocurrido un Error. Por Favor Intente Nuevamente', 'envialo-simple');?> </div>
        <?php endif; ?>
        <div id="contenedor-importacion">
            <div class="metodo-importacion">
                <div id="imagenCsv"></div>
                <p class="titulo"><?php _e('Importar Lista desde Archivo .csv', 'envialo-simple');?></p>

                <p class="texto"><?php _e('Haz click en el botón "Subir Archivo" y selecciona el documento .csv que deseas importar', 'envialo-simple');?></p>

                <div>
                    <div id="fine-uploader"></div>
                    <script type="text/javascript">
                        var urlUpload = "<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/php/fileUpload.php')?>"
                        jQuery('#fine-uploader').fineUploader({
                            request:{
                                endpoint:urlUpload
                            },
                            debug:false
                        }).on('complete', function (event, id, filename, responseJSON) {
                                    if (responseJSON.error) {
                                        alert(responseJSON.error)
                                        return false;
                                    }
                                    var file = responseJSON.file;
                                    jQuery.post(urlHandler, {accion:"uploadFile", file:file}, function (json) {
                                        mostrarCsvTabla(json)
                                        jQuery("input[name=accion]").val("processFile")
                                    }, "json");
                                });
                    </script>
                </div>
            </div>
            <div class="metodo-importacion">
                <div id="imagenCopy"></div>
                <p class="titulo"><?php _e('Copiar y pegar', 'envialo-simple');?></p>

                <p class="texto"><?php _e('Puedes copiar y pegar el contenido de un archivo o escribirlos manualmente en el siguiente formulario.', 'envialo-simple');?></p>

                <div class="formulario">
                    <textarea name="CopyPaste"></textarea>
                </div>
                <input type="submit" class="button-primary" id="boton-copypaste" style="width: 70px;display: block;margin: 10px auto;" value="<?php _e('Cargar', 'envialo-simple');?>"/>
            </div>
        </div>
        <form id="form-correspondencias" method="post" action="#">
            <div id="contenedor-campos-csv">
                <div id="contenedor-tabla-csv">
                    <p><?php _e('Selecciona los Campos que deseas Importar a la Lista.', 'envialo-simple');?></p>

                    <p><?php _e('Se muestran los 10 primeros Contactos que se importarán.', 'envialo-simple');?></p>
                    <table id="tabla-campos-csv">
                        <thead>
                            <tr>&nbsp;</tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <br/>

                <h3>Importar a:</h3>

                <p>
                    <select name="MailListsIds">
                        <?php echo $htmlListas; ?>
                    </select>
                </p>

                <p>
                    <input type="hidden" value="" name="accion"/>
                    <input type="submit" id="boton-procesar" class="button-primary" value="<?php _e('Procesar', 'envialo-simple');?>"/>
                    <input type="reset" class="button-secondary" onclick="window.location='<?php echo get_admin_url()."admin.php?page=envialo-simple-listas";?>' " value="<?php _e('Cancelar', 'envialo-simple');?>"/>
                </p>
            </div>
        </form>

        <div id="reportes-importacion">
            <h3><?php _e('Reportes de Importación', 'envialo-simple');?></h3>

            <p><?php _e('Contactos Importados', 'envialo-simple');?>: <span id="ImportStatsImported"></span></p>

            <p><?php _e('Contactos NO Importados', 'envialo-simple');?>: <span id="ImportStatsFailed"></span></p>

            <p><?php _e('Contactos Duplicados', 'envialo-simple');?>: <span id="ImportStatsDuplicates"></span></p>

            <p><?php _e('Contactos con Email Inválido', 'envialo-simple');?>: <span id="ImportStatusInvalidEmail"></span></p>

            <p><?php _e('Contactos con Email En Lista Negra', 'envialo-simple');?>: <span id="ImportStatusBlackListed"></span></p><br/>

            <p><?php _e('Líneas Procesadas', 'envialo-simple');?>: <span id="TotalLines"></span></p><br/> <br/>

            <p>
                <a class="button-secondary" href="<?php echo get_admin_url()."admin.php?page=envialo-simple-listas";?>"><?php _e('Continuar', 'envialo-simple');?></a>
            </p>

        </div>
    </div>
</div>

<script type="text/javascript">

    jQuery(document).ready(function () {
        jQuery("#boton-procesar").click(function (event) {
            event.preventDefault();
            var corresponder = {};
            corresponder.campos = []
            var i = 0;
            var accion;
            var MailListsIds = jQuery("select[name=MailListsIds]").val();
            jQuery(".corresponder").each(function () {
                if (jQuery(this).val() != "") {
                    var valor = new Object();
                    valor["Correspond" + i + ""] = jQuery(this).val();
                    corresponder.campos.push(valor);
                }
                i++;
            });
            if (jQuery("input[name=accion]").val() == "processFile") {
                accion = "processFile";
            } else {
                accion = "processCopy";
            }
            jQuery.post(urlHandler, {accion:accion, MailListsIds:MailListsIds, corresponder:corresponder, test:"alal"}, function (json) {
                if (json.root.ajaxResponse.success) {
                    var stats = json.root.ajaxResponse.stats;
                    jQuery("#ImportStatsImported").html(stats.ImportStatsImported);
                    jQuery("#ImportStatsFailed").html(stats.ImportStatsFailed);
                    jQuery("#ImportStatsDuplicates").html(stats.ImportStatsDuplicates);
                    jQuery("#ImportStatusInvalidEmail").html(stats.ImportStatusInvalidEmail);
                    jQuery("#ImportStatusBlackListed").html(stats.ImportStatusBlackListed);
                    jQuery("#TotalLines").html(stats.TotalLines);
                    jQuery('#contenedor-campos-csv').hide();
                    jQuery('#reportes-importacion').show(150);
                } else {
                    if (json.root.ajaxResponse.errors.errorMsg_selectEmailColumn == "") {
                        alert("<?php _e('No ha Seleccionado la Columna con la Dirección de Correo. Por favor Intente Nuevamente.', 'envialo-simple');?>")
                    } else {
                        alert("<?php _e('Se ha producido un error, por favor intente nuevamente.', 'envialo-simple');?>");
                    }
                }
            }, "json");
        });
        jQuery("#boton-copypaste").click(function () {
            var CopyPaste = jQuery("textarea[name=CopyPaste]").val();
            if (CopyPaste == "") {
                alert("<?php _e('Por Favor ingresa campos separados por coma en el formulario.', 'envialo-simple');?>");
                return false;
            }
            jQuery.post(urlHandler, {accion:"copypaste", CopyPaste:CopyPaste}, function (json) {
                mostrarCsvTabla(json)
                jQuery("input[name=accion]").val("processCopy")
            }, "json")
        });
    });

    function validarEmail(email) {
        return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(email);
    }

    function mostrarCsvTabla(json) {
        if (json.root.ajaxResponse.success) {
            jQuery("#contenedor-importacion").css("opacity", .1).animate({opacity:0}, 500, function () {
                jQuery("#contenedor-importacion").hide()
                jQuery("#contenedor-campos-csv").css("display", "block").css("opacity", .1).animate({opacity:1}, 600);
            });
            json = json.root.ajaxResponse;
            //agrego selects
            jQuery("#contenedor-select").html("");
            for (var j in json.filecolumns.column) {
                jQuery("#tabla-campos-csv thead tr").append("<th><select class='corresponder' name='Correspond" + j + "' id='select-columna-" + j + "'><option value=''><?php _e('No Importar', 'envialo-simple');?></option></select></th>")
                for (var i in json.fields.field) {
                    jQuery("#select-columna-" + j).append("<option value='" + json.fields.field[i].Id + "' >" + json.fields.field[i].Name + "</option>");
                }
            }
            for (var h in json.sampleLines.line) {
                jQuery("#tabla-campos-csv tbody").append("<tr/>");
                for (var g in json.sampleLines.line[h].column) {
                    jQuery("#tabla-campos-csv tbody tr").last().append("<td>" + json.sampleLines.line[h].column[g] + "</td>");
                    if (validarEmail(json.sampleLines.line[h].column[g])) {
                        jQuery("#select-columna-" + g).val("Email");
                    }
                }
            }
        }
    }
</script>