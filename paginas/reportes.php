<?php
include_once(ENVIALO_DIR."/clases/EnvialoSimple.php");
$ev = new EnvialoSimple();
$ev->checkSetup();

if(!isset($_GET["idCampana"])){
    _e("Problema en la Configuración del Plugin", 'envialo-simple');
    return;
}
$idCampana = filter_var($_GET["idCampana"], FILTER_SANITIZE_NUMBER_INT);
$parametros = array();
include_once(ENVIALO_DIR."/clases/Campanas.php");
$ca = new Campanas();
$repo = $ca->traerReportes($idCampana);
$campana = $ca->traerCampana($idCampana);
$r = $repo["root"]["ajaxResponse"]["report"];

?>

<?php include_once(ENVIALO_DIR."/paginas/header.php"); ?>

<div class="wrap">
    <div class="icon32" id="icon-options-general"><br></div>
    <h2><?php _e('Reportes de la Campaña', 'envialo-simple'); ?></h2>
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row"><label><?php _e('Fecha de Envío', 'envialo-simple'); ?> :</label></th>
                <td><label><?php echo $r["detailsOfSend"]["SendStartDateTime"];?></label></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label><?php _e('Total de Subscriptores', 'envialo-simple'); ?> :</label></th>
                <td><label><?php echo $r["detailsOfSend"]["TotalRecipients"];?></label></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label><?php _e('Email entregados', 'envialo-simple'); ?> :</label></th>
                <td><label><?php echo $r["detailsOfSend"]["TotalDelivered"];?></label></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label><?php _e('Aperturas Totales', 'envialo-simple'); ?> :</label></th>
                <td><label><?php echo $r["detailsOfSend"]["TotalOpened"];?></label></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label><?php _e('Aperturas Únicas', 'envialo-simple'); ?> :</label></th>
                <td><label><?php echo $r["detailsOfSend"]["UniqueOpened"];?></label></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label><?php _e('Clicks Totales', 'envialo-simple'); ?> :</label></th>
                <td><label><?php echo $r["detailsOfSend"]["TotalClicks"];?></label></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label><?php _e('Rebotes Totales', 'envialo-simple'); ?> :</label></th>
                <td><label><?php echo $r["detailsOfSend"]["TotalBounces"];?></label></td>
            </tr>
        </tbody>
    </table>
    <br/>

    <h3><?php _e('Reportes Avanzados', 'envialo-simple'); ?></h3>
    <?php if($campana["userinfo"]["role"] == "free"): ?>
        <p><?php _e('Para visualizarlos, tienes que loguearte en la aplicación de Envialo Simple.', 'envialo-simple'); ?></p>
        <p><?php _e('Los usuarios de cuentas Premium, pueden visualizarlos con un solo click.', 'envialo-simple'); ?></p>
        <p><a class="button-primary" target="_blank" href="http://v2.envialosimple.com/report/track/CampaignID/<?php echo $idCampana ?>"><?php _e('Ir a Reportes Avanzados', 'envialo-simple'); ?></a></p>
    <?php else: ?>
        <p><a class="button-primary" target="_blank" href="<?php echo $campana["campaign"]["publicURL"] ?>"><?php _e('Ver Reportes Avanzados', 'envialo-simple'); ?></a></p>
    <?php endif; ?>
</div>