<?php
  
/*
  Plugin Name: Bitcoin Donator Button Widget
  Plugin URI: 
  Description: Adds a Bitcoin Donate Button widget (with QR-code in a lightbox). 
  Author: Martin Liguori
  Version: 1.0
  Author URI: martin@infuy.com
*/

class Bitcoin_Donator_Button extends WP_Widget {

  private $root;
  
  public function __construct() {
    
    /* Widget settings. */
    $widget_ops = array(
      'classname' => 'bdbw_class', 
      'description' => 'Allows to display a bitcoin donate button widget.');

    /* Widget control settings. */
    $control_ops = array(
      'width' => 250, 
      'height' => 250, 
      'id_base' => 'bdbw-widget');

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'bootstrap-js', plugins_url('js/bootstrap.min.js', __FILE__) , array(), '1.0.0', true );
    wp_enqueue_script( 'bootstrap-lightbox-js', plugins_url('js/bootstrap-lightbox.min.js', __FILE__) , array(), '1.0.0', true );

    wp_enqueue_style( 'bootstrap-css', plugins_url('css/bootstrap.min.css', __FILE__) );
    wp_enqueue_style( 'bootstrap-lightbox-css', plugins_url('css/bootstrap-lightbox.min.css', __FILE__) );
    wp_enqueue_style( 'custom-css', plugins_url('css/custom.css', __FILE__) );

    parent::__construct('bdbw-widget','Bitcoin donate button',$widget_ops, $control_ops);  
    
  }
  
  function form($instance) {

    /* Set up some default widget settings. */
    $defaults = array(
        'bdbw_address'  =>  '15miZDs3cMtpXuxZukqAC5GasGqaZm3vHn',
        'bdbw_title'  =>  'Use this address to show your support');
    $instance = wp_parse_args( (array) $instance, $defaults ); 
    ?>
      <style>
        .bdbw_inputs {    
          border-spacing: 0;
        width: 100%;
        clear: both;
        margin: 0;
      }
      </style>
      <p>
        <label for="<?php echo $this->get_field_id('bdbw_address'); ?>">Your Bitcoin Address:</label>
        <input type="text" name="<?php echo $this->get_field_name('bdbw_address') ?>" id="<?php echo $this->get_field_id('bdbw_address') ?> " value="<?php echo $instance['bdbw_address'] ?>" class="bdbw_inputs">
      </p>
      
      <p>
        <label for="<?php echo $this->get_field_id('bdbw_title'); ?>">Popover Title Text:</label>
        <input type="text" name="<?php echo $this->get_field_name('bdbw_title') ?>" id="<?php echo $this->get_field_id('bdbw_title') ?> " value="<?php echo $instance['bdbw_title'] ?>" class="bdbw_inputs">
      </p>

    <?php
  }

  function update ($new_instance, $old_instance) {
    $instance = $old_instance;
    
    $instance['bdbw_address']=$new_instance['bdbw_address'];
    $instance['bdbw_title'] = $new_instance['bdbw_title'];

    return $instance;
  }

  function widget ($args,$instance) {

    include_once("phpqrcode.php");

    extract($args);
    $bdbw_address = $instance['bdbw_address'];
    $bdbw_title   = $instance['bdbw_title'];

    $justimage = "<img style='width:150px;text-align:center;' id='bdbw_button' src=".plugins_url('img/button_bitcoin.png', __FILE__)."/>";
    $filename = uniqid().".png";
    $plugin_abs_path = plugin_dir_path(__FILE__);
    $plugin_abs_path .= 'tmp/'.$filename;

    $qrimage = QRcode::png($bdbw_address, $plugin_abs_path, QR_ECLEVEL_L, 20);
    $toutput_img = "<a href='#demoLightbox' data-toggle='lightbox'>
                      ".$justimage."
                    </a>";

    $output_init = '<div id="demoLightbox" class="lightbox hide fade"  tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="lightbox-content">
                        <img src="'.plugins_url('tmp/'.$filename, __FILE__).'"/>
                        <div class="lightbox-caption"><p>'.$bdbw_title.'</p></div>
                        <div class="btw_address">
                          '.$bdbw_address.'
                        </div>
                      </div>
                    </div>';

         
    //print the widget for the sidebar
    echo $before_widget;
    echo $toutput_img.$output_init;
    echo $after_widget;
  }
  
}

function bdbw_load_widgets() {
  register_widget('Bitcoin_Donator_Button');
}

add_action('widgets_init', 'bdbw_load_widgets');

?>