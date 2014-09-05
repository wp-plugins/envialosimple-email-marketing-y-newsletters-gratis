<?php

require_once("Curl.php");

class Campanas extends Curl{


    /**
     * Modifica o da de Alta una Campaña
     *
     * @return string
     * @param $CampaignID
     * @param $CampaignName
     * @param $CampaignSubject
     * @param $MailListsIds[]
     * @param $FromID
     * @param $ReplyToID
     * @param $TrackLinkClicks
     * @param $TrackReads
     * @param $TrackAnalitics
     * @param $SendStateReport
     * @param $AddToPublicArchive
     * @param $ScheduleCampaign
     * @param $SendNow
     */
    function editarCampana($CampaignID,$CampaignName,$CampaignSubject,$FromID,$ReplyToID,$MailListsIds,
                            $AddToPublicArchive,$TrackLinkClicks,$TrackReads,$TrackAnalitics,$SendStateReport,
                            $changeScheduling,$SendNow,$ScheduleCampaign,$SendDate) {


       $parametros = array();
       $parametros['CampaignID'] = $CampaignID;
       $parametros['CampaignName']= stripslashes($CampaignName);
       $parametros['CampaignSubject']= stripslashes($CampaignSubject);
       $parametros['FromID'] = $FromID;
       $parametros['ReplyToID'] = $ReplyToID;
       $parametros['MailListsIds'] = $MailListsIds;
       $parametros['AddToPublicArchive'] = $AddToPublicArchive;
       $parametros['TrackLinkClicks'] = $TrackLinkClicks;
       $parametros['TrackReads'] = $TrackReads;
       $parametros['TrackAnalitics'] = $TrackAnalitics;
       $parametros['SendStateReport'] = $SendStateReport;
       $parametros['changeScheduling'] = $changeScheduling;
       $parametros['SendNow'] = $SendNow;
       $parametros['ScheduleCampaign'] = $ScheduleCampaign;
       $parametros['SendDate'] = $SendDate;

       return $this->curlJson($parametros, URL_BASE_API . "/campaign/save/format/json");
    }

    /**
     * Crea el cuerpo HTML de la Campaña
     *
     * @return string
     * @param $CampaignID
     * @param $URL
     * @param $HTML
     * @param $PlainText
     * @param $RemoteUnsubscribeBlock
     *
     */
    function crearCuerpoCampana($CampaignID, $URL, $HTML, $PlainText, $RemoteUnsubscribeBlock) {

        $parametros = array();
        $parametros['CampaignID'] = $CampaignID;
        $parametros['URL'] = $URL;
        $parametros['HTML'] = $HTML;
        $parametros['PlainText'] = $PlainText;
        $parametros['RemoteUnsubscribeBlock'] = $RemoteUnsubscribeBlock;

        return $this->curlJson($parametros, URL_BASE_API . "/content/edit/format/json");
    }

    /**
     * Recupera el HTML del cuerpo de la Campaña
     *
     * @return string
     * @param CampaignID
     */
    function traerCuerpoCampana($CampaignID) {

        $respuesta = json_decode(file_get_contents("http://v2.envialosimple.com/content/edit/format/json?APIKey=" . $GLOBALS["APIKey"] . "&CampaignID=" . $CampaignID), TRUE);

        if (isset($respuesta["root"]["ajaxResponse"]["success"])) {
            return $respuesta["root"]["ajaxResponse"]["content"]["HTML"];

        } else {
            $resp = array();
            $resp["root"]["ajaxResponse"]["content"]["HTML"] = "<p>Error al Recuperar el Contenido de la Campaña.</p><p>Por Favor Intente Nuevamente.</p>";
        }

    }

    /**
     * Devuelve un array con informacion acerca de una Campaña
     *
     * @return Array
     * @param CampaignID
     */
    function traerCampana($CampaignID) {

        $parametros = array();
        $parametros['CampaignID'] = $CampaignID;
        $campana = json_decode($this->curlJson($parametros, URL_BASE_API . '/campaign/load/format/json' ,TRUE), TRUE);
        return $campana["root"]["ajaxResponse"];
    }

    /**
     * Recupera las Campañas del Usuario
     *
     * @return array
     * @param  $parametros["absolutepage"]
     */
    function listarCampanas($absolutepage,$filter) {
        $parametros = array();
        $parametros["absolutepage"] = $absolutepage;
        $parametros["count"] = 10;
        $parametros["orderBy"] = "id";
        $parametros["desc"] = "1";
        $parametros["filter"] = $filter;

        $respuesta = json_decode(($this->curlJson($parametros, URL_BASE_API . '/campaign/list/format/json')), TRUE);
        if (isset($respuesta["root"]["ajaxResponse"]["success"])) {
            return $respuesta["root"]["ajaxResponse"];
        } else {
            $respuesta = array();
            $respuesta["root"]["ajaxResponse"]["success"] = FALSE;
            return $respuesta;
        }

    }

    /**
     * Previsualiza la Campaña en el navegador o por Email
     *
     * @return string
     * @param $Email (opcional)
     * @param $CampaignID
     */
    function previsualizarCampana($CampaignID,$Email='') {

        $url = "/campaign/preview/format/json";
        $parametros = array();
        if (!empty($Email)) {
            $parametros["Email"] = $Email;
            $url = "/campaign/preview/format/email";
        }
        $parametros["CampaignID"] = $CampaignID;
        return $this->curlJson($parametros, URL_BASE_API . $url);
    }

    /**
     * Envia o Reanuda la Campaña
     *
     * @return string
     * @param $CampaignID
     */
    function enviarCampana($CampaignID) {
        $parametros = array();
        $parametros["CampaignID"] = $CampaignID;
        return $this->curlJson($parametros, URL_BASE_API . '/campaign/resume/format/json');
    }

    /**
     * Pausa Campaña
     *
     * @return string
     * @param $CampaignID
     */
    function pausarCampana($CampaignID) {
        $parametros = array();
        $parametros['CampaignID'] = $CampaignID;
        return $this->curlJson($parametros, URL_BASE_API . '/campaign/pause/format/json');
    }

    /**
     * Recupera los reportes de la Campaña
     *
     * @return array
     * @param CampaignID
     */
    function traerReportes($CampaignID) {
        $parametros = array();
        $parametros['CampaignID'] = $CampaignID;
        return json_decode($this->curlJson($parametros, URL_BASE_API . '/report/track/format/json'), TRUE);

    }

    static function loadTag(& $html, $tag)
    {
        $iniBody = stripos($html, '<'.$tag);
        if($iniBody)
        {
            $iniBody = stripos($html, '>', $iniBody);
            $endBody = stripos($html, '</'.$tag, $iniBody);
            if($endBody)
            {
                $html = trim(substr($html, $iniBody+1, $endBody-$iniBody-1));
            }
            else
            {
                $html = trim(substr($html, $iniBody+1));
            }
            return true;
        }
        return false;
    }

}
?>
