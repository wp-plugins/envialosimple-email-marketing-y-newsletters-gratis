<?php

define("URL_BASE_API", 'https://app.envialosimple.com');
define("TABLA_CLAVES", "ev_claves");
require_once ("Curl.php");

class EnvialoSimple extends Curl {

    var $errorMsg;
    var $curlChannel;

    function EnvialoSimple() {
        if (is_file('cookie.txt'))
            @unlink('cookie.txt');
        $this->curlChannel = curl_init();
        curl_setopt($this->curlChannel, CURLOPT_USERAGENT, "WP-Plugin EnvialoSimple");
        curl_setopt($this->curlChannel, CURLOPT_TIMEOUT, 180);
        curl_setopt($this->curlChannel, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($this->curlChannel, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curlChannel, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curlChannel, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curlChannel, CURLOPT_COOKIEJAR, 'cookiejar/cookie.txt');
    }

    function checkSetup($noRedirect = NULL) {
        //comprueba que la tabla este creada y que existan claves. En caso negativo redirecciona a la configuracion inicial
        $respuesta = json_decode($this->traerTokenBD(), TRUE);
        if ($respuesta["success"]) {
            $GLOBALS["APIKey"] = $respuesta["Key"];
        } else if (is_null($noRedirect)) {
            $amindURL = get_admin_url();
            if ($respuesta["existenTablas"]) {
                //Se debe configurar y guardar token, mostrar configuracion
                echo "<div style='background-color:#FFFBCC; border:#E6DB55 1px solid; color:#555555; border-radius:3px; padding:5px 10px; margin:20px 15px 10px 0; text-align:left'>
                        " . __('Redireccionando a Configuración Inicial..', 'envialo-simple') . "
                      </div>
                      <script>
                          window.location = '{$amindURL}admin.php?page=envialo-simple-configuracion&setup=true';
                      </script>";
            } else {
                $version = get_bloginfo('version');
                $wp_language = get_bloginfo('language');
                $wp_site_url = site_url();
                $url = "https://donweb.com/imgmed/wp_check.php?version={$version}&language={$wp_language}&site_url={$wp_site_url}&img=1";
                if (!extension_loaded('curl')) {
                    $url .= '&curl=error';
                    echo 'Fatal Error: The cURL extension is not loaded.';
                    echo '<a href="http://php.net/manual/en/curl.installation.php">Please ensure its installed and activated.</a>';
                    echo "<img src='{$url}' size='1'>";
                    die();
                }
                echo "<img src='{$url}' size='1'>";
                $url .= '&curl=ok';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_USERAGENT, "WP-Plugin EnvialoSimple");
                curl_setopt($ch, CURLOPT_TIMEOUT, 180);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                curl_setopt($ch, CURLOPT_REFERER, $url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_HTTPGET, 1);
                curl_exec($ch);
                //crear tablas y redir a config
                $this->crearTablaClavesBD();
                echo "<div style='background-color:#FFFBCC; border:#E6DB55 1px solid; color:#555555; border-radius:3px; padding:5px 10px; margin:20px 15px 10px 0; text-align:left'>
                          " . __('Creando Tablas y Redireccionando a Configuración Inicial..', 'envialo-simple') . "
                      </div>
                       <script>
                           window.location = '{$amindURL}admin.php?page=envialo-simple-configuracion&setup=true'
                       </script>";
            }
            exit();
        }
    }

    function eliminarTablaBD() {
        if ($this->tablasCreadas()) {
            global $wpdb;
            $nombre_tabla = $wpdb->prefix . TABLA_CLAVES;
            $sql = "DROP TABLE {$nombre_tabla};";
            $wpdb->query($sql);
        }
    }

    function crearTablaClavesBD() {
        global $wpdb;
        $nombre_tabla = $wpdb->prefix . TABLA_CLAVES;
        $sql = "CREATE TABLE IF NOT EXISTS {$nombre_tabla} (
                  `idClave` INT NOT NULL AUTO_INCREMENT,
                  `KeyID` INT NOT NULL,
                  `KeyName` VARCHAR(45) NOT NULL,
                  `Key` TEXT NULL,
                  `Enabled` INT(1) NULL,
                  PRIMARY KEY (`idClave`)
                );";
        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Guarda la APIKey, el nombre, el ID y el estado
     *
     * @return bool
     * @param  $datos["KeyID"]
     * @param  $datos["KeyName"]
     * @param  $datos["Key"]
     * @param  $datos["Enabled"]
     */
    function guardarTokenBD($datos) {
        global $wpdb;
        $wpdb->hide_errors();
        $filasAfectadas = $wpdb->insert($wpdb->prefix . TABLA_CLAVES, $datos);
        if ($filasAfectadas > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function tablasCreadas() {
        global $wpdb;
        $nombre_tabla = $wpdb->prefix . TABLA_CLAVES;
        if ($wpdb->get_var("SHOW TABLES LIKE '{$nombre_tabla}'") != $nombre_tabla) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function mostrarCategoriasWP() {
        $categorias = get_categories();
        $html = "";
        foreach ($categorias as $ca) {
            $html .= "<option value='{$ca->cat_ID}'>{$ca->name}</option>";
        }
        return $html;
    }

    /**
     * Recupera los Post del Blog
     *
     * @return string
     * @param $parametros["category"]
     * @param $parametros["numberposts"]
     * @param $parametros[" offset"]
     */
    function mostrarPostsWP($category, $numberposts, $pagina) {


        $parametros = array();
        $parametros["orderby"] = "post_date";
        $parametros["order"] = "DESC";
        $parametros["post_type"] = "post";
        $parametros["post_status"] = "publish";
        $parametros["cat"] = $category;
        $parametros["posts_per_page"] = 10;
        $parametros["paged"] = isset($pagina) ? $pagina : 1;


        wp_reset_query();

        $posts = new WP_Query($parametros);
        //$posts = get_posts($parametros);
        $html = "";
        $i = 1;

        while ($posts->have_posts()) {
            $posts->the_post();

            $fecha = mysql2date(get_option("date_format"), get_the_time());
            $htmlImg = "";
            if ($images = get_posts(array('post_parent' => get_the_ID(), 'post_type' => 'attachment', 'numberposts' => -1, 'post_mime_type' => 'image',))) {
                foreach ($images as $image) {
                    $attachmenturl = wp_get_attachment_url($image->ID);
                    $attachmentimage = wp_get_attachment_image_src($image->ID, "thumbnail");
                    $imageDescription = apply_filters('the_description', $image->post_content);
                    $imageTitle = apply_filters('the_title', $image->post_title);
                    $htmlImg .= '<div><img name="' . $attachmenturl . '" src="' . $attachmentimage[0] . '" alt=""  /></a></div>';
                }
            } else {
                $htmlImg .= '<div><img src="' . plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/300x200.jpg') . '" alt="" height="150" width="150" /></a></div>';
            }
            $postContentResumido = wp_trim_words(get_the_content(), 30, "...<br /><a target='_blank' style='text-decoration:underline' class='ver-mas-post' href='" . get_the_permalink() . "'>" . __('Ver Más', 'envialo-simple') . "</a><br /><br />");
            $html .= "  <div class='post'>
                            <div class='contenedor-checkbox-post fl'>
                                <input type='checkbox' name='" . get_the_ID() . "' class='checkbox-post' />
                            </div>
                            <div class='contenido-post fl'>
                                <div class='contenedor-slider'>
                                    <div id='slide_{$i}'>
                                        <div class='slides_container'>{$htmlImg}</div>
                                    </div>
                                </div>
                                <div class='fecha-post'>{$fecha}</div>
                                <div class='titulo-post'>" . get_the_title() . "</div>
                                <div class='resumen-post'>{$postContentResumido}</div>
                            </div>
                        </div>
                        <script>jQuery('#slide_{$i}').slides()</script>";
            $i++;
        }

        $prev_post ="";
        $next_post ="";

        if ($posts->max_num_pages > 1) {
            //paginacion

            if ($pagina >= 2) {
                $pagina_ir = $pagina - 1;
                $prev_post = "<a href='#' class='paginacion-wp' data-pag-actual='{$pagina}' data-pag-ir='{$pagina_ir}'>Anterior</a>";
            }

            if ($pagina < $posts->max_num_pages) {
                $pagina_ir_n = $pagina + 1;
                $next_post = "<a href='#' class='paginacion-wp' data-pag-actual='{$pagina}' data-pag-ir='{$pagina_ir_n}'>Siguiente</a>";
            }
            $html .= "<div class='post-pagination'>{$prev_post} | {$next_post} </div>";
        }




        wp_reset_postdata();
        wp_reset_query();
        return $html;
    }

    function traerTokenBD() {
        global $wpdb;
        if ($this->tablasCreadas()) {
            $nombre_tabla = $wpdb->prefix . TABLA_CLAVES;
            @$resultado = $wpdb->get_results("SELECT * FROM {$nombre_tabla} ;", ARRAY_A);
            if (!empty($resultado)) {
                // existen tokens
                $resultado = array_merge($resultado[0], array("success" => TRUE));
                return json_encode($resultado);
            } else {
                return $this->error("No hay Claves", array("existenClaves" => FALSE, "existenTablas" => TRUE));
                //si no existe, pedirle los datos al usuario, loguearlo y generar clave
            }
        } else {
            return $this->error("No hay Tablas Creadas", array("existenTablas" => FALSE));
            //crear tablas
        }
    }

    function eliminarTokenBD($idClave) {
        global $wpdb;
        $GLOBALS["APIKey"] = '';
        if ($this->tablasCreadas()) {
            $nombre_tabla = $wpdb->prefix . TABLA_CLAVES;
            $resultado = $wpdb->query($wpdb->prepare("DELETE FROM $nombre_tabla WHERE idClave = %d;", $idClave));
            if ($resultado) {
                return exito("Clave Eliminada Correctamente");
            } else {
                return error("Error al Eliminar la Clave");
            }
        }
        return true;
    }

    /**
     * Recupera la cantidad de envios disponibles del usuario
     *
     * @return mixed
     */
    function traerEnviosDisponibles() {
        $respuesta = json_decode($this->curlJson('', URL_BASE_API . "/administrator/status"), TRUE);
        if (!$respuesta["root"]["ajaxResponse"]["success"]) {
            $this->errorMsg = __('No se puedo recuperar los creditos disponibles:', 'envialo-simple');
            return false;
        }
        $result = array();
        $result['white_label'] = $respuesta["root"]["ajaxResponse"]["userinfo"]["white_label"];
        $result['role'] = $respuesta["root"]["ajaxResponse"]["userinfo"]['role'];
        $result['credits'] = $respuesta["root"]["ajaxResponse"]["credits"];
        return $result;
    }

    /**
     * Recupera los precios de los envios segun el pais del usuario
     *
     * @return string
     * @param $parametros["APIKey"]
     */
    function traerPreciosEnvios($APIKey) {
        $respuesta = json_decode($this->curlJson(array($APIKey), URL_BASE_API . "/administrator/status"), TRUE);
        if (isset($respuesta["root"]["ajaxResponse"]["success"])) {
            $r = $respuesta["root"]["ajaxResponse"];
            $html = "<form method='post' id='form-comprar-envios' action='#'>
					<input type='hidden' name='codigoPais' value='{$r['userinfo']['priceList']['country']['codigoPais']}'/>
					<table id='tabla-precios-envios'>
						<thead>
							<tr>
								<td style='width: 300px;font-weight: bold;'>" . __('Cantidad de Envíos', 'envialo-simple') . "</td>
								<td style='font-weight: bold'>" . __('Precio Paquete', 'envialo-simple') . "</td>
							</tr>
						</thead>
						<tbody>";
            $i = 0;
            foreach ($r['userinfo']['priceList']['list'] as $precio) {
                if ($i & 1) {
                    $clase = "class='impar'";
                } else {
                    $clase = "class='par'";
                }
                $html .= "<tr {$clase}>
							<td><input type='radio' name='plan' id='{$precio['plan']}'  value='{$precio['plan']}'/><label for='{$precio["plan"]}'> {$precio["cantidad"]}</label> </td>
							<td>{$r['userinfo']['priceList']['country']['simb_moneda']}{$precio['valor']}</td>
						<tr>";
                $i++;
            }
            $html .= "</tbody></table>
				<input type='submit' id='comprar-envios' class='button-primary' value='" . __('Comprar Envíos', 'envialo-simple') . "'/>
					</form>";
            return $html;
        } else {
            return __('Error al Traer los Precios', 'envialo-simple');
        }
    }

    function traerCategoriasPlantillas() {
        $parametros = array();
        $respuesta = json_decode($this->curlJson($parametros, URL_BASE_API . "/content/templatecategories"), TRUE);
        if ($respuesta["root"]["ajaxResponse"]["success"]) {
            return $respuesta["root"]["ajaxResponse"];
        } else {
            return array("success" => FALSE);
        }
    }

    function traerPlantillas($limit, $retrieveList, $offset, $filterListByCategory, $filterListByCategory2) {
        $parametros = array();
        $parametros["retrieveList"] = $retrieveList;
        $parametros["limit"] = $limit / 1;
        $parametros["offset"] = $offset / 1;
        if (isset($filterListByCategory[0]) && $filterListByCategory[0] != 0) {
            $parametros["filterListByCategory"] = $filterListByCategory;
        }
        if (isset($filterListByCategory[0]) && $filterListByCategory2[0] != 0) {
            $parametros["filterListByCategory2"] = $filterListByCategory2;
        }
        $respuesta = json_decode($this->curlJson($parametros, URL_BASE_API . "/content/gallery/format/json"), TRUE);
        if (isset($respuesta["root"]["ajaxResponse"]["success"])) {
            return $respuesta["root"]["ajaxResponse"];
        } else {
            return array("success" => FALSE);
        }
    }

    function mostrarPlantillas($limit = 9, $retrieveList = "defaulTemplates", $offset = 0, $filterListByCategory = array(), $filterListByCategory2 = array()) {
        $respuesta = $this->traerCategoriasPlantillas();
        $categorias = $respuesta["list"]["category"];
        $colores = $respuesta["list"]["color"];
        $htmlCat = "";
        foreach ($categorias as $cat) {
            $sel = "";
            if (isset($filterListByCategory[0]) && $filterListByCategory[0] == $cat["CategoryID"]) {
                $sel = "selected = 'selected'";
            }
            $htmlCat .= "<option {$sel} value='{$cat["CategoryID"]}'>{$cat["NameToken"]}</option>";
        }
        $htmlCol = "";
        foreach ($colores as $cat) {
            $sel = "";
            if (isset($filterListByCategory[0]) && $filterListByCategory[0] == $cat["CategoryID"]) {
                $sel = "selected=selected";
            }
            $htmlCol .= "<option {$sel} value = '{$cat["CategoryID"]}' > {$cat["NameToken"]} </option>";
        }
        $plantillas = $this->traerPlantillas($limit, $retrieveList, $offset, $filterListByCategory, $filterListByCategory2);
        if (isset($plantillas["success"]) && $plantillas["success"]) {
            $html = "<div id='filtros-plantillas'>
                         <label>" . __('Filtrar por Categorías', 'envialo-simple') . "</label>
                         <select class='select-categorias'>
                            <option value='0'>" . __('Seleccionar..', 'envialo-simple') . "</option>
                            {$htmlCat}
                        </select>
                        &nbsp;&nbsp;
                        <label>" . __('Filtrar por Color', 'envialo-simple') . "</label>
                        <select class='select-categorias'>
                            <option value='0'>" . __('Seleccionar..', 'envialo-simple') . "</option>
                            {$htmlCol}
                        </select>
                         <div class='button-secondary' style='float: right;margin-right: 10px;margin-top: 2px;' id='cerrar-modal-plantillas'>" . __('Cancelar', 'envialo-simple') . "</div>
                    </div>
                    <div id='tabla-plantillas'>
                        <table name='wp-list-table widefat fixed posts'>
                            <tbody>
                                <tr>
                                    <td>
                                        <a class='plantilla-click' name='0000_new-blank_600px' href='#' >
                                            <div class='plantilla' >
                                                <div id='plantilla-blanco'>" . __('En Blanco', 'envialo-simple') . "</div>
                                                <br />
                                            </div>
                                        </a>
                                    </td>";
            $i = 2;
            foreach ($plantillas["myTemplatesList"]["template"] as $p) {
                $html .= "
                                    <td>
                                        <a class='plantilla-click' name='{$p["templateId"]}' href='' >
                                            <div class='plantilla' >
                                                <div class='plantilla-imagen'>
                                                    <img  title='{$p["templateTitle"]}' src='http://v2.envialosimple.com/mailing_templates/{$p["templateThumbnail"]["path"]}' />
                                                </div>
                                                <br />
                                            </div>
                                        </a>
                                    </td>";
                if ($i % 5 == 0) {
                    $html .= "   </tr>
                                <tr>";
                }
                $i++;
            }
            $html .= "           </tr>
                            </tbody>
                        </table>
                    </div>";
            if ($plantillas["totalTemplates"] > 9) {
                //pagino
                $totalPaginas = round($plantillas["totalTemplates"] / 9);
                $pagActual = $offset / 9;
                $html .= "<div id='paginador-plantillas'>";
                if ($pagActual > 0) {
                    $pagAnterior = $pagActual - 1;
                    $html .= "<span class='pag-plantilla' name='{$pagAnterior}'> < </span>";
                }
                for ($i = 0; $i < $totalPaginas; $i++) {
                    $j = $i + 1;
                    $class = "";
                    if ($pagActual == $i) {
                        $class = "pag-plantilla-activa";
                    }
                    $html .= "<span class='pag-plantilla {$class}' name='{$i}'>{$j}</span>";
                }
                if ($pagActual < ($totalPaginas - 1)) {
                    $pagSiguiente = $pagActual + 1;
                    $html .= "<span class='pag-plantilla' name='{$pagSiguiente}'> > </span>";
                }
                $html .= "</div>";
            }
            return $html;
        } else {
            return __('Error al Traer las Plantillas', 'envialo-simple');
        }
    }

    /**
     * Comprueba que la APIKey funcione
     *
     * @return mixed
     */
    function testToken() {
        $resultado = json_decode($this->curlJson("", URL_BASE_API . "/maillist/list/format/json"), TRUE);
        $resultado = $resultado["root"]["ajaxResponse"];
        if (array_key_exists("success", $resultado) && $resultado["success"]) {
            if ($this->guardarTokenBD(array("Key" => $GLOBALS["APIKey"]))) {
                return array("success" => TRUE, "mensaje" => "Clave Correcta");
            } else {
                return array("success" => FALSE, "mensaje" => "Error al Guardar en la BD");
            }
        } else {
            return array("success" => FALSE, "mensaje" => "Clave Incorrecta");
        }
    }

    /**
     * Trae Los emails de administrador del usuario
     *
     * @return array
     */
    function obtenerEmailAdministrador() {
        $email = json_decode($this->curlJson("", URL_BASE_API . '/administratoremail/list/format/json'), TRUE);
        if ($email["root"]["ajaxResponse"]["success"]) {
            return ($email["root"]["ajaxResponse"]["list"]);
        } else {
            return ( array("item" => ""));
        }
    }

    /**
     * Agrega un Nuevo Email de Administrador
     *
     * @return string
     * @param EmailAddress
     * @param Name
     */
    function agregarEmailAdministrador($EmailAddress, $Name) {
        $parametros = array();
        $parametros["EmailAddress"] = $EmailAddress;
        $parametros["Name"] = stripslashes($Name);
        return $this->curlJson($parametros, URL_BASE_API . '/administratoremail/edit/format/json/');
    }

    /**
     * recuperar los campos personalizados del usuario
     *
     * @return array
     */
    function traerCamposPersonalizados() {
        $respuesta = json_decode($this->curlJson(array(), URL_BASE_API . '/customfield/list/format/json'), TRUE);
        if (isset($respuesta["root"]["ajaxResponse"]["success"])) {
            return $respuesta["root"]["ajaxResponse"]["list"];
        } else {
            return array();
        }
    }

    function agregarCampoPersonalizado($Title, $FieldType, $Validation, $ItemsIsMultipleSelect, $DefaultValue = NULL, $ItemsValues = NULL, $ItemsNames) {
        $parametros = array();
        $parametros['Title'] = $Title;
        $parametros['FieldType'] = $FieldType;
        $parametros['Validation'] = $Validation;
        $parametros['ItemsValues'] = $ItemsValues;
        $parametros['ItemsNames'] = $ItemsNames;
        $parametros['ItemsIsMultipleSelect'] = $ItemsIsMultipleSelect;
        $parametros['DefaultValue'] = $DefaultValue;
        $respuesta = json_decode($this->curlJson($parametros, URL_BASE_API . "/customfield/edit/format/json"), TRUE);
        if (isset($respuesta['root']['ajaxResponse']['success'])) {
            return $respuesta['root']['ajaxResponse']['customField'];
        } else {
            return $respuesta['root'];
        }
    }

    function mostrarCamposPersonalizadosForm() {
        $campos = $this->traerCamposPersonalizados();
        if (!empty($campos)) {
            $html = "";
            foreach ($campos['item'] as $c) {
                $c['Validation'] = str_replace(" ", "", $c['Validation']);
                $html .= "<label>{$c['Title']}:<br />
                            <input type='text' class='{$c['Validation']}' name='{$c['CustomFieldID']}' />
                          </label><br />";
            }
            return $html;
        } else {
            return "Error al Recuperar los Campos Personalizados";
        }
    }

    function mostrarCamposPersonalizados($camposExistentes = array()) {
        $html = "";
        $campos = $this->traerCamposPersonalizados();
        //echo json_encode($campos);
        if (!empty($camposExistentes)) {
            foreach ($camposExistentes as $c) {
                $i = 0;
                foreach ($campos['item'] as $campo) {
                    if ($campo['CustomFieldID'] == $c) {
                        break;
                    } else {
                        $i++;
                    }
                }
                $html .= "<tr>
                              <td></td>
                              <td>
                                  <select name='CustomFieldsIds[]'>
                                  <option value='{$c}'>{$campos['item'][$i]['Title']}</option>
                                  </select>
                               </td>
                               <td>
                                   <span class='campo-nuevo eliminar' style='display:block;width:10px;' name='eliminar-x'>X</span>
                               </td>
                           </tr>";
            }
        }
        return $html;
    }

    function mostrarCampoPersonalizado($camposExistentes = array()) {
        $campos = $this->traerCamposPersonalizados();
        if (!empty($campos)) {
            $html = "<tr>
                  <td></td>
                     <td>
                      <select name='CustomFieldsIds[]'>
                      <option value='0'>" . __('Seleccionar..', 'envialo-simple') . "</option>";
            if (!empty($camposExistentes)) {
                foreach ($campos['item'] as $c) {
                    if (!in_array($c['CustomFieldID'], $camposExistentes)) {
                        $html .= "<option value='{$c['CustomFieldID']}' >{$c['Title']}</option>";
                    }
                }
            } else {
                foreach ($campos['item'] as $c) {
                    $html .= "<option value='{$c['CustomFieldID']}' >{$c['Title']}</option>";
                }
            }
            $html .= "<option class='selectCrear' value='-1'>".  __('+ Crear Nuevo Campo...', 'envialo-simple')."</option>
                        </select>
                          </td>
                        <td>
                            <span class='campo-nuevo eliminar' style='display:block' name='eliminar-x'>X</span>
                        </td>
                       </tr>  ";
            return $html;
        } else {
            return "Error al Recuperar los Campos Personalizados";
        }
    }

    /**
     * Logea al usuario en la aplicacion web
     * @param string $usuario
     * @param string $password
     * @return bool
     */
    function loginEnvialosimple($usuario, $password) {
        $parametros = array();
        $parametros['username'] = $usuario;
        $parametros['password'] = $password;
        $parametros['format'] = 'json';
        curl_setopt($this->curlChannel, CURLOPT_URL, URL_BASE_API . '/authentication/wplogin');
        curl_setopt($this->curlChannel, CURLOPT_POSTFIELDS, http_build_query($parametros));
        curl_setopt($this->curlChannel, CURLOPT_POST, 1);
        curl_setopt($this->curlChannel, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curlChannel, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($this->curlChannel);
        $jsonResponse = json_decode($response, true);

        if (curl_errno($this->curlChannel)) {
            $curl_errno = curl_errno($this->curlChannel);
            $curl_error = curl_error($this->curlChannel);
            $error = "curl_errno: {$curl_errno} | curl_error: {$curl_error}";
            return array(FALSE, $error);
        }

        if (!$jsonResponse['root']['ajaxResponse']['result']) {
            return array(FALSE);
        }
        return array(TRUE);
    }

    function logoutEnvialosimple() {
        //FIXME
        @unlink('cookiejar/cookie.txt');
        curl_close($this->curlChannel);
    }

    /**
     * Trae las APIKEY del usuario logeado en la web
     * @return mixed
     */
    function traerTokenUsuario() {
        curl_setopt($this->curlChannel, CURLOPT_HTTPGET, 1);
        curl_setopt($this->curlChannel, CURLOPT_URL, URL_BASE_API . "/key/list/format/json");
        curl_setopt($this->curlChannel, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curlChannel, CURLOPT_SSL_VERIFYHOST, false);
        $resultado = curl_exec($this->curlChannel);
        $jsonVar = json_decode($resultado, TRUE);
        if (empty($jsonVar["root"]["ajaxResponse"]["userinfo"]["Username"])) {
            $this->errorMsg = 'Error de Logueo';
            return false;
        }
        if (!$jsonVar["root"]["ajaxResponse"]["success"]) {
            $remoteErrors = print_r(',', $jsonVar["root"]["ajaxResponse"]['errors'], true);
            $this->errorMsg = "Error en la Respuesta de la API: {$remoteErrors}";
            return false;
        }
        if (!count($jsonVar["root"]["ajaxResponse"]["list"]["item"]) > 0) {
            $this->errorMsg = 'EL usuario no tiene claves';
            return false;
        }
        $clave = false;
        foreach ($jsonVar["root"]["ajaxResponse"]["list"]["item"] as $c) {
            if ($c['Enabled'] == 1) {
                $clave = $c;
            }
        }
        if (!$clave) {
            $this->errorMsg = 'EL usuario no tiene claves';
            return false;
        }
        $result = array();
        $result["KeyID"] = $clave['KeyID'];
        $result["KeyName"] = $clave['Name'];
        $result["Key"] = $clave['Key'];
        $result["Enabled"] = 1;
        return $result;
    }

    /**
     * Genera una APIKey para utilizar en el plugin, con el siguiente nombre "wordpress-"{HTTP-HOST}
     *
     * @return mixed
     */
    function generarTokenUsuario() {
        $parametros = array();
        $parametros['Name'] = "wordpress-{$_SERVER['HTTP_HOST']}";
        $parametros['Enable'] = 1;
        $parametros['format'] = 'json';
        curl_setopt($this->curlChannel, CURLOPT_POST, 1);
        curl_setopt($this->curlChannel, CURLOPT_POSTFIELDS, http_build_query($parametros));
        curl_setopt($this->curlChannel, CURLOPT_URL, URL_BASE_API . '/key/edit');
        curl_setopt($this->curlChannel, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curlChannel, CURLOPT_SSL_VERIFYHOST, false);
        $resultado = curl_exec($this->curlChannel);
        $jsonVar = json_decode($resultado, TRUE);
        if (empty($jsonVar['root']['ajaxResponse']['userinfo']['Username'])) {
            $this->errorMsg = 'Error de Logueo';
            return false;
        }
        if (!$jsonVar['root']['ajaxResponse']['success']) {
            $remoteErrors = print_r(',', $jsonVar["root"]["ajaxResponse"]['errors'], true);
            $this->errorMsg = "Error en la Respuesta de la API: {$remoteErrors}";
        }
        if (empty($jsonVar['root']['ajaxResponse']['key'])) {
            $remoteErrors = print_r($jsonVar["root"]["ajaxResponse"]['errors'], true);
            $this->errorMsg = "Error al crear Clave: {$remoteErrors}";
            return false;
        }
        $c = $jsonVar['root']['ajaxResponse']['key'];
        $result = array();
        $result['KeyID'] = $c['KeyID'];
        $result['KeyName'] = $c['Name'];
        $result['Enabled'] = 1;
        $result['Key'] = $c['Key'];
        return $result;
    }

    function dejarFeedback($mensaje) {
        $parametros = array();
        $parametros['feedback'] = $mensaje;
        $respuesta = json_decode($this->curlJson($parametros, URL_BASE_API . "/administrator/feedback/format/json"), TRUE);
        if (isset($respuesta['root']['ajaxResponse']['success'])) {
            return $respuesta;
        } else {
            $respuesta = array();
            $respuesta['root']['ajaxResponse']['success'] = FALSE;
            return $respuesta;
        }
    }

    function error($mensaje, $parametrosAdicionales = NULL) {
        $error = array();
        $error["success"] = FALSE;
        $error["mensaje"] = $mensaje;
        if (!is_null($parametrosAdicionales)) {
            $error = array_merge($error, $parametrosAdicionales);
        }
        return json_encode($error);
    }

    function exito($mensaje, $parametros) {
        $exito = array();
        $exito["success"] = TRUE;
        $exito["mensaje"] = $mensaje;
        $exito = array_merge($exito, $parametros);
        return json_encode($exito);
    }

}

?>