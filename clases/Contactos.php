<?php

class Contactos {

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
        }
        
        
        $listas = json_decode($this->curlJson($parametros, URL_BASE_API . '/maillist/list/format/json'), TRUE);

        if ($listas["root"]["ajaxResponse"]["success"]) {

            return array("success" => TRUE, $listas["root"]["ajaxResponse"]["list"]);
        } else {

            return ( array("success" => FALSE, $listas["root"]["ajaxResponse"]));
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
            									<th class='manage-column column-title sortable desc' style='height:30px;width: 250px;' >Nombre</th>
            									<th class='manage-column column-title sortable desc' style='width: 220px;'>Contactos (Total/Activos)</th>            									
            									<th class='manage-column column-title sortable desc' style='text-align: center;width: 370px;'>Acciones</th>
            									
            								</tr>	
        								</thead>        														
        								<tbody>";

                foreach ($listas[0]['item'] as $item) {

                    $html .= "<tr>
        								<td></td>
        								
        								<td><a href='' class='row-title'>{$item["Name"]}</a></td>
        								<td>{$item["MemberCount"]} / {$item["ActiveMemberCount"]}</td>        								
        								<td>
        								    <a href='' name='{$item["MailListID"]}' class='boton-sincronizar button-secondary' title='Agregar los Contactos de Wordpress a la Lista de Envialo Simple '>Agregar Usuarios de Wordpress </a> 
        								    <a href='' name='{$item["MailListID"]}' class='boton-agregar-contacto button-secondary' title='Agregar Contacto a la Lista de Envialo Simple '>Agregar Contacto</a>
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
                $html .= '<div class="wp-caption">Aún No tenés Listas Creadas. <a href="" class="button-secondary action">Crear Nueva Lista</a> </div>';

                return array(TRUE, $html);
            }

        } else {

            $html = "";
            $html .= '<br /><div class="mensaje msjError" style="display:inline">No se pudo recuperar las Listas de Contacto. Por Favor Intente Nuevamente</div><p>En caso de persistir el error, reconfigure el Plugin</p>';

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