<div style="display:none">
    <div id="modal-plantillas">
        <h2><?php _e('Seleccionar Plantilla','envialo-simple') ?></h2>
        <div id="contenedor-plantillas">
            <?php
            print_r($ev -> mostrarPlantillas());
            ?>
        </div>
    </div>
    <div class="dropeable"></div>
    <div id="modal-agregar-contenido">
        <div id="seleccion-posts">
            <h2><?php _e('Seleccione los Post que desea Incluir','envialo-simple') ?></h2>
            <div id="barra-seleccion-posts">
                <label style="margin-right: 10px;margin-left: 10px" class="fl"><?php _e('Filtrar Por Categoría','envialo-simple') ?></label>
                <select id="select-categoria-post" class="fl">
                    <option><?php _e('Seleccionar','envialo-simple') ?></option>
                    <?php echo $ev -> mostrarCategoriasWP(); ?>
                </select>
                <a href="#" id="modal-agregar-ok" class="button-primary fl" style="margin-left: 140px;margin-right: 10px;"><?php _e('Aceptar','envialo-simple') ?></a>
                <div class="button-secondary fl" id="modal-agregar-cancelar" >
                    <?php _e('Cancelar','envialo-simple') ?>
                </div>
            </div>
            <div id="contenedor-posts">
                
            </div>
        </div>
    </div>   
   
    <div id="contenido-1">
        <table width="100%" border="0" align="center" cellpadding="20" cellspacing="0" class="tobBlock tobClonable tobRemovable" style="background-color:transparent;">
            <tbody>
                <tr>
                    <td valign="top" style="padding: 20px;">
                    <div class="tobEditableImg" data-imgmaxwidth="335">
                        <img class="imagen-wp" src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/500x100.jpg')?>" alt="Nuestra Foto" name="foto" width="554" height="100" border="0" id="foto"/>
                    </div></td>
                </tr>
                <tr>
                    <td valign="top" style="padding: 20px; font-family: 'Trebuchet MS',Helvetica,sans-serif; font-size: 12px; color: #000; line-height: 17px;">
                    <div class="tobEditableHtml">
                        <p style="font-size: 13px; color: #000; text-align:left;">
                            <b>
                            <span class="titulo-wp">
                                Bajada del producto o servicio
                            </span></b>
                        </p>
                        <p>
                            <span class="contenido-wp">
                                Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat. Duis autem vel eum iriure .
                            </span>
                        </p>
                    </div></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="contenido-2">
        <table width="100%" border="0" align="center" cellpadding="20" cellspacing="0" class="tobBlock tobClonable tobRemovable" style="background-color:transparent;">
            <tbody>
                <tr>
                    <td valign="top" style="padding: 20px;">
                    <div class="tobEditableImg" data-imgmaxwidth="335">
                        <img class="imagen-wp" src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/300x200.jpg')?>" alt="Nuestra Foto" name="foto" width="301" height="200" border="0" id="foto"/>
                    </div></td>
                    <td valign="top" style="padding: 20px; padding-left:0px; font-family: 'Trebuchet MS',Helvetica,sans-serif; font-size: 12px; color: #000; line-height: 17px;">
                    <div class="tobEditableHtml">
                        <p style="font-size: 13px; color: #000; text-align:left;">
                            <b>
                            <span class="titulo-wp">
                                Bajada del producto o servicio
                            </span></b>
                        </p>
                        <p>
                            <span class="contenido-wp">
                                Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat. Duis autem vel eum iriure .
                            </span>
                        </p>
                    </div></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="contenido-3">
        <table width="100%" align="center" cellspacing="0" cellpadding="30" border="0" class="tobBlock tobClonable tobRemovable" style="background-color:transparent;">
            <tbody>
                <tr>
                    <td align="center" valign="top" style="padding: 20px; font-family: 'Trebuchet MS',Helvetica,sans-serif; font-size: 14px; color: #000; line-height: 20px !important;" >
                    <div  class="tobEditableHtml">
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                <p style="font-size: 20px; color: #000; text-align:left;">
                                    <a style="color: #000;" href="#"><b>
                                    <span class="titulo-wp">
                                        Bajada del producto o servicio
                                    </span></b></a>
                                </p>
                                <p style="font-size: 14px; color: #000; text-align:left;">
                                    <span class="contenido-wp">
                                        Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat. Duis autem vel eum iriure .
                                    </span>
                                </p></td>
                            </tr>
                        </table>
                    </div></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="contenido-4">
        <table width="100%" border="0" align="center" cellpadding="20" cellspacing="0" class="tobBlock tobClonable tobRemovable" style="background-color:transparent;">
            <tbody>
                <tr>
                    <td valign="top" style="padding: 20px; padding-right:0px; font-family: 'Trebuchet MS',Helvetica,sans-serif; font-size: 12px; color: #000; line-height: 17px;">
                    <div class="tobEditableHtml">
                        <p style="font-size: 13px; color: #000; text-align:left;">
                            <b>
                            <span class="titulo-wp">
                                Bajada del producto o servicio
                            </span></b>
                        </p>
                        <p >
                            <span class="contenido-wp">
                                Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat. Duis autem vel eum iriure .
                            </span>
                        </p>
                    </div></td>
                    <td valign="top" style="padding: 20px;">
                    <div class="tobEditableImg" data-imgmaxwidth="335">
                        <img class="imagen-wp" src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/300x200.jpg')?>" alt="Nuestra Foto" name="foto" width="301" height="200" border="0" id="foto"/>
                    </div></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="contenido-5">
        <table width="100%" align="center" cellspacing="0" cellpadding="30" border="0" class="tobBlock tobClonable tobRemovable" style="background-color:transparent;">
            <tbody>
                <tr>
                    <td align="center" valign="top" style="padding: 20px; font-family: 'Trebuchet MS',Helvetica,sans-serif; font-size: 14px; color: #000; line-height: 20px !important;" >
                    <div  class="tobEditableHtml">
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                <p style="font-size: 14px; color: #000; text-align:left;">
                                    <span class="contenido-wp">
                                        Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat. Duis autem vel eum iriure
                                        Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat. Duis autem vel eum iriure
                                        Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat. Duis autem vel eum iriure
                                        Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat. Duis autem vel eum iriure .
                                    </span>
                                </p></td>
                            </tr>
                        </table>
                    </div></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="contenido-6">
        <table width="100%" border="0" align="center" cellpadding="20" cellspacing="0" class="tobBlock tobClonable tobRemovable" style="background-color:transparent;">
            <tbody>
                <tr>
                    <td valign="top" style="padding: 20px;">
                    <div class="tobEditableImg" data-imgmaxwidth="335">
                        <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/500x200.jpg')?>" alt="Nuestra Foto" name="foto" width="560" height="200" border="0" id="foto">
                    </div></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="contenido-7">
        <table class="tobBlock tobClonable tobRemovable " width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
            <tbody>
                <tr>
                    <td align="center" valign="top" width="50%">
                    <div class="tobEditableImg"><img id="foto" style="border: solid 1px #333;" src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/300x200.jpg')?>" alt="Nuestra Foto" name="foto" width="240" height="190" data-mce-src="http://v2.email-marketing.adminsimple.com/mailing_templates/67/objects/foto.jpg" >
                    </div></td><td align="center" valign="top" width="50%">
                    <div class="tobEditableImg"><img id="foto2" style="border: solid 1px #333;" src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/300x200.jpg')?>" alt="Nuestra Foto" name="foto" width="240" height="190" data-mce-src="http://v2.email-marketing.adminsimple.com/mailing_templates/67/objects/foto2.jpg" >
                    </div></td>
                </tr>
            </tbody>
        </table>
    </div>
      <div id="contenido-8">
        <table width="100%" align="center" cellspacing="0" cellpadding="30" border="0" class="tobBlock tobClonable tobRemovable">
            <tbody>
                <tr>
                    <td align="center" valign="top" style="padding: 20px; font-family: 'Trebuchet MS',Helvetica,sans-serif; font-size: 14px; color: #000; line-height: 20px !important;" >
                    <div  class="tobEditableHtml">
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                <p style="font-size: 14px; color: #000; text-align:left;">
                                    <hr />
                                </p></td>
                            </tr>
                        </table>
                    </div></td>
                </tr>
            </tbody>
        </table>
    </div>
</div><!--/display none-->