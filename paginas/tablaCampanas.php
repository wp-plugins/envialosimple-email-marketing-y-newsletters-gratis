<?php
$adminUrl = get_admin_url();
include_once (ENVIALO_DIR . "/clases/Campanas.php");
$Campanas = new Campanas();
//TODO: FIXME llevar todo a donde se usa que esto solo devuelva los datos
$filter = isset($_REQUEST['filter']) ? filter_var($_REQUEST['filter'], FILTER_SANITIZE_STRING) : null;
$absolutepage = isset($_REQUEST['pagina']) ? filter_var($_REQUEST['pagina'], FILTER_SANITIZE_NUMBER_INT) : 1;

$c = $Campanas->listarCampanas($absolutepage, $filter);
?>

<div>
    <?php if (!$c['success']): ?>
        <span>
            <?php _e('Error de Conexion con el Servidor', 'envialo-simple'); ?>
        </span>
    <?php else: ?>
        <?php if (empty($c['list']['item'])): ?>
            <?php if (!empty($filter)): ?>
                <div class='wp-caption'>
                    <p>
                        <?php _e('No se han Encontrado Resultados. Intente Buscando Nuevamente.', 'envialo-simple'); ?>
                    </p>
                    <p>
                        <a style="" href="<?php echo $adminUrl; ?>admin.php?page=envialo-simple" class="button-primary" >
                            <?php _e('Volver', 'envialo-simple'); ?>
                        </a>
                    </p>
                </div>
            <?php else: ?>
                <div class='wp-caption'>
                    <p>
                        <?php _e('Aún No tienes Newsletters Creados.', 'envialo-simple'); ?></p>
                    <p>
                        <a style = "width:200px" href="<?php echo $adminUrl; ?>admin.php?page=envialo-simple-nuevo" id='abrir-modal-campana' class='button-primary abrir-modal-campana' >
                            <?php _e('Crear Nuevo Newsletter', 'envialo-simple'); ?>
                        </a>
                    </p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div>
                <table class='wp-list-table widefat fixed posts'>
                    <thead>
                        <tr>
                            <th scope='col' id='cb' class='manage-column column-cb check-column' style='width: 40px;'>
                                <span style='display: block;height: 30px;margin-left: 8px'>ID</span>
                            </th>
                            <th class='manage-column column-title sortable desc' style='width:200px;'>
                                <?php _e('Nombre', 'envialo-simple'); ?>
                            </th>
                            <th class='manage-column column-title sortable desc' style='width:125px;'>
                                <?php _e('Asunto', 'envialo-simple'); ?>
                            </th>
                            <th class='manage-column column-title sortable desc' style='width:70px;'>
                                <?php _e('Estado', 'envialo-simple'); ?>
                            </th>
                            <th class='manage-column column-title sortable desc' style='width:120px;'>
                                <?php _e('Destinatarios', 'envialo-simple'); ?>
                            </th>
                            <th class='manage-column column-title sortable desc' style='width:100px;'>
                                <?php _e('Reportes', 'envialo-simple'); ?>
                            </th>
                            <th class='manage-column column-title sortable desc' style='width:200px;'>
                                <?php _e('Fecha de Envío', 'envialo-simple'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($c['list']['item'] as $item) {
                            $clase = "impar";
                            if ($i % 2 == 0) {
                                $clase = "par";
                            }
                            $i++;
                            $fecha = '';
                            if ($item['Status'] == 'Draft') {
                                $pagina = 'campana-editar';
                            } elseif ($item['Status'] == 'Paused') {
                                $pagina = 'campana-editar';
                            } elseif ($item['Status'] == 'Scheduled') {
                                $pagina = 'campana-editar';
                            } elseif ($item['Status'] == 'Stopped') {
                                $pagina = 'campana-editar';
                            } elseif ($item['Status'] == 'Sending') {
                                $pagina = 'campana-completa';
                            } elseif ($item['Status'] == "Completed") {
                                $fecha = "&e={$item['SendStartDateTimeFormated']}";
                                $pagina = 'campana-completa';
                            }
                            ?>
                            <tr class="<?php echo $clase ?>">
                                <td>
                                    <?php echo $item['CampaignID']; ?>
                                </td>
                                <td>
                                    <a id='<?php echo $item['CampaignID']; ?>' name='<?php echo $item['Status']; ?>' href='<?php echo "{$adminUrl}admin.php?page=envialo-simple&accion={$pagina}&idCampana={$item['CampaignID']}{$fecha}"; ?>' class='row-title checkEstadoCampana'>
                                        <?php echo $item['CampaignName']; ?>
                                    </a>
                                    <div class='row-actions'>
                                        <?php
                                        switch ($item['Status']):
                                            case 'Draft':
                                                ?>
                                                <span class='edit'>
                                                    <a href='<?php echo "{$adminUrl}admin.php?page=envialo-simple&accion=campana-editar&idCampana={$item['CampaignID']}" ?>' title='Editá tu Campaña'>
                                                        <?php _e('Editar', 'envialo-simple'); ?>
                                                    </a> |
                                                </span>
                                                <?php break; ?>
                                            <?php case 'Paused': ?>
                                                <span class='view'>
                                                    <a href='' class='previsualizar-news' name='<?php echo $item['CampaignID'] ?>' title='<?php _e('Previsualizar Campaña en el Navegador o por Correo Electrónico', 'envialo-simple'); ?>' >
                                                        <?php _e('Previsualizar', 'envialo-simple'); ?>
                                                    </a> |
                                                </span>
                                                <span class='view'>
                                                    <a href='' class='reanudar-campana-bt' name='<?php echo $item['CampaignID'] ?>' title='<?php _e('Reanudar', 'envialo-simple'); ?>' >
                                                        <?php _e('Reanudar', 'envialo-simple'); ?>
                                                    </a> |
                                                </span>
                                                <?php break; ?>
                                            <?php case 'Scheduled': ?>
                                                <span class='view'>
                                                    <a href='' class='previsualizar-news' name='<?php echo $item['CampaignID'] ?>' title='<?php _e('Previsualizar Campaña en el Navegador o por Correo Electrónico', 'envialo-simple'); ?>' >
                                                        <?php _e('Previsualizar', 'envialo-simple'); ?>
                                                    </a> |
                                                </span>
                                                <span class='edit'>
                                                    <a href='' class='pausar-campana-bt' name='<?php echo $item['CampaignID'] ?>' title='<?php _e('Pausar Campaña', 'envialo-simple'); ?>' >
                                                        <?php _e('Pausar', 'envialo-simple'); ?>
                                                    </a> |
                                                </span>
                                                <?php break; ?>
                                            <?php case 'Stopped': ?>
                                                <span class='view'>
                                                    <a href='' class='previsualizar-news' name='<?php echo $item['CampaignID'] ?>' title='<?php _e('Previsualizar Campaña en el Navegador o por Correo Electrónico', 'envialo-simple'); ?>' >
                                                        <?php _e('Previsualizar', 'envialo-simple'); ?>
                                                    </a> |
                                                </span>
                                                <span class='edit'>
                                                    <a href='' class='reanudar-campana-bt' name='<?php echo $item['CampaignID'] ?>}' title='<?php _e('Reanudar Campaña', 'envialo-simple'); ?>' >
                                                        <?php _e('Reanudar', 'envialo-simple'); ?>
                                                    </a> |
                                                </span>
                                                <?php break; ?>
                                            <?php case 'Sending': ?>
                                                <span class='edit'>
                                                    <a href='' class='pausar-campana-bt' name='<?php echo $item['CampaignID'] ?>' title='<?php _e('Pausar Campaña', 'envialo-simple'); ?>' >
                                                        <?php _e('Pausar', 'envialo-simple'); ?>
                                                    </a> |
                                                </span>
                                                <?php break; ?>
                                            <?php case 'Completed': ?>
                                                <span class='view'>
                                                    <a href='' class='previsualizar-news' name='<?php echo $item['CampaignID'] ?>' title='<?php _e('Previsualizar Campaña en el Navegador o por Correo Electrónico', 'envialo-simple'); ?>' >
                                                        <?php _e('Previsualizar', 'envialo-simple'); ?>
                                                    </a> |
                                                </span>
                                                <?php break; ?>
                                        <?php endswitch; ?>
                                    </div>
                                </td>
                                <td><?php echo $item['Subject']; ?></td>
                                <td>
                                    <?php
                                    switch ($item['Status']):
                                        case 'Draft':
                                            ?>
                                            <div class='icono-estado borrador' title='<?php _e('Borrador', 'envialo-simple'); ?>'></div>
                                            <?php break; ?>
                                        <?php case 'Paused': ?>
                                            <div class='icono-estado pausada' title='<?php _e('Pausado', 'envialo-simple'); ?>'></div>
                                            <?php break; ?>
                                        <?php case 'Scheduled': ?>
                                            <div class='icono-estado programada' title='<?php _e('Programado', 'envialo-simple'); ?>'></div>
                                            <?php break; ?>
                                        <?php case 'Stopped': ?>
                                            <div class='icono-estado detenida' title='<?php _e('Detenido', 'envialo-simple'); ?>'></div>
                                            <?php break; ?>
                                        <?php case 'Sending': ?>
                                            <div class='icono-estado enviando' title='<?php _e('Enviando', 'envialo-simple'); ?>'></div>
                                            <?php break; ?>
                                        <?php case 'Completed': ?>
                                            <div class='icono-estado completada' title='<?php _e('Completado', 'envialo-simple'); ?>'></div>
                                            <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <td><?php echo $item['TotalRecipients']; ?></td>
                                <td>
                                    <?php if ($item['Status'] == 'Completed'): ?>
                                        <a  class='button-secondary ver-reportes-bt' href='<?php echo "{$adminUrl}admin.php?page=envialo-simple&accion=reportes&idCampana={$item['CampaignID']}"; ?>' >
                                            <?php _e('Ver Reportes', 'envialo-simple'); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td style='line-height: 33px;'>
                                    <?php
                                    if ($item['Status'] == 'Scheduled' || $item['Status'] == "Completed") {
                                        echo $item['SendStartDateTimeFormated'];
                                    }
                                    ?>&nbsp;
                                </td>
                            </tr>
                        <?php }; ?>
                        <tr class="tr-paginacion">
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td colspan='5'>
                                <div id='paginacion'>
                                    <?php if ($c['list']['pager']['absolutepage'] > 1): ?><a class='pag' href='<?php echo "{$adminUrl}admin.php?page=envialo-simple&pagina=" . ($c['list']['pager']['absolutepage'] - 1); ?>{$url}'> &lt; </a><?php endif; ?><?php for ($i = 1; $i <= $c['list']['pager']['pagecount']; $i++): ?><a  class='pag <?php echo $c['list']['pager']['absolutepage'] == $i ? 'pagActiva' : ''; ?>' href= '<?php echo "{$adminUrl}admin.php?page=envialo-simple&pagina={$i}"; ?>' accesskey=''> <?php echo $i; ?> </a><?php endfor; ?><?php if ($c['list']['pager']['absolutepage'] < $c['list']['pager']['pagecount']): ?><a class='pag' href='<?php echo "{$adminUrl}admin.php?page=envialo-simple&pagina=" . ($c['list']['pager']['absolutepage'] + 1); ?>'> &gt; </a><?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>