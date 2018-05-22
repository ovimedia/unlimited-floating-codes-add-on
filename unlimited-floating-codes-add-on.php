<?php
/*
Plugin Name: Unlimted Floating Codes Add on
Description: Generate many floating codes to display any content.
Author: Ovi García - ovimedia.es
Author URI: http://www.ovimedia.es/
Text Domain: unlimited-floating-codes
Version: 0.3
Plugin URI: http://www.ovimedia.es/
*/

if ( ! defined( 'ABSPATH' ) ) exit; 

if ( ! class_exists( 'unlimited_floating_codes' ) ) 
{
	class unlimited_floating_codes 
    {
        function __construct() 
        {   
            add_action( 'init', array( $this, 'ufc_load_languages') );
            add_action( 'init', array( $this, 'ufc_save_options') );
            add_action( 'save_post', array( $this, 'ufc_save_data') );
            add_action( 'add_meta_boxes', array( $this, 'ufc_init_metabox') ); 
            add_action( 'admin_print_scripts', array( $this, 'ufc_admin_js_css') );
            add_action( 'wp_head', array($this, 'ufc_front_js_css'));
            add_action( 'wp_footer', array( $this, 'ufc_load_floating_codes') );
            add_shortcode( 'ufc_show_content', array( $this, 'ufc_load_button_content') );
            add_action( 'vc_before_init',  array( $this, 'ufc_vc_button_content') );
        }
                
        public function ufc_load_languages() 
        {
            load_plugin_textdomain( 'unlimited-floating-codes', false, '/'.basename( dirname( __FILE__ ) ) . '/languages/' ); 
        }
    
        public function ufc_init_metabox()
        {
            add_meta_box( 'zone-code2', translate( 'Floating Code Options', 'unlimited-codes' ), 
                array( $this, 'ufc_meta_options'), 'code', 'side', 'low' );
        }
                       


        public function ufc_admin_js_css() 
        {
            if(get_post_type(get_the_ID()) == "code")
            {
                wp_register_style( 'custom_ufcodes_admin_css', WP_PLUGIN_URL. '/'.basename( dirname( __FILE__ ) ).'/css/ufc_admin_style.css', false, '1.0.0' );

                wp_enqueue_style( 'custom_ufcodes_admin_css' );

                wp_enqueue_script( 'ufcodes_script', WP_PLUGIN_URL. '/'.basename( dirname( __FILE__ ) ).'/js/ufc_admin.js', array('jquery') );
            }
        }

        public function ufc_front_js_css() 
        {
            wp_register_style( 'ufc_style_css', WP_PLUGIN_URL. '/'.basename( dirname( __FILE__ ) ).'/css/ufc_style.css', false, '1.0.0' );
            wp_enqueue_style( 'ufc_style_css' );

            wp_enqueue_script( 'wop_front_script', WP_PLUGIN_URL. '/'.basename( dirname( __FILE__ ) ).'/js/ufc.js', array('jquery') );
        }

        public function ufc_meta_options()
        { 
            global $wpdb;

            ?>

            <div class="meta_div_codes">         
                <p>
                    <label for="ufc_type">
                        <?php echo translate( 'Code Type:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p>
                    <select id="ufc_type" name="ufc_type">
                        <option value="neither" <?php if(get_post_meta( get_the_ID(), 'ufc_type', true) == "neither") echo ' selected="selected" '; ?> >
                            <?php echo translate( 'Neither', 'unlimited-floating-codes' ) ?>
                        </option>
                        <option value="button" <?php if(get_post_meta( get_the_ID(), 'ufc_type', true) == "button") echo ' selected="selected" '; ?> >
                            <?php echo translate( 'Button', 'unlimited-floating-codes' ) ?>
                        </option>
                        <option value="content" <?php if(get_post_meta( get_the_ID(), 'ufc_type', true) == "content") echo ' selected="selected" '; ?> >
                            <?php echo translate( 'Content', 'unlimited-floating-codes' ) ?>
                        </option>
                        <option value="popup" <?php if(get_post_meta( get_the_ID(), 'ufc_type', true) == "popup") echo ' selected="selected" '; ?> >
                            <?php echo translate( 'Popup', 'unlimited-floating-codes' ) ?>
                        </option>
                    </select>
                </p>
                <p class="content_option button_option">
                    <label for="ufc_location">
                        <?php echo translate( 'Code Location:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="content_option button_option">
                    <select id="ufc_location" name="ufc_location" >

                        <?php           

                            $positions = array("top", "bottom", "left", "right");

                            for ($x = 0; $x < count($positions); $x++) 
                            { 
                                echo "<option value='".$positions[$x]."' ";                             

                                if(get_post_meta( get_the_ID(), 'ufc_location', true) == $positions[$x]) 
                                    echo ' selected="selected" '; 

                                echo ">".translate( ucfirst ($positions[$x]), 'unlimited-floating-codes' )."</option>"; 

                            } 

                        ?>
                    </select>
                </p>
                <p class="content_option button_option">
                    <label for="ufc_position">
                        <?php echo translate( 'Code Position:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="content_option button_option">
                    <input type="text" placeholder="<?php echo translate( 'In px or %', 'unlimited-floating-codes' ); ?>" 
                    value='<?php echo get_post_meta( get_the_ID(), 'ufc_position', true); ?>' 
                    id="ufc_position" name="ufc_position" />
                </p>
                <p class="content_option popup_option">
                    <label for="ufc_width">
                        <?php echo translate( 'Code Width:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="content_option popup_option">
                   <input type="text" placeholder="<?php echo translate( 'In px or %', 'unlimited-floating-codes' ); ?>" 
                   value='<?php echo get_post_meta( get_the_ID(), 'ufc_width', true); ?>' 
                   id="ufc_width" name="ufc_width" />
                </p>
                <p class="content_option popup_option">
                    <label for="ufc_height">
                        <?php echo translate( 'Code Height:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="content_option popup_option">
                   <input type="text" placeholder="<?php echo translate( 'In px or %', 'unlimited-floating-codes' ); ?>" 
                   value='<?php echo get_post_meta( get_the_ID(), 'ufc_height', true); ?>' 
                   id="ufc_height" name="ufc_height" />
                </p>
                <p class="button_option content_option popup_option">
                    <label for="ufc_responsive">
                        <?php echo translate( 'Hide in mobile:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="button_option content_option popup_option">
                    <select id="ufc_responsive" name="ufc_responsive">
                        <option value="0" <?php if(get_post_meta( get_the_ID(), 'ufc_responsive', true) == 0) echo ' selected="selected" '; ?> >
                            <?php echo translate( 'No', 'unlimited-floating-codes' ) ?>
                        </option>
                        <option value="1" <?php if(get_post_meta( get_the_ID(), 'ufc_responsive', true) == 1) echo ' selected="selected" '; ?> >
                            <?php echo translate( 'Yes', 'unlimited-floating-codes' ) ?>
                        </option>
                    </select>
                </p>

                <p class="button_option">
                    <label for="ufc_rotation">
                        <?php echo translate( 'Rotate Button:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="button_option">
                    <select id="ufc_rotation" name="ufc_rotation">
                        <option value="0" <?php if(get_post_meta( get_the_ID(), 'ufc_rotation', true) == 0) echo ' selected="selected" '; ?> >
                            <?php echo translate( 'No', 'unlimited-floating-codes' ) ?>
                        </option>
                        <option value="1" <?php if(get_post_meta( get_the_ID(), 'ufc_rotation', true) == 1) echo ' selected="selected" '; ?> >
                            <?php echo translate( 'Yes', 'unlimited-floating-codes' ) ?>
                        </option>
                    </select>
                </p>
                <p class="content_option">
                    <label for="ufc_delay">
                        <?php echo translate( 'Delay of the appearance:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="content_option">
                   <input type="text" placeholder="<?php echo translate( 'In seconds', 'unlimited-floating-codes' ); ?>" 
                   value='<?php echo get_post_meta( get_the_ID(), 'ufc_delay', true); ?>' 
                   id="ufc_delay" name="ufc_delay" />
                </p>      

                 <p class="content_option">
                    <label for="ufc_contentbtn">
                        <?php echo translate( 'Button to show content:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="content_option">
                   <input type="text" readonly 
                   value='[ufc_show_content id="<?php echo get_the_ID();  ?>" text="Show"]'  />
                </p>           
                  
            </div>
            
            <?php
        }

        public function ufc_save_data( $post_id )
        {
            if ( "code" != get_post_type($post_id) || current_user_can("administrator") != 1 || !isset($_REQUEST['uc_validate_data'])) return;
            
            update_post_meta( $post_id, 'ufc_type',sanitize_text_field( $_REQUEST["ufc_type"]));

            if(sanitize_text_field( $_REQUEST["ufc_type"]) != "popup")
                update_post_meta( $post_id, 'ufc_location',sanitize_text_field( $_REQUEST["ufc_location"]));
            else
                update_post_meta( $post_id, 'ufc_location', ""); 

            if(sanitize_text_field( $_REQUEST["ufc_type"]) != "popup")
                update_post_meta( $post_id, 'ufc_position',sanitize_text_field( $_REQUEST["ufc_position"]));
            else
                update_post_meta( $post_id, 'ufc_position', ""); 

            if(sanitize_text_field( $_REQUEST["ufc_type"]) != "button")
                update_post_meta( $post_id, 'ufc_width',sanitize_text_field( $_REQUEST["ufc_width"]));
            else
                update_post_meta( $post_id, 'ufc_width', ""); 
        
            if(sanitize_text_field( $_REQUEST["ufc_type"]) != "button")
                update_post_meta( $post_id, 'ufc_height',sanitize_text_field( $_REQUEST["ufc_height"]));
            else
                update_post_meta( $post_id, 'ufc_height', ""); 
            
            if(sanitize_text_field( $_REQUEST["ufc_type"]) == "button")
                update_post_meta( $post_id, 'ufc_rotation', intval($_REQUEST["ufc_rotation"])); 
            else
                update_post_meta( $post_id, 'ufc_rotation', ""); 

            if(sanitize_text_field( $_REQUEST["ufc_type"]) == "content")
                update_post_meta( $post_id, 'ufc_delay', intval($_REQUEST["ufc_delay"])); 
            else
                update_post_meta( $post_id, 'ufc_delay', ""); 

            update_post_meta( $post_id, 'ufc_responsive', intval($_REQUEST["ufc_responsive"]));  
        }

        public function ufc_load_floating_codes()
        {
            $args = array(
                'posts_per_page'   => -1,
                'meta_key'         => 'uc_order_code',
                'orderby'          => 'meta_value_num',
                'order'            => 'ASC',
                'post_type'        => 'code',
                'post_status'      => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'ufc_type',
                        'value'  =>  array('button','content', 'popup')
                    )
                )
            );

            $codes = get_posts( $args );

            foreach ( $codes as $code )
            {
                $post_type = get_post_meta( $code->ID, 'uc_post_type_id' );
                $post_id = get_post_meta( $code->ID, 'uc_post_code_id');
                $exclude_post_id = get_post_meta( $code->ID, 'uc_exclude_post_code_id'); 
                
                if($this->check_wpml_languages($code->ID))
                    if(in_array("all", $post_type[0]) || in_array(get_post_type(get_the_id()), $post_type[0]))
                            if(in_array(get_the_id(), $post_id[0]) || in_array(-1, $post_id[0]) && !in_array(get_the_id(), $exclude_post_id[0] ))
                                $this->load_content_code($code);    
            } 
        
        }

        public function load_content_code($code)
        {
            $type = get_post_meta( $code->ID, 'ufc_type', true);
            $location =  get_post_meta( $code->ID, 'ufc_location', true);
            $position =  get_post_meta( $code->ID, 'ufc_position', true);
            $width = get_post_meta( $code->ID, 'ufc_width', true);
            $height = get_post_meta( $code->ID, 'ufc_height', true);
            $rotated = get_post_meta( $code->ID, 'ufc_rotation', true);
            $delay = get_post_meta( $code->ID, 'ufc_delay', true);
            $responsive = get_post_meta( $code->ID, 'ufc_responsive', true);

            $mobile = $style = $class = "";

            if($responsive == 1)
                $class .= " ufc_hide_mobile ";

            if($type == "popup")
            {
                if(substr($width,-2) == "px")
                    $style .= "margin-left:-".(substr($width, 0, -2) / 2).substr($width,-2).";";
                else
                    $style .= "margin-left:-".(substr($width, 0, -1) / 2).substr($width,-1).";";

                if(substr($height,-2) == "px")
                    $style .= "margin-top:-".(substr($height, 0, -2) / 2).substr($height,-2).";";
                else
                    $style .= "margin-top:-".(substr($height, 0, -1) / 2).substr($height,-1).";";
            }

            if($type == "button")
            {
                $style .= $location .": 0px;";

                if($location == "top" || $location == "bottom")
                    $style .= "left: ".$position.";";
                else
                    $style .= "top: ".$position.";";

                $rotation  = $float = "";

                if($location == "top" || $location == "bottom" || $rotated == 1)
                    $style .= "float: left;";

                if($rotated == 1)
                {
                    $class .= " ".$location."_rotated ";
                }  
            }    
            else
            {
                $style .= "width:".$width.";";
                $style .= "height:".$height.";";
            }

            if($type == "content")
            {
                if($location == "top" || $location == "bottom")
                    $style .= $location.":-".$height.";";
                else
                    $style .= $location.":-".$width.";";

                if($location == "top" || $location == "bottom")
                    $style .= "left: ".$position.";";
                else
                    $style .= "top: ".$position.";";

                $style .= "-webkit-transition:". $location. " ".$delay."s;";
                $style .= "transition:". $location. " ".$delay."s;";

                $class .= " ufc_hide_".$code->ID;

                echo "<style>.ufc_show_".$code->ID."{".$location.": 0px !important;}";
                
                if($location == "top" || $location == "bottom")
                    echo ".ufc_hide_".$code->ID."{".$location."-: ".$height." !important;}";
                else
                    echo ".ufc_hide_".$code->ID."{".$location."-: ".$width." !important;}";
                    
                echo "</style>";
            }


            echo "<div id='ufc_content_".$code->ID."' class='ufc_".$type." ".$class."' style='".$style."' >".do_shortcode($code->post_content)."</div>";
            
        }

        public function check_wpml_languages($code_id)
        {
            if ( function_exists('icl_object_id') )  
            {
                $wpml_languages = get_post_meta( $code_id, 'uc_wpml_languages_load' );
                
                if(in_array("all", $wpml_languages[0]) || in_array(ICL_LANGUAGE_CODE, $wpml_languages[0]) )
                    return true;
                else
                    return false;
            }
            
            return true; 
        }

        public function ufc_load_button_content($atts )
        {
            return "<span class='ufc_associated_content ".$atts['class']."'>".$atts['text']."<input type='hidden' value='".$atts['id']."' class='ufc_associated' /></span>";
        }

        public function ufc_vc_button_content() 
        {
            $content_code = array();

            $args = array(
                'posts_per_page'   => -1,
                'orderby'          => 'title',
                'order'            => 'ASC',
                'post_type'        => 'code',
                'meta_key'         => "ufc_type",
                'meta_value'       => 'content',
                'post_status'      => 'publish'
            );

            $codes = get_posts( $args );

            foreach ( $codes as $code )
            {
                $content_code[$code->post_title] =  $code->ID;
            } 

            vc_map( array(
                "name" => translate( "Button floating content", 'unlimited-floating-codes' ),
                "base" => "ufc_show_content",
                "class" => "",
                "icon" => WP_PLUGIN_URL. '/unlimited-codes/img/ufc_icon.png',
                "category" => __( "Unlimited Codes", "js_composer"),
                'admin_enqueue_js' => array(get_template_directory_uri().'/vc_extend/bartag.js'),
                'admin_enqueue_css' => array(get_template_directory_uri().'/vc_extend/bartag.css'),
                "params" => array(              
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => translate( "Floating code to show:",'unlimited-floating-codes' ),
                        "param_name" => "id",
                        "value" => $content_code,
                        "description" => translate( "Select the code to show on button click.", 'unlimited-floating-codes' )
                    ),
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => translate( "Button text:", 'unlimited-floating-codes' ),
                        "param_name" => "text",
                        "description" => translate( "Type the button text to show.", 'unlimited-floating-codes' ) 
                    ),
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => translate( "CSS Class:", "unlimited-codes" ),
                        "param_name" => "class",
                        "description" => translate( "Select a CSS class.", "unlimited-codes" )
                    )
                )
                
            ) );
        }
    }
}

$GLOBALS['unlimited_floating_codes'] = new unlimited_floating_codes();  
                       
?>