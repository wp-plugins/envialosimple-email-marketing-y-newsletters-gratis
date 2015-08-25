<?php

$principal = dirname(__FILE__) . '/envialosimple-email-marketing-y-newsletters-gratis.php';

include_once("clases/EnvialoSimple.php");
include_once("clases/Contactos.php");
include_once("clases/Campanas.php");
include_once("clases/Formularios.php");

global $wpdb, $table_prefix;

//importo la interfaz de BD de Wordpress
if (!isset($wpdb)) {
    require_once('../../../wp-config.php');
    require_once('../../../wp-includes/wp-db.php');
}



$accion = isset($_POST["accion"]) ? $_POST["accion"] : "";

$ev = new EnvialoSimple();

switch ($accion) {
    case 'traerToken':
        $usuario = $_POST["username"];
        $password = $_POST["password"];
        traerGenerarGuardarTokenMedianteAPI($usuario, $password);
        break;

    case 'testToken':
        $APIKey = $_POST["APIKey"];
        echo configurarTokenES($APIKey);
        break;
    case 'eliminarToken':
        $idClave = $_POST["idClave"];
        $ev = new EnvialoSimple();
        echo $ev->eliminarTokenBD($idClave);
        break;

    case 'crearLista':
        $co = new Contactos();
        $ev = new EnvialoSimple();
        $ev->checkSetup();
        echo $co->crearListaContactos($_POST["nombreLista"]);
        break;

    case 'sincronizarContactos':
        $idLista = $_POST["MailListID"];
        $co = new Contactos();
        $ev = new EnvialoSimple();
        $ev->checkSetup();
        $contactosWP = $co->listarContactosWordpress();
        $cantidadContactos = count($contactosWP);

        foreach ($contactosWP as $c) {

            $campos = array();
            $campos[] = stripslashes($c["first_name"]);
            $campos[] = stripslashes($c["last_name"]);


            //$respuesta = json_decode($co->agregarContactoALista($idLista,stripslashes($c["user_email"]),$campos),TRUE);
            $respuesta = $co->agregarContactoALista($idLista, stripslashes($c["user_email"]), $campos);


            $respuesta_array = json_decode($respuesta, TRUE);

            if (!$respuesta_array["root"]["ajaxResponse"]["success"]) {
                echo $respuesta;
            }
        }
        echo json_encode(array("success" => TRUE, "cantidadContactos" => $cantidadContactos));
        break;

    case 'agregarContactoLista':

        $campos = $_POST["campos"];
        $ev->checkSetup();
        $co = new Contactos();
        echo $co->agregarContactoALista($_POST['MailListID'], $_POST["Email"], $campos);


        break;

    case 'traerListaYEmailAdmin':
        $ev = new EnvialoSimple();
        $ev->checkSetup();
        $co = new Contactos();
        $emailsAdmin = array("emailAdmin" => $ev->obtenerEmailAdministrador());
        $listasContactos = array("listasContactos" => $co->listarListasContactos(-1));
        echo json_encode(array_merge($emailsAdmin, $listasContactos));
        break;

    case 'editarCampana':
        $ca = new Campanas();
        $ev = new EnvialoSimple();
        $ev->checkSetup();

        $CampaignID = $_POST['CampaignID'];
        $CampaignName = $_POST['CampaignName'];
        $CampaignSubject = $_POST['CampaignSubject'];
        $FromID = $_POST['FromID'];
        $ReplyToID = $_POST['ReplyToID'];
        $MailListsIds = $_POST['MailListsIds'];
        $AddToPublicArchive = $_POST['AddToPublicArchive'];
        $TrackLinkClicks = $_POST['TrackLinkClicks'];
        $TrackReads = $_POST['TrackReads'];
        $TrackAnalitics = $_POST['TrackAnalitics'];
        $SendStateReport = $_POST['SendStateReport'];
        $changeScheduling = $_POST['changeScheduling'];
        $SchedulingDate = $_POST['SchedulingDate'];
        $SchedulingHour = $_POST['SchedulingHour'];
        $SchedulingMinute = $_POST['SchedulingMinute'];


        if ($changeScheduling == 0) {
            //Envío Ahora
            $SendNow = '1';
            $ScheduleCampaign = '1';
        } elseif ($changeScheduling == 1) {
            //Programo envío
            $SendNow = '0';
            $ScheduleCampaign = '1';

            list($day, $month, $year) = sscanf($SchedulingDate, '%02d/%02d/%04d');
            //$datetime = new DateTime("$year-$month-$day");
            //$fecha =  $datetime->format('Y-m-d');
            $fecha = $year . "-" . $month . "-" . $day;
            $SendDate = $fecha . " " . $SchedulingHour . ":" . $SchedulingMinute . ":00";
        } elseif ($changeScheduling == 2) {
            //no programo envio
            $SendNow = '0';
            $ScheduleCampaign = '1';
        }
        echo $ca->editarCampana($CampaignID, $CampaignName, $CampaignSubject, $FromID, $ReplyToID, $MailListsIds, $AddToPublicArchive, $TrackLinkClicks, $TrackReads, $TrackAnalitics, $SendStateReport, $changeScheduling, $SendNow, $ScheduleCampaign, $SendDate);
        break;

    case 'guardarContenidoHTML':
        $ca = new Campanas();
        $ev = new EnvialoSimple();
        $ev->checkSetup();
        header("Content-Type: text/html; charset=utf-8");
        echo $ca->crearCuerpoCampana($_POST["CampaignID"], $_POST["URL"], stripslashes($_POST["HTML"]), $_POST["PlainText"], $_POST["RemoteUnsubscribeBlock"]);
        break;

    case 'enviarCampana':
        $ca = new Campanas();
        $ev = new EnvialoSimple();
        $ev->checkSetup();
        echo $ca->enviarCampana($_POST["CampaignID"]);
        break;

    case 'pausarCampana':

        $ev = new EnvialoSimple();
        $ev->checkSetup();
        $ca = new Campanas();
        echo $ca->pausarCampana($_POST['CampaignID']);
        break;

    case 'traerPlantillas':

        $ev = new EnvialoSimple();
        $ev->checkSetup();
        header("Content-Type: text/html; charset=utf-8");
        $filterListByCategory = $_POST["filterListByCategory"];
        $filterListByCategory2 = $_POST["filterListByCategory2"];

        $limit = $_POST["limit"];
        $retrieveList = "defaulTemplates";
        $offset = $_POST["offset"];
        echo $ev->mostrarPlantillas($limit, $retrieveList, $offset, $filterListByCategory, $filterListByCategory2);
        break;

    case 'mostrarPostsWP':
        //TODO
        header("Content-Type: text/html; charset=utf-8");

        $ev = new EnvialoSimple();
        echo ($ev->mostrarPostsWP($_POST['category'], $_POST['numberposts'], $_POST['offset']));
        break;

    case 'previsualizar-camp':

        $ev = new EnvialoSimple();
        $ev->checkSetup();
        $ca = new Campanas();
        echo $ca->previsualizarCampana($_POST['CampaignID'], $_POST['Email']);
        break;

    case 'traerPreciosEnvios' :
        $ev = new EnvialoSimple();
        $ev->checkSetup();
        echo $ev->traerPreciosEnvios($_POST["APIKey"]);
        break;

    case "agregarEmailAdmin":
        $ev->checkSetup();
        echo $ev->agregarEmailAdministrador($_POST["emailAdmin"], $_POST["nombreEmailAdmin"]);
        break;

    case "agregarCampoPersonalizado":

        $ev->checkSetup();
        echo json_encode($ev->agregarCampoPersonalizado($_POST['Title'], $_POST['FieldType'], $_POST['Validation'], $_POST['ItemsIsMultipleSelect'], $_POST['DefaultValue'], $_POST['ItemsValues'], $_POST['ItemsNames']));

        break;

    case "mostrarCamposPersonalizados":
        $ev->checkSetup();

        echo $ev->mostrarCampoPersonalizado($_POST["camposExistentes"]);

        break;
    case "refrescarNews":
        $ev->checkSetup();
        include_once(ENVIALO_DIR . "/paginas/tablaCampanas.php");
        break;

    //Formulario
    case "vistaPreviaForm":

        $ev->checkSetup();
        $fo = new Formularios();

        echo $fo->vistaPrevia($_POST['CustomFieldsIds'], $_POST['FormID'], $_POST['ConfirmSubscriptionEmailID'], $_POST['Title'], $_POST['Name'], $_POST['LabelSubmit'], $_POST['LabelEmailAddress'], $_POST['BackgroundColor'], $_POST['Width'], $_POST['Font'], $_POST['FontSize'], $_POST['FontColor'], $_POST['SubsCallbackOK'], $_POST['SubsCallbackFail'], $_POST['ConfCallbackOK'], $_POST['ConfCallbackFail'], $_POST['CustomCSS'], $_POST['ShowPoweredBy'], $_POST['SubscribeDobleOptIN']);

        break;
    case "guardarForm":


        $ev->checkSetup();
        $fo = new Formularios();

        $respuesta = json_decode($fo->guardarForm($_POST['CustomFieldsIds'], $_POST['MailListsIds'], $_POST['FormID'], $_POST['ConfirmSubscriptionEmailID'], $_POST['Title'], $_POST['Name'], $_POST['LabelSubmit'], $_POST['LabelEmailAddress'], $_POST['BackgroundColor'], $_POST['Width'], $_POST['Font'], $_POST['FontSize'], $_POST['FontColor'], $_POST['SubsCallbackOK'], $_POST['SubsCallbackFail'], $_POST['ConfCallbackOK'], $_POST['ConfCallbackFail'], $_POST['CustomCSS'], $_POST['ShowPoweredBy'], $_POST['SubscribeDobleOptIN']), TRUE);


        if (!isset($respuesta['root']['ajaxResponse']['success'])) {

            echo json_encode($respuesta);
        }

        $json = array();
        $json['form'] = $respuesta['root']['ajaxResponse']['form'];


        $FormID = !empty($_POST["FormID"]) ? $_POST["FormID"] : $respuesta['root']['ajaxResponse']['form']['FormID'];

        $respuesta = json_decode($fo->guardarRemitenteResponder($FormID, $_POST['EmailID'], $_POST['Name'], $_POST['FromName'], $_POST['FromEmail'], $_POST['ReplyToName'], $_POST['ReplyToEmail'], $_POST['Content']), TRUE);

        if (!isset($respuesta['root']['ajaxResponse']['success'])) {
            echo json_encode($respuesta);
        }

        $json['email'] = $respuesta['root']['ajaxResponse']['email'];
        $json['success'] = TRUE;

        echo json_encode($json);


        break;
    case "eliminarForm":

        $ev->checkSetup();
        $fo = new Formularios();
        echo $fo->eliminarFormulario($_POST['FormID']);

        break;
    case "feedback":

        $ev->checkSetup();
        echo json_encode($ev->dejarFeedback($_POST["mensaje"]));

        break;

    case "copypaste":
        $ev->checkSetup();

        $co = new Contactos();
        if ($co->importarCopyPaste($_POST['CopyPaste'])) {
            echo $co->importarPreProcess();
        } else {
            echo json_encode(array("error", "importarCopyPaste"));
        }

        break;

    case "uploadFile":

        $ev->checkSetup();
        $co = new Contactos();

        if ($co->importarUploadFile($_POST["file"], plugin_dir_path($principal))) {
            echo $co->importarPreProcess();
        } else {
            echo json_encode(array("error", "importarFile"));
        }



        break;

    case "processCopy":
        $ev->checkSetup();
        $co = new Contactos();

        echo $co->importarProcessCopy($_POST['MailListsIds'], $_POST["corresponder"]["campos"]);

        break;

    case "processFile":
        $ev->checkSetup();
        $co = new Contactos();

        echo $co->importarProcessFile($_POST['MailListsIds'], $_POST["corresponder"]["campos"]);


        break;

    default:
        break;
}

function traerGenerarGuardarTokenMedianteAPI($usuario, $password) {
    $ev = new EnvialoSimple();

    $respuestaLogin = $ev->loginEnvialosimple($usuario, $password);

    if (!$respuestaLogin[0]) {

        if (isset($respuestaLogin[1])) {
            echo error(__("Ocurrio un Error de Configuración del Servidor. Por favor contacte a su proveedor de Hosting.<br />" . $respuestaLogin[1], 'envialo-simple'), array("logueado" => false, "error" => $respuestaLogin[1]));
        } else {
            echo error(__("Usuario o Clave Incorrectas", 'envialo-simple'), array("logueado" => false, "error" => $respuestaLogin[1]));
        }


        return false;
    }
    $httpAPIKEY = $ev->traerTokenUsuario();
    if (!$httpAPIKEY) {
        if ($ev->errorMsg != 'EL usuario no tiene claves') {
            echo error($ev->errorMsg, array("logueado" => true));
            return false;
        }
        $httpAPIKEY = $ev->generarTokenUsuario();
        if (!$httpAPIKEY) {
            echo error($ev->errorMsg, array("logueado" => true));
            return false;
        }
    }
    $ev->logoutEnvialosimple();
    if ($ev->guardarTokenBD($httpAPIKEY)) {
        echo exito(__('La Clave de Api se ha configurado Correctamente!', 'envialo-simple'));
    } else {
        echo error(__("Se ha encontrado una clave previamente generada. Lamentablemente un error de Base de Datos ha ocurrido al intentar utilizarla.", 'envialo-simple'));
    }
    return true;
}

function configurarTokenES($APIKey) {
    $GLOBALS["APIKey"] = $APIKey;
    $ev = new EnvialoSimple();
    return json_encode($ev->testToken());
}

function error($mensaje, $parametrosAdicionales = array()) {
    $error = array();
    $error["success"] = FALSE;
    $error["mensaje"] = $mensaje;
    if (is_array($parametrosAdicionales) && !empty($parametrosAdicionales)) {
        $error = array_merge($error, $parametrosAdicionales);
    }
    return json_encode($error);
}

function exito($mensaje, $parametros = array()) {
    $exito = array();
    $exito["success"] = TRUE;
    $exito["mensaje"] = $mensaje;
    if (is_array($parametros) && !empty($parametros)) {
        $exito = array_merge($exito, $parametros);
    }
    return json_encode($exito);
}
