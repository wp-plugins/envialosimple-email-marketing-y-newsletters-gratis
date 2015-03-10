<?php
include_once(ENVIALO_DIR . "/clases/EnvialoSimple.php");
include_once(ENVIALO_DIR . "/clases/Campanas.php");
include_once(ENVIALO_DIR . "/clases/Contactos.php");
include_once(ENVIALO_DIR . "/clases/Formularios.php");

$ev = new EnvialoSimple();
$ev->checkSetup();
include_once(ENVIALO_DIR . "/paginas/header.php");
$fo = new Formularios();


$emailsAdmin = $ev->obtenerEmailAdministrador();
$htmlSelectEmailAdminFrom = "";
foreach ($emailsAdmin["item"] as $e) {
    $htmlSelectEmailAdminFrom .= "<option value='{$e['EmailID']}'> {$e['Name']} ({$e["EmailAddress"]}) </option>";
}

$htmlSelectEmailAdminReply = $htmlSelectEmailAdminFrom;

if (isset($_GET['idFormulario']) && $_GET['idFormulario'] != 0) {

    $idFormulario = filter_var($_GET['idFormulario'], FILTER_SANITIZE_NUMBER_INT);

    $f = $fo->traerFormulario($idFormulario);


    $htmlSelectEmailAdminFrom = "<option selected='selected'>{$f['email']['FromName']} ({$f['email']['FromEmail']}) </option>" . $htmlSelectEmailAdminFrom;
    $htmlSelectEmailAdminReply = "<option selected='selected'>{$f['email']['ReplyToName']} ({$f['email']['ReplyToEmail']}) </option>" . $htmlSelectEmailAdminReply;

    $AdministratorID = $f['AdministratorID'];
} else {
    $f = array();
    $f['form']['Name'] = __('Nuevo Formulario de Suscripción', 'envialo-simple');
    $f['form']['LabelEmailAddress'] = __('Direccion de email:', 'envialo-simple');
    $f['form']['LabelSubmit'] = __('Suscribir', 'envialo-simple');
    $f['form']['BackgroundColor'] = '#E9E9E9';
    $f['form']['Width'] = "200";
    $f['form']['Font'] = 'Arial';
    $f['form']['FontSize'] = '12';
    $f['form']['FontColor'] = '#555555';
    $f['form']['ShowPoweredBy'] = TRUE;
    $AdministratorID = filter_var($_GET['idA'], FILTER_SANITIZE_NUMBER_INT);
}
$urlImg = plugins_url("envialosimple-email-marketing-y-newsletters-gratis/imagenes/");
?>

<link rel="stylesheet"  href="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/css/miniColors.css"); ?>" type="text/css" media="all" />

<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/miniColors.js"); ?>"></script>
<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/jquery.dd.min.js"); ?>"></script>
<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/scriptFormSuscripcion.js"); ?>"></script>

<script type="text/javascript">
    var AdministratorID = "<?php echo $AdministratorID ?>";
</script>

<div class="wrap">
    <h3><?php _e('Formulario de Suscripción a Newsletter.', 'envialo-simple') ?></h3>

    <div class="mensaje" style="width: 60%"></div>

    <form id="form-editar-form" action="#" method="post">

        <input name="Content" type="hidden" value='<?php if (isset($f['email']['Content'])) echo stripslashes($f['email']['Content']) ?>' />

        <input name="FormID" value="<?php if (isset($f['form']['FormID'])) echo $f['form']['FormID'] ?>" type="hidden"/>
        <input name="EmailID" value="<?php if (isset($f['email']['EmailID'])) echo $f['email']['EmailID'] ?>" type="hidden"/>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2" style="margin-right: 300px;" >

                <div id="post-body-content" style="width: 100%;float: left;">
                    <table class="form-table">
                        <tbody>
                            <tr valign="top">
                                <th scope="row"><label for="nombre-form"><?php _e('Nombre Formulario:', 'envialo-simple') ?></label></th>
                                <td>
                                    <input type="text" value="<?php echo $f['form']['Name'] ?>" name="Name" id="nombre-form" style="width: 250px"/>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="MailListsIds"><?php _e('Lista de Destinatarios', 'envialo-simple') ?></label></th>
                                <td>
                                    <select name="MailListsIds[]" id="MailListsIds" style="width: 250px" multiple="multiple" class="validar">

                                        <?php
                                        $co = new Contactos();
                                        $listas = $co->listarListasContactos(-1);

                                        if (count($f['form']["MailListsIds"]["item"]) == 0) {
                                            foreach ($listas[0]["item"] as $l) {
                                                echo "<option value='{$l['MailListID']}'>{$l['Name']} ({$l['MemberCount']} " . __('Destinatarios', 'envialo-simple') . ")</option>";
                                            }
                                        } else {
                                            foreach ($listas[0]['item'] as $l) {
                                                $selected = "";
                                                foreach ($f['form']['MailListsIds']['item'] as $listaMail) {
                                                    if ($listaMail == $l['MailListID']) {
                                                        $selected = "selected='selected'";
                                                    }
                                                }
                                                echo "<option {$selected} value='{$l['MailListID']}' >{$l['Name']} ({$l['MemberCount']} " . __('Destinatarios', 'envialo-simple') . ")</option>";
                                            }
                                        }
                                        ?>

                                    </select>
                                    <div style="display: inline-block;position: relative;top: -9px;left: 10px;" >
                                        <a href="#" id="crear-lista"><?php _e('Crear Nueva', 'envialo-simple') ?></a>
                                    </div>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="FromID"><?php _e('Remitente Email Confirmación:', 'envialo-simple') ?></label></th>
                                <td>
                                    <select name="FromID" id="FromID" style="width: 250px">
                                        <?php
                                        echo $htmlSelectEmailAdminFrom;
                                        ?>
                                        <option class="selectCrear" value="agregar"><?php _e('+ Agregar Nuevo Email ..', 'envialo-simple') ?></option>
                                    </select>   <br />
                                    <div id="responder-contenedor" style="margin: 5px 0 0 5px;">
                                        <input type="checkbox" name="responder-check" id="responder-check" style="margin-right: 5px;"  />
                                        <label for="responder-check" ><?php _e('Utilizar una direcci&oacute;n de respuesta distinta', 'envialo-simple') ?></label>
                                    </div>
                                </td>
                            </tr>
                            <tr valign="top" id="responder-fila" style="display:none">
                                <th scope="row"><label for="ReplyToID"><?php _e('Responder a :', 'envialo-simple') ?></label></th>
                                <td>
                                    <select name="ReplyToID" id="ReplyToID">
                                        <?php
                                        echo $htmlSelectEmailAdminReply;
                                        ?>
                                        <option class="selectCrear" value="agregar"><?php _e('+ Agregar Nuevo Email ..', 'envialo-simple') ?></option>
                                    </select></td>
                            </tr>

                    </table>
                    <a href="#" id="mostrar-op-av"><?php _e('Opciones Avanzadas', 'envialo-simple') ?></a>
                    <div id="opciones-avanzadas-suscripcion">
                        <div class="form-avanzado">
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row"><label for="titulo-form"><?php _e('Título:', 'envialo-simple') ?></label></th>
                                    <td>
                                        <input type="text" value="<?php if (isset($f['form']['Title'])) echo $f['form']['Title'] ?>" name="Title" id="titulo-form" style="width: 250px"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="textoBt"><?php _e('Etiqueta Campo Email:', 'envialo-simple') ?></label></th>
                                    <td>
                                        <input type="text" name="LabelEmailAddress" value="<?php echo $f['form']['LabelEmailAddress'] ?>" style="width: 250px" />
                                    </td>

                                </tr>

                                <tr>
                                    <th scope="row"><label for="textoBt"><?php _e('Texto del Botón:', 'envialo-simple') ?></label></th>
                                    <td>
                                        <input type="text" name="LabelSubmit" value="<?php echo $f['form']['LabelSubmit'] ?>" id="textoBt" style="width: 250px"/>
                                    </td>
                                </tr>


                            </table>
                        </div>

                        <div class="form-avanzado">
                            <table id="tabla-campos-form" class="form-table">
                                <thead>
                                    <tr>
                                        <td style="width: 218px;"><?php _e('Campos Personalizados:', 'envialo-simple') ?></td>
                                        <td style="font-weight: bold;width: 250px" ><?php _e('Nombre del Campo', 'envialo-simple') ?></td>
                                        <td style="font-weight: bold;width: 250px"><?php _e('Eliminar', 'envialo-simple') ?></td>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    if (isset($f['form']['CustomfieldsIds']['item'])) {
                                        echo $ev->mostrarCamposPersonalizados($f['form']['CustomfieldsIds']['item']);
                                    } else {
                                        echo $ev->mostrarCamposPersonalizados();
                                    }
                                    ?>
                                    <tr class="addCampoTr"></tr>
                                    <tr>
                                        <td>
                                            <a href="#" id="agregarCampo"><?php _e('Añadir Campo', 'envialo-simple') ?></a>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="form-avanzado">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="BackgroundColor"><?php _e('Color de Fondo:', 'envialo-simple') ?></label></th>
                                    <td>
                                        <input type="text" name="BackgroundColor" value="<?php echo $f['form']['BackgroundColor'] ?>" style="width: 60px" />
                                    </td>

                                </tr>

                                <tr>
                                    <th scope="row"><label for="Width"><?php _e('Ancho (px)', 'envialo-simple') ?></label></th>
                                    <td>
                                        <input type="text" name="Width" value="<?php echo $f['form']['Width'] ?>"  style="width: 60px;float:left"/>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row"><label for="Font"><?php _e('Letra / Fuente', 'envialo-simple') ?></label></th>
                                    <td>
                                        <input type="text" name="Font" value="<?php echo stripslashes($f['form']['Font']) ?>"  style="width: 180px;float:left"/>


                                        <div class="fontContainer" data-relinput="Font">
                                            <div class="arrowDown" data-tooltype="fontPicker"></div>
                                            <ul class="fontList" style="display: none;">
                                                <li style="font-family:Arial, Helvetica, sans-serif;" data-value="Arial, Helvetica, sans-serif">Arial, Helvetica, sans-serif</li>
                                                <li style="font-family:Verdana, Geneva, sans-serif;" data-value="Verdana, Geneva, sans-serif">Verdana, Geneva, sans-serif</li>
                                                <li style="font-family:Georgia, 'Times New Roman', Times, serif;" data-value="Georgia, Times New Roman, Times, serif">Georgia, "Times New Roman", Times, serif</li>
                                                <li style="font-family:Courier New, Courier, monospace;" data-value="Courier New, Courier, monospace">"Courier New", Courier, monospace</li>
                                                <li style="font-family:Tahoma, Geneva, sans-serif;" data-value="Tahoma, Geneva, sans-serif" class="selected">Tahoma, Geneva, sans-serif</li>
                                                <li style="font-family:Trebuchet MS, Arial, Helvetica, sans-serif;" data-value="Trebuchet MS, Arial, Helvetica, sans-serif">"Trebuchet MS", Arial, Helvetica, sans-serif</li>
                                                <li style="font-family:Arial Black, Gadget, sans-serif;" data-value="Arial Black, Gadget, sans-serif" class="">"Arial Black", Gadget, sans-serif</li>
                                                <li style="font-family:Times New Roman, Times, serif;" data-value="Times New Roman, Times, serif">"Times New Roman", Times, serif</li>
                                                <li style="font-family:Palatino Linotype, Book Antiqua, Palatino, serif;" data-value="Palatino Linotype, Book Antiqua, Palatino, serif">"Palatino Linotype", "Book Antiqua", Palatino, serif</li>
                                                <li style="font-family:Lucida Sans Unicode, Lucida Grande, sans-serif;" data-value="Lucida Sans Unicode, Lucida Grande, sans-serif">"Lucida Sans Unicode", "Lucida Grande", sans-serif</li>
                                                <li style="font-family:MS Serif, New York, serif;" data-value="MS Serif, New York, serif">"MS Serif", "New York", serif</li>
                                                <li style="font-family:Lucida Console, Monaco, monospace;" data-value="Lucida Console, Monaco, monospace">"Lucida Console", Monaco, monospace</li>
                                                <li style="font-family:Comic Sans MS, cursive;" data-value="Comic Sans MS, cursive">"Comic Sans MS", cursive</li>
                                            </ul>
                                        </div>



                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row"><label for="FontSize"><?php _e('Letra / Tamaño (px)', 'envialo-simple') ?></label></th>
                                    <td>
                                        <input type="text" name="FontSize" value="<?php echo $f['form']['FontSize'] ?>"  style="width: 60px" class="fl"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="FontColor"><?php _e('Letra / Color', 'envialo-simple') ?></label></th>
                                    <td>
                                        <input type="text" name="FontColor" value="<?php echo $f['form']['FontColor'] ?>"  style="width: 60px"/>
                                    </td>
                                </tr>

                            </table>
                        </div>
                        <div class="form-avanzado">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="ShowPoweredBy"><?php _e('Logo EnvialoSimple', 'envialo-simple') ?></label></th>
                                    <td>
                                        <input type="checkbox" name="ShowPoweredBy" <?php if (isset($f['form']['ShowPoweredBy'])) echo ($f['form']['ShowPoweredBy']) ? 'checked="checked"' : ""; ?> value="1" style="width: 40px" />
                                    </td>

                                </tr>
                                <tr>
                                    <th scope="row"><label for="SubscribeDobleOptIN"><?php _e('Doble Opt-In', 'envialo-simple') ?></label></th>
                                    <td>
                                        <input type="checkbox" name="SubscribeDobleOptIN" <?php if (isset($f['form']['SubscribeDobleOptIN'])) echo ($f['form']['SubscribeDobleOptIN']) ? 'checked="checked"' : ""; ?> value="1" style="width: 40px" /><?php _e('¿Requiere confirmación de suscripción?', 'envialo-simple') ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>  <!--/opciones avanzadas -->

                </div><!-- post body content-->

                <div id="postbox-container-1" class="postbox-container" style="float: right;margin-right: -300px;width: 280px;">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">
                        <div id="acciones" class="postbox ">
                            <h3 class='hndle'><span><?php _e('Acciones    ', 'envialo-simple') ?></span></h3>

                            <div style='padding: 10px;'>
                                <input style=";" type="submit" id="guardar-cambios-form" value="<?php _e('Guardar Cambios', 'envialo-simple') ?>" class="button-primary"/>

                                <input type="submit" class="obtener-codigo button-secondary" name="obtener-codigo" value="<?php _e('Obtener Código', 'envialo-simple') ?>" />
                            </div>

                        </div>

                        <div id="preview" class="postbox ">
                            <h3 class='hndle'>
                                <?php _e('Vista Previa', 'envialo-simple') ?>
                                <div id="actualizar-preview" ><?php _e('Actualizar', 'envialo-simple') ?>
                                    <img style="height: 15px;" src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/imagenes/refresh.png") ?>" alt="refresh">
                                </div>
                            </h3>
                            <div id="contenedor-vista-previa"></div>
                        </div>

                    </div> <!-- sortable-->
                </div><!--post box cointaner1-->

            </div><!--post body-->
        </div><!--post stuff-->
    </form>

</div>


<div style="display:none">
    <div id="modal-agregar-email">
        <form id="form-agregar-email" action="#" method="post">
            <div id="label-error-mail-admin" class="mensaje"></div>
            <label for="nombreEmailAdmin"><?php _e('Nombre', 'envialo-simple') ?></label><br />
            <input type="text"  name="nombreEmailAdmin" id="nombreEmailAdmin"/><br />

            <label for="emailAdmin"><?php _e('Email:', 'envialo-simple') ?></label><br />
            <input type="text"  name="emailAdmin" id="emailAdmin"/><br />
            <input type="submit" value="<?php _e('Agregar', 'envialo-simple') ?>" class="button-primary" style="margin-top: 20px;margin-bottom: 10px;"/>
            <input type="reset" value="<?php _e('Cancelar', 'envialo-simple') ?>" class="button-secondary" onclick='jQuery("#modal-agregar-email").dialog("close")'/>

        </form>


    </div>


    <div id="modal-obtener-codigo" style="top:50%">
        <p><?php _e('Copia y pega éste código en el lugar de tu blog donde desees visualizar el Formulario de Suscripción.', 'envialo-simple') ?></p>

        <textarea style="width: 555px;height: 70px;">
            <script type='text/javascript' src='http://v2.envialosimple.com/form/show/AdministratorID//FormID   /format/widget'></script>
        </textarea>


        <p><?php _e('Para utilizar el Formulario de Suscripción como <b>Widget</b>, realiza los siguientes pasos:', 'envialo-simple') ?></p>

        <ol>

            <li><?php _e('Ingresa al Menú <b>Apariencia</b>.', 'envialo-simple') ?></li>
            <li><?php _e('Selecciona el ítem <b>Widgets</b>.', 'envialo-simple') ?></li>
            <li><?php _e('Arrastra y suelta un nuevo Bloque <b>Formulario Suscripción EnvialoSimple</b> en la Barra de Widgets de tu preferencia.', 'envialo-simple') ?></li>
            <li><?php _e('Selecciona el Formulario que desees utilizar.', 'envialo-simple') ?></li>
            <li><?php _e('Guarda los cambios.', 'envialo-simple') ?></li>
            <li><?php _e('El Formulario aparecerá como Widget en la Barra que seleccionaste y estará listo para usar.', 'envialo-simple') ?></li>
        </ol>

        <p><?php _e('Si deseas utilizarlo en una <b>Página</b> o <b>Posteo</b>, desde el Editor de Contenido, selecciona la pestaña HTML y pega el codigo del formulario donde lo desees.', 'envialo-simple') ?></p>


        <a style="float: right;margin: 20px;" href="#" onclick="jQuery('#modal-obtener-codigo').dialog('close')" class="button-secondary"><?php _e('Cerrar', 'envialo-simple') ?></a>

    </div>

    <div id="modalAgregarCampo">

        <form id="form-agregar-campo" action="#" method="post">


            <input name="tipoCampo" type="hidden" value=""/>
            <div id="mensaje-agregar-campo" class="mensaje"></div>


            <label for="titulo-campo"><?php _e('Nombre Campo:', 'envialo-simple') ?></label><br />
            <input type="text"  name="Title-Campo" id="titulo-campo"/><br />

            <label for="tipo-campo" style="margin-top:15px;display:block;"><?php _e("Tipo de Campo Personalizado:", 'envialo-simple') ?></label>

            <select id="tipo-campo">
                <option value="select"><?php _e("Seleccionar..", 'envialo-simple') ?></option>
                <option data-imagesrc='<?php echo $urlImg ?>formP8.png' value="Text field"><?php _e("Campo de texto", 'envialo-simple') ?></option>
                <option data-imagesrc='<?php echo $urlImg ?>formP1.png' value="Password field"><?php _e("Contraseña", 'envialo-simple') ?></option>
                <option data-imagesrc='<?php echo $urlImg ?>formP9.png' value="Hidden field"><?php _e("Campo oculto", 'envialo-simple') ?></option>
                <option data-imagesrc='<?php echo $urlImg ?>formP7.png' value="Text area"><?php _e("Area de texto", 'envialo-simple') ?></option>
                <option data-imagesrc='<?php echo $urlImg ?>formPe.png' value="Drop list"><?php _e("Listado", 'envialo-simple') ?></option>
                <option data-imagesrc='<?php echo $urlImg ?>formPe.png' value="Drop list"><?php _e("Listado de paises", 'envialo-simple') ?></option>
                <option data-imagesrc='<?php echo $urlImg ?>formP5.png' value="List"><?php _e("Listado con selección múltiple", 'envialo-simple') ?></option>
                <option data-imagesrc='<?php echo $urlImg ?>formPa.png' value="Check box"><?php _e("Checkbox", 'envialo-simple') ?></option>
                <option data-imagesrc='<?php echo $urlImg ?>formP2.png' value="Radio button"><?php _e("Radio", 'envialo-simple') ?></option>
                <option data-imagesrc='<?php echo $urlImg ?>formP6.png' value="Notice"><?php _e("Nota al pié", 'envialo-simple') ?></option>


            </select>

            <div id="valorPorDefectoCampo" class="formPerOcultar">
                <label for="valor-campo"><?php _e('Valor por Defecto:', 'envialo-simple') ?></label><br />
                <input type="text"  name="DefaultValue-Campo" id="valor-campo"/><br />
            </div>

            <div id="validacionCampo" class="formPerOcultar">
                <label for="validacion-campo"><?php _e('Validación:', 'envialo-simple') ?></label><br />
                <select id="validacion-campo">
                    <option value="Do not Apply" selected=""><?php _e("Desactivado", 'envialo-simple') ?></option>
                    <option value="Numeric Only"><?php _e("Aceptar solo dígitos (sin espacios)", 'envialo-simple') ?></option>
                    <option value="Alpha Only"><?php _e("Aceptar solo letras y espacios", 'envialo-simple') ?></option>
                    <option value="Alpha Numeric Only"><?php _e("Aceptar solo letras, números y espacios", 'envialo-simple') ?></option>
                    <option value="Email Format Check"><?php _e("Aceptar solo direcciones de Email", 'envialo-simple') ?></option>
                    <option value="Custom"><?php _e("Personalizado", 'envialo-simple') ?></option>
                </select><br />

            </div>


            <div id="listadoValores" class="formPerOcultar">
                <span><?php _e("Listado:", 'envialo-simple') ?></span>

                <ul>

                </ul>
                <div id="agregarValor" class="button-secondary"><?php _e("Agregar Valor", 'envialo-simple') ?></div>
            </div>

            <div id="contenedorCamposValores" class="formPerOcultar">
                <label id="label-text-valor"><?php _e("Texto:", 'envialo-simple') ?></label>
                <input name="input-opcion-texto" class='input-opcion' type="text" style="width:100px"/>

                <label style="margin-left: 20px"><?php _e("Valor:", 'envialo-simple') ?></label>
                <input name="input-opcion-valor" class='input-opcion' type="text" style="width:90px" /><br />


                <div class="button-secondary" id="cancelar-agregar-valor-bt" style ="float: right;margin: 10px 37px 10px 0;"> Cancelar</div>
                <div class="button" id="agregar-valor-bt" style="float: right;margin: 10px 5px 10px 0;" ><?php _e("Agregar Opción", 'envialo-simple') ?></div>
                <br />
            </div>
            <div style="clear:both"></div>
            <input type="submit" value="<?php _e('Agregar', 'envialo-simple') ?>" class="button-primary" style="margin-top: 20px;margin-bottom: 10px;"/>
            <input type="reset" value="<?php _e('Cancelar', 'envialo-simple') ?>" class="button-secondary" onclick='jQuery("#modalAgregarCampo").dialog("close");
                    jQuery("#listadoValores ul").html("");
                    jQuery("#tabla-campos-form select").val(0);'/>


        </form>

    </div>


    <div id="modal-crear-lista">

        <form action="#" id="form-crear-lista" method="post">
            <p><?php _e('Escribí el Nombre de tu Nueva Lista de Contactos.', 'envialo-simple') ?></p>
            <label for="nombre-lista"><?php _e('Nombre', 'envialo-simple') ?></label>
            <input type="hidden" name="accion" value="crear-lista"/>
            <input type="text" id="nombre-lista" name="nombre-lista"  style="margin-bottom: 20px" value=""/><br />

            <div style="width:180px;margin: 0 auto 0 auto;">
                <input type="submit" value="<?php _e('Crear Lista', 'envialo-simple') ?>" class="button-primary"/>
                <input type="reset" value="<?php _e('Cancelar', 'envialo-simple') ?>" class="button-secondary" id="cerrar-modal"/>
            </div>
        </form>
    </div>

</div><!-- display none-->



<script type="text/javascript">
    var enterPress = 'false';
    var selectAgregar;
    var alertarPageLeave = false;

    jQuery(document).ready(function () {

        //agregar lista

        jQuery("#form-crear-lista").submit(function (event) {

            event.preventDefault();

            if (checkVacio(jQuery("input[name=nombre-lista]"))) {
                return false;
            } else {

                var nombre = jQuery("input[name=nombre-lista]").val();

                jQuery.post(urlHandler, {accion: "crearLista", nombreLista: nombre}, function (json) {
                    if (json.root.ajaxResponse.success) {

                        var lista = json.root.ajaxResponse.maillist;
                        jQuery("#MailListsIds").append("<option value='" + lista.MailListID + "'>" + lista.MailListName + "</option>")
                        jQuery("#MailListsIds").val(lista.MailListID);

                        jQuery("#MailListsIds").trigger("liszt:updated");
                        alert("<?php _e('Lista Agregada Correctamente!', 'envialo-simple') ?>")
                        jQuery("#modal-crear-lista").dialog("close");
                    } else {
                        alert("<?php _e('Error al Crear la Lista.', 'envialo-simple') ?>");
                    }
                }, "json");
            }
        });

        jQuery("#modal-crear-lista").dialog({
            autoOpen: false,
            height: 180,
            width: 320,
            dialogClass: 'fixed-dialog',
            modal: true,
            title: "<?php _e('Crear Lista', 'envialo-simple') ?>"

        });
        jQuery("#cerrar-modal").click(function () {
            jQuery("#modal-crear-lista").dialog("close");

        });

        jQuery("#crear-lista").click(function () {
            jQuery("#modal-crear-lista").dialog("open");
        })

        //</agregar lista


        mostrarBotonObtenerCodigo()

        jQuery(window).bind('beforeunload', function () {
            if (alertarPageLeave) {
                return '<?php _e('Al abandonar ésta página, se perderán todos los cambios no guardados.', 'envialo-simple') ?>';
            } else {
                return;
            }
        });

        jQuery(window).bind('keypress', function (event) {
            alertarPageLeave = true;
        });

        jQuery("#form-agregar-campo").submit(function (event) {

            event.preventDefault();

            if (enterPress == 'true') {
                enterPress = 'false';
                return false;
            }


            jQuery("#titulo-campo").css("border", "1px solid #ddd");

            if (jQuery("#titulo-campo").val() == "") {
                jQuery("#titulo-campo").css("border", "1px solid red");
                jQuery("#mensaje-agregar-campo").removeClass("msjExito").removeClass("msjError").addClass("msjError").html("<?php _e('Por favor Revise todos los campos', 'envialo-simple') ?>").show();
                return false;
            }

            var tipoCampo = jQuery("input[name=tipoCampo]").val();
            switch (tipoCampo) {

                case "Text field":
                case "Password field":
                case "Text area":
                case "Hidden field":
                case "Notice":

                    var Title = jQuery("#titulo-campo").val();
                    var FieldType = tipoCampo;
                    var Validation = jQuery("#validacion-campo").val();
                    var DefaultValue = jQuery("#valor-campo").val();
                    var ItemsIsMultipleSelect = 0;


                    jQuery.post(urlHandler, {accion: "agregarCampoPersonalizado",
                        Title: Title,
                        FieldType: tipoCampo,
                        Validation: Validation,
                        ItemsIsMultipleSelect: ItemsIsMultipleSelect,
                        DefaultValue: DefaultValue
                    },
                    function (json) {

                        if (json.CustomFieldID != null) {
                            jQuery(selectAgregar).find("option:last").before("<option value='" + json.CustomFieldID + "'>" + json.Title + " </option>");
                            jQuery(selectAgregar).val(jQuery(selectAgregar).find("option:last").prev().val())
                            jQuery(selectAgregar).change();
                            alert("<?php _e('Campo Agregado Correctamente!', 'envialo-simple') ?>");
                            jQuery("#modalAgregarCampo").dialog("close");
                        } else {
                            alert("<?php _e('Error al Agregar Campo!', 'envialo-simple') ?>");
                        }

                    }, "json")


                    break;

                default:
                    //listadito

                    var Title = jQuery("#titulo-campo").val();
                    var Validation = jQuery("#validacion-campo").val();
                    var ItemsNames = [];
                    var ItemsValues = [];
                    var ItemsIsMultipleSelect = 0;

                    if (tipoCampo == 'Check box' || tipoCampo == 'List') {
                        ItemsIsMultipleSelect = 1;
                    }


                    jQuery("#listadoValores li").each(function () {
                        ItemsNames.push(jQuery(this).find("span").first().html());
                        ItemsValues.push(jQuery(this).attr("name"));
                    })


                    jQuery.post(urlHandler, {accion: "agregarCampoPersonalizado",
                        Title: Title,
                        FieldType: tipoCampo,
                        ItemsNames: ItemsNames,
                        Validation: Validation,
                        ItemsValues: ItemsValues,
                        ItemsIsMultipleSelect: ItemsIsMultipleSelect
                    },
                    function (json) {

                        if (json.CustomFieldID != null) {

                            jQuery(selectAgregar).find("option:last").before("<option value='" + json.CustomFieldID + "'>" + json.Title + " </option>");

                            jQuery(selectAgregar).val(jQuery(selectAgregar).find("option:last").prev().val())

                            jQuery(selectAgregar).change();

                            alert("<?php _e('Campo Agregado Correctamente!', 'envialo-simple') ?>");
                            jQuery("#modalAgregarCampo").dialog("close");
                        } else {
                            alert("<?php _e('Error al Agregar Campo!', 'envialo-simple') ?>");
                        }

                    }, "json")

                    break;
            }
        });

        jQuery(".obtener-codigo").click(function (event) {

            event.preventDefault();
            jQuery("#modal-obtener-codigo").dialog("open");
            jQuery("#modal-obtener-codigo").dialog("option", "position", 100);

            return false;
        });


        var keyPress = false;

        jQuery("input[name=Title],input[name=LabelEmailAddress],input[name=LabelSubmit],input[name=Width],input[name=FontSize]").keypress(function () {

            keyPress = true;

        });


        jQuery("input[name=Title],input[name=LabelEmailAddress],input[name=LabelSubmit],input[name=Width],input[name=FontSize]").focusout(function () {

            if (keyPress) {
                generarVistaPrevia()
                keyPress = false;
            }
        });



        jQuery("#modal-obtener-codigo").dialog({
            autoOpen: false,
            height: "auto",
            width: "auto",
            modal: true,
            position: "top",
            title: "<?php _e('Obtener Código', 'envialo-simple') ?>"
        })


        jQuery("#modalAgregarCampo").dialog({
            autoOpen: false,
            height: "auto",
            resize: "auto",
            width: "auto",
            modal: true,
            title: "<?php _e('Agregar Campo Personalizado', 'envialo-simple') ?>",
            open: function (event, ui) {
                jQuery("#listadoValores ul").html("");
                jQuery("#mensaje-agregar-campo").hide()
                jQuery("#titulo-campo").val("");


            },
            close: function (event, ui) {
                jQuery("#tipo-campo").ddslick('select', {index: 1});

            }
        });


        jQuery("input[name=ShowPoweredBy]").change(function (event) {
            if (rolUsuario == "free") {
                alert("<?php _e('Esta opción es solamente para cuentas Premium. Actualiza tu cuenta para poder utilizarla.', 'envialo-simple') ?>")
                event.preventDefault();
                jQuery(this).attr("checked", true)
                return false;
            }
            generarVistaPrevia();
        });

        generarVistaPrevia()


        //color pickers
        jQuery("input[name=FontColor],input[name=BackgroundColor]").miniColors({
            close: function (hex, rgba) {
                generarVistaPrevia();
            }
        });


        jQuery(".upDownContainer .arrowUp").click(function () {
            valorInput = parseInt(jQuery(this).parent().parent().find("input").val());
            valorInput += 1;
            jQuery(this).parent().parent().find("input").val(valorInput);

        });

        jQuery(".upDownContainer .arrowDown").click(function () {
            valorInput = parseInt(jQuery(this).parent().parent().find("input").val());
            valorInput -= 1;
            jQuery(this).parent().parent().find("input").val(valorInput);
        });



        //fuente
        jQuery("input[name=Font], .fontContainer").click(function () {
            jQuery(".fontList").toggle(150)
            jQuery("input[name=Font]").focus()
        });

        jQuery(".fontList li").click(function () {

            jQuery("input[name=Font]").val(jQuery(this).html());
            generarVistaPrevia();

        });

        jQuery("#actualizar-preview").click(function () {
            generarVistaPrevia();
        });


        jQuery("#guardar-cambios-form").click(function (event) {



            jQuery(".chzn-choices").css("border", "1px solid #DDD");
            jQuery("#nombre-form").css("border", "1px solid #DDD");

            if (jQuery("#MailListsIds").val() == null) {

                alert("<?php _e('Por Favor revise todos los campos.', 'envialo-simple') ?>")
                jQuery(".chzn-choices").css("border", "1px solid red")

                return false;
            }
            if (jQuery("#nombre-form").val() == "") {

                alert("<?php _e('Por Favor revise todos los campos.', 'envialo-simple') ?>")
                jQuery("#nombre-form").css("border", "1px solid red")

                return false;
            }

            var selectOK = true;

            jQuery("select[name=FromID],select[name=ReplyToID]").each(function () {

                jQuery(this).css("border", "1px solid #ddd");

                if (jQuery(this).val() == "agregar") {
                    alert("<?php _e('Por Favor revise todos los campos.', 'envialo-simple') ?>")
                    jQuery(this).css("border", "1px solid red");
                    selectOK = false;
                    return false;
                }

            });

            if (!selectOK) {
                return false;
            }

            event.preventDefault();
            var Name = jQuery("input[name=Name]").val();
            var Title = jQuery("input[name=Title]").val();
            var LabelSubmit = jQuery("input[name=LabelSubmit]").val();
            var LabelEmailAddress = jQuery("input[name=LabelEmailAddress]").val();
            var Width = jQuery("input[name=Width]").val();
            var ShowPoweredBy = jQuery("input[name=ShowPoweredBy]:checked").length;
            var SubscribeDobleOptIN = jQuery("input[name=SubscribeDobleOptIN]:checked").length;
            var Font = jQuery("input[name=Font]").val();
            var FontSize = jQuery("input[name=FontSize]").val();
            var FontColor = jQuery("input[name=FontColor]").val();
            var BackgroundColor = jQuery("input[name=BackgroundColor]").val();
            var MailListsIds = jQuery("select[name^=MailListsIds]").val();
            var FormID = jQuery("input[name=FormID]").val();
            var EmailID = jQuery("input[name=EmailID]").val();

            var textFrom = jQuery("select[name=FromID] :selected").text()
            var textReply = jQuery("select[name=ReplyToID] :selected").text()

            var FromName = textFrom.substring(0, textFrom.lastIndexOf("("));
            var FromEmail = textFrom.substring(textFrom.lastIndexOf("(") + 1, textFrom.lastIndexOf(")"));

            var ReplyToName = textReply.substring(0, textReply.lastIndexOf("("));
            var ReplyToEmail = textReply.substring(textReply.lastIndexOf("(") + 1, textReply.lastIndexOf(")"));
            var CustomFieldsIds = [];

            var Content = jQuery("input[name=Content]").val();

            jQuery("select[name^=CustomFieldsIds]").each(function () {

                if (jQuery(this).val() != 0 && jQuery(this).val() != -1) {
                    CustomFieldsIds.push(jQuery(this).val());
                }


            });

            jQuery.post(urlHandler, {accion: "guardarForm",
                Name: Name,
                Title: Title,
                LabelSubmit: LabelSubmit,
                LabelEmailAddress: LabelEmailAddress,
                BackgroundColor: BackgroundColor,
                Width: Width,
                Font: Font,
                FontSize: FontSize,
                FontColor: FontColor,
                ShowPoweredBy: ShowPoweredBy,
                SubscribeDobleOptIN: SubscribeDobleOptIN,
                MailListsIds: MailListsIds,
                FormID: FormID,
                ConfirmSubscriptionEmailID: "",
                SubsCallbackOK: "",
                SubsCallbackFail: "",
                ConfCallbackOK: "",
                ConfCallbackFail: "",
                EmailID: EmailID,
                FromName: FromName,
                FromEmail: FromEmail,
                ReplyToName: ReplyToName,
                ReplyToEmail: ReplyToEmail,
                CustomFieldsIds: CustomFieldsIds,
                Content: Content
            }
            , function (json) {


                if (json.success) {

                    jQuery("input[name=FormID]").val(json.form.FormID);
                    jQuery("input[name=EmailID]").val(json.email.EmailID);

                    alertarPageLeave = false;
                    jQuery(".mensaje").hide(100).removeClass("msjError").removeClass("msjExito").addClass("msjExito").html("<?php _e('Formulario Guardado Correctamente!', 'envialo-simple') ?>").show(100);

                    setTimeout(function () {
                        jQuery(".mensaje").hide(300)
                    }, 7000);
                    generarVistaPrevia()
                    mostrarBotonObtenerCodigo()
                } else {
                    jQuery(".mensaje").hide(100).removeClass("msjError").removeClass("msjExito").addClass("msjError").html("<?php _e('Error al Guardar el Formulario. Revise los Campos e Intente Nuevamente', 'envialo-simple') ?>").show(100);
                }

            }, "json");




        });

        jQuery("#mostrar-op-av").click(function (event) {

            event.preventDefault();
            jQuery("#opciones-avanzadas-suscripcion").toggle(50);

        });

        jQuery("#agregarCampo").click(function (event) {

            alertarPageLeave = true;

            event.preventDefault();
            var camposExistentes = [];
            var agregarCampo = true;
            var controlDeseleccionado
            jQuery("#tabla-campos-form select").each(function () {

                jQuery(this).css("border", "1px solid #DFDFDF");

                if (jQuery(this).val() == 0 && jQuery(this).val() == -1) {
                    agregarCampo = false;
                    controlDeseleccionado = jQuery(this);
                } else {
                    camposExistentes.push(jQuery(this).val());
                }

            });

            if (!agregarCampo) {
                controlDeseleccionado.css("border", "1px solid #4BD329");
                return false;
            }

            jQuery.post(urlHandler, {accion: "mostrarCamposPersonalizados", camposExistentes: camposExistentes}, function (html) {

                jQuery(".addCampoTr").last().before(html);
            }, "html");

        });

        jQuery("#tabla-campos-form").on("click", ".campo-nuevo.eliminar", function () {
            alertarPageLeave = true;

            var valor = jQuery(this).parent().parent().find("select").val();
            if (valor == 0 && valor == -1) {
                jQuery(this).parent().parent().remove();
                return false;
            }
            jQuery(this).parent().parent().remove();
            generarVistaPrevia()
        });

        jQuery("#tabla-campos-form").on("change", "select", function () {

            alertarPageLeave = true;

            jQuery(this).css("border", "1px solid #DFDFDF");

            jQuery(this).parent().parent().find(".campo-nuevo").show();
            if (jQuery(this).val() != 0 && jQuery(this).val() != -1) {
                generarVistaPrevia();
            }

            valor = parseInt(jQuery(this).val())

            if (valor == -1) {
                if (rolUsuario == 'free') {
                    alert("<?php _e('Esta opción es solamente para cuentas Premium. Actualiza tu cuenta para poder utilizarla.', 'envialo-simple') ?>")
                    return false;
                } else {
                    jQuery("#modalAgregarCampo").dialog("open");
                    selectAgregar = jQuery(this);
                }
            }
        });

    });

    function mostrarBotonObtenerCodigo() {

        var FormID = jQuery("input[name=FormID]").val();

        if (FormID != "") {
            jQuery("input[name=obtener-codigo]").show();

            var script = "<script type='text/javascript' src='http://v2.envialosimple.com/form/show/AdministratorID/" + AdministratorID + "/FormID/" + FormID + "/format/widget'><\/script>";
            jQuery("#modal-obtener-codigo textarea").text(script);
        } else {
            jQuery("input[name=obtener-codigo]").hide();
        }


    }


    function generarVistaPrevia() {
        var Name = jQuery("input[name=Name]").val();
        var Title = jQuery("input[name=Title]").val();
        var LabelSubmit = jQuery("input[name=LabelSubmit]").val();
        var LabelEmailAddress = jQuery("input[name=LabelEmailAddress]").val();
        var Width = jQuery("input[name=Width]").val();
        var ShowPoweredBy = jQuery("input[name=ShowPoweredBy]:checked").length;
        var SubscribeDobleOptIN = jQuery("input[name=SubscribeDobleOptIN]:checked").length;
        var Font = jQuery("input[name=Font]").val();
        var FontSize = jQuery("input[name=FontSize]").val();
        var FontColor = jQuery("input[name=FontColor]").val();
        var BackgroundColor = jQuery("input[name=BackgroundColor]").val();
        var MailListsIds = jQuery("select[name^=MailListsIds]").val();
        var FormID = jQuery("input[name=FormID]").val();
        var EmailID = jQuery("input[name=EmailID]").val();

        var textFrom = jQuery("select[name=FromID] :selected").text()
        var textReply = jQuery("select[name=ReplyToID] :selected").text()

        var FromName = textFrom.substring(0, textFrom.lastIndexOf("("));
        var FromEmail = textFrom.substring(textFrom.lastIndexOf("(") + 1, textFrom.lastIndexOf(")"));

        var ReplyToName = textReply.substring(0, textReply.lastIndexOf("("));
        var ReplyToEmail = textReply.substring(textReply.lastIndexOf("(") + 1, textReply.lastIndexOf(")"));
        var CustomFieldsIds = [];

        jQuery("select[name^=CustomFieldsIds]").each(function () {

            if (jQuery(this).val() != 0 && jQuery(this).val() != -1) {
                CustomFieldsIds.push(jQuery(this).val());
            }


        });


        jQuery.post(urlHandler, {accion: "vistaPreviaForm",
            Name: Name,
            Title: Title,
            LabelSubmit: LabelSubmit,
            LabelEmailAddress: LabelEmailAddress,
            BackgroundColor: BackgroundColor,
            Width: Width,
            Font: Font,
            FontSize: FontSize,
            FontColor: FontColor,
            ShowPoweredBy: ShowPoweredBy,
            SubscribeDobleOptIN: SubscribeDobleOptIN,
            MailListsIds: MailListsIds,
            FormID: FormID,
            ConfirmSubscriptionEmailID: "",
            SubsCallbackOK: "",
            SubsCallbackFail: "",
            ConfCallbackOK: "",
            ConfCallbackFail: "",
            EmailID: EmailID,
            FromName: FromName,
            FromEmail: FromEmail,
            ReplyToName: ReplyToName,
            ReplyToEmail: ReplyToEmail,
            CustomFieldsIds: CustomFieldsIds
        }
        , function (html) {


            jQuery("#contenedor-vista-previa").html(html);

        }, "html");





    }



    function checkVacio(obj) {

        if (obj.val() == "") {
            obj.css("border", "1px solid red");
            return true;
        } else {
            obj.css("border", "1px solid #DFDFDF");
            return false;
        }
    }



</script>
