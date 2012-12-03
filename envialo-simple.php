<?php
/*
 Plugin Name: Envialo Simple
 Plugin URI: http://envialosimple.com/envialosimple-wordpress-plugin
 Description: Email Marketing Efectivo
 Version: 1.1
 Author: Dattatec
 Author URI: http://www.dattatec.com
 License: GPLv2 or later
 */

define('ENVIALO_DIR', dirname(__FILE__));
add_action('admin_enqueue_scripts', 'cargarscripts');
add_action('admin_menu', 'agregar_menues');

include_once("clases/Widget.php");



add_action( 'widgets_init', create_function( '', 'register_widget( "widget" );' ) );

function aviso_admin() {
    $div = '<div style="background-color:#FFFBCC; border:#E6DB55 1px solid; color:#555555; border-radius:3px; padding:5px 10px; margin:20px 15px 10px 0; text-align:left">';
    echo $div . "Aviso de admin</div>";
}

function agregar_menues() {
    add_menu_page('Envialo Simple', 'Envialo Simple', 'add_users', 'envialo-simple', 'mostrarPagina', plugins_url('envialo-simple/imagenes/icono.png'), 81.8);
    add_submenu_page('envialo-simple', 'Newsletters', 'Newsletters', 'add_users', 'envialo-simple', '');
    add_submenu_page('envialo-simple', 'Crear Newsletter', 'Crear Newsletter', 'add_users', 'envialo-simple-nuevo', 'mostrarCrearNews');
    add_submenu_page('envialo-simple', 'Listas y Contactos', 'Listas y Contactos', 'add_users', 'envialo-simple-listas', 'mostrarListasyContactos');
    add_submenu_page('envialo-simple', 'Formulario de Suscripción', 'Formulario de Suscripción', 'add_users', 'envialo-simple-formulario', 'mostrarFormSuscripcion');
    add_submenu_page('envialo-simple', 'Configuración', 'Configuración', 'add_users', 'envialo-simple-configuracion', 'mostrarConfiguracion');
}

function cargarscripts() {
    
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-widget');
    wp_enqueue_script('jquery-ui-mouse');
    wp_enqueue_script('jquery-ui-accordion');
    wp_enqueue_script('jquery-ui-accordion');    
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-droppable');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-resizable');
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('media-upload');    
}



function mostrarPagina() {

    if (isset($_GET["accion"]) && $_GET["accion"] == "campana-contenido") {

        require_once (ENVIALO_DIR . "/paginas/campanas-contenido.php");

    } elseif (isset($_GET["accion"]) && $_GET["accion"] == "campana-editar") {

        require_once (ENVIALO_DIR . "/paginas/campanas-editar.php");

    } elseif (isset($_GET["accion"]) && $_GET["accion"] == "campana-completa") {

        require_once (ENVIALO_DIR . "/paginas/campanas-completa.php");

    } elseif (isset($_GET["accion"]) && $_GET["accion"] == "reportes") {

        require_once (ENVIALO_DIR . "/paginas/reportes.php");

    } else {
        require_once (ENVIALO_DIR . "/paginas/index.php");
    }

}
function mostrarFormSuscripcion(){
    
    if(!isset($_GET['idFormulario'])){
        require_once (ENVIALO_DIR . "/paginas/form-suscripcion-listado.php");    
    }else{
        require_once (ENVIALO_DIR . "/paginas/form-suscripcion.php");
    }
    
}


function mostrarCrearNews() {

    $seleccPlantilla = TRUE;
    require_once (ENVIALO_DIR . "/paginas/campanas-editar.php");
}

function mostrarListasyContactos() {
    require_once (ENVIALO_DIR . "/paginas/listas-contactos.php");
}
function mostrarConfiguracion() {
    add_action('admin_menu', 'agregar_menues');
    require_once (ENVIALO_DIR . "/paginas/configuracion.php");
}
