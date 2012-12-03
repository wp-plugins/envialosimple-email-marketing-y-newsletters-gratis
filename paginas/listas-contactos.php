
<?php

    include_once(ENVIALO_DIR."/clases/EnvialoSimple.php");
    include_once(ENVIALO_DIR."/clases/Contactos.php");              
    $ev = new EnvialoSimple();
    $ev->checkSetup();   
    $co = new Contactos();
    
    if(isset($_GET["pagina"])){
        $pagina = filter_var($_GET["pagina"],FILTER_SANITIZE_NUMBER_INT);   
    }else{
        $pagina=1;
    }
    
    $listaContactos = $co->mostrarListasContactos($pagina);
    
   
    
?>


<?php include_once(ENVIALO_DIR."/paginas/header.php"); ?>

    <?php if(!isset($_GET['MailListsIds'])){?>

    <div class="wrap">
        
      
    <div id="listas-contactos">
    <div id="icon-users" class="icon32 ">
            <br>
        </div><h2>Listas de Contactos <a href="#" class="add-new-h2 abrir-modal-lista">Nueva Lista</a> </h2>
        <div class="tool-box" id="contenedor-1">
            
            <p>Para Administrar tus Listas de Contactos en detalle, por favor accede a <a href="http://v2.envialosimple.com/maillist/list" target="_blank">Envialo Simple</a></p>      
                        
            <?php
            
                if(isset($_GET["listaCreada"]) && $_GET["listaCreada"]){
                    echo "<div class='mensaje msjExito' style='display:block'>Lista Creada con Éxito!</div>";
                }
                
                if(isset($_GET["contactoCreado"]) && $_GET["contactoCreado"]){
                    echo "<div class='mensaje msjExito' style='display:block'>Contacto Agregado con Éxito!</div>";
                }
                
                
                echo $listaContactos[1];
            
             ?>
        </div>
        
        </div>
    </div>

<div style="display:none">
    <div id="modal-crear-lista">
        
        <form action="#" id="form-crear-lista" method="post">
            <p>Escribí el Nombre de tu Nueva Lista de Contactos.</p>
            <label for="nombre-lista">Nombre</label>
            <input type="hidden" name="accion" value="crear-lista"/>
            <input type="text" id="nombre-lista" name="nombre-lista"  style="margin-bottom: 20px" value=""/><br />
            
            <div style="width:180px;margin: 0 auto 0 auto;">
                <input type="submit" value="Crear Lista" class="button-primary"/>
                <input type="reset" value="Cancelar" class="button-secondary" id="cerrar-modal"/>    
            </div>          
        </form> 
    </div>


    <div id="modal-agregar-contacto">
        <div class="mensaje" id="msj-agregar-contacto"></div>
        <form id="form-agregar-contacto" method="post" action="#">
            <input type="hidden" name="MailListID" value="" />
            
            
                <p>Completa los datos para Agregar un Contacto a tu Lista</p>
                <label>Email:<br /><input type="text" value="" name="Email"/></label><br />
                <?php
                    echo $ev->mostrarCamposPersonalizadosForm();
                ?>
                
                <input type="submit" class="button-primary" value="Agregar Contacto" style="margin-top: 20px;margin-bottom: 20px;"/>
                <input type="reset" class="button-secondary" value="Cancelar" onclick="jQuery('#modal-agregar-contacto').dialog('close')"/>
            
        </form>
        
        
        
    </div>
    
</div>    
    <script type="text/javascript">
    
    jQuery(".boton-sincronizar").click(function(event){
        
        event.preventDefault();
        
        if(confirm("Está Seguro?")){
            
            
            var idLista = jQuery(this).attr("name");
            
            jQuery.post("<?php echo plugins_url('envialo-simple/handler.php')?>",{accion:"sincronizarContactos",MailListID:idLista},function(json){
                
                if(json.success){                   
                    alert("Se Sincronizaron Correctamente tus Contactos de Wordpress con la Lista seleccionada."  );
                    window.location = "<?php echo get_admin_url()."admin.php?page=envialo-simple-listas";?>";
                }else{
                    alert("Error");
                }
                
                
            },"json");      
            
        }
    });
    
      jQuery(".boton-agregar-contacto").click(function(event){
        
        event.preventDefault();
        jQuery("input[name=MailListID]").val(jQuery(this).attr("name"));
        jQuery("#modal-agregar-contacto").dialog("open");
    });
    
    
    
        jQuery("#form-crear-lista").submit(function(event){
            
                event.preventDefault();
                
                if(checkVacio(jQuery("input[name=nombre-lista]"))){
                    
                    return false;
                }else{
                
                var nombre = jQuery("input[name=nombre-lista]").val();  
                                    
                jQuery.post(urlHandler,{accion:"crearLista",nombreLista:nombre},function(json){

                        if(json.root.ajaxResponse.success){
                            jQuery("#modal-crear-lista").dialog("close");
                            window.location = "<?php echo get_admin_url()."admin.php?page=envialo-simple-listas&listaCreada=1";?>";
                        }
                        
                    },"json");
                }   
            
        });
    
        jQuery("#form-agregar-contacto").submit(function(event){
            
            jQuery("#msj-agregar-contacto").hide(150)
            event.preventDefault()
            
            var campos = [];        
            var check = true;
            
            jQuery("input[name=Email]").css("border-color","#DFDFDF");
            if(!validarEmail(jQuery("input[name=Email]").val())){  
                jQuery("input[name=Email]").css("border-color","red");
                check = false;
                 jQuery("#msj-agregar-contacto").show(150).removeClass("msjError").removeClass("msjExito").addClass("msjError").html("Por favor Ingresa un Email Válido.");                  
                return false;
               
            }
            
            
            jQuery("#form-agregar-contacto input[type=text]").not("input[name=Email]").each(function(){
                   
                   /*if(checkVacio(jQuery(this))){  
                       check = false;
                              jQuery("#msj-agregar-contacto").show(150).removeClass("msjError").removeClass("msjExito").addClass("msjError").html("Por favor Completa todos los Campos.");                  
                        return false;
                        
                 
                    }*/
                       
                    campos.push(jQuery(this).val());                                 
                });
            
            if(check){
                jQuery.post(urlHandler,{accion:"agregarContactoLista",
                                        campos:campos,
                                        Email:jQuery("input[name=Email]").val(),
                                        MailListID:jQuery("input[name=MailListID]").val()
                                        
                                        },function(json){
                
                    if(json.root.ajaxResponse.success){                      
                        jQuery("#modal-agregar-contacto").dialog("close");
                        window.location = "<?php echo get_admin_url()."admin.php?page=envialo-simple-listas&contactoCreado=1";?>";
                    }else{
                        alert("Error al Agregar Contacto. Por favor revise los campos e intente nuevamente");
                    }
                
                },"json");    
            }            
            
            
            
            
        });
        
      
    
    function checkVacio(obj){
        
        if(obj.val() == ""){
            obj.css("border","1px solid red");
            return true;
        }else{
            obj.css("border","1px solid #DFDFDF");
            return false;
        }
    }   
    
function validarEmail(email) {
    return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(email);
}
        
    
jQuery(function() {
        
        jQuery( "#dialog:ui-dialog" ).dialog( "destroy" );
        
            
        jQuery( "#modal-crear-lista" ).dialog({
            autoOpen: false,
            height: 180,
            width: 320,
            dialogClass: 'fixed-dialog',
            modal: true,
            title:"Crear Lista"         
            
        });
        
        jQuery( "#modal-agregar-contacto" ).dialog({
            autoOpen: false,
            height: "auto",
            width: "auto",            
            modal: true,            
            title:"Agregar Contacto a la Lista"         
            
        });

        jQuery( ".abrir-modal-lista" ).click(function() {
                jQuery( "#modal-crear-lista" ).dialog( "open" );
            });
        
        jQuery("#cerrar-modal").click(function(){
            jQuery( "#modal-crear-lista" ).dialog( "close" );
            
        }); 
    });
    </script>
    


<?php 
    }else{    
        include_once("importar-contactos.php");    
}?>