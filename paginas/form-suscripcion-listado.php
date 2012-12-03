<?php
    

    include_once (ENVIALO_DIR . "/clases/EnvialoSimple.php");
    include_once (ENVIALO_DIR . "/clases/Formularios.php");
    
    
    $ev = new EnvialoSimple();
   
    $ev->checkSetup();
    if (isset($GLOBALS["APIKey"])) {        
        $fo = new Formularios();     
        $formularios = $fo->mostrarFormularios();       
    } else {        
        echo "Problema en la Configuración del Plugin";
    }
    
           
?>


<?php include_once(ENVIALO_DIR."/paginas/header.php"); ?>
    
    <script type="text/javascript"> 
       
      jQuery(document).ready(function(){
                    
          jQuery("#modal-obtener-codigo").dialog({
                autoOpen : false,
                height : "auto",
                width : "auto",
                modal : true,
                position:"top",              
                title : "Obtener Código",
                close : function(event, ui) {
                    jQuery("#modal-obtener-codigo textarea").html('')
                }
            })
          
          jQuery(".eliminar-form").click(function(event){
                event.preventDefault();
                
                if(confirm("Seguro que desea Eliminar el Formulario? Este cambio afectará a todos los sitios donde se está mostrando.")){
                
                    var FormID = jQuery(this).attr("name");
                    
                    jQuery.post(urlHandler,{accion:"eliminarForm",FormID:FormID},function(json){
                        
                        console.log(json);
                        if(json.root.ajaxResponse.success){
                            alert("Eliminado Correctamente");
                            window.location= urlAdmin+"admin.php?page=envialo-simple-formulario";
                        }else{
                            alert("Error al Eliminar. Intente Nuevamente");
                        }
                        
                        
                    },"json");
              
              }
          });
          
          jQuery(".obtener-codigo").click(function(event){
                event.preventDefault();
                jQuery("#modal-obtener-codigo").dialog("open");
                
                jQuery("#modal-obtener-codigo").dialog( "option", "position", 100 );
                jQuery("#modal-obtener-codigo textarea").text(jQuery(this).attr("name"));
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
        </div><h2 style="float:left">Formularios de Suscripción <a href="<?php echo get_admin_url()."admin.php?page=envialo-simple-formulario&idFormulario=0&idA=".$GLOBALS['AdministratorID'] ?>" class="add-new-h2">Crear Nuevo</a>  </h2>
        
        <div style="clear:both"></div>
        
        <div class="tool-box" id="contenedor-formularios">
            
            <?php 
                echo $formularios; 
            ?>
               
        </div>
</div>      
<div style="display:none">

    <div id="modal-obtener-codigo" style="top:50%">
        <p>Copia y pega éste código en el lugar de tu blog donde desees visualizar el Formulario de Suscripción.</p>
        
        <textarea style="width: 555px;height: 70px;">        
            <script type='text/javascript' src='http://v2.envialosimple.com/form/show/AdministratorID//FormID//format/widget'></script>
        </textarea>
        
        
        <p>Para utilizar el Formulario de Suscripción como <span style="font-weight: bold;">Widget</span>, realiza los siguientes pasos:</p>
        
        <ol>
            <li>Copia al Portapapeles el Código del Formulario.</li>
            <li>Ingresa al Menú <span style="font-weight: bold">Apariencia</span>.</li>
            <li>Selecciona el ítem <span style="font-weight: bold">Widgets</span>.</li>
            <li>Arrastra y suelta un nuevo Bloque <span style="font-weight: bold">Texto</span> en la Barra de Widgets de tu preferencia.</li>
            <li>Pega el Código del formulario de Suscripción en el Bloque.</li>
            <li>Guarda los cambios.</li>
            <li>El Formulario aparecerá como Widget en la Barra que seleccionaste y estará listo para usar.</li>
        </ol>
        
        <p>Si deseas utilizarlo en una <span style="font-weight: bold">Página</span> o <span style="font-weight: bold">Posteo</span>, desde el Editor de Contenido, selecciona la pestaña HTML y pega el codigo del formulario donde lo desees.</p>
       <!-- <p class="mensaje" style="display:block" >Los cambios que realices a éste formulario se verán reflejados en TODOS LOS SITIOS que lo utilicen.</p>-->
        
        <a style="float: right;margin: 20px;" href="#" onclick="jQuery('#modal-obtener-codigo').dialog('close')" class="button-secondary">Cerrar</a>
                
        
    </div>
</div>
