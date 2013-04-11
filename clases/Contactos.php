<?php

require_once("Curl.php");

class Contactos extends Curl {

    function listarContactosWordpress($table = 'users') {

        global $wpdb;
        // Get the columns and create the first row of the CSV
        switch($table) {
            case 'comments' :
                $fields = array('Name', 'E-Mail', 'URL');
                break;
            case 'users' :
            default :
                $fields = array('URL', 'E-Mail', 'URL', 'Display Name', 'Registration Date', 'First Name', 'Last Name', 'Nickname');
                break;
        }

        // Query the entire contents from the Users table and put it into the CSV
        switch($table) {
            case 'comments' :
                $query = "SELECT DISTINCT comment_author, comment_author_email, comment_author_url FROM $wpdb->comments WHERE comment_approved = '1'";
                break;
            case 'users' :
            default :
                $query = "SELECT ID as UID, user_email, user_url, user_nicename, user_registered FROM $wpdb->users";
                break;
        }
        $results = $wpdb->get_results($query, ARRAY_A);

        $i = 0;
        if ($table == 'users') {
            while ($i < count($results)) {
                $query = "SELECT meta_value FROM " . $wpdb->prefix . "usermeta WHERE user_id = " . $results[$i]['UID'] . " AND meta_key = ";
                $fnquery = $query . "'first_name'";
                $results[$i]['first_name'] = $wpdb->get_var($fnquery);
                $lnquery = $query . "'last_name'";
                $results[$i]['last_name'] = $wpdb->get_var($lnquery);
                $nnquery = $query . "'nickname'";
                $results[$i]['nickname'] = $wpdb->get_var($nnquery);
                $i++;
            }
        }

        return $results;
    }

    /**
     * Traer Las Listas de Contacto del Usuario
     *
     * @return array
     * @param $absolutePages
     */

    function listarListasContactos($absolutePage) {

        if($absolutePage != -1){
            $parametros["count"] = "5";
            $parametros["absolutepage"] = $absolutePage;
        }else{

            $parametros["count"] = "999999999999";
        }


        $listas = json_decode($this->curlJson($parametros, URL_BASE_API . '/maillist/list/format/json'), TRUE);

        if ($listas["root"]["ajaxResponse"]["success"]) {

            return array("success" => TRUE, $listas["root"]["ajaxResponse"]["list"]);
        } else {

            return ( array("success" => FALSE, $listas["root"]["ajaxResponse"]));
        }

    }


    function listarContactos($absolutePage ,$MailListsIds) {

        if($absolutePage != -1){
            $parametros["count"] = "25";
            $parametros["absolutepage"] = $absolutePage;
        }else{

            $parametros["count"] = "999999999999";
        }


        $parametros['MailListsIds'] = array($MailListsIds);


        $contactos = json_decode($this->curlJson($parametros, URL_BASE_API . '/member/list/format/json'), TRUE);



        if ($contactos["root"]["ajaxResponse"]["success"]) {

            return array("success" => 1, $contactos["root"]["ajaxResponse"]["list"]);
        } else {

            return ( array("success" => 0, $contactos["root"]["ajaxResponse"]));
        }

    }


    /**
     * Crea una lista de contactos
     *
     * @return string
     * @param $MailListName
     */

    function crearListaContactos($MailListName) {

        $parametros = array();
        $parametros["MailListName"] = $MailListName;
        return $this->curlJson($parametros, URL_BASE_API . "/maillist/edit/format/json");

    }

    /**
     * Agrega un contacto a la lista especificada
     *
     * @return string
     * @param $parametros["MailListID"]
     * @param $params['Email']
     * @param $params['CustomField1']
     * @param $params['CustomField2']
     */
    function agregarContactoALista($MailListID,$Email,$campos) {

        $parametros = array();
        $parametros["MailListID"] = $MailListID;
        $parametros["Email"] = $Email;

        $i = 1;
        foreach ($campos as $c) {
            $parametros["CustomField".$i] = $c ;
            $i ++;
        }

        return $this->curlJson($parametros, URL_BASE_API . "/member/edit/format/json");
    }

    /**
     * Muestra las Listas de Contactos del Usuario
     *
     * @return string
     */
    function mostrarListasContactos($absolutePage) {

        $listas = $this->listarListasContactos($absolutePage);

        if ($listas["success"]) {

            if (!empty($listas[0]["item"])) {
                $html = "";
                $html = "<div>
        							<table class='wp-list-table widefat fixed posts'>
        								<thead>
            								<tr>
            									<th scope='col' id='cb' class='manage-column column-cb check-column' style=''></th>
            									<th class='manage-column column-title sortable desc' style='height:30px;width: 250px;' >".__('Nombre','envialo-simple')."</th>
            									<th class='manage-column column-title sortable desc' style='width: 220px;'>".__('Contactos (Total/Activos)','envialo-simple')."</th>
            									<th class='manage-column column-title sortable desc' style='text-align: center;width: 565px;'>".__('Acciones','envialo-simple')."</th>
            								</tr>
        								</thead>
        								<tbody>";

                foreach ($listas[0]['item'] as $item) {
                    $url = $url = get_admin_url() . "admin.php?page=envialo-simple-listas&MailListsIds=";
                    $html .= "<tr>
    								<td></td>

    								<td><span class='row-title'>{$item["Name"]}</span></td>
    								<td>{$item["MemberCount"]} / {$item["ActiveMemberCount"]}</td>
    								<td>
    								    <a href='#' name='{$item["MailListID"]}' class='boton-sincronizar button-secondary' title='Agregar los Contactos de Wordpress a la Lista de Envialo Simple '>".__('Agregar Usuarios de Wordpress','envialo-simple')."</a>
    								    <a href='#' name='{$item["MailListID"]}' class='boton-agregar-contacto button-secondary' title='Agregar Contacto a la Lista de Envialo Simple '>".__('Agregar Contacto','envialo-simple')."</a>
    								    <a href='{$url}{$item["MailListID"]}' name='{$item["MailListID"]}' class='button-secondary boton-importar-contacto' title='Importar Contactos '>".__('Importar Contactos','envialo-simple')."</a>
    								</td>

							</tr>";

                }


                 //paginacion

                $paginas = $listas[0]['pager']['pagecount'];
                $pagActual = $listas[0]['pager']['absolutepage'];

                $pag = "<div id='paginacion'> ";

                if ($pagActual > 1) {

                    $pagDecrementada = $pagActual - 1;
                    $url = get_admin_url() . "admin.php?page=envialo-simple-configuracion&pagina=" . $pagDecrementada;
                    $pag .= "<a class='pag' href='{$url}'> < </a>";
                }

                for ($i = 1; $i <= $paginas; $i++) {

                    if ($pagActual == $i) {
                        $clase = "class='pag pagActiva'";
                    } else {
                        $clase = "class='pag'";
                    }
                    $url = get_admin_url() . "admin.php?page=envialo-simple-configuracion&pagina=" . $i;
                    $pag .= "<a  {$clase} href='{$url}'> {$i} </a>";
                }

                if ($pagActual < $paginas) {

                    $pagAumentada = $pagActual + 1;
                    $url = get_admin_url() . "admin.php?page=envialo-simple-configuracion&pagina=" . $pagAumentada;
                    $pag .= "<a class='pag' href='{$url}'> > </a>";
                }

                $pag .= "</div>";

                $html .= "<tr><td></td> <td></td><td colspan='2'>{$pag}</td> </tr>";
                $html .= "</tbody></table></div>";

                return array(TRUE, $html);

            } else {

                $html = "";
                $html .= '<div class="wp-caption">Aún No tenés Listas Creadas. <a href="" class="button-secondary action">'.__('Crear Nueva Lista','envialo-simple').'</a> </div>';

                return array(TRUE, $html);
            }

        } else {

            $html = "";
            $html .= '<br /><div class="mensaje msjError" style="display:inline">'.__('No se pudo recuperar las Listas de Contacto. Por Favor Intente Nuevamente','envialo-simple').'</div><p>'.__('En caso de persistir el error, reconfigure el Plugin','envialo-simple').'</p>';

            return array(FALSE, $html);
        }

    }


    function importarSelectSource($MailListsIds){

        $parametros['MailListsIds'] = array($MailListsIds);
        $respuesta = json_decode($this->curlJson($parametros, URL_BASE_API."/maillist/selectsource"),TRUE);
        if(isset($respuesta['root']['ajaxResponse']['success'])){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function importarCopyPaste($CopyPaste){

        $parametros = array();
        $parametros['CopyPaste'] = $CopyPaste;

        $respuesta = json_decode($this->curlJson($parametros, URL_BASE_API."/maillist/copypaste"),TRUE);

        if(isset($respuesta['root']['ajaxResponse']['success'])){
            return TRUE;
        }else{
            return FALSE;
        }



    }

    function importarUploadFile($nombreArchivo,$path){
        $parametros = array();

        $pathCompleta = $path ."php/uploads/".$nombreArchivo;

        if(file_exists($pathCompleta)){

            $parametros['qqfile'] = "@".$pathCompleta;


        $respuesta = $this->curlJson($parametros, URL_BASE_API."/maillist/uploadfile",FALSE ,TRUE);

        return $respuesta;

        }else{

            return "no es archivo";
        }




    }

    function importarPreProcess(){
        $parametros = array();
        $parametros['Delimiter'] = ",";
        $parametros['Qualifier'] = "";

        $respuesta = $this->curlJson($parametros, URL_BASE_API."/maillist/preprocess");

        return $respuesta;

    }

    function importarProcessCopy($MailListsIds,$correspondencias){

          $parametros = array();
          $parametros['MailListsIds']= array($MailListsIds);
          $parametros['Delimiter'] = ",";
          $parametros['Qualifier'] = "";

          foreach ($correspondencias as $c) {
             $parametros =  array_merge($parametros,$c);
          }

          $respuesta = $this->curlJson($parametros, URL_BASE_API."/maillist/processcopypaste");
          return $respuesta;

    }


     function importarProcessFile($MailListsIds,$correspondencias){

          $parametros = array();
          $parametros['MailListsIds']= array($MailListsIds);
          $parametros['Delimiter'] = ",";
          $parametros['Qualifier'] = "";

          foreach ($correspondencias as $c) {
             $parametros =  array_merge($parametros,$c);
          }

          $respuesta = $this->curlJson($parametros, URL_BASE_API."/maillist/processfile");
          return $respuesta;

    }



}
?>