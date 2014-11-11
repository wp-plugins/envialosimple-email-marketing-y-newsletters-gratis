<?php
include_once (ENVIALO_DIR . "/clases/EnvialoSimple.php");
$ev = new EnvialoSimple();
$ev->checkSetup();
if (!isset($GLOBALS['APIKey'])) {
    //plugin configurado incorrectamente
    _e("Problema en la Configuración del Plugin", 'envialo-simple');
    return;
}

$campoBusqueda = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : __('Buscar Newsletters..', 'envialo-simple');
$checkInputFiltro = isset($_GET['filter']) && $_GET['filter'] == "Newsletter del" ? "checked='checked'" : "";
?>

<?php include_once(ENVIALO_DIR . "/paginas/header.php"); ?>

<script type="text/javascript">

    jQuery(document).ready(function () {
        if (jQuery(".icono-estado.enviando").length > 0) {
            setInterval(function () {
                refrescarNewsletters()
            }, 15000);
        }
    });

    function refrescarNewsletters() {
        var pagina = 1;
        var filtro = "";
        if (getUrlVars()["pagina"] != undefined) {
            pagina = getUrlVars()["pagina"]
        }
        if (getUrlVars()["filter"] != undefined) {
            filtro = getUrlVars()["filter"]
        }
        mostrarOver = false;
        jQuery.post(urlHandler, {accion: "refrescarNews", pagina: pagina, filtro: filtro}, function (html) {
            jQuery("#contenedor-newsletters").html(html);
        }, "html");
    }

    function getUrlVars() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
            vars[key] = value;
        });
        return vars;
    }

</script>
<?php if (isset($_GET["configurado"])): ?>
    <div id='msj-respuesta' class='mensaje msjExito' style='width: 55%; display:inline-block'>
        <?php _e('El Plugin ha sido Configurado Correctamente! Ya podés comenzar a Enviar tus Newsletters.', 'envialo-simple'); ?>
    </div>
<?php endif; ?>

<div class="wrap">
    <div id="icon-edit-comments" class="icon32">
        <br/>
    </div>
    <h2 style="float:left">
        <?php _e('Newsletters', 'envialo-simple'); ?>
        <a href="<?php echo get_admin_url() . "admin.php?page=envialo-simple-nuevo" ?>" class="add-new-h2 abrir-modal-campana"><?php _e('Crear Nuevo', 'envialo-simple'); ?></a>
    </h2>

    <div class="busqueda-campanas">

        <form name="busquedaCampana" action="#" method="get">
            <input type="text" name="filter" id="buscarCampana" value="<?php echo $campoBusqueda; ?>">
            <input type="hidden" name="page" value="envialo-simple"/> <input type="submit" style="visibility: hidden;"/>
        </form>
        <div id="filter-news">
            <span><?php _e('Mostrar únicamente Newsletters creados desde el blog?', 'envialo-simple') ?></span>
            <input type="checkbox" value="filtrarNews" id="input-filter-news" <?php echo $checkInputFiltro; ?>/>
        </div>
    </div>
    <div style="clear:both"></div>
    <div class="tool-box" id="contenedor-newsletters">
        <?php if (isset($_GET["camp-enviada"])): ?>
            <div id='msj-respuesta' class='mensaje msjExito' style='width: 55%; display:inline-block'>
                <?php _e('Newsletter Enviado Correctamente !!', 'envialo-simple'); ?>
            </div>
        <?php endif; ?>
        <?php include_once(ENVIALO_DIR . "/paginas/tablaCampanas.php"); ?>
    </div>
</div>