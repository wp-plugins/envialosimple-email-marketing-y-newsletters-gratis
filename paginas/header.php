<?php
	if(isset($GLOBALS["APIKey"])){
		$envios =  $ev->traerEnviosDisponibles();        
	}
?>
<link rel="stylesheet"  href="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/css/smoothness/jquery-ui.css"); ?>" type="text/css" media="all" />
<link rel="stylesheet"  href="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/css/chosen.css"); ?>" type="text/css" media="all" />
<link rel="stylesheet"  href="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/css/estilos.css"); ?>" type="text/css" media="all" />

<script type="text/javascript">
    var urlHandler =  "<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/handler.php')?>";
    var CampaignID =  "<?php if (isset($idCampana)){echo $idCampana;}; ?>";
    var urlSubidaImagenes = "<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/php/subidaImagenes.php") ?>";
    var urlAdmin = "<?php echo get_admin_url()?>";
    var urlCss = "<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/css/') ?>";
    var urlImg = "<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/') ?>";
    var urlColorPicker = "<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/colorpicker/') ?>";
    var rolUsuario = "<?php echo $envios['role']?>"
    
    var dominio = "<?php echo site_url(); ?>";
    function __(text){
        return text;
    }
</script>
<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/chosen.jquery.min.js"); ?>"></script>
<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/scripts.js"); ?>"></script>


<div id="cargando" class="ui-widget-overlay">
    <img src="<?php echo plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/throbber.gif'); ?>" />
</div>


<div id="header">
    <a href="<?php echo get_admin_url()."admin.php?page=envialo-simple"?>"><div id="logo-ev"></div></a>
	<?php if(isset($envios)): ?>	    
		<div id="envios">
		    <div><?php printf(__('Tienes %d Envíos Disponibles','envialo-simple'),$envios['credits']["availableCredits"]); if($envios['role'] == 'free'){_e('para este mes.','envialo-simple');} ?></div>			
			<?php if(!$envios['white_label']): ?>
				<?php if($envios['role'] == 'free'): ?>
					<a href="#" id="abrir-modal-creditos"class="button-secondary"><?php _e('Actualizar a Versión PREMIUM','envialo-simple') ?></a>
				<?php else: ?>
					<a href="#" id="abrir-modal-creditos"  class="button-secondary"><?php _e('Comprar Más','envialo-simple') ?></a>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php if(!$envios['white_label']): ?>
			<div style="display: none">
				<div id="modal-comprar-envios">
				    <?php if($envios['role'] == 'free'): ?>				        
				        <div class="actualizarCol fl">
                            <h3><?php _e('Actualizar tu cuenta te permitirá envias más email y habilitará las siguentes funcionalidades:','envialo-simple') ?></h3>
                            <ul>
                                <li><?php _e('Creación y gestión de múltiples cuentas de <em><strong>envialo</strong>simple</em>','envialo-simple') ?></li>
                                <li><?php _e('Distribución de los envíos comprados, entre múltiples cuentas','envialo-simple') ?></li>
                                <li><?php _e('Colocación de logotipo por cuenta','envialo-simple') ?></li>
                                <li><?php _e('Reventa de email marketing con tu marca','envialo-simple') ?></li>
                                <li><?php _e('Sin referencia a <em><strong>envialo</strong>simple</em> en los emails enviados','envialo-simple') ?></li>
                                <li><?php _e('Ilimitados campos personalizados','envialo-simple') ?></li>
                                <li><?php _e('Compartir reportes de campañas','envialo-simple') ?></li>
                            </ul>
                        </div>				        
				    <?php endif ?>  
				    <div class="preciosEnvios fl">  
    					<h2><?php _e('Cuántos Envíos Necesitas ?','envialo-simple') ?></h2>
    					<div id="contenedor-precios"></div>
    					<p style="font-weight: bold;"><?php _e('¡Mientras más compras, más ahorras!','envialo-simple') ?></p>
    					<p><?php _e('Los envíos no tienen vencimiento y son acumulables.','envialo-simple') ?></p>
					</div>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>
<div style="display: none">
    <div id="modalPrevisualizar">
        <div id="form-previsualizar-contenedor">
            <form id="form-previsualizar">
                <input type="hidden" id="input-campana-id" name="CampaignID" value=""/>
                <p>
                    <?php _e('Si desea Previsualizar su Newsletter mediante Email, por favor Ingrese una dirección de Correo.','envialo-simple') ?>
                </p>
                <label><?php _e('Email','envialo-simple') ?></label>
                <input type="text" name="input-email" />
                <br />
                <label id="label-error-mail" style="color:red;display:none;"><?php _e('Ingrese un Email Válido','envialo-simple') ?></label>
                <br />
                <br />
                <input type="submit" id="prev-email-bt" class="button-primary" value="<?php _e('Previsualizar por Email','envialo-simple') ?>" />
                <input type="reset" class="prev-cancelar button-secondary"  value="<?php _e('Cancelar','envialo-simple') ?>" />
                <br />
                <hr style="margin: 20px 0 20px 0;"/>
                <p>
                    <?php _e('De lo contrario, puede visualizarlo en el navegador.','envialo-simple') ?>
                </p>
                <input style="margin-bottom:25px;" type="submit" id="prev-navegador-bt" class="button-primary"  value="<?php _e('Previsualizar en Navegador','envialo-simple') ?>" />
                <input type="reset" class="prev-cancelar button-secondary"  value="<?php _e('Cancelar','envialo-simple') ?>" />
                <br />
            </form>
        </div>
        <div id="prev-navegador-contenedor"></div>
    </div>
    
  </div>
   
<div id="feedback">
    <div id="feedback-solapa">
        <div><a href="#" id="solapa-click" ><?php _e('Danos tu Opinión o Sugerencia','envialo-simple') ?></a></div>    
    </div>
    <div style="clear:both"></div>
    <div id="feedback-form">
        
        <form id="form-feedback" action="" method="post" target="_blank">
            <p><?php _e('Este Formulario no es para Soporte Técnico. Para soporte, <a href="https://administracion.dattatec.com/clientes/index.php?modulo=soporte&archivo=asistente" >Click Aquí</a>','envialo-simple') ?></p>
            <label for="mensaje-feedback"><?php _e('Mensaje:','envialo-simple') ?></label><br/>
            <textarea style="width: 375px;height: 100px;" name="feedback" id="mensaje-feedback"></textarea><br />
           
            <input type="reset" value="<?php _e('Cancelar','envialo-simple') ?>" style="float:right;margin-top:10px;"  class="button-secondary" onclick="ocultarFeedback()" />
             <input type="submit" value="<?php _e('Enviar','envialo-simple') ?>" style="float:right;margin-top:10px;"  class="button-primary" />
        </form>
        
    </div>   

</div>

<script type="text/javascript">
    
    function ocultarFeedback(){
        jQuery("#feedback").animate({"bottom":34},700);
    }    
    
    jQuery(document).ready(function(){        
        
        jQuery("#solapa-click").click(function(){            
            jQuery("#feedback").animate({"bottom":350},250,function(){                
                jQuery("#feedback").animate({"bottom":265},250,function(){                    
                    jQuery("#feedback").animate({"bottom":283},300);                    
                });
            });                        
        });
            
        jQuery("#form-feedback").submit(function(event){
            
            event.preventDefault();
            
            var mensaje = "[WP-PLUGIN] "+jQuery("#mensaje-feedback").val()
            
            jQuery.post(urlHandler,{accion:"feedback",mensaje:mensaje},function(json){                
                
                if(json.root.ajaxResponse.success){
                    alert("<?php _e('Mensaje Enviado Correctamente!','envialo-simple') ?>");
                    ocultarFeedback();
                }else{
                    alert("<?php _e('Error al Enviar el Mensaje. Intente nuevamente.','envialo-simple') ?>");
                }
                
            },"json");
            
        });    
        
        
    });
    
</script>




