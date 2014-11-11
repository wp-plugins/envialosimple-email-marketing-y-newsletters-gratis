<?php
if (isset($_GET["paginaContactos"])) {
    $pagina = filter_var($_GET["paginaContactos"], FILTER_SANITIZE_NUMBER_INT);
} else {
    $pagina = 1;
}

$MailListsIds = isset($_GET['MailListsIds']) ? $_GET['MailListsIds'] : 80;

$contactos = $co->listarContactos($pagina, $MailListsIds);

$MailListName = isset($_GET['MailListName']) ? $_GET['MailListName'] : "";
?>


<div class="wrap">
    <div id="ver-contactos">
        <div id="icon-users" class="icon32 ">
            <br/>
        </div>
        <h2><?php _e('Ver Contactos', 'envialo-simple'); ?></h2>


        <h3><?php echo $MailListName; ?></h3>

        <div class="tool-box" id="contenedor-1">

            <?php if (!$contactos["success"]): ?>
                <br/>
                <div class="mensaje msjError" style="display:inline">
                    <?php _e('No se pudo recuperar el Listado de Contactos. Por Favor Intente Nuevamente', 'envialo-simple'); ?>
                </div>
                <p>
                    <?php _e('En caso de persistir el error, reconfigure el Plugin', 'envialo-simple'); ?>
                </p>
            <?php else: ?>
                <?php if (empty($contactos[0]["item"])): ?>
                    <div class="wp-caption"><?php _e('La Lista seleccionada, no posee contactos.', 'envialo-simple'); ?>
                    </div>
                <?php else: ?>
                    <div>
                        <table class='wp-list-table widefat fixed posts'>
                            <thead>
                                <tr>
                                    <th class='manage-column column-title sortable desc' style="padding-left: 12px;height: 35px;"><?php _e('ID', 'envialo-simple'); ?></th>
                                    <th class='manage-column column-title sortable desc'><?php _e('Email', 'envialo-simple'); ?></th>
                                    <th class='manage-column column-title sortable desc'><?php _e('Cantidad de Listas', 'envialo-simple'); ?></th>

                                                                        <!--<th class='manage-column column-title sortable desc' style='text-align: center;width: 565px;'><?php _e('Acciones', 'envialo-simple'); ?></th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($contactos[0]['item'] as $item) :
                                    $i = 1;
                                    $clase = "impar";
                                    if ($i % 2 == 0) {
                                        $clase = "par";
                                    }
                                    $i++;
                                    ?>
                                    <tr class='<?php echo $clase; ?>'>
                                        <td><span><?php echo $item["MemberID"]; ?></span></td>
                                        <td><span class="row-title" ><?php echo $item["Email"]; ?></span></td>
                                        <td><span  ><?php echo $item["MailLists"]['activeCount']; ?></span></td>

                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan='3'>
                                        <div id='paginacion'>
                                            <?php if ($contactos[0]['pager']['absolutepage'] > 1): ?>
                                                <a class='pag' href='<?php echo "{$adminUrl}admin.php?page=envialo-simple-listas&MailListsIds={$MailListsIds}&verContactos=1&MailListName={$MailListName}&paginaContactos=" . ($listas[0]['pager']['absolutepage'] - 1); ?>'> &lt; </a>
                                            <?php endif; ?>
                                            <?php for ($i = 1; $i <= $contactos[0]['pager']['pagecount']; $i++): ?>
                                                <a class='pag <?php echo $contactos[0]['pager']['absolutepage'] == $i ? 'pagActiva' : ''; ?>' href='<?php echo "{$adminUrl}admin.php?page=envialo-simple-listas&MailListName={$MailListName}&MailListsIds={$MailListsIds}&verContactos=1&paginaContactos=" . $i; ?>'> <?php echo $i; ?> </a>
                                            <?php endfor; ?>
                                            <?php if ($contactos[0]['pager']['absolutepage'] < $contactos[0]['pager']['pagecount']): ?>
                                                <a class='pag' href='<?php echo "{$adminUrl}admin.php?page=envialo-simple-listas&MailListsIds={$MailListsIds}&verContactos=1&MailListName={$MailListName}&paginaContactos=" . ($contactos[0]['pager']['absolutepage'] + 1); ?>'> &gt; </a>
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

