<?php
/*
Plugin Name: Unlimted Floating Codes Add on
Description: Generate many floating codes to display any content.
Author: Ovi García - ovimedia.es
Author URI: http://www.ovimedia.es/
Text Domain: unlimited-floating-codes
Version: 0.1
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
            add_action( 'init', array( $this, 'ufc_init_taxonomy'));
            add_action( 'admin_print_scripts', array( $this, 'ufc_admin_js_css') );
            add_action( 'floating_group_add_form_fields',  array( $this, 'add_feature_group_field'), 10, 2 );
            add_action( 'floating_group_edit_form_fields',  array( $this, 'edit_feature_group_field'), 10, 2 );
            add_action( 'created_floating_group', array( $this, 'ufc_save_floating_group_data'), 10, 2 );
            add_action( 'edited_floating_group', array( $this, 'ufc_save_floating_group_data'), 10, 2 );
            add_filter( 'manage_edit-floating_group_columns',  array( $this, 'ufc_add_floating_group_columns') );
            add_filter( 'manage_floating_group_custom_column', array( $this, 'ufc_add_floating_group_column_content'), 10, 3 );

            add_action( 'wp_head', array($this, 'ufc_front_js_css'));
            add_action( 'wp_footer', array( $this, 'ufc_load_floating_codes') );
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
                       

        public function ufc_init_taxonomy()
        {    
            $labels = array(
                'name' => translate( 'Floating Groups', 'unlimited-floating-codes' ),
                'singular_name' => translate( 'Floating Group', 'unlimited-floating-codes' ),
                'search_items' =>  translate( 'Search Floating Groups', 'unlimited-floating-codes' ),
                'all_items' => translate( 'Show all Floating Groups', 'unlimited-floating-codes' ),
                'parent_item' => translate( 'Floating Group Parent', 'unlimited-floating-codes' ),
                'edit_item' => translate( 'Edit Floating Group', 'unlimited-floating-codes' ),
                'update_item' => translate( 'Update Floating Group', 'unlimited-floating-codes' ),
                'add_new_item' =>translate( 'Add Floating Group', 'unlimited-floating-codes' ),
                'new_item_name' => translate( 'Floating Group Name', 'unlimited-floating-codes' ),
            );
            register_taxonomy( 'floating_group', array( 'code' ), 
            array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'floating_group' ),
            ));
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

            wp_enqueue_script( 'wop_front_script', WP_PLUGIN_URL. '/'.basename( dirname( __FILE__ ) ).'/js/ufc_style.js', array('jquery') );
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
                    </select>
                </p>
                <p>
                    <label for="ufc_width">
                        <?php echo translate( 'Code Width:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p>
                   <input type="text" placeholder="<?php echo translate( 'In px or %', 'unlimited-floating-codes' ); ?>" 
                   value='<?php echo get_post_meta( get_the_ID(), 'ufc_width', true); ?>' 
                   id="ufc_width" name="ufc_width" />
                </p>
                <p>
                    <label for="ufc_height">
                        <?php echo translate( 'Code Height:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p>
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
                <p class="button_option">
                    <label for="ufc_associated_uc">
                        <?php echo translate( 'Show Associated Code:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="button_option">
                    <select  id="ufc_associated_uc" name="ufc_associated_uc">
                        <option value="0" <?php if(get_post_meta( get_the_ID(), 'ufc_associated_uc', true) == 0) echo ' selected="selected" '; ?> >
                            <?php echo translate( 'Neither', 'unlimited-floating-codes' ) ?>
                        </option>
                        <?php

                            $args = array(
                                'posts_per_page'   => -1,
                                'orderby'          => 'title',
                                'order'            => 'ASC',
                                'exclude'          => get_the_ID(),
                                'post_type'        => 'code',
                                'meta_key'         => "ufc_type",
                                'meta_value'       => 'content',
                                'post_status'      => 'publish'
                            );

                            $codes = get_posts( $args );

                            foreach ( $codes as $code )
                            {
                                echo '<option ';

                                if( get_post_meta( get_the_ID(), 'ufc_associated_uc', true) == $code->ID )
                                    echo ' selected="selected" ';

                                echo ' value="'.$code->ID.'">'.ucfirst ($code->post_title).'</option>';
                            } 

                        ?>
                    </select>
                </p>
                <p class="content_option">
                    <label for="ufc_delay">
                        <?php echo translate( 'Delay of the appearance:', 'unlimited-floating-codes' ); ?>
                    </label>
                </p>
                <p class="content_option">
                   <input type="text" placeholder="<?php echo translate( 'In miliseconds', 'unlimited-floating-codes' ); ?>" 
                   value='<?php echo get_post_meta( get_the_ID(), 'ufc_delay', true); ?>' 
                   id="ufc_delay" name="ufc_delay" />
                </p>                
                  
            </div>
            
            <?php
        }

        public function ufc_save_data( $post_id )
        {
            if ( "code" != get_post_type($post_id) || current_user_can("administrator") != 1 || !isset($_REQUEST['uc_validate_data'])) return;
            
            update_post_meta( $post_id, 'ufc_type',sanitize_text_field( $_REQUEST["ufc_type"]));

            update_post_meta( $post_id, 'ufc_width',sanitize_text_field( $_REQUEST["ufc_width"]));
        
            update_post_meta( $post_id, 'ufc_height',sanitize_text_field( $_REQUEST["ufc_height"]));
            
            if(sanitize_text_field( $_REQUEST["ufc_type"]) == "button")
                update_post_meta( $post_id, 'ufc_rotation', intval($_REQUEST["ufc_rotation"])); 
            else
                update_post_meta( $post_id, 'ufc_rotation', ""); 
        
            if(sanitize_text_field( $_REQUEST["ufc_type"]) == "button")
                update_post_meta( $post_id, 'ufc_associated_uc', intval($_REQUEST["ufc_associated_uc"])); 
            else
                update_post_meta( $post_id, 'ufc_associated_uc', ""); 

            if(sanitize_text_field( $_REQUEST["ufc_type"]) == "content")
                update_post_meta( $post_id, 'ufc_delay', intval($_REQUEST["ufc_delay"])); 
            else
                update_post_meta( $post_id, 'ufc_delay', ""); 
        }

        
        public function add_feature_group_field($taxonomy) 
        {
            ?>
                <div class="form-field term-group">

                    <p>
                        <label for="ufc_floating_group_location"><?php echo translate( 'Floating Group Location:', 'unlimited-floating-codes' ); ?></label>

                        <select id="ufc_floating_group_location" name="ufc_floating_group_location" >

                            <?php           

                                $positions = array("top", "bottom", "left", "right");

                                for ($x = 0; $x < count($positions); $x++) 
                                { 
                                    echo "<option value='".$positions[$x]."' ";                             

                                    if(get_term_meta( $taxonomy->term_id, 'ufc_floating_group_location', true) == $positions[$x]) 
                                        echo ' selected="selected" '; 

                                    echo ">".translate( ucfirst ($positions[$x]), 'unlimited-floating-codes' )."</option>"; 

                                } 

                            ?>
                        </select>
                    </p>
                    <p>
                        <label for="ufc_floating_group_position">
                            <?php echo translate( 'Floating Group Position:', 'unlimited-floating-codes' ); ?>
                        </label>
                    </p>
                    <p>
                        <input type="text" placeholder="<?php echo translate( 'In px or %', 'unlimited-floating-codes' ); ?>" 
                        value='<?php echo get_term_meta( $taxonomy->term_id, 'ufc_floating_group_position', true); ?>' 
                        id="ufc_floating_group_position" name="ufc_floating_group_position" />
                    </p>
                </div>
                
            <?php
        }

        public function edit_feature_group_field($taxonomy) 
         {
            ?>
                <tr class="form-field term-group-wrap">
                    <th scope="row"><label for="ufc_floating_group_location"><?php echo translate( 'Floating Group Location:', 'unlimited-floating-codes' );  ?></label></th>
                    <td>
                        <select id="ufc_floating_group_location" name="ufc_floating_group_location" >

                            <?php           

                                $positions = array("top", "bottom", "left", "right");

                                for ($x = 0; $x < count($positions); $x++) 
                                { 
                                    echo "<option value='".$positions[$x]."' ";                             

                                    if(get_term_meta( $taxonomy->term_id, 'ufc_floating_group_location', true) == $positions[$x]) 
                                        echo ' selected="selected" '; 

                                    echo ">".translate( ucfirst ($positions[$x]), 'unlimited-floating-codes' )."</option>"; 

                                } 

                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="ufc_floating_group_position"><?php echo translate( 'Floating Group Position:', 'unlimited-floating-codes' );?></label></th>
                    <td>
                        <input type="text" placeholder="<?php echo translate( 'In px or %', 'unlimited-floating-codes' ); ?>" 
                        value='<?php echo get_term_meta( $taxonomy->term_id, 'ufc_floating_group_position', true); ?>' 
                        id="ufc_floating_group_position" name="ufc_floating_group_position" />
                    </td>
                </tr>
                
            <?php
        }

        public function ufc_save_floating_group_data( $term_id, $tt_id )
        {
            if( isset( $_REQUEST['ufc_floating_group_location'] ) && '' != $_REQUEST['ufc_floating_group_location'] )
            {
                update_term_meta( $term_id, 'ufc_floating_group_location', sanitize_text_field( $_REQUEST["ufc_floating_group_location"]) );
                update_term_meta( $term_id, 'ufc_floating_group_position', sanitize_text_field( $_REQUEST["ufc_floating_group_position"]) );
            }
        }

        public function ufc_add_floating_group_columns( $columns ){

            $columns = array(
                'cb' => '<input type="checkbox" />',
                'name' => __( 'Name' ), 
                'fg_location' => translate( 'Location', 'unlimited-floating-codes' ),
                'fg_position' => translate( 'Position', 'unlimited-floating-codes' ),
                'posts' => translate( 'Codes', 'unlimited-floating-codes' )
            );
            
            return $columns;
        }

        public function ufc_add_floating_group_column_content( $content, $column_name, $term_id )
        {
            switch( $column_name )
            {
                case 'fg_location':

                    echo ucfirst(get_term_meta( $term_id, 'ufc_floating_group_location', true ));

                break;

                case 'fg_position':

                    echo get_term_meta( $term_id, 'ufc_floating_group_position', true );

                break;
            }           
 
            return $content;
        }

        public function ufc_load_floating_codes()
        {
            $floating_groups = get_terms( array(
                'taxonomy' => 'floating_group',
                'hide_empty' => false,
            ) );

            foreach($floating_groups as $group)
            {
                $position = get_term_meta( $group->term_id, 'ufc_floating_group_position', true);
                $location = get_term_meta( $group->term_id, 'ufc_floating_group_location', true );

                $group_location = $location .": 0px;";

                if($location == "top" || $location == "bottom")
                    $group_position = "left: ".$position.";";
                else
                    $group_position = "top: ".$position.";";

                echo "<div class='ufc_group' style='".$group_location.$group_position."'>"; 

                $args = array(
                    'posts_per_page'   => -1,
                    'meta_key'         => 'uc_order_code',
                    'orderby'          => 'meta_value_num',
                    'order'            => 'ASC',
                    'post_type'        => 'code',
                    'post_status'      => 'publish',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'floating_group',
                            'field' => 'term_id',
                            'terms' => $group->term_id,
                        ),
                        array(
                            'meta_key' => 'ufc_type',
                            'meta_value'  =>  array('button','content')
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
                                    $this->load_content_code($code, $location, $position);    
                } 

                echo "</div>";
            }
        }

        public function load_content_code($code, $location, $position)
        {
            $type = get_post_meta( $code->ID, 'ufc_type', true);
            $width = get_post_meta( $code->ID, 'ufc_width', true);
            $height = get_post_meta( $code->ID, 'ufc_height', true);
            $rotated = get_post_meta( $code->ID, 'ufc_rotation', true);

            $rotation  = $float = "";
            $associated = 0;

            if($location == "top" || $location == "bottom" || $rotated == 1)
                $float = "float: left;";

            if($type == "button")
            {
                if($rotated  == 1)
                {
                    $rotation = "rotated";
                }

                $associated = get_post_meta( $code->ID, 'ufc_associated_uc', true);        
            }    

            echo "<div id='ufc_content_".$code->ID."' class='ufc_".$type." ".$location."_".$rotation."'  style='".$float."' >".
            do_shortcode($code->post_content);
            
            if($associated != 0) echo "<input type='hidden' value='".$associated ."' id='ufc_associated_'".$code->ID." />";
            
            echo "</div>";
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
    }
}

$GLOBALS['unlimited_floating_codes'] = new unlimited_floating_codes();  
                       
?>