<?php
include_once (ENVIALO_DIR . "/paginas/header.php");

$version = get_bloginfo( 'version' );
?>



<div class="wrap">



    <?php  if ( version_compare( $version, '3.3', '<' ) ) { ?>
        <div class="mensaje" style="display: block">

            <?php printf(__('El plugin de EnvialoSimple requiere la versión 3.3 de Wordpress para funcionar Correctamente. Tú tienes la Versión %s . Por favor Actualiza Wordpress a la Última Versión.','envialo-simple'),$version); ?>
        </div>
    <?php } ?>


    <div id="icon-tools" class="icon32">
        <br>
    </div><h2><?php _e('Configuración Inicial','envialo-simple') ?></h2>

    <p>
        <?php _e('Para poder configurar el Plugin, por favor seleccioná la opción correspondiente.','envialo-simple') ?>
    </p>

    <div id="tabs">


         <div id="tengo-nada" class="conf-inicial">
            <div class="tool-box" id="contenedor-3">
                <h3 class="title"><?php _e('No Tengo Cuenta de Envialo Simple','envialo-simple') ?></h3>
                <p style="font-size: 12px;">
                    <?php _e('<b>Te regalamos 1.000 envíos mensuales</b> para que envíes tus Newsletters.','envialo-simple') ?>
                </p>
                <a href="#" target="_blank" id="boton-cuenta-nueva" class="boton-cuenta"><?php _e('Crear una Cuenta Gratis','envialo-simple') ?></a>

            </div>
        </div>

       <div id="tengo-cuenta" class="conf-inicial">

            <div class="tool-box" id="contenedor-2">
                <h3 class="title"><?php _e('Tengo Cuenta de Envialo Simple','envialo-simple') ?></h3>
                <p>
                   <?php _e(' Si dispones de una Cuenta de Envialo Simple, utiliza el siguiente formulario para terminar de configurar el Plugin','envialo-simple') ?>
                </p>
                <div id="msj-respuesta-token" class="mensaje">

                </div>
                <div id="msj-respuesta" class="mensaje">

                </div>
                <div id="contenedor-exito2" style="display:none">
                    <p>
                        <?php _e('Ya puedes comenzar a utilizar el Plugin !','envialo-simple') ?>
                    </p>
                    <p>
                        <a href="<?php echo get_admin_url().'/admin.php?page=envialo-simple'?>" class="button-primary" ><?php _e('Comenzar','envialo-simple') ?></a>
                    </p>

                </div>

                <form action="#" method="post" name="form-conf" id="form-conf" class="add:users: validate">
                    <input type="hidden" name="accion" value="traerToken"/>
                    <input type="hidden"  name="admin_url" value="<?php echo get_admin_url() ?>"/>
                    <table class="form-table">
                        <tbody>
                            <tr class="form-field form-required">
                                <th scope="row"><label for="username"><?php _e('Usuario','envialo-simple') ?> <span class="description"><?php _e('(requerido)','envialo-simple') ?></span></label></th>
                                <td>
                                <input name="username" type="text" id="usuario" value="<?php
                                    if (isset($_GET["u"])) {echo $_GET["u"];
                                    }
                                ?>" aria-required="true">
                                </td>
                            </tr>
                            <tr class="form-field form-required">
                                <th scope="row"><label for="password"><?php _e('Contraseña','envialo-simple') ?><span class="description"><?php _e('(requerido)','envialo-simple') ?></span></label></th>
                                <td>
                                <input name="password" type="password" id="password" value="">
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <p class="submit">
                        <input type="submit" name="createuser" id="createusersub" class="button-primary" value="<?php _e('Aceptar y Configurar','envialo-simple') ?>">
                    </p>
                </form>

            </div>
        </div>
       <div id="api-key" class="conf-inicial">

            <div class="tool-box" id="contenedor-3">
                <h3 class="title"><?php _e('Usuario avanzado','envialo-simple') ?></h3>
                <p>
                   <?php _e('Si deseas puedes configurar el plugin utilizando una clave de API. Ingresa tu clave de API para utilizar el plugin','envialo-simple') ?>
                </p>
                <div id="msj-respuesta-api" class="mensaje">

                </div>
                <div id="msj-respuesta" class="mensaje">

                </div>
                <div id="contenedor-exito3" style="display:none">
                    <p>
                        <?php _e('Ya puedes comenzar a utilizar el Plugin !','envialo-simple') ?>
                    </p>
                    <p>
                        <a href="<?php echo get_admin_url().'admin.php?page=envialo-simple'?>" class="button-primary" ><?php _e('Comenzar','envialo-simple') ?></a>
                    </p>

                </div>

                <form action="#" method="post" id="form-token" class="add:users: validate">

                    <input type="hidden"  name="admin_url" value="<?php echo get_admin_url() ?>"/>
                    <table class="form-table">
                        <tbody>
                            <tr class="form-field form-required">
                                <th scope="row"><label for="APIKey"><?php _e('Clave API','envialo-simple') ?> <span class="description"><?php _e('(requerido)','envialo-simple') ?></span></label></th>
                                <td>
                                <input name="APIKey" type="text" id="APIKey" value="" aria-required="true">
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <p class="submit">
                        <input type="submit" name="createuser" id="traer-api" class="button-primary" value="<?php _e('Aceptar y Configurar','envialo-simple') ?>">
                    </p>
                </form>

            </div>
        </div>



    </div>
    <!--tabs-->
</div>
<!--wrap-->

<script type="text/javascript">
    var usuario;
    var pass;
    var token;

    jQuery("input[type=text]").click(function () {

        if (jQuery(this).css("border") == "1px solid rgb(255, 0, 0)") {
            jQuery(this).css("border", "1px solid #DFDFDF");
        }

    });

    jQuery("#form-token").submit(function (event) {

        if (checkVacio(jQuery("input[name=APIKey]"))) {

            event.preventDefault();

        } else {

            event.preventDefault();
            var clave = jQuery.trim(jQuery("input[name=APIKey]").val());

            jQuery.post(urlHandler, {
                accion: "testToken",
                APIKey: clave
            }, function (json) {

                if (json.success) {

                    //todo ok , redirijo al index
                    //window.location = "<?php echo get_admin_url() ."admin.php?page=envialo-simple"; ?>";

                    jQuery("#msj-respuesta-api").removeClass("msjError").addClass("msjExito").show(300).html(json.mensaje);

                    jQuery("#contenedor-2").hide(300);
                    jQuery("#form-token , #tengo-cuenta , #tengo-nada").hide(300);
                    jQuery("#contenedor-exito3").show(300);
                } else {
                    jQuery("#msj-respuesta-api").addClass("msjError").show(300).html(json.mensaje);
                    jQuery("input[name=APIKey]").css("border", "1px solid red");
                }

            }, "json");
        }
    });

    jQuery("#form-conf").submit(function (event) {

        event.preventDefault();
        jQuery("#msj-respuesta").hide();
        jQuery("#generar-clave").hide();

        if (checkVacio(jQuery("input[name=username]")) || checkVacio(jQuery("input[name=password]"))) {
            event.preventDefault();
            return false;
        }


        usuario = jQuery("input[name=username]").val();
        pass = jQuery("input[name=password]").val();

        jQuery.post("<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/handler.php')?>", {
            accion: "traerToken",
            username: usuario,
            password: pass
        }, function (json) {

            if (json.success) {

                jQuery("#msj-respuesta").show(300).html(json.mensaje);
                jQuery("#msj-respuesta").removeClass("msjError").addClass("msjExito");

                jQuery("form[name=form-conf]").hide(300);
                jQuery("#contenedor-1").hide(300);
                jQuery("#contenedor-3 , #api-key , #tengo-nada").hide(300);
                jQuery("#contenedor-exito2").show(300);

            } else {

                if (json.logueado) {

                    if (!json.tieneClaves) {

                        jQuery("#msj-respuesta").show(300).html(json.mensaje);
                        jQuery("#msj-respuesta").addClass("msjError");

                    } else {
                        jQuery("#msj-respuesta").show(300).html(json.mensaje);
                        jQuery("#msj-respuesta").removeClass("msjError").addClass("msjExito");

                        jQuery("#contenedor-1").hide(300);
                        jQuery("#contenedor-3").hide(300);
                        jQuery("contedor-exito2").show(300);
                    }

                } else {

                    jQuery("#msj-respuesta").show(300).html(json.mensaje);
                    jQuery("#msj-respuesta").addClass("msjError");
                }
            }

        }, "json");

    });

    function checkVacio(obj) {

        if (obj.val() == "") {
            obj.css("border", "1px solid red");
            return true;
        } else {
            obj.css("border", "1px solid #DFDFDF");
            return false;
        }
    }

    Object.size = function (obj) {
        var size = 0,
            key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };

</script>
