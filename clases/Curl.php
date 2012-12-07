<?php 

class Curl{
    
    
    function curlJson($parametros, $url, $esGet = FALSE, $esArchivo = FALSE) {
        $cookie = "cookie.txt";

        $parametros["APIKey"] = $GLOBALS["APIKey"];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "WP-Plugin EnvialoSimple");
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);        
        
         if ($esGet) {
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
        } else {
            curl_setopt($ch, CURLOPT_POST, 1);
        }       
        
        if($esArchivo){                           
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);            
        }else{
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parametros));
            
        }
        $resultado = curl_exec($ch);
  
        if (curl_errno($ch)) {                
            
            $curl_errno = curl_errno($ch);
            $curl_error = curl_error($ch);
            
            echo "<pre>".__('Error de Conexion con el Servidor:','envialo-simple')." <br/>
                    curl_errno: {$curl_errno} | curl_error: {$curl_error}<br/>
                    ".__('De ser Posible, envíe este error mediante el formulario de feedback.','envialo-simple')."
                    </pre>";
             die();   

        } else {
            return $resultado;
        }

    }
     
    
}


?>