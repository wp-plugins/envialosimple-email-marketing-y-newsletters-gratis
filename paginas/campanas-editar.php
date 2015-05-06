<?php
include_once(ENVIALO_DIR . "/clases/EnvialoSimple.php");
include_once(ENVIALO_DIR . "/clases/Campanas.php");
include_once(ENVIALO_DIR . "/clases/Contactos.php");

function isCheck($valor) {
    if ($valor) {
        return "checked='checked'";
    } else {
        return "";
    }
}

$ev = new EnvialoSimple();
$ev->checkSetup();
$ca = new Campanas();
$template = '';
$addPreheader = false;


//click desde la lista
if (isset($_GET["idCampana"])) {
    $idCampana = filter_var($_GET["idCampana"], FILTER_SANITIZE_NUMBER_INT);

    $camp = $ca->traerCampana($idCampana);

    if (isset($camp["success"])) {
        $c = $camp["campaign"];
        if ($c["From"]["EmailID"] != $c["ReplyTo"]["EmailID"]) {
            $c["responder-check"] = 1;
        }
    } else {
        $msg = "<p>" . _e("No se puede establecer la conexión con el servidor.", "envialo-simple") . "</p>
                    <p><a href='#' class='button-primary'>" . _e('Vuelva a intentarlo', 'envialo-simple') . "</a></p>";
        echo $msg;
        die();
    }


    $c["Name"] = isset($_POST["CampaignName"]) ? $_POST["CampaignName"] : $c["Name"];
    $c["From"]["EmailID"] = isset($_POST["FromID"]) ? $_POST["FromID"] : $c["From"]["EmailID"];

    $c["ReplyTo"]["EmailID"] = isset($_POST["ReplyToID"]) ? $_POST["ReplyToID"] : $c["ReplyTo"]["EmailID"];
    $c["SendStateReport"] = isset($_POST["SendStateReport"]) ? $_POST["SendStateReport"] : $c["SendStateReport"];
    $c["AddToPublicArchive"] = isset($_POST["AddToPublicArchive"]) ? $_POST["AddToPublicArchive"] : $c["AddToPublicArchive"];
    $c["contenidoAnterior"] = isset($_POST["contenidoAnterior"]) ? TRUE : FALSE;


    if (isset($_POST["changeScheduling"])) {

        if (!isset($_POST["changeScheduling"]) || $_POST["changeScheduling"] == 0) {
            //Envío Ahora
            $c["schedule"]["ScheduleType"] = "Send Now";
            $c["schedule"]["ScheduleSendDate"] = "0000-00-00 00:00:00";
        } elseif ($_POST["changeScheduling"] == 1) {
            //Programo envío
            $c["schedule"]["ScheduleType"] = "One time scheduled";
            list($day, $month, $year) = sscanf($_POST["SchedulingDate"], '%02d/%02d/%04d');
            $fecha = $year . "-" . $month . "-" . $day;
            $c["schedule"]["ScheduleSendDate"] = $fecha . " " . $_POST["SchedulingHour"] . ":" . $_POST["SchedulingMinute"] . ":00";
        } elseif ($_POST["changeScheduling"] == 2) {
            //no programo envio
            $c["schedule"]["ScheduleType"] = "Not scheduled";
            $c["schedule"]["ScheduleSendDate"] = "0000-00-00 00:00:00";
        }
    }



    $c["Subject"] = isset($_POST["CampaignSubject"]) ? $_POST["CampaignSubject"] : $c["Subject"];
    $c["TrackLinkClicks"] = isset($_POST["TrackLinkClicks"]) ? $_POST["TrackLinkClicks"] : $c["TrackLinkClicks"];
    $c["TrackReads"] = isset($_POST["TrackReads"]) ? $_POST["TrackReads"] : $c["TrackReads"];
    $c["TrackAnalitics"] = isset($_POST["TrackAnalitics"]) ? $_POST["TrackAnalitics"] : $c["TrackAnalitics"];



    if (isset($_POST["MailListsIds"])) {
        $c["maillists"]["count"] = isset($_POST["MailListsIds"]) ? count($_POST["MailListsIds"]) : $c["maillists"]["count"];

        if ($c["maillists"]["count"]) {
            $listas = array();
            foreach ($_POST["MailListsIds"] as $id) {
                array_push($listas, array("MailListID" => $id));
            }
            $c["maillists"]["rows"] = $listas;
        }
    }

    $c["responder-check"] = isset($_POST["responder-check"]) ? 1 : 0;
} else {
    //Cuando es nueva
    $c = array();
    $c["CampaignID"] = isset($_POST["CampaignID"]) ? $_POST["CampaignID"] : '';
    date_default_timezone_set("America/Buenos_Aires"); //TODO: ESTO HAY QUE VER COMO FUNCIONA EN OTrOS SERVIDORES
    $c["Name"] = isset($_POST["CampaignName"]) ? $_POST["CampaignName"] : "Newsletter " . date("d-m-Y H:i");
    $c["From"]["EmailID"] = isset($_POST["FromID"]) ? $_POST["FromID"] : 0;
    $c["From"]["EmailAddress"] = 0;
    $c["From"]["Name"] = 0;
    $c["ReplyTo"]["EmailID"] = isset($_POST["ReplyToID"]) ? $_POST["ReplyToID"] : 0;
    $c["SendStateReport"] = isset($_POST["SendStateReport"]) ? $_POST["SendStateReport"] : 1;
    $c["AddToPublicArchive"] = isset($_POST["AddToPublicArchive"]) ? $_POST["AddToPublicArchive"] : 0;
    $c["contenidoAnterior"] = isset($_POST["contenidoAnterior"]) ? TRUE : FALSE;
    if (!isset($_POST["changeScheduling"]) || $_POST["changeScheduling"] == 0) {
        //Envío Ahora
        $c["schedule"]["ScheduleType"] = "Send Now";
        $c["schedule"]["ScheduleSendDate"] = "0000-00-00 00:00:00";
    } elseif ($_POST["changeScheduling"] == 1) {
        //Programo envío
        $c["schedule"]["ScheduleType"] = "One time scheduled";

        list($day, $month, $year) = sscanf($_POST["SchedulingDate"], '%02d/%02d/%04d');

        $fecha = $year . "-" . $month . "-" . $day;

        $c["schedule"]["ScheduleSendDate"] = $fecha . " " . $_POST["SchedulingHour"] . ":" . $_POST["SchedulingMinute"] . ":00";
    } elseif ($_POST["changeScheduling"] == 2) {
        //no programo envio
        $c["schedule"]["ScheduleType"] = "Not scheduled";
        $c["schedule"]["ScheduleSendDate"] = "0000-00-00 00:00:00";
    }

    $c["Subject"] = isset($_POST["CampaignSubject"]) ? $_POST["CampaignSubject"] : '';
    $c["TrackLinkClicks"] = isset($_POST["TrackLinkClicks"]) ? $_POST["TrackLinkClicks"] : 1;
    $c["TrackReads"] = isset($_POST["TrackReads"]) ? $_POST["TrackReads"] : 1;
    $c["TrackAnalitics"] = isset($_POST["TrackAnalitics"]) ? $_POST["TrackAnalitics"] : 1;
    $c["Status"] = "Draft";
    $c["maillists"]["count"] = isset($_POST["MailListsIds"]) ? count($_POST["MailListsIds"]) : 0;
    if ($c["maillists"]["count"]) {
        $listas = array();
        foreach ($_POST["MailListsIds"] as $id) {
            array_push($listas, array("MailListID" => $id));
        }
        $c["maillists"]["rows"] = $listas;
    }



    $c["responder-check"] = isset($_POST["responder-check"]) ? 1 : 0;
}
include_once(ENVIALO_DIR . "/paginas/header.php");

$seleccionarPlantilla = FALSE;
$alertarPageLeave = isset($_POST['idPlantilla']) ? "true" : "false";


if (isset($_POST['idPlantilla']) && $_POST['idPlantilla']) {
    $idPlantilla = $_POST["idPlantilla"];
    $template = utf8_encode(file_get_contents("http://v2.envialosimple.com/mailing_templates/" . $idPlantilla . "/content.htm"));
    $addPreheader = true;
} else {
    if ($c["contenidoAnterior"]) {
        $template = stripslashes($_POST["contenidoAnterior"]);
    } elseif (isset($c["Content"]) && $c["Content"]) {
        $template = $ca->traerCuerpoCampana($idCampana);
    } else {
        $seleccionarPlantilla = TRUE;
    }
}
Campanas::loadTag($template, 'body');
$template = str_replace('-|campaignBaseURL|-', "http://v2.envialosimple.com", $template);
?>
<link rel="stylesheet"  href="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/css/templateEditorRestoreNormalCss.css"); ?>" type="text/css" media="all" />
<link rel="stylesheet"  href="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/css/templateEditor.css"); ?>" type="text/css" media="all" />
<link rel="stylesheet"  href="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/css/jquery.wysiwyg.css"); ?>" type="text/css" media="all" />
<link rel="stylesheet"  href="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/css/jPicker-1.1.6.css"); ?>" type="text/css" media="all" />
<link rel="stylesheet"  href="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/css/common.combined.css"); ?>" type="text/css" media="all" />


<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/common.combined.js"); ?>"></script>
<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/contentEditor.js"); ?>"></script>
<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/contentEdit.js"); ?>"></script>
<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/jquery.wysiwyg.js"); ?>"></script>
<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/bootstrap.js"); ?>"></script>
<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/simplemodal.1.4.3.js"); ?>"></script>
<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/wysiwygExtras.js"); ?>"></script>
<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/getText.js"); ?>"></script>

<?php
$locale = substr(get_locale(), 0, 2);
switch ($locale) {
    case 'es':
        $urlLoc = "envialosimple-email-marketing-y-newsletters-gratis/languages/es.json";
        break;

    case 'pt':
        $urlLoc = "envialosimple-email-marketing-y-newsletters-gratis/languages/pt.json";
        break;

    case 'en':
        $urlLoc = "envialosimple-email-marketing-y-newsletters-gratis/languages/en.json";
        break;

    default:
        break;
}
?>

<script type="text/javascript"  src="<?php echo plugins_url($urlLoc); ?>"></script>

<script xmlns="" language="javascript" type="text/javascript">

    var locale = "<?php echo $locale; ?>"

    TemplateEditor.setCurrentRecordID("<?php echo $c["CampaignID"]; ?>");
    TemplateEditor.addPreheaderOnTemplateLoad = <?php echo $addPreheader ? '1' : '0'; ?>;

    var alertarPageLeave = <?php echo $alertarPageLeave ?>;

    jQuery(window).bind('beforeunload', function () {

        if (alertarPageLeave) {
            return '<?php _e('Al abandonar ésta página, se perderán todos los cambios no guardados.', 'envialo-simple'); ?>';
        } else {
            return;
        }
    });

    jQuery(window).bind('keypress', function (event) {
        alertarPageLeave = true;
    });



    /**
     * Translation
     *
     * @copyright   Dattatec.com s.r.l.
     * @author      Javier Valderrama
     *
     * @Ver http://jsgettext.berlios.de/doc/html/Gettext.html para mas info de Gettext
     */


    var params = {
        "domain": 'javascript-' + locale,
        "locale_data": json_locale_data
    };
    var gt = new Gettext(params);


    /**
     * @param {String} term
     * @param {Boolean} override
     * @returns {String} gt.gettext(term) or term if override is true
     *
     */
    function __(term, override) {

        term = term ? jQuery.trim(term) : '';
        override = override || false;

        if (override) {
            return term;
        }

        var messageTranslated = gt.gettext(term)
                , exeptionsList;

        if ((term.substring(0, 9) === "errorMsg_" || locale !== 'es') && messageTranslated === term)
        {

            exeptionsList = ['clicks', 'e-mail', 'etc', 'password', 'spam rating'
                        , 'info:', 'info', 'no', 'video tutorial', '¡email marketing!'
                        , 'error', 'ok', 'robot', 'hexadecimal', 'links', 'zen', 'spa'
                        , 'simple', 'retro', 'newsletter', 'hotel', 'call center'
                        , 'general', 'url', 'editor', 'email'
            ];

        }

        return messageTranslated;
    }


</script>
<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/jpicker-1.1.6.js"); ?>"></script>


<div class="wrap">
    <div id="msj-respuesta" class="mensaje" style="width: 55%">
    </div>
    <form id="form-editar-campana" action="#" method="post">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2" style="margin-right: 300px;" >

                <div id="post-body-content" style="width: 100%;float: left;">

                    <div id="currentTemplateInfo">
                        <input type="hidden" value="" name="currentTemplateURL">
                            <input type="hidden" value="" name="currentTemplateAdvanceEditable">
                                </div>
                                <input type="hidden" name="CampaignID" value="<?php echo $c["CampaignID"]; ?>"/>
                                <input xmlns="" type="hidden" value="<?php echo $c["CampaignID"]; ?>" name="currentRecordID" id="currentRecordID">

                                    <table class="form-table">
                                        <tbody>
                                            <input name="CampaignName" type="hidden" id="CampaignName" style="width: 270px" value="<?php echo $c["Name"]; ?>" class="regular-text validar">

                                                <tr valign="top">
                                                    <th scope="row"><label for="CampaignSubject"><?php _e('Asunto del Mensaje', 'envialo-simple') ?> </label></th>
                                                    <td>
                                                        <input type="text" id="CampaignSubject" name="CampaignSubject" style="width: 386px;" value="<?php echo htmlspecialchars($c["Subject"]); ?>" class="validar" />
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label for="FromID"><?php _e('Remitente', 'envialo-simple') ?></label></th>
                                                    <td>
                                                        <select name="FromID" id="FromID">
                                                            <?php
                                                            $emailsAdmin = $ev->obtenerEmailAdministrador();
                                                            foreach ($emailsAdmin["item"] as $e) {
                                                                if ($c["From"]["EmailID"] == $e["EmailID"]) {
                                                                    echo "<option selected='selected' value='{$e['EmailID']}'>{$e['Name']} ({$e['EmailAddress']}) </option>";
                                                                } else {
                                                                    echo "<option value='{$e['EmailID']}'> {$e['Name']} ({$e["EmailAddress"]}) </option>";
                                                                }
                                                            }
                                                            ?>
                                                            <option value="agregar" class="selectCrear"><?php _e('+ Agregar Nuevo Email ..', 'envialo-simple') ?> </option>
                                                        </select>   <br />
                                                        <div id="responder-contenedor" style="margin: 5px 0 0 5px;">
                                                            <input type="checkbox" name="responder-check" id="responder-check" style="margin-right: 5px;" <?php echo isCheck($c["responder-check"]); ?>   />
                                                            <label for="responder-check"><?php _e('Utilizar una direcci&oacute;n de respuesta distinta', 'envialo-simple'); ?> </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr valign="top" id="responder-fila" style="display:none">
                                                    <th scope="row"><label for="ReplyToID"><?php _e('Responder a', 'envialo-simple') ?>  </label></th>
                                                    <td>
                                                        <select name="ReplyToID" id="ReplyToID">

                                                            <?php
                                                            $emailsAdmin = $ev->obtenerEmailAdministrador();

                                                            foreach ($emailsAdmin["item"] as $e) {
                                                                if ($c['ReplyTo']['EmailID'] == $e['EmailID']) {
                                                                    echo "<option selected='selected' value='{$e['EmailID']}'>{$e['Name']} ({$e['EmailAddress']}) </option>";
                                                                } else {
                                                                    echo "<option value='{$e['EmailID']}' >{$e['Name']} ({$e['EmailAddress']}) </option>";
                                                                }
                                                            }
                                                            ?>
                                                            <option value="agregar" class="selectCrear"><?php _e('+ Agregar Nuevo Email ..', 'envialo-simple') ?></option>
                                                        </select></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><label for="MailListsIds"> <?php _e('Lista de Destinatarios', 'envialo-simple') ?></label></th>
                                                    <td>
                                                        <select name="MailListsIds[]" id="MailListsIds" style="width: 388px;" multiple="multiple" class="validar">

                                                            <?php
                                                            $co = new Contactos();
                                                            $listas = $co->listarListasContactos(-1);

                                                            if ($c["maillists"]["count"] == 0) {
                                                                foreach ($listas[0]["item"] as $l) {
                                                                    echo "<option value='{$l['MailListID']}'>{$l['Name']} ({$l['MemberCount']} Destinatarios)</option>";
                                                                }
                                                            } else {
                                                                foreach ($listas[0]['item'] as $l) {
                                                                    $selected = "";
                                                                    foreach ($c['maillists']['rows'] as $listaMail) {
                                                                        if ($listaMail['MailListID'] == $l['MailListID']) {
                                                                            $selected = "selected='selected'";
                                                                        }
                                                                    }
                                                                    echo "<option {$selected} value='{$l['MailListID']}' >{$l['Name']} ({$l['MemberCount']} Destinatarios)</option>";
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                        </tbody>
                                    </table>
                                    <!-- contenido campañas-->
                                    <?php
                                    if ($seleccionarPlantilla) {
                                        ?>
                                        <div id="ifr-vacio" class="abrir-modal-plantillas">
                                            <span class="abrir-modal-plantillas" > <?php _e('Click para seleccionar una plantilla y comenzar!', 'envialo-simple') ?> </span>
                                        </div>

                                        <?php
                                    } else {
                                        ?>
                                        <div id="postdivrich" class="postarea">

                                            <div data-containername="editorBlocksContainer" data-recordid="<?php echo $c["CampaignID"]; ?>">
                                                <div data-containername="templateEditorRuler"><span class="ruler"></span></div>
                                                <div data-containername="templateEditorContainer">
                                                    <div class="savingThrobber" style="display: none; "> <?php _e('Guardando', 'envialo-simple') ?></div>
                                                    <div data-containername="templateEditorThrobber" style="display: none; "><?php _e('Cargando', 'envialo-simple') ?></div>

                                                    <div data-containername="htmlEditorContainer">
                                                        <div data-containername="secondaryTemplateEditorNav" style="display:none">
                                                            <div data-containername="modifyTemplateBoundaryBackgroundColor"><?php _e('Color de fondo del e-mail', 'envialo-simple') ?></div>
                                                            <div data-containername="modifyTemplateSourceCode"><?php _e('Modificar código fuente', 'envialo-simple') ?></div>
                                                        </div>
                                                        <div data-containername="templateEditorBody" class="restoreNormalCss" style="display: block; ">
                                                            <?php echo $template; ?>
                                                        </div>
                                                    </div>
                                                    <div data-containername="plainTextEditorContainer">
                                                        <textarea name="plainTextVersionContent" data-containername="plainTextVersionContent" class="disabled"></textarea>
                                                    </div>
                                                    <div data-containername="sourceCodeEditorContainer">
                                                        <span data-containername="sourceCodeEditor-Label">
                                                            <span class="sourceCodeEditor-title">&lt; <?php _e('Edición del código fuente', 'envialo-simple') ?> &gt;</span>
                                                            <span class="sourceCodeEditor-action">
                                                                <a href="#" data-containername="sourceCode-applyChanges" data-toolaction="sourceCode-applyChanges"><?php _e('Aplicar cambios en el código fuente', 'envialo-simple') ?></a>
                                                                <span class="sourceCodeEditor-close" data-toolaction="sourceCodeEditor-close"> x </span>
                                                            </span>
                                                        </span>
                                                        <textarea name="sourceCodeVersionContent" data-containername="sourceCodeVersionContent" wrap="off"></textarea>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>


                                        <div id="templateEditorOriginalContent" style="display:none;">

                                        </div>


                                        <?php
                                    }
                                    ?>
                                    <!-- /contenido campañas-->
                                    </div><!-- post body content-->

                                    <div id="postbox-container-1" class="postbox-container" style="float: right;margin-right: -300px;width: 280px;">
                                        <div id="side-sortables" class="meta-box-sortables ui-sortable">
                                            <div id="acciones" class="postbox ">
                                                <h3 class="hndle"><span><?php _e('Acciones', 'envialo-simple') ?></span></h3>
                                                <div class="inside">
                                                    <div class="submitbox" id="submitpost">
                                                        <div id="minor-publishing" style="border:0">
                                                            <div id="minor-publishing-actions">
                                                                <div id="save-action">
                                                                    <div id="guardar-cambios-bt" style="float:left;font-weight: bold; " class="button-secondary"><?php _e('Guardar cambios', 'envialo-simple') ?></div>
                                                                </div>
                                                                <div id="preview-action">
                                                                    <a  class="preview button previsualizar-news" name="<?php echo $idCampana; ?>" href="#" ><?php _e('Previsualizar', 'envialo-simple') ?></a>
                                                                </div>
                                                                <div class="clear"></div>
                                                            </div>
                                                            <div id="misc-publishing-actions">
                                                                <div class="misc-pub-section">
                                                                    <label for="post_status"><?php _e('Estado del Newsletter:', 'envialo-simple') ?></label>
                                                                    <?php
                                                                    $estado = "";
                                                                    $enviada = FALSE;
                                                                    if ($c["Status"] == "Draft") {
                                                                        $estado = __('Borrador', 'envialo-simple');
                                                                        $accion = __('Enviar!', 'envialo-simple');
                                                                    } else if ($c["Status"] == "Active") {
                                                                        $estado = __('Activo', 'envialo-simple');
                                                                    } else if ($c["Status"] == "Completed") {
                                                                        $estado = __('Completado', 'envialo-simple');
                                                                        $enviada = TRUE;
                                                                    } else if ($c["Status"] == "Paused") {
                                                                        $accion = __('Reanudar!', 'envialo-simple');
                                                                        $estado = __('Pausado', 'envialo-simple');
                                                                    }
                                                                    ?>
                                                                    <span id="estado-campana" style="font-weight: bold;"><?php echo $estado; ?></span>

                                                                </div>
                                                                <div class="misc-pub-section">
                                                                    <a href="#" id="opciones-avanzadas-bt"><?php _e('Opciones Avanzadas', 'envialo-simple') ?></a>
                                                                    <div style="margin-top:5px;display:none;" id="opciones-avanzadas">
                                                                        <input type="checkbox" class="check-avanzado" name="AddToPublicArchive" value="1" title="<?php _e('Agregar al Archivo público', 'envialo-simple') ?>" <?php echo isCheck($c["AddToPublicArchive"]); ?>>
                                                                            <?php _e('Agregar al Archivo público', 'envialo-simple') ?>
                                                                            <br />
                                                                            <input type="checkbox" class="check-avanzado" name="TrackLinkClicks" value="1" title="<?php _e('Seguir Enlaces', 'envialo-simple') ?>" <?php echo isCheck($c["TrackLinkClicks"]); ?>>
                                                                                <?php _e('Seguir Enlaces', 'envialo-simple') ?>
                                                                                <br />
                                                                                <input type="checkbox" class="check-avanzado" name="TrackReads" value="1" title="<?php _e('Contar Aperturas', 'envialo-simple') ?>" <?php echo isCheck($c["TrackReads"]); ?>>
                                                                                    <?php _e('Contar Aperturas', 'envialo-simple') ?>
                                                                                    <br />
                                                                                    <input type="checkbox" class="check-avanzado" name="TrackAnalitics" value="1" title="<?php _e('Usar Google Analytics', 'envialo-simple') ?>" <?php echo isCheck($c["TrackAnalitics"]); ?> >
                                                                                        <?php _e('Usar Google Analytics', 'envialo-simple') ?>
                                                                                        <br />
                                                                                        <input type="checkbox" class="check-avanzado" name="SendStateReport" value="1" title="<?php _e('Enviar informe al finalizar', 'envialo-simple') ?>" <?php echo isCheck($c["SendStateReport"]); ?>>
                                                                                            <?php _e('Enviar informe al finalizar', 'envialo-simple') ?>
                                                                                            <br />
                                                                                            </div>
                                                                                            </div>
                                                                                            <div class="misc-pub-section curtime" style="border-bottom:0;">

                                                                                                <?php
                                                                                                $optionSelect = "";
                                                                                                switch ($c["schedule"]["ScheduleType"]) {
                                                                                                    case 'Send Now':
                                                                                                        $enviar = __('Enviar <b>Ahora</b>', 'envialo-simple');
                                                                                                        $optionSelect = '
                                                <option selected="selected" value="0" >' . __('Enviar Ahora', 'envialo-simple') . '</option>
                                                <option value="1">' . __('Envío Programado', 'envialo-simple') . '</option>';
                                                                                                        $programado = "display:none";
                                                                                                        break;
                                                                                                    case 'One time scheduled':
                                                                                                        $enviar = __('Envío <b>Programado</b>', 'envialo-simple');
                                                                                                        $optionSelect = '
                                                <option value="0" >' . __('Enviar Ahora', 'envialo-simple') . '</option>
                                                <option selected="selected" value="1">' . __('Envío Programado', 'envialo-simple') . '</option>';
                                                                                                        $programado = "display:inline-block;margin: 5px 0 5px 15px;padding-left:27px;";
                                                                                                        break;

                                                                                                    default:
                                                                                                        $enviar = __('Enviar <b>Ahora</b>', 'envialo-simple');
                                                                                                        $optionSelect = '
                                                <option selected="selected" value="0" >' . __('Enviar Ahora', 'envialo-simple') . '</option>
                                                <option value="1">' . __('Envío Programado', 'envialo-simple') . '</option>';
                                                                                                        $programado = "display:none";

                                                                                                        break;
                                                                                                }
                                                                                                ?>

                                                                                                <?php if ($enviada) { ?>

                                                                                                    <?php
                                                                                                    if (isset($_GET["e"])) {
                                                                                                        $fechaEnvio = $_GET["e"];
                                                                                                    }
                                                                                                    ?>

                                                                                                    <span id="timestamp"><?php printf(__('Enviada el %', 'envialo-simple'), $fechaEnvio); ?></span>

                                                                                                <?php } else { ?>
                                                                                                    <span id="timestamp"><?php echo $enviar; ?></span>
                                                                                                    <a href="#" class="edit-timestamp" id="editar-programacion-envio"><?php _e('Editar', 'envialo-simple') ?></a>
                                                                                                <?php } ?>
                                                                                            </div>


                                                                                            <div class="misc-pub-section curtime" id="programacion-envio" style="display: none;border-bottom:0;">
                                                                                                <span id="timestamp"></span>
                                                                                                <select id="programacion-envio-select" name="changeScheduling">

                                                                                                    <?php
                                                                                                    echo $optionSelect;
                                                                                                    ?>

                                                                                                </select>

                                                                                            </div>
                                                                                            <div class="misc-pub-section-last" id="programacion-envio-alta" style="<?php echo $programado; ?>">


                                                                                                <?php
                                                                                                if ($c["schedule"]["ScheduleSendDate"] != "0000-00-00 00:00:00") {

                                                                                                    list($year, $month, $day, $hour, $minute) = sscanf($c["schedule"]["ScheduleSendDate"], '%04d-%02d-%02d %02d:%02d:%02d');

                                                                                                    $dia = $day . "/" . $month . "/" . $year;
                                                                                                    $hora = '<option value="' . $hour . '" selected="selected">' . $hour . ' </option>';
                                                                                                    $min = '<option value="' . $minute . '" selected="selected">' . $minute . ' </option>';
                                                                                                } else {
                                                                                                    $hora = "";
                                                                                                    $min = "";
                                                                                                    $dia = date("j/m/Y");
                                                                                                }
                                                                                                ?>

                                                                                                <input id="input-fecha" name="SchedulingDate" type="text" value="<?php echo $dia; ?>"/>

                                                                                                <select name="SchedulingHour" id="input-hora">
                                                                                                    <?php echo $hora; ?>
                                                                                                    <option value="00">00</option>
                                                                                                    <option value="01">01</option>
                                                                                                    <option value="02">02</option>
                                                                                                    <option value="03">03</option>
                                                                                                    <option value="04">04</option>
                                                                                                    <option value="05">05</option>
                                                                                                    <option value="06">06</option>
                                                                                                    <option value="07">07</option>
                                                                                                    <option value="08">08</option>
                                                                                                    <option value="09">09</option>
                                                                                                    <option value="10">10</option>
                                                                                                    <option value="11">11</option>
                                                                                                    <option value="12">12</option>
                                                                                                    <option value="13">13</option>
                                                                                                    <option value="14">14</option>
                                                                                                    <option value="15">15</option>
                                                                                                    <option value="16">16</option>
                                                                                                    <option value="17">17</option>
                                                                                                    <option value="18">18</option>
                                                                                                    <option value="19">19</option>
                                                                                                    <option value="20">20</option>
                                                                                                    <option value="21">21</option>
                                                                                                    <option value="22">22</option>
                                                                                                    <option value="23">23</option>
                                                                                                </select>
                                                                                                <span>:</span>
                                                                                                <select id="input-minuto" name="SchedulingMinute" >
                                                                                                    <?php echo $min; ?>
                                                                                                    <option value="00">00</option>
                                                                                                    <option value="05">05</option>
                                                                                                    <option value="10">10</option>
                                                                                                    <option value="15">15</option>
                                                                                                    <option value="20">20</option>
                                                                                                    <option value="25">25</option>
                                                                                                    <option value="30">30</option>
                                                                                                    <option value="35">35</option>
                                                                                                    <option value="40">40</option>
                                                                                                    <option value="45">45</option>
                                                                                                    <option value="50">50</option>
                                                                                                    <option value="55">55</option>

                                                                                                </select>
                                                                                            </div>
                                                                                            </div>
                                                                                            <div class="clear"></div>
                                                                                            </div>

                                                                                            <div id="major-publishing-actions" style="margin-top:0">
                                                                                                <div id="publishing-action" style="float:right">

                                                                                                    <?php if (!$enviada) { ?>
                                                                                                        <a href="#" class="button-primary" id="enviar-campana-bt"  style="display: block;text-align: center;"><?php echo $accion; ?></a>
                                                                                                    <?php } ?>
                                                                                                </div>
                                                                                                <div class="clear"></div>
                                                                                            </div>
                                                                                            </div>

                                                                                            </div>
                                                                                            </div>

                                                                                            <!-- contenido campaña-->

                                                                                            <div id="estilo" class="postbox">
                                                                                                <h3 class="hndle" style="cursor:default"><span><?php _e('Diseño', 'envialo-simple') ?></span></h3>
                                                                                                <div class="inside">
                                                                                                    <a style="text-decoration:none"  class="abrir-modal-plantillas" id="seleccionar-plantilla-bt" href="#">
                                                                                                        <div class="button-secondary" style="margin: 15px;text-align:center;">
                                                                                                            <?php _e('Seleccionar Otra Plantilla', 'envialo-simple') ?>
                                                                                                        </div>
                                                                                                    </a>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div id="contenido" class="postbox " style="display: block; ">
                                                                                                <h3 class="hndle" style="cursor:default"><span><?php _e('Contenido', 'envialo-simple') ?></span></h3>
                                                                                                <div class="inside">
                                                                                                    <div class="submitbox" id="submitpost">

                                                                                                        <div class="misc-pub-section" style="border:0">
                                                                                                            <div id="acordeon"style="width: 238px;">
                                                                                                                <h3 style="cursor: pointer!important" ><?php _e('Contenido WordPress', 'envialo-simple') ?></h3>
                                                                                                                <div id="contenedor-wp" style="width:236px">
                                                                                                                    <div id="cont1wp" class="drag-contenido">
                                                                                                                        <span class="drag-contenido-icon">
                                                                                                                            <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/icon1.png') ?>" />
                                                                                                                        </span>
                                                                                                                        <span>img + texto</span>
                                                                                                                    </div>
                                                                                                                    <div id="cont2wp" class="drag-contenido">
                                                                                                                        <span class="drag-contenido-icon">
                                                                                                                            <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/icon2.png') ?>" />
                                                                                                                        </span>
                                                                                                                        <span>img + texto</span>
                                                                                                                    </div>
                                                                                                                    <div id="cont3wp" class="drag-contenido">
                                                                                                                        <span class="drag-contenido-icon">
                                                                                                                            <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/icon3.png') ?>" />
                                                                                                                        </span>
                                                                                                                        <span>texto</span>
                                                                                                                    </div>
                                                                                                                    <div id="cont4wp" class="drag-contenido">
                                                                                                                        <span class="drag-contenido-icon">
                                                                                                                            <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/icon4.png') ?>" />
                                                                                                                        </span>
                                                                                                                        <span>texto + img</span>
                                                                                                                    </div>

                                                                                                                </div>
                                                                                                                <h3 style="cursor: pointer!important" ><?php _e('Contenido Estático', 'envialo-simple') ?></h3>
                                                                                                                <div id="contenedor-estatico" style="height: 150px; width:236px">
                                                                                                                    <div id="cont1" class="drag-contenido">
                                                                                                                        <span class="drag-contenido-icon">
                                                                                                                            <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/icon1.png') ?>" />
                                                                                                                        </span>
                                                                                                                        <span>img + texto</span>
                                                                                                                    </div>
                                                                                                                    <div id="cont2" class="drag-contenido">
                                                                                                                        <span class="drag-contenido-icon">
                                                                                                                            <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/icon2.png') ?>" />
                                                                                                                        </span>
                                                                                                                        <span>img + texto</span>
                                                                                                                    </div>
                                                                                                                    <div id="cont3" class="drag-contenido">
                                                                                                                        <span class="drag-contenido-icon">
                                                                                                                            <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/icon3.png') ?>" />
                                                                                                                        </span>
                                                                                                                        <span>texto</span>
                                                                                                                    </div>

                                                                                                                    <div id="cont4" class="drag-contenido">
                                                                                                                        <span class="drag-contenido-icon">
                                                                                                                            <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/icon4.png') ?>" />
                                                                                                                        </span>
                                                                                                                        <span>texto + img</span>
                                                                                                                    </div>
                                                                                                                    <div id="cont5" class="drag-contenido">
                                                                                                                        <span class="drag-contenido-icon">
                                                                                                                            <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/icon5.png') ?>" />
                                                                                                                        </span>
                                                                                                                        <span>texto</span>
                                                                                                                    </div>
                                                                                                                    <div id="cont6" class="drag-contenido">
                                                                                                                        <span class="drag-contenido-icon">
                                                                                                                            <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/icon6.png') ?>" />
                                                                                                                        </span>
                                                                                                                        <span>imagen</span>
                                                                                                                    </div>
                                                                                                                    <div id="cont7" class="drag-contenido">
                                                                                                                        <span class="drag-contenido-icon">
                                                                                                                            <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/icon7.png') ?>" />
                                                                                                                        </span>
                                                                                                                        <span>imagen</span>
                                                                                                                    </div>
                                                                                                                    <div id="cont8" class="drag-contenido">
                                                                                                                        <span class="drag-contenido-icon">
                                                                                                                            <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/icon8.png') ?>" />
                                                                                                                        </span>
                                                                                                                        <span>separador</span>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <!-- //contenido campaña-->
                                                                                            </div> <!-- sortable-->

                                                                                            </div><!--post box cointaner1-->

                                                                                            </div><!--post body-->
                                                                                            </div><!--post stuff-->
                                                                                            </form>


                                                                                            <div style="clear: both"> </div>




                                                                                            </div><!--wrap-->


                                                                                            <div style="display:none">

                                                                                                <div id="modal-agregar-email">
                                                                                                    <form id="form-agregar-email" action="#" method="post">
                                                                                                        <div id="label-error-mail-admin" class="mensaje"></div>
                                                                                                        <label for="nombreEmailAdmin"><?php _e('Nombre:', 'envialo-simple') ?></label><br />
                                                                                                        <input type="text"  name="nombreEmailAdmin" id="nombreEmailAdmin"/><br />

                                                                                                        <label for="emailAdmin"><?php _e('Email:', 'envialo-simple') ?></label><br />
                                                                                                        <input type="text"  name="emailAdmin" id="emailAdmin"/><br />
                                                                                                        <input type="submit" value="Agregar" class="button-primary" style="margin-top: 20px;margin-bottom: 10px;"/>
                                                                                                        <input type="reset" value="Cancelar" class="button-secondary" style="margin-top: 20px;margin-bottom: 10px;" onclick='jQuery("#modal-agregar-email").dialog("close")'/>

                                                                                                    </form>


                                                                                                </div>

                                                                                                <div id="modal-insertar-img">

                                                                                                    <iframe  style="width:660px;height:500px;" id="contenedor-wp-media"></iframe>

                                                                                                </div>

                                                                                                <div id="modal-editar-img">
                                                                                                    <div class="editar-img-contenedor" >
                                                                                                        <div class="editar-img-campos fl">
                                                                                                            <p>
                                                                                                                <label><?php _e('Url de Imagen:', 'envialo-simple') ?></label><br />
                                                                                                                <span class="fl" id="urlImagen"></span><input type="submit" style="float: left;width:100px;margin-left: 10px;" class="button-secondary" id="editar-img-cambiar" value="Cambiar Imagen" />
                                                                                                            </p>
                                                                                                            <div style="clear:both;"></div>
                                                                                                            <p>
                                                                                                                <label><?php _e('Enlace:', 'envialo-simple') ?></label><br />
                                                                                                                <input type="text" name="enlaceImagen"/>
                                                                                                            </p>
                                                                                                            <p>
                                                                                                                <label><?php _e('Texto Alternativo:', 'envialo-simple') ?></label><br />
                                                                                                                <input type="text" name="altImagen"/>
                                                                                                            </p>

                                                                                                        </div>
                                                                                                        <div style="clear:both"></div>
                                                                                                        <div id="editar-propiedades">

                                                                                                            <p style="text-decoration: underline;"><?php _e('Tama&ntildeo', 'envialo-simple') ?></p>
                                                                                                            <input type="hidden" value="" name="isIframe"/>

                                                                                                            <div class="fl">
                                                                                                                <label><?php _e('Ancho(px):', 'envialo-simple') ?></label><br />
                                                                                                                <input type="text" class="editar-img-input"  name="ancho" />
                                                                                                            </div>
                                                                                                            <div class="fl" style="margin-left: 20px;">
                                                                                                                <label><?php _e('Alto(px):', 'envialo-simple') ?></label><br />
                                                                                                                <input type="text" class="editar-img-input" name="alto" />
                                                                                                            </div>
                                                                                                            <div style="clear:both"></div>
                                                                                                            <p>
                                                                                                                <label><input type="checkbox" name="editar-img-proporcion"/>&nbsp;&nbsp;<?php _e('Mantener proporción', 'envialo-simple') ?></label>
                                                                                                            </p>


                                                                                                            <p style="text-decoration: underline;"><?php _e('Alineación', 'envialo-simple') ?></p>
                                                                                                            <input type="radio" name="align" id="izq" value="izq"><label for="izq">&nbsp;<?php _e('Izquierda', 'envialo-simple') ?></label><br />
                                                                                                                <input type="radio" name="align" id="cen" value="cen"><label for="cen">&nbsp;<?php _e('Centro', 'envialo-simple') ?></label><br />
                                                                                                                    <input type="radio" name="align" id="der" value="der"><label for="der">&nbsp;<?php _e('Derecha', 'envialo-simple') ?></label><br />
                                                                                                                        <input type="radio" name="align" id="none" value="none"><label for="der">&nbsp;<?php _e('Ninguna', 'envialo-simple') ?></label><br />
                                                                                                                            <br /><br />

                                                                                                                            </div>
                                                                                                                            <div id="vista-previa-img" ></div>

                                                                                                                            <div style="clear:both"></div>
                                                                                                                            <span style="font-size: 11px;margin-left: 155px;"><?php _e('Para editar el tamaño proporcionalmente, mantenga apretada la tecla Shift al arrastrar.', 'envialo-simple') ?></span>
                                                                                                                            <div class="editar-img-botones">

                                                                                                                                <input type="reset" style="float:right;float: right;margin-right: 18px;" id="modal-editar-img-cancelar" value="<?php _e('Cancelar', 'envialo-simple') ?>" class="button-secondary" onclick="jQuery('#modal-editar-img').dialog('close');
                                                                                                                                        return false;"/>
                                                                                                                                <input type="submit" style="float:right" id="modal-editar-img-aceptar" value="<?php _e('Aceptar', 'envialo-simple') ?>" class="button-primary"  />

                                                                                                                            </div>
                                                                                                                            </div>
                                                                                                                            </div>


                                                                                                                            </div>


                                                                                                                            <?php include_once(ENVIALO_DIR . "/paginas/contenidoExtra.php"); ?>

