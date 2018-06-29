<?php
/*
Plugin Name: Unlimted Floating Codes Add on
Description: Generate many floating codes to display any content.
Author: Ovi García - ovimedia.es
Author URI: http://www.ovimedia.es/
Text Domain: unlimited-floating-codes
Version: 0.6
Plugin URI: https://github.com/ovimedia/unlimited-floating-codes-add-on
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
            add_shortcode( 'ufc_hide_popup', array( $this, 'ufc_hide_popup_button') );
            
            add_action( 'vc_before_init',  array( $this, 'ufc_vc_button_content') );
            add_action( 'vc_before_init',  array( $this, 'ufc_vc_button_popup') );
            add_filter( 'manage_edit-code_columns', array( $this, 'ufc_edit_code_columns' )) ;
            add_action( 'manage_code_posts_custom_column', array( $this, 'ufc_manage_code_columns'), 10, 2 );
        }
                
        public function ufc_load_languages() 
        {
            load_plugin_textdomain( 'unlimited-floating-codes', false, '/'.basename( dirname( __FILE__ ) ) . '/languages/' ); 
        }
    
        public function ufc_init_metabox()
        {
            add_meta_box( 'zone-code2', translate( 'Floating Code Options', 'unlimited-floating-codes' ), 
                array( $this, 'ufc_meta_options'), 'code', 'side', 'low' );
        }
                       
        public function ufc_edit_code_columns( $columns ) 
        {
            unset($columns["order"]);
            unset($columns["date"]);
            unset($columns["shortcode"]);
            $columns["codetype"] = translate( 'Floating code type', 'unlimited-floating-codes' );
            $columns["date"]  = __( 'Date' );
            
            return $columns;
        }

        public function ufc_manage_code_columns( $column, $post_id ) 
        {
            if( $column == 'codetype')
                 echo translate( ucfirst(get_post_meta( $post_id, 'ufc_type', true)) , 'unlimited-floating-codes' );
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
                        <?php echo translate( 'Code Type', 'unlimited-floating-codes' ); ?>:
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
                            <?php echo translate( 'Content Block', 'unlimited-floating-codes' ) ?>
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

                            $positions = array("top", "bottom", "left", "right", "center");

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
                <p class="button_option popup_option">
                    <label for="ufc_scroll">
                        <?php echo translate( 'Scroll to show:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="button_option popup_option">
                   <input type="text" placeholder="<?php echo translate( 'Without px', 'unlimited-floating-codes' ); ?>" 
                   value='<?php echo get_post_meta( get_the_ID(), 'ufc_scroll', true); ?>' 
                   id="ufc_scroll" name="ufc_scroll" />
                </p>     

                <p class="popup_option">
                    <label for="ufc_fullpopup">
                        <?php echo translate( 'Show content overlay background:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="popup_option">
                    <select id="ufc_fullpopup" name="ufc_fullpopup">
                        <option value="0" <?php if(get_post_meta( get_the_ID(), 'ufc_fullpopup', true) == 0) echo ' selected="selected" '; ?> >
                            <?php echo translate( 'No', 'unlimited-floating-codes' ) ?>
                        </option>
                        <option value="1" <?php if(get_post_meta( get_the_ID(), 'ufc_fullpopup', true) == 1) echo ' selected="selected" '; ?> >
                            <?php echo translate( 'Yes', 'unlimited-floating-codes' ) ?>
                        </option>
                    </select>
                </p>   
 
                <p class="button_option content_option popup_option">
                    <label for="ufc_responsive">
                        <?php echo translate( 'Hide on:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="button_option content_option popup_option">
                    <select multiple="multiple"  id="ufc_responsive" name="ufc_responsive[]">
                        <?php $responsive = get_post_meta( get_the_ID(), 'ufc_responsive', true) ; print_r($responsive); ?>
                        <option value="ufc_hide_desktop" <?php if(in_array("ufc_hide_desktop", $responsive )) echo ' selected="selected" '; ?> >
                            <?php echo translate( 'Desktop', 'unlimited-floating-codes' ) ?>
                        </option>
                        <option value="ufc_hide_tablet" <?php if(in_array("ufc_hide_tablet", $responsive )) echo ' selected="selected" '; ?> >
                            <?php echo translate( 'Tablet', 'unlimited-floating-codes' ) ?>
                        </option>
                        <option value="ufc_hide_mobile" <?php if(in_array("ufc_hide_mobile", $responsive )) echo ' selected="selected" '; ?> >
                            <?php echo translate( 'Mobile', 'unlimited-floating-codes' ) ?>
                        </option>
                    </select>
                </p>
                 <p class="content_option">
                    <label for="ufc_contentbtn">
                        <?php echo translate( 'Button Shortcode to show content:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="content_option">
                   <input type="text" readonly 
                   value='[ufc_show_content id="<?php echo get_the_ID();  ?>" textshow="" texthide="" class=""]'  />
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

            if(sanitize_text_field( $_REQUEST["ufc_type"]) != "content")
                update_post_meta( $post_id, 'ufc_scroll', sanitize_text_field(str_replace("px", "",$_REQUEST["ufc_scroll"]))); 
            else
                update_post_meta( $post_id, 'ufc_scroll', "");           
                
            if(sanitize_text_field( $_REQUEST["ufc_type"]) == "popup")
                update_post_meta( $post_id, 'ufc_fullpopup', intval($_REQUEST["ufc_fullpopup"])); 
            else
                update_post_meta( $post_id, 'ufc_fullpopup', ""); 
                
            $hide_responsive = array();

            foreach( $_REQUEST['ufc_responsive'] as $hide)
            {
                if(wp_check_invalid_utf8( $hide, true ) != "")
                    $hide_responsive[] = sanitize_text_field($hide);
            }

            update_post_meta( $post_id, 'ufc_responsive', $hide_responsive);  
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
                $post_type = get_post_meta( $code->ID, 'uc_post_type_id', true );
                $post_id = get_post_meta( $code->ID, 'uc_post_code_id', true);
                $exclude_post_id = get_post_meta( $code->ID, 'uc_exclude_post_code_id', true); 
                
                if($this->check_wpml_languages($code->ID))
                    if(in_array("all", $post_type) || in_array(get_post_type(get_the_id()), $post_type))
                            if(in_array(get_the_id(), $post_id) || in_array(-1, $post_id) && !in_array(get_the_id(), $exclude_post_id ))
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
            $scroll = get_post_meta( $code->ID, 'ufc_scroll', true);
            $fullpopup = get_post_meta( $code->ID, 'ufc_fullpopup', true);

            $mobile = $style = $class = "";

            $class .= " ufc_type_".$type." ";

       

            foreach($responsive as $device)
                $class .= " ".$device." ";
    
            if($scroll  != "")
                $class .= " ufc_hide_scroll ";

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

                if($popup == 0)
                    $class .= " ufc_popup_btn_hide ";
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
                $class .= " ufc_content_".$location." ";
                if($location != "center")
                {
                    if($location == "top" || $location == "bottom")
                        $style .= $location.":-".$height.";";
                    else
                        $style .= $location.":-".$width.";";
                }
                else
                {
                    $style .= "left:50%;";

                    if(substr($width,-2) == "px")
                        $style .= "margin-left:-".(substr($width, 0, -2) / 2).substr($width,-2).";";
                    else
                        $style .= "margin-left:-".(substr($width, 0, -1) / 2).substr($width,-1).";";
                }

                if($location == "top" || $location == "bottom")
                    $style .= "left: ".$position.";";
                else
                    $style .= "top: ".$position.";";

                $style .= "-webkit-transition:". $location. " ".$delay."s;";
                $style .= "transition:". $location. " ".$delay."s;";

                $class .= " ufc_hide_".$code->ID;

                echo "<style>";
                if($location != "center")
                {
                    echo ".ufc_show_".$code->ID."{".$location.": 0px !important;}";
                    
                    if($location == "top" || $location == "bottom")
                        echo ".ufc_hide_".$code->ID."{".$location."-: ".$height." !important;}";
                    else
                        echo ".ufc_hide_".$code->ID."{".$location."-: ".$width." !important;}";
                }
                else
                {
                    echo ".ufc_show_".$code->ID."{visibility: visible !important; opacity: 1 !important;}";
                    echo ".ufc_hide_".$code->ID."{visibility: hidden !important; opacity: 0 !important;}";
                    $style .= "-webkit-transition: visibility  ".$delay."s, opacity ".$delay."s; ";
                    $style .= "transition: visibility  ".$delay."s, opacity ".$delay."s; ";
                }

                echo "</style>";
            }

            if($fullpopup == 1) echo "<div id='background_popup_".$code->ID."' class='background_popup'></div>";

            echo "<div id='ufc_content_".$code->ID."' class='ufc_".$type." ".$class."' style='".$style."' >";
            
            echo do_shortcode($code->post_content);

            if($scroll != "") echo "<input type='hidden' value='".$scroll."' class='ufc_scroll_code' />";

            if($type == "popup") echo "<input type='hidden' value='".$code->ID."' class='ufc_popup_id' />
            <span class='ufc_popup_btn ufc_cross_btn'>
            <img src='".WP_PLUGIN_URL. '/'.basename( dirname( __FILE__ ) ).'/img/cancel.png'."' )></span>";
            
            echo "</div>";

            $style = "";
            $result = $code->post_content;
            $pos = 0;

            $total = substr_count($result, 'css=".', $pos);
            
            $style .= "<style>";

            if($total > 0)
            {
                for($x=0; $x < $total; $x++)
                {
                    $pos = strpos($result, 'css=".', $pos);
                    
                    $style .= substr($result, $pos + 5, strpos( $result, "}", $pos) - $pos - 4);

                    $pos++;
                }
            }
            
            $style .= get_post_meta( $code->ID, "_wpb_post_custom_css", true );
            $style .= "</style>";

            echo $style;
            
        }

        public function check_wpml_languages($code_id)
        {
            if ( function_exists('icl_object_id') )  
            {
                $wpml_languages = get_post_meta( $code_id, 'uc_wpml_languages_load', true );
                
                if(in_array("all", $wpml_languages) || in_array(ICL_LANGUAGE_CODE, $wpml_languages) )
                    return true;
                else
                    return false;
            }
            
            return true; 
        }

        public function ufc_load_button_content($atts )
        {   
            if($atts['imageshow'] == "")
                return "<p class='ufc_associated_content ".$atts['class']."'>
                <span class='btn_show'>".$atts['textshow']."</span>
                <span class='btn_hide'>".$atts['texthide']."</span>
                <input type='hidden' value='".$atts['id']."' class='ufc_associated' /></p>";
            else
                return "<p class='ufc_associated_content ".$atts['class']."'>
                <img src=".wp_get_attachment_url($atts['imageshow'], 'full')." class='btn_show' />
                <img src=".wp_get_attachment_url($atts['imagehide'], 'full')." class='btn_hide' />
                <input type='hidden' value='".$atts['id']."' class='ufc_associated' /></p>";
        }

        public function ufc_hide_popup_button($atts )
        {   
            return "<p class='ufc_popup_btn ".$atts['class']."'>".$atts['buttontext']."</p>";
        }
        
        public function ufc_vc_button_popup() 
        {                           
            vc_map( array("name" => translate( "Hide Popup button", 'unlimited-floating-codes' ),
                    "base" => "ufc_hide_popup",
                    "class" => "",
                    "icon" => WP_PLUGIN_URL. '/unlimited-codes/img/ufc_icon.png',
                    "category" => __( "Unlimited Codes", "js_composer"),
                    'admin_enqueue_js' => array(get_template_directory_uri().'/vc_extend/bartag.js'),
                    'admin_enqueue_css' => array(get_template_directory_uri().'/vc_extend/bartag.css'),
                    "params" => array(              
                        array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => translate( "Button text:", 'unlimited-floating-codes' ),
                            "param_name" => "buttontext",
                            "description" => translate( "Button text to hide Popup.", 'unlimited-floating-codes' ) 
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
                )
            );
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
                    "name" => translate( "Floating content button", 'unlimited-floating-codes' ),
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
                            "heading" => translate( "Text to show:", 'unlimited-floating-codes' ),
                            "param_name" => "textshow",
                            "description" => translate( "Button text to show floating code.", 'unlimited-floating-codes' ) 
                        ),
                        array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => translate( "Text to hide:", 'unlimited-floating-codes' ),
                            "param_name" => "texthide",
                            "description" => translate( "Button text to hide floating code.", 'unlimited-floating-codes' ) 
                        ),
                        array(
                            "type" => "attach_image",
                            "holder" => "div",
                            "class" => "",
                            "heading" => translate( "Image to show:", 'unlimited-floating-codes' ),
                            "param_name" => "imageshow",
                            "description" => translate( "Button image to show floating code.", 'unlimited-floating-codes' ) 
                        ),
                        array(
                            "type" => "attach_image",
                            "holder" => "div",
                            "class" => "",
                            "heading" => translate( "Image to hide:", 'unlimited-floating-codes' ),
                            "param_name" => "imagehide",
                            "description" => translate( "Button image to hide  floating code.", 'unlimited-floating-codes' ) 
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
                    
                )
            );      
        }
    }
}

$GLOBALS['unlimited_floating_codes'] = new unlimited_floating_codes();  
                       
?>