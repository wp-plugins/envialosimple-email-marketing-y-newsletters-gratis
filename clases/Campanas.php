<?php

class Campanas {

    
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
        $campana = json_decode($this->curlJson($parametros, URL_BASE_API . '/campaign/load/format/json'), TRUE);
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
    function previsualizarCampana($Email,$CampaignID) {

        
        $url = "/campaign/preview/format/html";

        $parametros = array();
        if (isset($Email)) {
            $parametros["Email"];
            $url = "/campaign/preview/format/email";
        }
        $parametros["CampaignID"]=$CampaignID;
        
        return $this->curlJson($parametros, URL_BASE_API . $url);
    }

    /**
     * Muestra las Campañas del Usuario
     *
     * @return string     
     * @param  $absolutepage   
     */
    function mostrarCampanas($absolutepage,$filter) {
        $adminUrl = get_admin_url();

        $c = $this->listarCampanas($absolutepage,$filter);
        if ($c["success"]) {

            //var_dump($c)			;

            if (!empty($c["list"]["item"])) {
                $html = "";
                $html = "<div>
							<table class='wp-list-table widefat fixed posts'>
								<thead>
								<tr>
									<th scope='col' id='cb' class='manage-column column-cb check-column' style='width: 40px;'><span style='display: block;height: 30px;margin-left: 8px'>ID</span></th>
									
									<th class='manage-column column-title sortable desc' style='width:200px;'>Nombre</th>
									<th class='manage-column column-title sortable desc' style='width:125px;'>Asunto</th>
									<th class='manage-column column-title sortable desc' style='width:70px;'>Estado</th>
									<th class='manage-column column-title sortable desc' style='width:120px;'>Destinatarios</th>
									<th class='manage-column column-title sortable desc' style='width:100px;'>Reportes</th>									
									<th class='manage-column column-title sortable desc' style='width:200px;'>Fecha de Envío</th>
								</tr>	
								</thead>							
														
								<tbody>
						";

                foreach ($c["list"]["item"] as $item) {

                    $acciones = "";
                    $reportes = "";
                    $fechaEnvio = "";

                    if ($item["Status"] == "Draft") {

                        $estadoCampana = "<div class='icono-estado borrador' title='Borrador'></div>";

                        $url = get_admin_url() . 'admin.php?page=envialo-simple&accion=campana-editar&idCampana=' . $item['CampaignID'];
                        $acciones .= "<span class='edit'>
                                        <a href='{$url}' title='Editá tu Campaña'>Editar</a> | 
                                    </span>";
                        $pagina = "campana-editar";

                    } elseif ($item['Status'] == 'Paused') {

                        $estadoCampana = "<div class='icono-estado pausada' title='Pausado'></div>";

                        $pagina = "campana-editar";
                        $acciones .= "<span class='view'>
                                        <a href='' class='previsualizar-news' name='{$item['CampaignID']}' title='Previsualizar Campaña en el Navegador o por Correo Electrónico' >Previsualizar</a> |
                                     </span>
                                     <span class='view'>
                                        <a href='' class='reanudar-campana-bt' name='{$item['CampaignID']}' title='Reanudar' >Reanudar</a> |
                                     </span>";

                    } elseif ($item['Status'] == 'Scheduled') {
                        $estadoCampana = "<div class='icono-estado programada' title='Programado'></div>";

                        $acciones .= "<span class='view'>
                                    <a href='' class='previsualizar-news' name='{$item['CampaignID'] }' title='Previsualizar Campaña en el Navegador o por Correo Electrónico' >Previsualizar</a> |
                                 </span>                            
						         <span class='edit'>
								   	 <a href='' class='pausar-campana-bt' name='{$item["CampaignID"] }' title='Pausar Campaña' >Pausar</a> | 
								   </span>
								 ";
                        $pagina = "campana-editar";

                        $fechaEnvio = $item['ScheduleSendDateFormated'];

                    } elseif ($item['Status'] == "Stopped") {
                        $estadoCampana = "<div class='icono-estado detenida' title='Detenido'></div>";

                        $acciones .= "<span class='view'>
                                        <a href='' class='previsualizar-news' name='{$item['CampaignID']}' title='Previsualizar Campaña en el Navegador o por Correo Electrónico' >Previsualizar</a> |
                                     </span>                            
                                     <span class='edit'>
                                         <a href='' class='reanudar-campana-bt' name='{$item['CampaignID']}' title='Reanudar Campaña' >Reanudar</a> | 
                                       </span>
                                     ";
                        $pagina = "campana-editar";

                    } elseif ($item['Status'] == "Sending") {
                        $estadoCampana = "<div class='icono-estado enviando' title='Enviando'></div>";

                        $acciones .= "<span class='edit'>
                                     <a href='' class='pausar-campana-bt' name='{$item["CampaignID"]}' title='Pausar Campaña' >Pausar</a> | 
                                   </span>
                               ";
                        $pagina = "campana-completa";

                    } elseif ($item['Status'] == "Completed") {
                        $estadoCampana = "<div class='icono-estado completada' title='Completado'></div>";
                        $fecha = "&e=" . $item['SendStartDateTimeFormated'];
                        $pagina = "campana-completa";

                        $acciones .= "<span class='view'>
                                        <a href='' class='previsualizar-news' name='{$item['CampaignID']}' title='Previsualizar Campaña en el Navegador o por Correo Electrónico' >Previsualizar</a> |
                                     </span>";
                        $url = get_admin_url() . "admin.php?page=envialo-simple&accion=reportes&idCampana=" . $item["CampaignID"];
                        $reportes = "<a  class='button-secondary ver-reportes-bt' href='{$url}' >Ver Reportes</a>";
                        $fechaEnvio = $item['SendStartDateTimeFormated'];

                    }

                    $html .= "<tr>
								<td>{$item["CampaignID"]}</td>								
								<td>
								    <a id='{$item["CampaignID"]}' name='{$item["Status"]}' href='{$adminUrl}admin.php?page=envialo-simple&accion={$pagina}&idCampana={$item["CampaignID"]}{$fecha}' class='row-title checkEstadoCampana'>
								        {$item["CampaignName"]}
								    </a>
								        <div class='row-actions'>
								            {$acciones}
								         </div>
								</td>
								<td>{$item["Subject"]}</td>
								<td>{$estadoCampana}</td>
								<td>{$item["TotalRecipients"]}</td>
								<td>{$reportes}</td>
								<td style='line-height: 33px;'>{$fechaEnvio}</td>
							</tr>";
                }

                //paginacion

                $paginas = $c['list']['pager']['pagecount'];
                $pagActual = $c['list']['pager']['absolutepage'];

                $pag = "<div id='paginacion'> ";

                if ($pagActual > 1) {

                    $pagDecrementada = $pagActual - 1;
                    $url = get_admin_url() . "admin.php?page=envialo-simple&pagina=" . $pagDecrementada;
                    $pag .= "<a class='pag' href='{$url}'> < </a>";
                }

                for ($i = 1; $i <= $paginas; $i++) {

                    if ($pagActual == $i) {
                        $clase = "class='pag pagActiva'";
                    } else {
                        $clase = "class='pag'";
                    }
                    $url = get_admin_url() . "admin.php?page=envialo-simple&pagina=" . $i;
                    $pag .= "<a  {$clase} href='{$url}'> {$i} </a>";
                }

                if ($pagActual < $paginas) {

                    $pagAumentada = $pagActual + 1;
                    $url = get_admin_url() . "admin.php?page=envialo-simple&pagina=" . $pagAumentada;
                    $pag .= "<a class='pag' href='{$url}'> > </a>";
                }

                $pag .= "</div>";

                $html .= "<tr><td></td> <td></td><td colspan='5'>{$pag}</td> </tr>";
                $html .= "</tbody></table></div>";

                return $html;

            } else {
                
                if(!empty($filter)){
                    $html = "";
                    $url = get_admin_url() . "admin.php?page=envialo-simple";
                    $html .= "<div class='wp-caption'><p>No se han Encontrado Resultados. Intente Buscando Nuevamente. </p> <p><a style='' href='{$url}' class='button-primary' >Volver</a></p> </div>";
                    return $html;                    
                }

                $html = "";
                $url = get_admin_url() . "admin.php?page=envialo-simple-nuevo";
                $html .= "<div class='wp-caption'><p>Aún No tienes Newsletters Creados. </p> <p><a style='width:200px' href='{$url}' id='abrir-modal-campana' class='button-primary abrir-modal-campana' >Crear Nuevo Newsletter</a></p> </div>";

                return $html;

            }

        } else {
            echo "Error de Conexion con el Servidor";
        }

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

    function curlJson($parametros, $url, $esGet = FALSE) {
        $cookie = "cookie.txt";

        $parametros["APIKey"] = $GLOBALS["APIKey"];
        
        

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_USERAGENT, "WP-Plugin EnvialoSimple");
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parametros));

        if ($esGet) {
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
        } else {
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        $resultado = curl_exec($ch);

        if (curl_errno($ch)) {

            return json_encode(array("root" => array("ajaxResponse" => array("curlError" => curl_errno($ch)))));

        } else {

            return $resultado;
        }

    }

}
?>
