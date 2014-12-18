<?php
include_once(ENVIALO_DIR . "/clases/EnvialoSimple.php");
include_once(ENVIALO_DIR . "/clases/Contactos.php");
$ev = new EnvialoSimple();
$ev->checkSetup();
$co = new Contactos();
$adminUrl = get_admin_url();
if (isset($_GET["pagina"])) {
    $pagina = filter_var($_GET["pagina"], FILTER_SANITIZE_NUMBER_INT);
} else {
    $pagina = 1;
}

$listas = $co->listarListasContactos($pagina);
$verContactos = (isset($_GET['verContactos']) && $_GET['verContactos'] == 1 ) ? TRUE : FALSE;
?>

<?php include_once(ENVIALO_DIR . "/paginas/header.php"); ?>

<?php if (isset($_GET['MailListsIds']) && !$verContactos): ?>

    <?php include_once("importar-contactos.php"); ?>

<?php elseif ($verContactos): ?>

    <?php include_once("ver-contactos.php"); ?>

<?php else: ?>

    <div class="wrap">
        <div id="listas-contactos">
            <div id="icon-users" class="icon32 ">
                <br/>
            </div>
            <h2><?php _e('Listas y Contactos', 'envialo-simple'); ?>
                <a href="#" class="add-new-h2 abrir-modal-lista"><?php _e('Nueva Lista', 'envialo-simple'); ?></a>
            </h2>

            <div class="tool-box" id="contenedor-1">
                <p>
                    <?php _e('Para Administrar tus Listas de Contactos en detalle, por favor accede a', 'envialo-simple'); ?>
                    <a href="http://v2.envialosimple.com/maillist/list" target="_blank">Envialo Simple</a>
                </p>
                <?php if (isset($_GET["listaCreada"]) && $_GET["listaCreada"]): ?>
                    <div class='mensaje msjExito' style='display:block'>
                        <?php _e('Lista Creada con Éxito!', 'envialo-simple'); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET["contactoCreado"]) && $_GET["contactoCreado"]): ?>
                    <div class='mensaje msjExito' style='display:block'>
                        <?php _e('Contacto Agregado con Éxito!', 'envialo-simple'); ?>
                    </div>
                <?php endif; ?>

                <?php if (!$listas["success"]): ?>
                    <br/>
                    <div class="mensaje msjError" style="display:inline">
                        <?php _e('No se pudo recuperar las Listas de Contacto. Por Favor Intente Nuevamente', 'envialo-simple'); ?>
                    </div>
                    <p>
                        <?php _e('En caso de persistir el error, reconfigure el Plugin', 'envialo-simple'); ?>
                    </p>
                <?php else: ?>
                    <?php if (empty($listas[0]["item"])): ?>
                        <div class="wp-caption"><?php _e('Aún No tenés Listas Creadas.', 'envialo-simple'); ?>
                            <a href="" class="button-secondary action"><?php _e('Crear Nueva Lista', 'envialo-simple'); ?></a></div>
                    <?php else: ?>
                        <div>
                            <table class='wp-list-table widefat fixed posts'>
                                <thead>
                                    <tr>
                                        <th scope='col' id='cb' class='manage-column column-cb check-column' style=''></th>
                                        <th class='manage-column column-title sortable desc' style='height:41px;width: 250px;'><?php _e('Nombre', 'envialo-simple'); ?></th>
                                        <th class='manage-column column-title sortable desc' style='width: 220px;'><?php _e('Contactos (Total/Activos)', 'envialo-simple'); ?></th>
                                        <th class='manage-column column-title sortable desc' style='text-align: center;width: 565px;'><?php _e('Acciones', 'envialo-simple'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($listas[0]['item'] as $item) {
                                        $clase = "impar";
                                        if ($i % 2 == 0) {
                                            $clase = "par";
                                        }
                                        $i++;
                                        ?>
                                        <tr class='<?php echo $clase; ?>'>
                                            <td>&nbsp;</td>
                                            <td><span class='row-title'><?php echo $item["Name"]; ?></span></td>
                                            <td><?php echo $item["MemberCount"]; ?> / <?php echo $item["ActiveMemberCount"]; ?></td>
                                            <td>
                                                <a href='#' name='<?php echo $item["MailListID"]; ?>' class='boton-sincronizar button-secondary' title='<?php _e('Agregar los Contactos de Wordpress a la Lista de Envialo Simple', 'envialo-simple'); ?>'><?php _e('Agregar Usuarios de Wordpress', 'envialo-simple'); ?></a>
                                                <a href='#' name='<?php echo $item["MailListID"]; ?>' class='boton-agregar-contacto button-secondary' title='<?php _e('Agregar Contacto a la Lista de Envialo Simple', 'envialo-simple'); ?>'><?php _e('Agregar Contacto', 'envialo-simple'); ?></a>
                                                <a href='<?php echo "{$adminUrl}admin.php?page=envialo-simple-listas&MailListsIds={$item["MailListID"]}"; ?>' name='<?php echo $item["MailListID"]; ?>' class='button-secondary boton-importar-contacto' title='<?php _e('Importar Contactos', 'envialo-simple'); ?>'><?php _e('Importar Contactos', 'envialo-simple'); ?></a>
                                                <a href='<?php echo "{$adminUrl}admin.php?page=envialo-simple-listas&verContactos=1&MailListsIds={$item["MailListID"]}&MailListName={$item["Name"]}"; ?>' name='<?php echo $item["MailListID"]; ?>' class='button-secondary ' title='<?php _e('Ver Contactos', 'envialo-simple'); ?>'><?php _e('Ver Contactos', 'envialo-simple'); ?></a>
                                            </td>
                                        </tr>
                                    <?php }; ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td colspan='2'>
                                            <div id='paginacion'>
                                                <?php if ($listas[0]['pager']['absolutepage'] > 1): ?>
                                                    <a class='pag' href='<?php echo "{$adminUrl}admin.php?page=envialo-simple-listas&pagina=" . ($listas[0]['pager']['absolutepage'] - 1); ?>'> &lt; </a>
                                                <?php endif; ?>
                                                <?php for ($i = 1; $i <= $listas[0]['pager']['pagecount']; $i++): ?>
                                                    <a class='pag <?php echo $listas[0]['pager']['absolutepage'] == $i ? 'pagActiva' : ''; ?>' href='<?php echo "{$adminUrl}admin.php?page=envialo-simple-listas&pagina=" . $i; ?>'> <?php echo $i; ?> </a>
                                                <?php endfor; ?>
                                                <?php if ($listas[0]['pager']['absolutepage'] < $listas[0]['pager']['pagecount']): ?>
                                                    <a class='pag' href='<?php echo "{$adminUrl}admin.php?page=envialo-simple-listas&pagina=" . ($listas[0]['pager']['absolutepage'] + 1); ?>'> &gt; </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div style="display:none">
        <div id="modal-crear-lista">
            <form action="#" id="form-crear-lista" method="post">
                <p><?php _e('Escribí el Nombre de tu Nueva Lista de Contactos.', 'envialo-simple'); ?></p>
                <label for="nombre-lista"><?php _e('Nombre', 'envialo-simple'); ?></label>
                <input type="hidden" name="accion" value="crear-lista"/>
                <input type="text" id="nombre-lista" name="nombre-lista" style="margin-bottom: 20px" value=""/> <br/>

                <div style="width:180px;margin: 0 auto 0 auto;">
                    <input type="submit" value="<?php _e('Crear Lista', 'envialo-simple'); ?>" class="button-primary"/>
                    <input type="reset" value="<?php _e('Cancelar', 'envialo-simple'); ?>" class="button-secondary" id="cerrar-modal"/>
                </div>
            </form>
        </div>
        <div id="modal-agregar-contacto">
            <div class="mensaje" id="msj-agregar-contacto"></div>
            <form id="form-agregar-contacto" method="post" action="#">
                <input type="hidden" name="MailListID" value=""/>

                <p><?php _e('Completa los datos para Agregar un Contacto a tu Lista', 'envialo-simple'); ?></p>
                <label><?php _e('Email', 'envialo-simple'); ?> :<br/> <input type="text" value="" name="Email"/> </label> <br/>
                <?php
                echo $ev->mostrarCamposPersonalizadosForm();
                ?>
                <input type="submit" class="button-primary" value="<?php _e('Agregar Contacto', 'envialo-simple'); ?>" style="margin-top: 20px;margin-bottom: 20px;"/>
                <input type="reset" class="button-secondary" value="<?php _e('Cancelar', 'envialo-simple'); ?>" style="margin-top: 20px;margin-bottom: 20px;" onclick="jQuery('#modal-agregar-contacto').dialog('close')"/>
            </form>
        </div>
    </div>
    <script type="text/javascript">

        jQuery(document).ready(function () {

            jQuery(".boton-sincronizar").click(function (event) {
                event.preventDefault();
                if (confirm("<?php _e('Está Seguro?', 'envialo-simple'); ?>")) {
                    var idLista = jQuery(this).attr("name");
                    jQuery.post("<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/handler.php') ?>", {accion: "sincronizarContactos", MailListID: idLista}, function (json) {
                        if (json.success) {
                            alert("<?php _e('Se Sincronizaron Correctamente tus Contactos de Wordpress con la Lista seleccionada.', 'envialo-simple'); ?>");
                            window.location = "<?php echo "{$adminUrl}admin.php?page=envialo-simple-listas"; ?>";
                        } else {
                            alert("Error");
                        }
                    }, "json");
                }
            });
            jQuery(".boton-agregar-contacto").click(function (event) {
                event.preventDefault();
                jQuery("input[name=MailListID]").val(jQuery(this).attr("name"));
                jQuery("#modal-agregar-contacto").dialog("open");
            });
            jQuery("#form-crear-lista").submit(function (event) {
                event.preventDefault();
                if (checkVacio(jQuery("input[name=nombre-lista]"))) {
                    return false;
                } else {
                    var nombre = jQuery("input[name=nombre-lista]").val();
                    jQuery.post(urlHandler, {accion: "crearLista", nombreLista: nombre}, function (json) {
                        if (json.root.ajaxResponse.success) {
                            jQuery("#modal-crear-lista").dialog("close");
                            window.location = "<?php echo "{$adminUrl}admin.php?page=envialo-simple-listas&listaCreada=1"; ?>";
                        }
                    }, "json");
                }
            });
            jQuery("#form-agregar-contacto").submit(function (event) {
                jQuery("#msj-agregar-contacto").hide(150);
                event.preventDefault();
                var campos = [];
                var check = true;
                jQuery("input[name=Email]").css("border-color", "#DFDFDF");
                if (!validarEmail(jQuery("input[name=Email]").val())) {
                    jQuery("input[name=Email]").css("border-color", "red");
                    check = false;
                    jQuery("#msj-agregar-contacto").show(150).removeClass("msjError").removeClass("msjExito").addClass("msjError").html("<?php _e('Por favor Ingresa un Email Válido.', 'envialo-simple') ?>");
                    return false;
                }
                jQuery("#form-agregar-contacto input[type=text]").not("input[name=Email]").each(function () {
                    campos.push(jQuery(this).val());
                });
                if (check) {
                    jQuery.post(urlHandler, {accion: "agregarContactoLista",
                        campos: campos,
                        Email: jQuery("input[name=Email]").val(),
                        MailListID: jQuery("input[name=MailListID]").val()
                    }, function (json) {
                        if (json.root.ajaxResponse.success) {
                            jQuery("#modal-agregar-contacto").dialog("close");
                            window.location = "<?php echo "{$adminUrl}admin.php?page=envialo-simple-listas&contactoCreado=1"; ?>";
                        } else {
                            alert("<?php _e('Error al Agregar Contacto. Por favor revise los campos e intente nuevamente', 'envialo-simple'); ?>");
                        }
                    }, "json");
                }
            });


            jQuery("#dialog:ui-dialog").dialog("destroy");
            jQuery("#modal-crear-lista").dialog({
                autoOpen: false,
                height: 260,
                width: 320,
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "<?php _e('Crear Lista', 'envialo-simple'); ?>"
            });
            jQuery("#modal-agregar-contacto").dialog({
                autoOpen: false,
                height: "auto",
                width: "auto",
                modal: true,
                title: "<?php _e('Agregar Contacto a la Lista', 'envialo-simple'); ?>"
            });
            jQuery(".abrir-modal-lista").click(function () {
                jQuery("#modal-crear-lista").dialog("open");
            });
            jQuery("#cerrar-modal").click(function () {
                jQuery("#modal-crear-lista").dialog("close");
            });

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

        function validarEmail(email) {
            return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(email);
        }


    </script>

<?php endif; ?>