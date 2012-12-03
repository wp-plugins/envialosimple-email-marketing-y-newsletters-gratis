<?php
    include_once(ENVIALO_DIR."/clases/EnvialoSimple.php");
    include_once(ENVIALO_DIR."/clases/Campanas.php");
    include_once(ENVIALO_DIR."/clases/Contactos.php");
    include_once(ENVIALO_DIR."/clases/Formularios.php");

    $ev = new EnvialoSimple();
    $ev->checkSetup();
    include_once(ENVIALO_DIR."/paginas/header.php");
    $fo = new Formularios();
    
    
    $emailsAdmin = $ev->obtenerEmailAdministrador();
    $htmlSelectEmailAdminFrom ="";            
    foreach ($emailsAdmin["item"] as $e) {                                                
            $htmlSelectEmailAdminFrom .= "<option value='{$e['EmailID']}'> {$e['Name']} ({$e["EmailAddress"]}) </option>";                                                
    }
    
    $htmlSelectEmailAdminReply = $htmlSelectEmailAdminFrom;
    
    if(isset($_GET['idFormulario']) && $_GET['idFormulario'] != 0){
        
        $idFormulario = filter_var($_GET['idFormulario'],FILTER_SANITIZE_NUMBER_INT);
        
        $f = $fo->traerFormulario($idFormulario);        
        
        echo"<pre>";
        //print_r($f);
        echo"</pre>";
        
        $htmlSelectEmailAdminFrom = "<option selected='selected'>{$f['email']['FromName']} ({$f['email']['FromEmail']}) </option>".$htmlSelectEmailAdminFrom;
        $htmlSelectEmailAdminReply = "<option selected='selected'>{$f['email']['ReplyToName']} ({$f['email']['ReplyToEmail']}) </option>".$htmlSelectEmailAdminReply;        
        
        $AdministratorID = $f['AdministratorID']; 
        
    }else{        
        $f = array();
        $f['form']['Name'] = "Nuevo Formulario de Suscripción";
        $f['form']['LabelEmailAddress'] = 'Direccion de email:';
        $f['form']['LabelSubmit'] = 'Suscribir';
        $f['form']['BackgroundColor'] = '#E9E9E9';
        $f['form']['Width'] = "200";
        $f['form']['Font'] = 'Arial';
        $f['form']['FontSize']  = '12';
        $f['form']['FontColor'] = '#555555';
        $f['form']['ShowPoweredBy'] = TRUE;
        $AdministratorID = filter_var($_GET['idA'],FILTER_SANITIZE_NUMBER_INT);
    }
       
?>

<link rel="stylesheet"  href="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/css/miniColors.css"); ?>" type="text/css" media="all" />
<script type="text/javascript"  src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/js/miniColors.js"); ?>"></script>

<script type="text/javascript">
    var AdministratorID = "<?php echo $AdministratorID?>";    
</script>

<div class="wrap">
    <h3>Formulario de Suscripción a Newsletter.</h3>
    
    <div class="mensaje" style="width: 60%"></div>
    
    <form id="form-editar-form" action="#" method="post">
        
        <input name="FormID" value="<?php echo $f['form']['FormID'] ?>" type="hidden"/>
        <input name="EmailID" value="<?php echo $f['email']['EmailID'] ?>" type="hidden"/>
              <div id="poststuff">
                  <div id="post-body" class="metabox-holder columns-2">

                      <div id="post-body-content">                        
                        <table class="form-table">
                        <tbody>
                            
                            <tr valign="top">
                                <th scope="row"><label for="nombre-form">Nombre Formulario:</label></th>
                                <td>
                                    <input type="text" value="<?php echo $f['form']['Name'] ?>" name="Name" id="nombre-form" style="width: 250px"/>
                                </td>                                
                            </tr>
                          
                               <tr>
                                <th scope="row"><label for="MailListsIds">Lista de Destinatarios</label></th>
                                <td>
                                    <select name="MailListsIds[]" id="MailListsIds" style="width: 250px" multiple="multiple" class="validar">
            
                                        <?php                                             
                                            $co = new Contactos();
                                            $listas = $co->listarListasContactos(-1);            
            
                                            if(count($f['form']["MailListsIds"]["item"]) == 0){            
                                                foreach ($listas[0]["item"] as $l) {            
                                                    echo "<option value='{$l['MailListID']}'>{$l['Name']} ({$l['MemberCount']} Destinatarios)</option>";
                                                }            
                                            }else{            
                                                foreach ($listas[0]['item'] as $l) {
                                                    $selected = "";            
                                                    foreach ($f['form']['MailListsIds']['item'] as $listaMail) {            
                                                        if($listaMail == $l['MailListID']){
                                                            $selected = "selected='selected'";
                                                        }
            
                                                    }
                                                    echo "<option {$selected} value='{$l['MailListID']}' >{$l['Name']} ({$l['MemberCount']} Destinatarios)</option>";            
                                                }
                                            }                                                                                           
                                        ?>
                                    </select>            
                                </td>            
                            </tr>                      
                            <tr valign="top">
                                <th scope="row"><label for="FromID">Remitente Email Confirmación:</label></th>
                                <td>
                                <select name="FromID" id="FromID" style="width: 250px">            
                                    <?php
                                        echo $htmlSelectEmailAdminFrom;        
                                     ?>
                                    <option class="selectCrear" value="agregar">+ Agregar Nuevo Email ..</option>
                                </select>   <br />
                                    <div id="responder-contenedor" style="margin: 5px 0 0 5px;">
                                        <input type="checkbox" name="responder-check" id="responder-check" style="margin-right: 5px;"  /> 
                                        <label for="responder-check" >Utilizar una direcci&oacute;n de respuesta distinta</label>
                                    </div>
                                 </td>
                            </tr>
                            <tr valign="top" id="responder-fila" style="display:none">
                                <th scope="row"><label for="ReplyToID">Responder a :</label></th>
                                <td>
                                <select name="ReplyToID" id="ReplyToID">            
                                    <?php                                                       
                                       echo $htmlSelectEmailAdminReply;
                                    ?>
                                    <option class="selectCrear" value="agregar">+ Agregar Nuevo Email ..</option>     
                                </select></td>
                            </tr>
                            
                            </table>
                            <a href="#" id="mostrar-op-av">Opciones Avanzadas</a>
                            <div id="opciones-avanzadas-suscripcion">
                                <div class="form-avanzado">
                                    <table class="form-table">
                                      <tr valign="top">
                                        <th scope="row"><label for="titulo-form">Título:</label></th>
                                        <td>
                                            <input type="text" value="<?php echo $f['form']['Title'] ?>" name="Title" id="titulo-form" style="width: 250px"/>
                                        </td>                                
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="textoBt">Etiqueta Campo Email:</label></th>
                                        <td>
                                          <input type="text" name="LabelEmailAddress" value="<?php echo $f['form']['LabelEmailAddress'] ?>" style="width: 250px" />               
                                        </td>
                                        
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row"><label for="textoBt">Texto del Botón:</label></th>
                                        <td>
                                            <input type="text" name="LabelSubmit" value="<?php echo $f['form']['LabelSubmit'] ?>" id="textoBt" style="width: 250px"/>            
                                        </td>            
                                    </tr>
                                    
                                    
                                    </table>
                                </div>
                            
                                <div class="form-avanzado">
                                    <table id="tabla-campos-form" class="form-table">
                                        <thead>
                                            <tr>
                                                <td style="width: 218px;">Campos Personalizados:</td>
                                                <td style="font-weight: bold;width: 250px" >Nombre del Campo</td>                                                                            
                                                <td style="font-weight: bold;width: 250px">Eliminar</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <?php 
                                                
                                               echo $ev->mostrarCamposPersonalizados($f['form']['CustomfieldsIds']['item']);
                                            ?>
                                            <tr class="addCampoTr"></tr>                                                        
                                            <tr>                                
                                                <td>
                                                    <a href="#" id="agregarCampo">Añadir Campo</a>
                                                </td>
                                            </tr>
                                 
                                        </tbody>
                                    </table>
                                </div>
                                 <div class="form-avanzado">
                                    <table class="form-table">
                                    <tr>
                                        <th scope="row"><label for="BackgroundColor">Color de Fondo:</label></th>
                                        <td>
                                          <input type="text" name="BackgroundColor" value="<?php echo $f['form']['BackgroundColor'] ?>" style="width: 60px" />               
                                        </td>
                                        
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row"><label for="Width">Ancho (px)</label></th>
                                        <td>
                                            <input type="text" name="Width" value="<?php echo $f['form']['Width'] ?>"  style="width: 60px;float:left"/>     
                                            <!--<div class="upDownContainer fl" >
                                                <div class="arrowUp" ></div>
                                                <div class="arrowDown" ></div>
                                            </div>-->       
                                        </td>            
                                    </tr>
                                    
                                      <tr>
                                        <th scope="row"><label for="Font">Letra / Fuente</label></th>
                                        <td>
                                            <input type="text" name="Font" value="<?php echo stripslashes($f['form']['Font']) ?>"  style="width: 180px;float:left"/> 
                                            
                                            
                                            <div class="fontContainer" data-relinput="Font">
                                                <div class="arrowDown" data-tooltype="fontPicker"></div>
                                                <ul class="fontList" style="display: none;">
                                                    <li style="font-family:Arial, Helvetica, sans-serif;" data-value="Arial, Helvetica, sans-serif">Arial, Helvetica, sans-serif</li>
                                                    <li style="font-family:Verdana, Geneva, sans-serif;" data-value="Verdana, Geneva, sans-serif">Verdana, Geneva, sans-serif</li>
                                                    <li style="font-family:Georgia, &quot;Times New Roman&quot;, Times, serif;" data-value="Georgia, &quot;Times New Roman&quot;, Times, serif">Georgia, "Times New Roman", Times, serif</li>
                                                    <li style="font-family:&quot;Courier New&quot;, Courier, monospace;" data-value="&quot;Courier New&quot;, Courier, monospace">"Courier New", Courier, monospace</li>
                                                    <li style="font-family:Tahoma, Geneva, sans-serif;" data-value="Tahoma, Geneva, sans-serif" class="selected">Tahoma, Geneva, sans-serif</li>
                                                    <li style="font-family:&quot;Trebuchet MS&quot;, Arial, Helvetica, sans-serif;" data-value="&quot;Trebuchet MS&quot;, Arial, Helvetica, sans-serif">"Trebuchet MS", Arial, Helvetica, sans-serif</li>
                                                    <li style="font-family:&quot;Arial Black&quot;, Gadget, sans-serif;" data-value="&quot;Arial Black&quot;, Gadget, sans-serif" class="">"Arial Black", Gadget, sans-serif</li>
                                                    <li style="font-family:&quot;Times New Roman&quot;, Times, serif;" data-value="&quot;Times New Roman&quot;, Times, serif">"Times New Roman", Times, serif</li>
                                                    <li style="font-family:&quot;Palatino Linotype&quot;, &quot;Book Antiqua&quot;, Palatino, serif;" data-value="&quot;Palatino Linotype&quot;, &quot;Book Antiqua&quot;, Palatino, serif">"Palatino Linotype", "Book Antiqua", Palatino, serif</li>
                                                    <li style="font-family:&quot;Lucida Sans Unicode&quot;, &quot;Lucida Grande&quot;, sans-serif;" data-value="&quot;Lucida Sans Unicode&quot;, &quot;Lucida Grande&quot;, sans-serif">"Lucida Sans Unicode", "Lucida Grande", sans-serif</li>
                                                    <li style="font-family:&quot;MS Serif&quot;, &quot;New York&quot;, serif;" data-value="&quot;MS Serif&quot;, &quot;New York&quot;, serif">"MS Serif", "New York", serif</li>
                                                    <li style="font-family:&quot;Lucida Console&quot;, Monaco, monospace;" data-value="&quot;Lucida Console&quot;, Monaco, monospace">"Lucida Console", Monaco, monospace</li>
                                                    <li style="font-family:&quot;Comic Sans MS&quot;, cursive;" data-value="&quot;Comic Sans MS&quot;, cursive">"Comic Sans MS", cursive</li>
                                                </ul>
                                            </div>
                                        
                                            
                                                       
                                        </td>            
                                    </tr>
                                    
                                      <tr>
                                        <th scope="row"><label for="FontSize">Letra / Tamaño (px)</label></th>
                                        <td>
                                            <input type="text" name="FontSize" value="<?php echo $f['form']['FontSize'] ?>"  style="width: 60px" class="fl"/> 
                                            
                                                <!--<div class="upDownContainer fl" >
                                                    <div class="arrowUp" ></div>
                                                    <div class="arrowDown" ></div>
                                                </div>-->
                                                      
                                        </td>            
                                    </tr>
                                     <tr>
                                        <th scope="row"><label for="FontColor">Letra / Color</label></th>
                                        <td>
                                            <input type="text" name="FontColor" value="<?php echo $f['form']['FontColor'] ?>"  style="width: 60px"/>            
                                        </td>            
                                    </tr>                            
                                    
                                    </table>
                                </div>
                                 <div class="form-avanzado">
                                     <table class="form-table">
                                    <tr>
                                        <th scope="row"><label for="ShowPoweredBy">Logo EnvialoSimple</label></th>
                                        <td>
                                          <input type="checkbox" name="ShowPoweredBy" <?php echo ($f['form']['ShowPoweredBy']) ? 'checked="checked"': ""; ?> value="1" style="width: 40px" />               
                                        </td>
                                        
                                    </tr>
                                     <tr>
                                        <th scope="row"><label for="SubscribeDobleOptIN">Doble Opt-In</label></th>
                                        <td>
                                          <input type="checkbox" name="SubscribeDobleOptIN" <?php echo ($f['form']['SubscribeDobleOptIN']) ? 'checked="checked"': ""; ?> value="1" style="width: 40px" />¿Requiere confirmación de suscripción?               
                                        </td>                                        
                                    </tr>
                                    </table>                                     
                                 </div>                                
                             </div>  <!--/opciones avanzadas -->

       </div><!-- post body content-->

       <div id="postbox-container-1" class="postbox-container">
           <div id="side-sortables" class="meta-box-sortables ui-sortable">
              <div id="acciones" class="postbox ">
                <h3><span>Acciones</span></h3>                
                    <input style="margin: 20px;" type="submit" id="guardar-cambios-form" value="Guardar Cambios" class="button-primary"/>
                    
                    <input type="submit" class="obtener-codigo button-secondary" name="obtener-codigo" value="Obtener Código" />                
                </div>
                
                <div id="preview" class="postbox ">
                <h3>
                    Vista Previa 
                    <div id="actualizar-preview" >Actualizar 
                        <img style="height: 15px;" src="<?php echo plugins_url("envialosimple-email-marketing-y-newsletters-gratis/imagenes/refresh.png") ?>" alt="refresh">
                    </div>
                </h3>                
                    <div id="contenedor-vista-previa"></div>                
                </div>                
                
            </div> <!-- sortable-->
            </div><!--post box cointaner1-->

          </div><!--post body-->
         </div><!--post stuff-->
         </form>   
    
</div>


<div style="display:none">
     <div id="modal-agregar-email">        
        <form id="form-agregar-email" action="#" method="post">            
            <div id="label-error-mail-admin" class="mensaje"></div>            
            <label for="nombreEmailAdmin">Nombre</label><br />
            <input type="text"  name="nombreEmailAdmin" id="nombreEmailAdmin"/><br />            
            
            <label for="emailAdmin">Email:</label><br />
            <input type="text"  name="emailAdmin" id="emailAdmin"/><br />            
            <input type="submit" value="Agregar" class="button-primary" style="margin-top: 20px;margin-bottom: 10px;"/>
            <input type="reset" value="Cancelar" class="button-secondary" onclick='jQuery("#modal-agregar-email").dialog("close")'/>
            
        </form>
        
        
    </div>
    
    
    <div id="modal-obtener-codigo" style="top:50%">
          <p>Copia y pega éste código en el lugar de tu blog donde desees visualizar el Formulario de Suscripción.</p>
        
        <textarea style="width: 555px;height: 70px;">        
            <script type='text/javascript' src='http://v2.envialosimple.com/form/show/AdministratorID//FormID//format/widget'></script>
        </textarea>
        
        
        <p>Para utilizar el Formulario de Suscripción como <span style="font-weight: bold;">Widget</span>, realiza los siguientes pasos:</p>
        
        <ol>
            
            <li>Ingresa al Menú <span style="font-weight: bold">Apariencia</span>.</li>
            <li>Selecciona el ítem <span style="font-weight: bold">Widgets</span>.</li>
            <li>Arrastra y suelta un nuevo Bloque <span style="font-weight: bold">Formulario Suscripción EnvialoSimple</span> en la Barra de Widgets de tu preferencia.</li>
            <li>Selecciona el Formulario que desees utilizar.</li>
            <li>Guarda los cambios.</li>
            <li>El Formulario aparecerá como Widget en la Barra que seleccionaste y estará listo para usar.</li>
        </ol>
        
 <p>Si deseas utilizarlo en una <span style="font-weight: bold">Página</span> o <span style="font-weight: bold">Posteo</span>, desde el Editor de Contenido, selecciona la pestaña HTML y pega el codigo del formulario donde lo desees.</p>
       <!-- <p class="mensaje" style="display:block" >Los cambios que realices a éste formulario se verán reflejados en TODOS LOS SITIOS que lo utilicen.</p>-->
        
        <a style="float: right;margin: 20px;" href="#" onclick="jQuery('#modal-obtener-codigo').dialog('close')" class="button-secondary">Cerrar</a>
               
    </div>
    
     <div id="modalAgregarCampo">
        
        <form id="form-agregar-campo" action="#" method="post">
            <div id="mensaje-agregar-campo" class="mensaje"></div>        
                
                        
            <label for="titulo-campo">Nombre Campo:</label><br />
            <input type="text"  name="Title-Campo" id="titulo-campo"/><br />

            <label for="valor-campo">Valor por Defecto:</label><br />                        
            <input type="text"  name="DefaultValue-Campo" id="valor-campo"/><br />            
            
            <input type="submit" value="Agregar" class="button-primary" style="margin-top: 20px;margin-bottom: 10px;"/>
            <input type="reset" value="Cancelar" class="button-secondary" onclick='jQuery("#modalAgregarCampo").dialog("close")'/>
            
        </form>
        
    </div>
    
   
    
</div><!-- display none-->



<script type="text/javascript">

    var selectAgregar;    
    var alertarPageLeave = false;       
         
    jQuery(document).ready(function(){
        
        mostrarBotonObtenerCodigo()
        
        jQuery(window).bind('beforeunload', function() {            
            if (alertarPageLeave) {
                return 'Al abandonar ésta página, se perderán todos los cambios no guardados.';
            } else {
                return;
            } 
       });            
       
       jQuery(window).bind('keypress',function(event){           
           alertarPageLeave = true;
       });  
        
        jQuery("#form-agregar-campo").submit(function(event){
            
            event.preventDefault();
            jQuery("#titulo-campo").css("border","1px solid #ddd");
            
            if(jQuery("#titulo-campo").val() == ""){
                jQuery("#titulo-campo").css("border","1px solid red");
                
                jQuery("#mensaje-agregar-campo").removeClass("msjExito").removeClass("msjError").addClass("msjError").html("Por favor Revise todos los campos").show();
                
                return false;    
            }
            
            var Title= jQuery("#titulo-campo").val()
            var DefaultValue= jQuery("#valor-campo").val()
            
            jQuery.post(urlHandler,{accion:"agregarCampoPersonalizado",Title:Title,DefaultValue:DefaultValue},function(json){
                                
                if(json.CustomFieldID != null){
                    
                                        
                    jQuery(selectAgregar).find("option:last").before("<option value='"+json.CustomFieldID+"'>"+json.Title+" </option>");
                    
                    jQuery(selectAgregar).val(jQuery(selectAgregar).find("option:last").prev().val())
                    
                    jQuery(selectAgregar).change();
                    
                    alert("Campo Agregado Correctamente!");
                    jQuery("#modalAgregarCampo").dialog("close");
                }
                
                
            },"json")
            
            
            
        });
        
        
           jQuery(".obtener-codigo").click(function(event){
                
                event.preventDefault();
                jQuery("#modal-obtener-codigo").dialog("open");                
                jQuery("#modal-obtener-codigo").dialog( "option", "position", 100 );
                
                return false;
           }); 
        
        
        var keyPress = false;        
        
        jQuery("input[name=Title],input[name=LabelEmailAddress],input[name=LabelSubmit],input[name=Width],input[name=FontSize]").keypress(function(){
            
           keyPress = true;
            
            });
        
        
        jQuery("input[name=Title],input[name=LabelEmailAddress],input[name=LabelSubmit],input[name=Width],input[name=FontSize]").focusout(function(){
            
            if(keyPress){
                generarVistaPrevia()
                keyPress = false;    
            }            
        });
            
         
            
         jQuery("#modal-obtener-codigo").dialog({
            autoOpen : false,
            height : "auto",
            width : "auto",
            modal : true,
            position:"top",              
            title : "Obtener Código"            
        })
        
        
         jQuery("#modalAgregarCampo").dialog({
                autoOpen : false,
                height : "auto",
                width : "auto",
                modal : true,                
                title : "Agregar Campo Personalizado",
                close : function(event, ui) {                    
                }
          });
        
        
        jQuery("input[name=ShowPoweredBy]").change(function(event){
              
              
              if(rolUsuario == "free"){
                  alert("Esta opción es solamente para cuentas Premium. Actualiza tu cuenta para poder utilizarla.")
                  event.preventDefault();
                  jQuery(this).attr("checked",true)
                  return false;
              }
              generarVistaPrevia();
          });
        
         generarVistaPrevia()
        
        
        //color pickers
        jQuery("input[name=FontColor],input[name=BackgroundColor]").miniColors({
            close:function(hex,rgba){
                generarVistaPrevia();
            }
        });
        
              
        jQuery(".upDownContainer .arrowUp").click(function(){
            valorInput = parseInt(jQuery(this).parent().parent().find("input").val());
            valorInput += 1;
            jQuery(this).parent().parent().find("input").val(valorInput);
            
        });
        
        jQuery(".upDownContainer .arrowDown").click(function(){
            valorInput = parseInt(jQuery(this).parent().parent().find("input").val());
            valorInput -= 1;
            jQuery(this).parent().parent().find("input").val(valorInput);            
        });
        
       
        
        //fuente
        jQuery("input[name=Font], .fontContainer").click(function(){
            jQuery(".fontList").toggle(150)
            jQuery("input[name=Font]").focus()
        });
        
        jQuery(".fontList li").click(function(){
            
            jQuery("input[name=Font]").val(jQuery(this).html());
            generarVistaPrevia();
            
        });
        
        jQuery("#actualizar-preview").click(function(){
                generarVistaPrevia();
        });
        
        
        jQuery("#guardar-cambios-form").click(function(event){
            
            
            
            jQuery(".chzn-choices").css("border","1px solid #DDD");
            jQuery("#nombre-form").css("border","1px solid #DDD");
            
            if(jQuery("#MailListsIds").val() == null){
                
                alert("Por Favor revise todos los campos.")
                jQuery(".chzn-choices").css("border","1px solid red")
                
                return false;
            }
            if(jQuery("#nombre-form").val() == ""){
                
                alert("Por Favor revise todos los campos.")
                jQuery("#nombre-form").css("border","1px solid red")
                
                return false;
            }
            
            var selectOK = true;
            
            jQuery("select[name=FromID],select[name=ReplyToID]").each(function(){
               
               jQuery(this).css("border","1px solid #ddd");
               
               if(jQuery(this).val() == "agregar"){
                    alert("Por Favor revise todos los campos.")
                    jQuery(this).css("border","1px solid red");
                    selectOK = false;
                    return false;
               } 
                
            });
            
            if(!selectOK){
                return false;
            }
            
            event.preventDefault();
            var Name = jQuery("input[name=Name]").val();
            var Title = jQuery("input[name=Title]").val();
            var LabelSubmit = jQuery("input[name=LabelSubmit]").val();
            var LabelEmailAddress = jQuery("input[name=LabelEmailAddress]").val();
            var Width = jQuery("input[name=Width]").val();
            var ShowPoweredBy = jQuery("input[name=ShowPoweredBy]:checked").length;
            var SubscribeDobleOptIN = jQuery("input[name=SubscribeDobleOptIN]:checked").length;
            var Font = jQuery("input[name=Font]").val();
            var FontSize = jQuery("input[name=FontSize]").val();
            var FontColor = jQuery("input[name=FontColor]").val();
            var BackgroundColor = jQuery("input[name=BackgroundColor]").val();
            var MailListsIds = jQuery("select[name^=MailListsIds]").val();
            var FormID = jQuery("input[name=FormID]").val();
            var EmailID = jQuery("input[name=EmailID]").val();
            
            var textFrom = jQuery("select[name=FromID] :selected").text()
            var textReply = jQuery("select[name=ReplyToID] :selected").text()
            
            var FromName = textFrom.substring(0,textFrom.lastIndexOf("("));
            var FromEmail = textFrom.substring(textFrom.lastIndexOf("(")+1,textFrom.lastIndexOf(")"));
            
            var ReplyToName = textReply.substring(0,textReply.lastIndexOf("("));
            var ReplyToEmail = textReply.substring(textReply.lastIndexOf("(")+1,textReply.lastIndexOf(")"));
            var CustomFieldsIds = [];
            
            jQuery("select[name^=CustomFieldsIds]").each(function(){  
                
                if(jQuery(this).val() != 0 && jQuery(this).val() != -1){
                    CustomFieldsIds.push(jQuery(this).val());    
                }              
                
                
            });
            
            jQuery.post(urlHandler,{accion:"guardarForm",
                                        Name:Name,
                                        Title:Title,
                                        LabelSubmit:LabelSubmit,
                                        LabelEmailAddress:LabelEmailAddress,
                                        BackgroundColor:BackgroundColor,
                                        Width:Width,
                                        Font:Font,
                                        FontSize:FontSize,
                                        FontColor:FontColor,
                                        ShowPoweredBy:ShowPoweredBy,
                                        SubscribeDobleOptIN:SubscribeDobleOptIN,
                                        MailListsIds:MailListsIds,
                                        FormID:FormID,
                                        ConfirmSubscriptionEmailID:"",
                                        SubsCallbackOK:"",
                                        SubsCallbackFail:"",
                                        ConfCallbackOK:"",
                                        ConfCallbackFail:"",
                                        EmailID:EmailID,
                                        FromName:FromName,
                                        FromEmail:FromEmail,
                                        ReplyToName:ReplyToName,
                                        ReplyToEmail:ReplyToEmail,
                                        CustomFieldsIds:CustomFieldsIds
                            }
            ,function(json){
                                
                                               
                if(json.success){
                    
                    jQuery("input[name=FormID]").val(json.form.FormID);
                    jQuery("input[name=EmailID]").val(json.email.EmailID);
                    
                    alertarPageLeave = false;                
                    jQuery(".mensaje").hide(100).removeClass("msjError").removeClass("msjExito").addClass("msjExito").html("Formulario Guardado Correctamente!").show(100);
                    
                    setTimeout(function(){jQuery(".mensaje").hide(300) },7000);
                    generarVistaPrevia()
                     mostrarBotonObtenerCodigo()
                }else{
                           jQuery(".mensaje").hide(100).removeClass("msjError").removeClass("msjExito").addClass("msjError").html("Error al Guardar el Formulario. Revise los Campos e Intente Nuevamente").show(100);
                }
                
             },"json");
           
           
          
            
        });
        
        jQuery("#mostrar-op-av").click(function(event){
            
            event.preventDefault();
            jQuery("#opciones-avanzadas-suscripcion").toggle(50);
            
        });
            
        jQuery("#agregarCampo").click(function(event){
            
            alertarPageLeave = true;
            
            event.preventDefault();            
            var camposExistentes = [];        
            var agregarCampo = true;
            var controlDeseleccionado
            jQuery("#tabla-campos-form select").each(function(){
                
                jQuery(this).css("border","1px solid #DFDFDF");
                
                if(jQuery(this).val() == 0 && jQuery(this).val() == -1){                        
                        agregarCampo = false;
                        controlDeseleccionado = jQuery(this);
                }else{
                    camposExistentes.push(jQuery(this).val());
                }
                
            });
            
            if(!agregarCampo){
                controlDeseleccionado.css("border","1px solid #4BD329");
                return false;
            }
            
            jQuery.post(urlHandler,{accion:"mostrarCamposPersonalizados",camposExistentes:camposExistentes},function(html){
                
                   jQuery(".addCampoTr").last().before(html);                
               },"html");
            
        });
            
         jQuery("#tabla-campos-form").on("click",".campo-nuevo.eliminar",function(){
                alertarPageLeave = true;
                
                var valor = jQuery(this).parent().parent().find("select").val();
                if(valor == 0 && valor == -1){                
                    jQuery(this).parent().parent().remove();
                    return false;
                }
                jQuery(this).parent().parent().remove();
                generarVistaPrevia()
         });   
            
        jQuery("#tabla-campos-form").on("change","select",function(){
            
            alertarPageLeave = true;
            
            jQuery(this).css("border","1px solid #DFDFDF");
            
            jQuery(this).parent().parent().find(".campo-nuevo").show();  
            if(jQuery(this).val() != 0 && jQuery(this).val() != -1){                              
                   generarVistaPrevia(); 
            }            
            
            valor = parseInt(jQuery(this).val())
            
            if(valor == -1){                
                jQuery("#modalAgregarCampo").dialog("open");
                selectAgregar = jQuery(this);
            }             
        });
        
    });
    
    function mostrarBotonObtenerCodigo(){
        
        var FormID = jQuery("input[name=FormID]").val();
        
        if(FormID != ""){            
            jQuery("input[name=obtener-codigo]").show();
            
            var script = "<script type='text/javascript' src='http://v2.envialosimple.com/form/show/AdministratorID/"+AdministratorID+"/FormID/"+FormID+"/format/widget'><\/script>";
            jQuery("#modal-obtener-codigo textarea").text(script);
        }else{
            jQuery("input[name=obtener-codigo]").hide();   
        }
        
        
    }
    
    
    function generarVistaPrevia(){
            var Name = jQuery("input[name=Name]").val();
            var Title = jQuery("input[name=Title]").val();
            var LabelSubmit = jQuery("input[name=LabelSubmit]").val();
            var LabelEmailAddress = jQuery("input[name=LabelEmailAddress]").val();
            var Width = jQuery("input[name=Width]").val();
            var ShowPoweredBy = jQuery("input[name=ShowPoweredBy]:checked").length;
            var SubscribeDobleOptIN = jQuery("input[name=SubscribeDobleOptIN]:checked").length;
            var Font = jQuery("input[name=Font]").val();
            var FontSize = jQuery("input[name=FontSize]").val();
            var FontColor = jQuery("input[name=FontColor]").val();
            var BackgroundColor = jQuery("input[name=BackgroundColor]").val();
            var MailListsIds = jQuery("select[name^=MailListsIds]").val();
            var FormID = jQuery("input[name=FormID]").val();
            var EmailID = jQuery("input[name=EmailID]").val();
            
            var textFrom = jQuery("select[name=FromID] :selected").text()
            var textReply = jQuery("select[name=ReplyToID] :selected").text()
            
            var FromName = textFrom.substring(0,textFrom.lastIndexOf("("));
            var FromEmail = textFrom.substring(textFrom.lastIndexOf("(")+1,textFrom.lastIndexOf(")"));
            
            var ReplyToName = textReply.substring(0,textReply.lastIndexOf("("));
            var ReplyToEmail = textReply.substring(textReply.lastIndexOf("(")+1,textReply.lastIndexOf(")"));
            var CustomFieldsIds = [];
            
            jQuery("select[name^=CustomFieldsIds]").each(function(){  
                
                if(jQuery(this).val() != 0 && jQuery(this).val()!= -1){
                    CustomFieldsIds.push(jQuery(this).val());    
                }              
                
                
            });
    
    
        jQuery.post(urlHandler,{accion:"vistaPreviaForm",
                                        Name:Name,
                                        Title:Title,
                                        LabelSubmit:LabelSubmit,
                                        LabelEmailAddress:LabelEmailAddress,
                                        BackgroundColor:BackgroundColor,
                                        Width:Width,
                                        Font:Font,
                                        FontSize:FontSize,
                                        FontColor:FontColor,
                                        ShowPoweredBy:ShowPoweredBy,
                                        SubscribeDobleOptIN:SubscribeDobleOptIN,
                                        MailListsIds:MailListsIds,
                                        FormID:FormID,
                                        ConfirmSubscriptionEmailID:"",
                                        SubsCallbackOK:"",
                                        SubsCallbackFail:"",
                                        ConfCallbackOK:"",
                                        ConfCallbackFail:"",
                                        EmailID:EmailID,
                                        FromName:FromName,
                                        FromEmail:FromEmail,
                                        ReplyToName:ReplyToName,
                                        ReplyToEmail:ReplyToEmail,
                                        CustomFieldsIds:CustomFieldsIds
                            }
            ,function(html){
                                
                                               
                jQuery("#contenedor-vista-previa").html(html);
                
             },"html");

        
              
    
        
    }
    
  
    
</script>
