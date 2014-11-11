<?php
include_once(ENVIALO_DIR . "/clases/EnvialoSimple.php");
include_once(ENVIALO_DIR . "/clases/Contactos.php");
$adminUrl = get_admin_url();
if (isset($_GET["APIKey"])) {
    $ev = new EnvialoSimple();
    $GLOBALS["APIKey"] = $_GET["APIKey"];
    $respuesta = $ev->testToken();
    if ($respuesta["success"]) {
        $url = "{$adminUrl}admin.php?page=envialo-simple&configurado=1";
        echo "<script>window.location = '{$url}' </script>";
        exit();
    } else {
        echo "<div id='msj-respuesta' class='mensaje msjExito' style='width: 55%; display:inline-block'>{$respuesta["mensaje"]}</div>";
    }
}

if (!isset($_GET["setup"])) {
    $ev = new EnvialoSimple();
    $ev->checkSetup();
    $token = json_decode($ev->traerTokenBD(), TRUE);
}

$co = new Contactos();
$listaContactos = $co->mostrarListasContactos(1);

$keyActiva = $listaContactos[0] == TRUE ? __('Activada', 'envialo-simple') : __('Desactivada', 'envialo-simple');
?>

<?php include_once(ENVIALO_DIR . "/paginas/header.php"); ?>

<?php if (isset($_GET["setup"])): ?>
    <?php include_once(ENVIALO_DIR . "/paginas/configuracion-inicial.php"); ?>
<?php else: ?>

    <div class="wrap">
        <div id="icon-tools" class="icon32">
            <br/>
        </div>
        <h2><?php _e('Configuración', 'envialo-simple'); ?></h2>

        <div class="tool-box" id="contenedor-1">
            <h3 class="title"><?php _e('Llaves de acceso API HTTP', 'envialo-simple'); ?></h3>

            <p><?php _e('El Plugin está utilizando la siguiente clave para comunicarse con Envialo Simple.', 'envialo-simple'); ?></p>
            <table id="" class="wp-list-table widefat fixed posts">
                <thead>
                    <tr>
                        <th class="manage-column column-title sortable desc" style="width: 600px;padding-left: 10px;height: 30px;"><?php _e('Clave', 'envialo-simple'); ?></th>
                        <th class="manage-column column-title sortable desc"><?php _e('Estado', 'envialo-simple'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="fila-clave">
                        <td><?php echo $token["Key"] ?>
                            <div class="row-actions">
                                <span class="inline hide-if-no-js">
                                    <span class="trash" id="eliminar-clave-bt" name="<?php echo $token["idClave"]; ?>">
                                        <a class="submitdelete" style="visibility: visible;" title="<?php _e('Eliminar esta Clave', 'envialo-simple'); ?>" href="#"><?php _e('Eliminar', 'envialo-simple'); ?></a>
                                    </span>
                                </span>
                            </div>
                        </td>
                        <td><?php echo $keyActiva; ?></td>
                    </tr>
                </tbody>
            </table>
            <div id="reconfigurar" class="dn">
                <p><?php _e('Ha eliminado la clave actual. Para proseguir deberá configurar nuevamente el Plugin', 'envialo-simple'); ?></p>

                <p>
                    <a class="button-secondary" href="<?php echo "{$adminUrl}admin.php?page=envialo-simple-configuracion"; ?>"><?php _e('Configuración', 'envialo-simple'); ?></a>
                </p>
            </div>
        </div>
        <br/>
    </div>
    <script type="text/javascript">
        jQuery("#eliminar-clave-bt").click(function () {
            if (confirm("<?php _e('Al Eliminar la Clave, tendrá que reconfigurar el Plugin. Está Seguro?', 'envialo-simple'); ?>")) {
                var idClave = jQuery(this).attr("name");
                jQuery.post(urlHandler, {accion: "eliminarToken", idClave: idClave}, function (json) {
                    if (json.success) {
                        jQuery("#fila-clave").hide(300, function () {
                            jQuery("#reconfigurar").show(300);
                        });
                        jQuery("#listas-contactos").hide()
                    } else {
                        alert("Error al Eliminar");
                    }
                }, "json");
            }
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

        jQuery(function () {
            jQuery("#dialog:ui-dialog").dialog("destroy");
            jQuery("#modal-crear-lista").dialog({
                autoOpen: false,
                height: 180,
                width: 320,
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Crear Lista"
            });
            jQuery(".abrir-modal-lista")
                    .click(function () {
                        jQuery("#modal-crear-lista").dialog("open");
                    });
            jQuery("#cerrar-modal").click(function () {
                jQuery("#modal-crear-lista").dialog("close");
            });
        });
    </script>

<?php endif; ?>