<?php

/*
  Plugin Name: EnvialoSimple: Email Marketing y Newsletters GRATIS
  Plugin URI: http://envialosimple.com/envialosimple-wordpress-plugin
  Description: El plugin de EnvialoSimple te permitirá crear y enviar Newsletters de calidad profesional, en minutos y directamente desde tu Wordpress.
  Version: 1.107
  Author: dattatec.com
  Author URI: http://www.dattatec.com
  License: GPLv2 or later
 */

include_once("clases/Widget.php");

define('ENVIALO_DIR', dirname(__FILE__));
add_action('admin_enqueue_scripts', 'cargarscripts');
add_action('admin_menu', 'agregar_menues');
add_action('widgets_init', create_function('', 'register_widget("widgetenvialo");'));
add_action('plugins_loaded', 'cargar_traduccion');


/*
  add_action('wp_insert_comment','comentario_insertado',10,2);

  function comentario_insertado($comment_id, $comment_object) {
  print_r($comment_object);
  die();
  }
 */

function cargar_traduccion() {
    load_plugin_textdomain('envialo-simple', false, basename(dirname(__FILE__)) . '/languages');
}

function aviso_admin() {
    $div = '<div style="background-color:#FFFBCC; border:#E6DB55 1px solid; color:#555555; border-radius:3px; padding:5px 10px; margin:20px 15px 10px 0; text-align:left">';
    echo $div . "Aviso de admin</div>";
}

function agregar_menues() {
    add_menu_page('Envialo Simple', 'Envialo Simple', 'add_users', 'envialo-simple', 'mostrarPagina', plugins_url('envialosimple-email-marketing-y-newsletters-gratis/imagenes/icono.png'));
    add_submenu_page('envialo-simple', 'Newsletters', 'Newsletters', 'add_users', 'envialo-simple', '');
    add_submenu_page('envialo-simple', __('Crear Newsletter', 'envialo-simple'), __('Crear Newsletter', 'envialo-simple'), 'add_users', 'envialo-simple-nuevo', 'mostrarCrearNews');
    add_submenu_page('envialo-simple', __('Listas y Contactos', 'envialo-simple'), __('Listas y Contactos', 'envialo-simple'), 'add_users', 'envialo-simple-listas', 'mostrarListasyContactos');
    add_submenu_page('envialo-simple', __('Formulario de Suscripción', 'envialo-simple'), __('Formulario de Suscripción', 'envialo-simple'), 'add_users', 'envialo-simple-formulario', 'mostrarFormSuscripcion');
    add_submenu_page('envialo-simple', __('Configuración', 'envialo-simple'), __('Configuración', 'envialo-simple'), 'add_users', 'envialo-simple-configuracion', 'mostrarConfiguracion');
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

function mostrarFormSuscripcion() {

    if (!isset($_GET['idFormulario'])) {
        require_once (ENVIALO_DIR . "/paginas/form-suscripcion-listado.php");
    } else {
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
