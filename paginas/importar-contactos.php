
<?php

    include_once(ENVIALO_DIR."/clases/EnvialoSimple.php");
    include_once(ENVIALO_DIR."/clases/Contactos.php");
    
    
              
    $ev = new EnvialoSimple();
    $ev->checkSetup();
   
    $co = new Contactos();
    
    if(isset($_GET["MailListsIds"])){
        $MailListsIds = filter_var($_GET["MailListsIds"],FILTER_SANITIZE_NUMBER_INT);   
    }
    
   
    
?>



    <div class="wrap">
      
    <div id="listas-contactos">
    <div id="icon-users" class="icon32">
            <br>
        </div><h2>Importar Contactos a la Lista</h2>
        <div class="tool-box" id="contenedor-1">
           <?php 
                if(!$co->importarSelectSource($MailListsIds)){
                    echo "<div class='mensaje msjError' style='display:block'>Ha Ocurrido un Error. Por Favor Intente Nuevamente </div>";       
                }
           
           ?> 
           
           
           <div>
               <p>Seleccione el método para importar contactos</p>
               
               <div class="metodo-importacion">
                   <div id="imagenCsv"></div>
                   <p class="titulo">Importar Lista desde Archivo .csv</p>
                   <p class="texto">Haz click en el botón "Subir Archivo" y selecciona el documento .csv que deseas importar</p>
                   <div class="boton-cuenta">Subir Archivo</div>
               </div>
               
              <div class="metodo-importacion">
                  <div id="imagenCopy"></div>
                   <p class="titulo">Copiar y pegar</p>
                   <p class="texto">Puedes copiar y pegar el contenido de un archivo o escribirlos manualmente en el siguiente formulario.</p>
                   <div class="formulario">
                       <textarea></textarea>
                       
                   </div>
                   <div class="boton-cuenta">Cargar</div>
               </div> 
               
           </div>
                           
                        
            
          </div>
        
        </div>
    </div>

  
    