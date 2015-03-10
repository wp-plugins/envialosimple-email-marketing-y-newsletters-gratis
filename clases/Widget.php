<?php

/**
 * Adds Foo_Widget widget.
 */
class WidgetEnvialo extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'widget', // Base ID
            __('Formulario Suscripción EnvialoSimple','envialo-simple'), // Name
            array( 'description' => __('Arrastra y Suelta este Bloque en la Barra de Widgets para Seleccionar tu Formulario de Suscripción.','envialo-simple'), ) // Args
        );

    }


    function WidgetEnvialo(){
         WidgetEnvialo:: __construct();
    }


    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
     function widget( $args, $instance ) {
        extract( $args );
        //$title = apply_filters( 'widget_title', $instance['title'] );
        $FormID = $instance['FormID'];
        $AdministratorID = $instance['AdministratorID'];

        echo $before_widget;

        if ( ! empty( $FormID ) )
            //echo $before_title . $title . $after_title;

            echo "<script type='text/javascript' src='http://v2.envialosimple.com/form/show/AdministratorID/{$AdministratorID}/FormID/{$FormID}/format/widget'></script>";

            echo $after_widget;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['FormID'] =strip_tags( $new_instance['FormID'] );
        $instance['AdministratorID'] =strip_tags( $new_instance['AdministratorID'] );

        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    function form( $instance ) {
        include_once("Formularios.php");
        include_once("EnvialoSimple.php");
        $ev = new EnvialoSimple();
        $ev->checkSetup("NoRedirect");
        $fo = new Formularios();

        $forms = $fo->listarFormularios();


        if ( isset( $instance['AdministratorID'] ) ) {
            $AdministratorID = $instance[ 'AdministratorID' ];
        }
        else {
            $AdministratorID = $forms['AdministratorID'];
        }
        ?>
        <p>
         <input type="hidden" name="<?php echo $this->get_field_name('AdministratorID'); ?>" value="<?php echo $AdministratorID; ?>" />
         <label for="<?php echo $this->get_field_id( 'FormID' ); ?>"><?php _e('Formularios Disponibles:','envialo-simple')?></label>
         <select id="<?php echo $this->get_field_id( 'FormID' ); ?>" name="<?php echo $this->get_field_name('FormID'); ?>">
             <option><?php _e("Seleccionar..", 'envialo-simple') ?></option>
             <?php
                $htmlOption = "";
                foreach ($forms['list']['item'] as $f) {
                    $selected = "";
                    if($f['FormID'] == $instance['FormID']){
                        $selected = "selected='selected'";
                    }
                    $htmlOption .= "<option {$selected} value='{$f['FormID']}'> {$f['Name']}</option>";
                }
                echo $htmlOption;
              ?>
         </select>
        </p>
        <?php
    }

} // class Foo_Widget
?>