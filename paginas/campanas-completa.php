<?php

	include_once(ENVIALO_DIR."/clases/EnvialoSimple.php");
	include_once(ENVIALO_DIR."/clases/Campanas.php");
	include_once(ENVIALO_DIR."/clases/Contactos.php");


	function isCheck($valor){
		if($valor){
			return "Sí";
		}else{
			return "No";
		}
	}

	$ev = new EnvialoSimple();
	$ev->checkSetup();

    include_once(ENVIALO_DIR."/paginas/header.php");    
    $ca = new Campanas();	
	if(isset($_GET["idCampana"])){
		$idCampana = filter_var($_GET["idCampana"],FILTER_SANITIZE_NUMBER_INT);		
		$camp = $ca->traerCampana($idCampana);
		if(isset($camp["success"])){
			$c = $camp["campaign"];
		}else{
			
            $msg = "<p>"._e("No se puede establecer la conexión con el servidor.","envialo-simple")."</p>                    
                    <p><a href='#' class='button-primary'>"._e('Vuelva a intentarlo','envialo-simple')."</a></p>";                                              
            echo $msg;
            die();
		}
	}    
	$template = $ca->traerCuerpoCampana($idCampana);		

    if(isset($_GET["e"])){
        $fechaEnvio =$_GET["e"];
    }
   		
?>

	
<div class="wrap">
    
	
	<form name="form-completa" id="form-completa" action="<?php echo get_admin_url()."admin.php?page=envialo-simple-nuevo";?>" method="post">
	    <input name="CampaignName" type="hidden" id="CampaignName" value="<?php echo $c["Name"];?>" >
	    <input name="CampaignSubject" type="hidden" id="CampaignSubject"  value="<?php echo $c["Subject"];?>" />	    
	    <input name="FromID" type="hidden" value="<?php echo $c["From"]["EmailID"]?>" id="FromID" />        
        <input name="ReplyToID" id="ReplyToID" type="hidden" value="<?php $c["ReplyTo"]["EmailID"]?>">   
        
        <select name="MailListsIds[]" style="display:none" style="width: 388px;" multiple="multiple" >        
                <?php                                                                                                                              
                        foreach ($c["maillists"]["rows"] as $list) {                                            
                            echo "<option selected='selected' value='{$list['MailListID']}' ></option>";
                        }                                             
                ?>
        </select>       
	    
	    
     <div id="poststuff">

	    <div id="post-body" class="metabox-holder columns-2" style="margin-right: 300px;" >

		    <div id="post-body-content" style="width: 100%;float: left;">          
                                 
               <!-- contenido campañas-->       
    
              <div id="postdivrich" class="postarea">
                    <div id='ifr-completo' style="border: 1px solid #838383;border-radius: 4px;" class="restoreNormalCss">
                    <?php
                        echo $template;
                    ?>
                    </div>
                    <input type="hidden" name="contenidoAnterior" id="contenidoNews" value=""/>
                </div>       
    
               <!-- /contenido campañas-->

            </div><!-- post body content-->

        <div id="postbox-container-1" class="postbox-container" style="float: right;margin-right: -300px;width: 280px;">
		        <div id="side-sortables" class="meta-box-sortables ui-sortable">
		          <div id="acciones" class="postbox ">
               <h3 class="hndle"><span><?php _e('Acciones','envialo-simple') ?></span></h3>
                <div class="inside">
                    <div class="submitbox" id="submitpost">
                        <div >                            
                            <div>                                
                                <div class="misc-pub-section curtime" style="border-bottom:0;">                                                                     
                                    <input type="submit" class="button-primary" value="<?php _e('Duplicar Campaña','envialo-simple') ?>" />
                                </div>                             

                            </div>
                            <div class="clear"></div>
                        </div>                       
                    </div>
                </div>
                </div>
                
                 <div id="acciones" class="postbox ">
                    <h3 class="hndle"><span><?php _e('Detalles','envialo-simple') ?></span></h3>
                    <div class="inside">
                        <div class="submitbox" id="submitpost">
                            <div>                            
                                <div id="misc-publishing-actions">                                    
                                    
                                         <dl>
                                           <dt><?php _e('Estado del Newsletter:','envialo-simple') ?></dt>
                                           <dd><?php _e('Completado','envialo-simple') ?></dd>
                                           
                                           <dt><?php _e('Enviado el:','envialo-simple') ?></dt>
                                           <dd><?php echo $fechaEnvio ?></dd>
                                           
                                           <dt><?php _e('Asunto del Mensaje:','envialo-simple') ?></dt>
                                           <dd><?php echo $c["Subject"];?></dd>
                                           
                                           <dt><?php _e('Remitente:','envialo-simple') ?></dt>
                                           <dd><?php echo $c["ReplyTo"]["Name"]. " (".$c["ReplyTo"]["EmailAddress"].")"; ?></dd>
                                           
                                           <dt><?php _e('Responder a :','envialo-simple') ?></dt>
                                           <dd><?php echo $c["ReplyTo"]["Name"]. " (".$c["ReplyTo"]["EmailAddress"].")";  ?></dd>
                                           
                                           <dt><?php _e('Listas de Destinatarios:','envialo-simple') ?></dt>
                                           <dd>
                                               <ul style="margin: 0;">
                                                   <?php
                                                     foreach ($c["maillists"]["rows"] as $list) {                                            
                                                        echo "<li >{$list['Name']} ( {$list['MemberCount']} Destinatarios)</li>";
                                                     }                    
                                                   ?>
                                               </ul>
                                           </dd>
                                       </dl>
                                           
                                           <ul id="opciones-avanzadas-detalle">
                                               <li><span><?php _e('Agregar al Archivo público:','envialo-simple') ?></span> <?php echo isCheck($c["AddToPublicArchive"]);?></li>
                                               <li><span><?php _e('Seguir Enlaces:','envialo-simple') ?></span> <?php echo isCheck($c["TrackLinkClicks"]);?></li>
                                               <li><span><?php _e('Contar Aperturas:','envialo-simple') ?></span> <?php echo isCheck($c["TrackReads"]);?></li>
                                               <li><span><?php _e('Usar Google Analytics:','envialo-simple') ?></span>  <?php echo isCheck($c["TrackAnalitics"]);?></li>
                                               <li><span><?php _e('Enviar informe al finalizar:','envialo-simple') ?></span> <?php echo isCheck($c["SendStateReport"]);?> </li>                                               
                                           </ul>                                    
          
    
                                </div>
                                <div class="clear"></div>
                            </div>                       
                        </div>
                    </div>
                </div>
                
                
                
             </div> <!-- sortable-->
		    </div><!--post box cointaner1-->
	      </div><!--post body-->
		 </div><!--post stuff-->
		 </form>
		<div style="clear: both"> </div>
</div><!--wrap-->


<script type="text/jscript">
    
    jQuery("#form-completa").submit(function(){        
        jQuery("input,select").prop("disabled",null)
        jQuery("#contenidoNews").val(jQuery("#ifr-completo").html());        
    });
    
    
</script>