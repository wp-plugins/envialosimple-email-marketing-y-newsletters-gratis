
<?php

	include_once(ENVIALO_DIR."/clases/EnvialoSimple.php");
	include_once(ENVIALO_DIR."/clases/Contactos.php");
	
    
    if(isset($_GET["APIKey"])){
        
        $ev = new EnvialoSimple();      
        $GLOBALS["APIKey"] = $_GET["APIKey"];        
        $respuesta = $ev->testToken();        
        if($respuesta["success"]){
            
            $url= get_admin_url()."admin.php?page=envialo-simple&configurado=1";            
            echo "<script>window.location = '{$url}' </script>";
            exit();
            
        }else{               
           echo "<div id='msj-respuesta' class='mensaje msjExito' style='width: 55%; display:inline-block'>{$respuesta["mensaje"]}</div>";
        }        
    }
    
	
	if(!isset($_GET["setup"]) ){			
		
						
		$ev = new EnvialoSimple();
		$ev->checkSetup();
		$token = json_decode($ev->traerTokenBD(),TRUE);
		
	}
    
    $co = new Contactos();
    
    if(isset($_GET["pagina"])){
        $pagina = filter_var($_GET["pagina"],FILTER_SANITIZE_NUMBER_INT);   
    }else{
        $pagina=1;
    }
    
    $listaContactos = $co->mostrarListasContactos($pagina);
    
    $keyActiva = $listaContactos[0] == TRUE ? "Activada" : "Desactivada";
	
?>


<?php include_once(ENVIALO_DIR."/paginas/header.php"); 

if(!isset($_GET["setup"]) ){   ?>


        
	<div class="wrap">
		<div id="icon-tools" class="icon32">
			<br>
		</div><h2>Configuración </h2>
		<div class="tool-box" id="contenedor-1">
			<h3 class="title">Llaves de acceso API HTTP</h3>
			
			<p>El Plugin está utilizando la siguiente clave para comunicarse con Envialo Simple.</p>	
			
			<table id="" class="wp-list-table widefat fixed posts">
    			<thead>
    			<tr>
    				<th class="manage-column column-title sortable desc" style="width: 600px;padding-left: 10px;height: 30px;">Clave	</th>
    				<th class="manage-column column-title sortable desc">Estado</th>
    			</tr>	
    			</thead>
    			<tbody>
    
    				<tr id="fila-clave">
    					
    						
    					<td><?php echo $token["Key"]?>
    					<div class="row-actions">
    						
    							<span class="inline hide-if-no-js">								
    								<span class="trash" id="eliminar-clave-bt" name="<?php echo $token["idClave"];?>"><a class="submitdelete" title="Eliminar esta Clave" href="#">Eliminar</a> </span>
    							</span>	
    					</div></td>
    					
    					<td><?php echo $keyActiva; ?></td>
    				</tr>
    
    			</tbody>
		      </table>
			<div id="reconfigurar" class="dn">
				<p>Ha eliminado la clave actual. Para proseguir deberá configurar nuevamente el Plugin</p>
				<p><a class="button-secondary" href="<?php echo get_admin_url()."admin.php?page=envialo-simple-configuracion";?>">Configuración</a></p>
			</div>
			
		</div>
	<br />

	</div>



	<script type="text/javascript">		
		
		jQuery("#eliminar-clave-bt").click(function(){
			
			if(confirm("Al Eliminar la Clave, tendrá que reconfigurar el Plugin. Está Seguro?")){
				
				var idClave = jQuery(this).attr("name");
			
				jQuery.post(urlHandler,{accion:"eliminarToken",idClave:idClave},function(json){
					
					
					if(json.success){
						
						jQuery("#fila-clave").hide(300,function(){jQuery("#reconfigurar").show(300);});	
						jQuery("#listas-contactos").hide()					
						
					}else{
						alert("Error al Eliminar");
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

		jQuery( ".abrir-modal-lista" )
			
			.click(function() {
				jQuery( "#modal-crear-lista" ).dialog( "open" );
			});
		
		jQuery("#cerrar-modal").click(function(){
			jQuery( "#modal-crear-lista" ).dialog( "close" );
			
		});	
	});
	</script>

<?php } else { 
    include_once(ENVIALO_DIR."/paginas/configuracion-inicial.php");

}?>
