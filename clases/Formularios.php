<?php 

    class Formularios{
        
        function traerFormulario($idFormulario){                 
                   
            $form = json_decode(file_get_contents(URL_BASE_API."/form/edit?APIKey={$GLOBALS['APIKey']}&format=json&FormID={$idFormulario}"),TRUE);       
                                               
            $formulario = array();
            if(!isset($form['root']['ajaxResponse']['success'])){                
               return $form;                 
            }                        
            $formulario['form'] = $form['root']['ajaxResponse']['form'];
             
            $EmailID = $formulario['form']['ConfirmSubscriptionEmailID'];            
            $email = json_decode(file_get_contents(URL_BASE_API."/email/edit?APIKey={$GLOBALS['APIKey']}&format=json&EmailID={$EmailID}"),TRUE);

                                    
            if(!isset($email['root']['ajaxResponse']['success'])){                
                return $email;                
            }
            
            $formulario['email'] = $email['root']['ajaxResponse']['email'];
            
            $formulario['success'] = TRUE;
            
            $formulario['AdministratorID'] =$form['root']['ajaxResponse']['userinfo']['AdministratorID'];
            
            return $formulario;
            
        }
        
        
        function listarFormularios(){
            
            $respuesta = json_decode($this->curlJson(array(), URL_BASE_API."/form/list/format/json"),TRUE);
            
            if(isset($respuesta['root']['ajaxResponse']['success'])){
                
                
                
                $forms =  array();
                $forms['list'] = $respuesta['root']['ajaxResponse']['list'];
                $forms['AdministratorID'] = $respuesta['root']['ajaxResponse']['userinfo']['AdministratorID'];
                
                return $forms;
            }else{
                return array();
            }
            
        }
        
               
        function mostrarFormularios(){
            
            $forms = $this->listarFormularios();            
            
            $GLOBALS['AdministratorID'] = $forms['AdministratorID'];
            
            if(count($forms['list']['item'])>0){
                    $url = get_admin_url() . 'admin.php?page=envialo-simple-formulario&idFormulario=';                   
                    $i=0 ;      
                    foreach ($forms['list']['item'] as $f) {
                            
                        $urlIframe =  plugins_url("envialo-simple/paginas/vista-previa-form.php?AdministratorID={$forms['AdministratorID']}&FormID={$f['FormID']}");  
                                                          
                        $html .= "<div class='contenedor-form'>
                                      <div class='vista-previa'>
                                      
                                        <iframe id='iframe-{$i}' src='{$urlIframe}' onload=\"sizeFrame('iframe-{$i}')\" ></iframe>
                                                                  
                                      </div>
                                      <div class='contenido-form'>                                
                                            <div class='nombre-form'>
                                                <a href='{$url}{$f['FormID']}'> {$f['Name']}</a>
                                            </div>
                                            <div class='titulo-form'>
                                                <span>{$f['Title']}</span>
                                            </div>
                                            <div class='acciones-form'>
                                                <a href='{$url}{$f['FormID']}' class='button-primary' >Editar</a>                                                
                                                <a href='#' name=\"<script type='text/javascript' src='http://v2.envialosimple.com/form/show/AdministratorID/{$forms['AdministratorID']}/FormID/{$f['FormID']}/format/widget'></script>\" class='obtener-codigo button-secondary'>Obtener Código</a>
                                                <a href='#' name='{$f['FormID']}' class='eliminar-form button-secondary' >Eliminar</a>
                                            </div>
                                            
                                      </div>
                                  </div>
                                  <div style='clear:both'></div>";
                        $i ++;
                        
                    }
                    
                                
                    return $html;
            }else{
                return "<p>Aquí podrás Crear y Configurar formularios de suscripción donde luego, tus visitantes podrán suscribirse para recibir tus Newsletters.</p>";
                
            }
                     
          
            
        }
        
        
        function vistaPrevia($CustomFieldsIds = array(),$FormID = "",$ConfirmSubscriptionEmailID = "",$Title = "",$Name = "",$LabelSubmit = "",
                            $LabelEmailAddress = "",$BackgroundColor= "",$Width= "",$Font= "",$FontSize= "",
                            $FontColor= "",$SubsCallbackOK= "",$SubsCallbackFail= "",$ConfCallbackOK= "",
                            $ConfCallbackFail= "",$CustomCSS= "",$ShowPoweredBy= "",$SubscribeDobleOptIN= ""){
            
            
            $parametros = array();
            $parametros['FormID'] = $FormID;
            $parametros['ConfirmSubscriptionEmailID'] = $ConfirmSubscriptionEmailID;
            $parametros['Title'] = $Title;
            $parametros['Name'] = $Name;
            $parametros['LabelSubmit'] = $LabelSubmit;
            $parametros['LabelEmailAddress'] = $LabelEmailAddress;
            $parametros['BackgroundColor'] = $BackgroundColor;
            $parametros['Width'] = $Width;
            $parametros['Font'] = $Font;
            $parametros['FontSize'] = $FontSize;
            $parametros['FontColor'] = $FontColor;
            $parametros['SubsCallbackOK'] = $SubsCallbackOK;
            $parametros['SubsCallbackFail'] = $SubsCallbackFail;
            $parametros['ConfCallbackOK'] = $ConfCallbackOK;
            $parametros['ConfCallbackFail'] = $ConfCallbackFail;
            $parametros['CustomCSS'] = $CustomCSS;
            $parametros['ShowPoweredBy'] = $ShowPoweredBy;
            $parametros['SubscribeDobleOptIN'] = $SubscribeDobleOptIN;     
            $parametros['CustomFieldsIds'] = $CustomFieldsIds;     
                
            
            
            return $this->curlJson($parametros, URL_BASE_API."/form/preview/format/widget");
            
        } 
        
          function guardarForm($CustomFieldsIds = array(),$MailListsIds = array(), $FormID = "",$ConfirmSubscriptionEmailID = "",$Title = "",$Name = "",$LabelSubmit = "",
                            $LabelEmailAddress = "",$BackgroundColor= "",$Width= "",$Font= "",$FontSize= "",
                            $FontColor= "",$SubsCallbackOK= "",$SubsCallbackFail= "",$ConfCallbackOK= "",
                            $ConfCallbackFail= "",$CustomCSS= "",$ShowPoweredBy= "",$SubscribeDobleOptIN= ""){
            
            
            $parametros = array();
            $parametros['FormID'] = $FormID;
            $parametros['MailListsIds'] = $MailListsIds;
            $parametros['ConfirmSubscriptionEmailID'] = $ConfirmSubscriptionEmailID;
            $parametros['Title'] = $Title;
            $parametros['Name'] = $Name;
            $parametros['LabelSubmit'] = $LabelSubmit;
            $parametros['LabelEmailAddress'] = $LabelEmailAddress;
            $parametros['BackgroundColor'] = $BackgroundColor;
            $parametros['Width'] = $Width;
            $parametros['Font'] = $Font;
            $parametros['FontSize'] = $FontSize;
            $parametros['FontColor'] = $FontColor;
            $parametros['SubsCallbackOK'] = $SubsCallbackOK;
            $parametros['SubsCallbackFail'] = $SubsCallbackFail;
            $parametros['ConfCallbackOK'] = $ConfCallbackOK;
            $parametros['ConfCallbackFail'] = $ConfCallbackFail;
            $parametros['CustomCSS'] = $CustomCSS;
            $parametros['ShowPoweredBy'] = $ShowPoweredBy;
            $parametros['SubscribeDobleOptIN'] = $SubscribeDobleOptIN;         
            $parametros['CustomFieldsIds'] = $CustomFieldsIds;         
            
            
            return $this->curlJson($parametros, URL_BASE_API."/form/edit/format/json");
            
        } 
        
        
        function guardarRemitenteResponder($FormID,$EmailID,$Name,$FromName,$FromEmail,$ReplyToName,
                                            $ReplyToEmail){
                                                
                                                
             $Subject = 'Solicitud de confirmación de suscripción nuestra lista de contactos';                                   
            $Content= '<div class="templateBoundary" style="width:100%;height:100%; background:#EEEEEE;">
    <div><table width="100%" border="0" cellspacing="0" cellpadding="15" align="center" class="tobBlock tobClonable" style="background:#22AAE4;">
        <tbody><tr valign="middle">
            <td style="text-align:center;">
                <div class="tobEditableHtml">
                    <span style="font-family:Arial, Helvetica, sans-serif; font-size:30px;color:#ffffff; line-height:45px">Solicitud de confirmación de suscripción</span>
                </div>
            </td>
        </tr>
    </tbody></table>
    <br>
    <table width="570" border="0" cellspacing="0" cellpadding="20" align="center" style="margin:0 auto; background:#FFFFFF;" class="tobBlock tobClonable">
        <tbody><tr>
            <td style="font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:22px;  text-align:left; color:#444444">
                <div class="tobEditableHtml">
                    <strong>Recibimos tu solicitud de suscripción a nuestra lista de correos. </strong><br>
                    <br>
                    Para confirmar tu suscripción haz click <a href="%Link%">aquí</a> o ingresa a la siguente Url con tu navegador:<br>
                    <br>
                    <span style="color:#888888;  ">%Link%</span>
                </div>
            </td>
        </tr>
        <tr>
            <td style="text-align:right;">
                <div class="tobEditableHtml">
                    <span style="font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:22px; color:#888888; font-style:italic; font-weight:bold"> Cordiales saludos. </span>
                </div>
            </td>
        </tr>
    </tbody></table>
    <br>
</div></div><!-- \\ Template // -->';                                                
                                                
                                                
                                                
            $parametros = array();
            $parametros['FormID'] = $FormID;                                          
            $parametros['EmailID'] = $EmailID;                                          
            $parametros['Name'] = $Name;                                          
            $parametros['FromName'] = $FromName;                                          
            $parametros['FromEmail'] = $FromEmail;                                          
            $parametros['ReplyToName'] = $ReplyToName;                                          
            $parametros['ReplyToEmail'] = $ReplyToEmail;                                          
            $parametros['Subject'] = $Subject;                                          
            $parametros['Content'] = $Content;            
            $parametros['HistoryLabel'] = "defaultMessage";
            
            return $this->curlJson($parametros, URL_BASE_API."/email/edit/format/json/");
            
                        
        }
        
        
        
        function eliminarFormulario($FormID){
            
            $parametros = array();
            $parametros['FormsIds'] = array($FormID);
            
            return $this->curlJson($parametros, URL_BASE_API."/form/delete/format/json");
            
        }
        
        function curlJson($parametros, $url, $esGet = FALSE) {
            $cookie = "cookie.txt";
            $ch = curl_init();
            $parametros["APIKey"] = $GLOBALS["APIKey"];
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, "WP-Plugin EnvialoSimple");
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
           
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parametros));
            if ($esGet) {
                curl_setopt($ch, CURLOPT_HTTPGET, 1);
                curl_setopt($ch, CURLOPT_POST, 0);
            } else {
                curl_setopt($ch, CURLOPT_HTTPGET, 0);
                curl_setopt($ch, CURLOPT_POST, 1);
            }
            $response = curl_exec($ch);            
            $curl_errno = curl_errno($ch);
            $curl_error = curl_error($ch);
            if($curl_errno){
                $resultado = json_encode(array("root"=>array("ajaxResponse" => array("curlError"=>curl_errno($ch)))));
            } else {
                $resultado = $response;
            }
            $logear = true;
            if($logear){
                $logMsg = date('Y-m-d H:i:s')."<<\n{$url}\n".print_r($parametros,true);
                if($curl_errno){
                    $logMsg.= "curl_errno: {$curl_errno} | curl_error: {$curl_error}\n";
                }
                $logMsg.= "{$response}\n>>\n";
                 error_log($logMsg,3,'esApiLog.log');
                 
            }
            return $resultado;
        }
                
        
    }


?>