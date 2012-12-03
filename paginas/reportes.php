<?php
		
	include_once(ENVIALO_DIR."/clases/EnvialoSimple.php");
	include_once(ENVIALO_DIR."/clases/Campanas.php");
	include_once(ENVIALO_DIR."/clases/Contactos.php");
	
	$ev = new EnvialoSimple();
	$ev->checkSetup();
	
	if(isset($_GET["idCampana"])){
		
		$idCampana = filter_var($_GET["idCampana"],FILTER_SANITIZE_NUMBER_INT);			
		$parametros = array();				
		$ca = new Campanas();
		$repo = $ca->traerReportes($idCampana);
        $campana = $ca->traerCampana($idCampana); 
		$r = $repo["root"]["ajaxResponse"]["report"];
        include_once(ENVIALO_DIR."/paginas/header.php");	

?>



<div class="wrap">
<div class="icon32" id="icon-options-general"><br></div>
		<h2>Reportes de la Campaña</h2>
			<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="CampaignName">Fecha de Envío</label></th>
					<td>
					    <label><?php echo $r["detailsOfSend"]["SendStartDateTime"];?></label>										
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="CampaignName">Total de Subscriptores:</label></th>
					<td>
					    <label><?php echo $r["detailsOfSend"]["TotalRecipients"];?></label>
											
					</td>
				</tr>	
				<tr valign="top">
					<th scope="row"><label for="CampaignName">Email entregados:</label></th>
					<td>
					    <label><?php echo $r["detailsOfSend"]["TotalDelivered"];?></label>
											
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="CampaignName">Aperturas Totales:</label></th>
					<td>
					    <label><?php echo $r["detailsOfSend"]["TotalOpened"];?></label>
										
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="CampaignName">Aperturas Únicas:</label></th>
					<td>
					    <label><?php echo $r["detailsOfSend"]["UniqueOpened"];?></label>												
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="CampaignName">Clicks Totales:</label></th>
					<td>
					    <label><?php echo $r["detailsOfSend"]["TotalClicks"];?></label>
												
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="CampaignName">Rebotes Totales:</label></th>
					<td>
					    <label><?php echo $r["detailsOfSend"]["TotalBounces"];?></label>
												
					</td>
				</tr>
				
								
				
	
			</tbody>
		</table>
			<br />
			<h3>Reportes Avanzados</h3>
			<?php if($campana["userinfo"]["role"] == "free"){?>
			    
			    <p>Para visualizarlos, tienes que loguearte en la aplicación de Envialo Simple.</p>
			    <p>Los usuarios de cuentas Premium, pueden visualizarlos con un solo click.</p>
                <p><a class="button-primary" target="_blank" href="http://v2.envialosimple.com/report/track/CampaignID/<?php echo $idCampana ?>" >Ir a Reportes Avanzados</a></p>
			<?php }else{ ?>   
    			     <p><a class="button-primary" target="_blank" href="<?php echo $campana["campaign"]["publicURL"] ?>" >Ver Reportes Avanzados</a></p>
			<?php } ?>
</div>
<?php
}
?>