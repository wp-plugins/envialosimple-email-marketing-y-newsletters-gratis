<?php
    

    include_once (ENVIALO_DIR . "/clases/EnvialoSimple.php");
    include_once (ENVIALO_DIR . "/clases/Formularios.php");
    
    
    $ev = new EnvialoSimple();
   
    $ev->checkSetup();
    if (isset($GLOBALS["APIKey"])) {        
        $fo = new Formularios();     
        $formularios = $fo->mostrarFormularios();       
    } else {        
        e_('Problema en la Configuración del Plugin','envialo-simple');
    }
    
           
?>


<?php include_once(ENVIALO_DIR."/paginas/header.php"); ?>
    
    <script type="text/javascript"> 
       
      jQuery(document).ready(function(){
                    
          jQuery("#modal-obtener-codigo").dialog({
                autoOpen : false,
                height : "500",
                width : "999",
                modal : true,
                position:"top",              
                title : "Obtener Código",
                close : function(event, ui) {
                    jQuery("#modal-obtener-codigo textarea").html('')
                }
            })
            
              
          
          jQuery(".eliminar-form").click(function(event){
                event.preventDefault();
                
                if(confirm("<?php _e('Seguro que desea Eliminar el Formulario? Este cambio afectará a todos los sitios donde se está mostrando.','envialo-simple') ?>")){
                
                    var FormID = jQuery(this).attr("name");
                    
                    jQuery.post(urlHandler,{accion:"eliminarForm",FormID:FormID},function(json){
                        
                        
                        if(json.root.ajaxResponse.success){
                            alert("<?php _e('Eliminado Correctamente','envialo-simple') ?>");
                            window.location= urlAdmin+"admin.php?page=envialo-simple-formulario";
                        }else{
                            alert("<?php _e('Error al Eliminar. Intente Nuevamente','envialo-simple') ?>");
                        }
                        
                        
                    },"json");
              
              }
          });
          
          jQuery(".obtener-codigo").click(function(event){
                event.preventDefault();
                
                jQuery("#modal-obtener-codigo").dialog("option", "position", {my: "center", at: "center", of: window});                
                jQuery("#modal-obtener-codigo textarea").text(jQuery(this).attr("name"));                
                jQuery("#modal-obtener-codigo").dialog("open");
                
                
                
           });
                    
      });      


    
    function sizeFrame(frameId) {
        var F = document.getElementById(frameId);
        if(F.contentDocument) {
            F.height = F.contentDocument.documentElement.scrollHeight+30; //FF 3.0.11, Opera 9.63, and Chrome
        } else {
        
            F.height = F.contentWindow.document.body.scrollHeight+30; //IE6, IE7 and Chrome
        }
    
    }        
        function getUrlVars() {
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
            });
            return vars;
        }

        
    </script>
    
    <div class="wrap">
        <div id="icon-edit-comments" class="icon32">
            <br>
        </div>
        <h2 style="float:left"><?php _e('Formularios de Suscripción','envialo-simple') ?> 
            <a href="<?php echo get_admin_url()."admin.php?page=envialo-simple-formulario&idFormulario=0&idA=".$GLOBALS['AdministratorID'] ?>" class="add-new-h2">
                <?php _e('Crear Nuevo','envialo-simple') ?>
            </a>  
        </h2>
        
        <div style="clear:both"></div>
        
        <div class="tool-box" id="contenedor-formularios">            
            <?php 
                echo $formularios; 
            ?>               
        </div>
    </div>      
<div style="display:none">

        <div id="modal-obtener-codigo">
          <p><?php _e('Copia y pega éste código en el lugar de tu blog donde desees visualizar el Formulario de Suscripción.','envialo-simple') ?></p>
        
        <textarea style="width: 555px;height: 70px;">        
            <script type='text/javascript' src='http://v2.envialosimple.com/form/show/AdministratorID/FormID/format/widget'></script>
        </textarea>
        
        
        <p><?php _e('Para utilizar el Formulario de Suscripción como <b>Widget</b>, realiza los siguientes pasos:','envialo-simple') ?></p>
        
        <ol>
            
            <li><?php _e('Ingresa al Menú <b>Apariencia</b>.','envialo-simple') ?></li>
            <li><?php _e('Selecciona el ítem <b>Widgets</b>.','envialo-simple') ?></li>
            <li><?php _e('Arrastra y suelta un nuevo Bloque <b>Formulario Suscripción EnvialoSimple</b> en la Barra de Widgets de tu preferencia.','envialo-simple') ?></li>
            <li><?php _e('Selecciona el Formulario que desees utilizar.','envialo-simple') ?></li>
            <li><?php _e('Guarda los cambios.','envialo-simple') ?></li>
            <li><?php _e('El Formulario aparecerá como Widget en la Barra que seleccionaste y estará listo para usar.','envialo-simple') ?></li>
        </ol>
        
 <p><?php _e('Si deseas utilizarlo en una <b>Página</b> o <b>Posteo</b>, desde el Editor de Contenido, selecciona la pestaña HTML y pega el codigo del formulario donde lo desees.','envialo-simple') ?></p>
       
        
        <a style="float: right;margin: 20px;" href="#" onclick="jQuery('#modal-obtener-codigo').dialog('close')" class="button-secondary"><?php _e('Cerrar','envialo-simple') ?></a>
               
    </div>
</div>
