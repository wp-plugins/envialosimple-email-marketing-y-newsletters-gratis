<?php
    include_once (ENVIALO_DIR . "/clases/EnvialoSimple.php");
    include_once (ENVIALO_DIR . "/clases/Campanas.php");
    include_once (ENVIALO_DIR . "/clases/Contactos.php");
    $ev = new EnvialoSimple();
    $co = new Contactos();
    $ev
    ->checkSetup();
    if (isset($GLOBALS["APIKey"])) {
        $ca = new Campanas();
        $co = new Contactos();
        //echo "clave seteada<";
    } else {
        //plugin configurado incorrectamente
        echo "Problema en la Configuración del Plugin";
    }
    
    $campoBusqueda = isset($_GET["filter"]) ? $_GET["filter"] : "Buscar Newsletters.."; 
    ?>


<?php include_once(ENVIALO_DIR."/paginas/header.php"); ?>
	
	<script type="text/javascript">	
	   
	   jQuery(document).ready(function(){
	       if(jQuery(".icono-estado.enviando").length > 0){
                setInterval(function(){refrescarNewsletters()},15000);    
            }   
	   });
	    
	    
	    
	    function refrescarNewsletters(){
	        var pagina = 1;
	        var filtro = "";
	        
	        if(getUrlVars()["pagina"] != undefined){
	           pagina = getUrlVars()["pagina"]    
	        }
	        
	        if(getUrlVars()["filter"] != undefined){
               filtro = getUrlVars()["filter"]    
            }
            
            mostrarOver = false;	        	        
	        jQuery.post(urlHandler,{accion:"refrescarNews",pagina:pagina,filtro:filtro},function(html){	            
	            jQuery("#contenedor-newsletters").html(html);
	        },"html");	        
	    }
	    
	    
	    function getUrlVars() {
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
            });
            return vars;
        }

	    
	</script>
	  <?php
            if(isset($_GET["configurado"])){
                          $msj = "El Plugin ha sido Configurado Correctamente! Ya podés comenzar a Enviar tus Newsletters.";    
                          echo "<div id='msj-respuesta' class='mensaje msjExito' style='width: 55%; display:inline-block'>{$msj}</div>";
                          
                       }
        
        ?>
 	<div class="wrap">
 	  
 	    
		<div id="icon-edit-comments" class="icon32">
			<br>
		</div><h2 style="float:left">Newsletters <a href="<?php echo get_admin_url()."admin.php?page=envialo-simple-nuevo" ?>" class="add-new-h2 abrir-modal-campana">Crear Nuevo</a>  </h2>
		<div class="busqueda-campanas">
		    <form name="busquedaCampana" action="#" method="get">
		      <input type="text" name="filter" id="buscarCampana" value="<?php echo $campoBusqueda; ?>">
		      <input type="hidden" name="page" value="envialo-simple"/>
		      <input type="submit" style="visibility: hidden;" />
		    </form>
		</div>
		<div style="clear:both"></div>
		
		<div class="tool-box" id="contenedor-newsletters">
		    
		    <?php if(isset($_GET["camp-enviada"])){
		              $msj = "Newsletter Enviado Correctamente !!";
                      echo "<div id='msj-respuesta' class='mensaje msjExito' style='width: 55%; display:inline-block'>{$msj}</div>";
                   }
		    ?>
		<?php 
				$filter = "";
                $absolutepage = 1;
				if(isset($_GET["pagina"])){
					$absolutepage = filter_var($_GET["pagina"],FILTER_SANITIZE_NUMBER_INT);	
                }
                
				if(isset($_GET['filter'])){
				    $filter = filter_var($_GET["filter"],FILTER_SANITIZE_STRING);
				}				
				echo $ca->mostrarCampanas($absolutepage,$filter);
        ?>			
		</div>
</div>		
