<?php

/*

Plugin Name: Emagine Dev Test Plugin

Plugin URI: https://frankcollinsdev.com/

Description: Emagine Dev Test Slider

Version: 0.0.1

Author: Frank Collins

Author URI: https://frankcollinsdev.com/

*/

function var_error_log( $object=null ){
    ob_start();                    // start buffer capture
    var_dump( $object );           // dump the values
    $contents = ob_get_contents(); // put the buffer into a variable
    ob_end_clean();                // end capture
    error_log( $contents );        // log contents of the result of var_dump( $object )
}

class Emagine_Plugin{
    private $version = '0.0.1';
    function __construct(){
        add_action( 'init', array($this,'em_cards_post_type') );
        if(function_exists('acf_register_block_type')){
            add_action('acf/init',array($this,'register_emagine_block'));
            add_action('acf/init',array($this,'register_emagine_block_fields'));
        }
        add_action( 'add_meta_boxes', array($this,'add_redirect_url_metabox') );
        add_action( 'save_post', array($this,'save_redirect_url'));
        add_action('template_redirect',array($this,'em_card_redirect'));
        if(is_admin()){
            add_action( 'enqueue_block_editor_assets', array($this,'emagine_block_scripts'));
        }else{
            add_action( 'wp_enqueue_scripts',array($this,'emagine_block_scripts'));
        }
        
       

    }

    function em_cards_post_type(){
        $labels = array(
            'name'               => _x( 'Em Cards', 'Case Studies general name' ),
            'singular_name'      => _x( 'Em Card', 'Case Studies singular name' ),
            'add_new'            => __( 'Add New Em Card'),
            'add_new_item'       => __( 'Add New Em Card' ),
            'edit_item'          => __( 'Edit Em Card' ),
            'new_item'           => __( 'New Em Card' ),
            'all_items'          => __( 'All Em Cards' ),
            'view_item'          => __( 'View Em Card' ),
            'search_items'       => __( 'Search Em Cards' ),
            'not_found'          => __( 'No Em Cards found' ),
            'not_found_in_trash' => __( 'No Em Cards found in the Trash' ),
            'menu_name'          => 'Em Cards'
          );
          $args = array(
            'labels'        => $labels,
            'description'   => 'Add Em Card',
            'public'        => true,
            'menu_position' => 7,
            'supports'      => array( 'title', 'thumbnail', 'excerpt'),
            'show_in_rest' => true,
            'has_archive'   => true,
            'rewrite'            =>  array('slug' => 'cards',
                                            'with_front'=>FALSE
                                        ),
            'taxonomies'          => array('em_cards')
          );
          register_post_type( 'em_cards', $args );
    }

    function add_redirect_url_metabox(){
        add_meta_box('redirect_url',
        'Redirect URL',
        array($this,'redirect_metabox_html'),
        'em_cards',
        'side',
        'default');
    }

    function redirect_metabox_html($post){
        wp_nonce_field( 'redirect_url_nonce', 'redirect_url_nonce' );

        $value = get_post_meta( $post->ID, 'redirect_url', true );
        $value = htmlspecialchars( $value );

        echo "<input style='width:100%' value='{$value}' name='redirect_url' id='redirect_url'>";
    }

    function save_redirect_url( $post_id ) {

        if ( ! isset( $_POST['redirect_url_nonce'] ) ) {
            return;
        }
    
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['redirect_url_nonce'], 'redirect_url_nonce' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
    
        // Check permissions.
        if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'page') {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
        }else {
    
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }
    
        if ( ! isset( $_POST['redirect_url'] ) ) {
            return;
        }
    
        $redirect_url = filter_var($_POST['redirect_url'], FILTER_SANITIZE_URL);
        
        //save redirect url
        update_post_meta( $post_id, 'redirect_url', $redirect_url );
    }

    function em_card_redirect(){
        global $wp_query;
        $page_id = $wp_query->get_queried_object_id();
        if(get_post_type($page_id) == 'em_cards' && !is_admin()){
          $resource_url= get_post_meta($page_id,'redirect_url',true);
          $redirect_code =  302;
          wp_redirect($resource_url,$redirect_code);
          exit;
        }
    }

    function emagine_block_scripts(){
        wp_dequeue_script( 'jquery');

        wp_enqueue_script(
            'jquery',
            'https://code.jquery.com/jquery-3.6.0.min.js',
            array(),
            false,
            false
        );
        
        wp_enqueue_script(
            'slick-js',
            plugin_dir_url( __FILE__ ) . 'assets/slick/slick.js',
            array( 'wp-blocks', 'wp-element','jquery'),
            false,
            true
        );
        wp_enqueue_script(
            'emagine-block-js',
            plugin_dir_url( __FILE__ ) . 'assets/em-card-block.js',
            array( 'wp-blocks', 'wp-element','jquery'),
            false,
            true
        );

        wp_enqueue_style(
            'emagine-google-fonts',
            'https://fonts.googleapis.com/css2?family=Roboto:wght@400;900&display=swap',
            array(),
            false,
            'all'
        );

        wp_enqueue_style(
            'slick-css',
            plugin_dir_url( __FILE__ ) . 'assets/slick/slick.css',
            array( 'wp-edit-blocks' ),
            false,
            'all'
        );
        wp_enqueue_style(
            'emagine-block-css',
            plugin_dir_url( __FILE__ ) . 'assets/em-card-block.css',
            array( 'wp-edit-blocks' ),
            false,
            'all'
        );

    }

    function register_emagine_block(){
        $arr = array(
            'name'=>'em_cards_block',
            'title'=>'Em Cards',
            'description'=>'Add Em Card Block Slider',
            'render_template'=> plugin_dir_path( __FILE__ ) . 'templates/em_card_slider.php',
            'icon'=>'columns',
            'keywords'=>array('cards','emagine','slider')
        );

        acf_register_block_type($arr);
    }

    function register_emagine_block_fields(){
        $group_args = array(
            'key' => 'group_em_cards',
	        'title' => 'Em Card Slider',
            'fields' => array (),
            'location' => array (
                array (
                    array (
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/em-cards-block',
                    ),
                ),
            )
        );
        acf_add_local_field_group($group_args);
        acf_add_local_field(array(
            'key'=>'field_cards',
            'label'=>'Cards',
            'name'=>'em_cards',
            'type'=>'repeater',
            'parent'=>'group_em_cards'
        ));
        acf_add_local_field(array(
            'key' => 'field_card_post',
            'label' => 'Card',
            'name' => 'em_card',
            'post_type' => 'em_cards',
            'parent' => 'field_cards',
            'type'=>'post_object',
            'required' => 1,
        ));
        acf_add_local_field(array(
            'key'=>'field_show_dots',
            'label'=>'Show Dots?',
            'name'=>'show_dots',
            'type'=>'true_false',
            'parent'=>'group_em_cards'
        ));
    }
}

new Emagine_Plugin();