<?php
    //if uninstall not called from WordPress exit
    if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
        exit ();


    include_once("clases/EnvialoSimple.php");
    
    $ev = new EnvialoSimple();
    $ev->eliminarTablaBD();

?>