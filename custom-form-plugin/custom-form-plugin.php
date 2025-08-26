<?php
/**
 * Plugin Name: Custom Form Plugin
 * Description: Configurable forms with topic sets and per-page forms.
 * Version: 1.0.0
 * Author: ChatGPT
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CFP_Plugin {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_admin_pages' ] );
        add_action( 'init', [ $this, 'register_shortcode' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_assets' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'public_assets' ] );
        add_action( 'init', [ $this, 'handle_form_submission' ] );
        add_filter( 'the_content', [ $this, 'append_form_to_content' ] );
    }

    public function register_admin_pages() {
        add_menu_page( 'Form Plugin', 'Form Plugin', 'manage_options', 'cfp_topic_sets', [ $this, 'topic_sets_page' ], 'dashicons-feedback' );
        add_submenu_page( 'cfp_topic_sets', 'Topic Sets', 'Topic Sets', 'manage_options', 'cfp_topic_sets', [ $this, 'topic_sets_page' ] );
        add_submenu_page( 'cfp_topic_sets', 'Form Settings', 'Form Settings', 'manage_options', 'cfp_form_settings', [ $this, 'form_settings_page' ] );
        add_submenu_page( 'cfp_topic_sets', 'Messages', 'Messages', 'manage_options', 'cfp_messages', [ $this, 'messages_page' ] );
    }

    public function admin_assets( $hook ) {
        if ( strpos( $hook, 'cfp_' ) !== false ) {
            wp_enqueue_style( 'cfp-admin', plugins_url( 'assets/admin.css', __FILE__ ) );
            wp_enqueue_script( 'cfp-admin', plugins_url( 'assets/admin.js', __FILE__ ), [ 'jquery' ], false, true );
        }
    }

    public function public_assets() {
        wp_enqueue_style( 'cfp-public', plugins_url( 'assets/public.css', __FILE__ ) );
        wp_enqueue_script( 'cfp-public', plugins_url( 'assets/public.js', __FILE__ ), [ 'jquery' ], false, true );
    }

    public function topic_sets_page() {
        include __DIR__ . '/includes/admin-topic-sets.php';
    }

    public function form_settings_page() {
        include __DIR__ . '/includes/admin-form-settings.php';
    }

    public function messages_page() {
        include __DIR__ . '/includes/admin-messages.php';
    }

    public function register_shortcode() {
        add_shortcode( 'cfp_form', [ $this, 'render_form_shortcode' ] );
    }

    public function render_form_shortcode( $atts, $content = null ) {
        ob_start();
        include __DIR__ . '/includes/form-render.php';
        return ob_get_clean();
    }

    public function handle_form_submission() {
        if ( isset( $_POST['cfp_form_submission'] ) ) {
            $page_id = intval( $_POST['cfp_page_id'] ?? 0 );
            $forms = get_option( 'cfp_forms', [] );
            $sets  = get_option( 'cfp_topic_sets', [] );
            $config = $forms[ $page_id ] ?? $forms['default'] ?? [];

            $topic_index = intval( $_POST['cfp_topic'] ?? -1 );
            $emails = [];
            if ( isset( $config['set'] ) && isset( $sets[ $config['set'] ]['topics'][ $topic_index ] ) ) {
                $emails = $sets[ $config['set'] ]['topics'][ $topic_index ]['emails'];
            }

            $data = array_map( 'sanitize_text_field', $_POST );
            $message = [
                'time' => current_time( 'mysql' ),
                'data' => $data,
            ];
            $messages = get_option( 'cfp_messages', [] );
            $messages[] = $message;
            update_option( 'cfp_messages', $messages );

            if ( ! empty( $emails ) ) {
                $body = '';
                foreach ( [ 'fio', 'company', 'email', 'phone', 'comment' ] as $field ) {
                    if ( ! empty( $data[ $field ] ) ) {
                        $body .= ucfirst( $field ) . ': ' . $data[ $field ] . "\n";
                    }
                }
                wp_mail( $emails, 'Form submission', $body );
            }

            wp_redirect( add_query_arg( 'cfp_sent', '1', wp_get_referer() ) );
            exit;
        }
    }

    public function append_form_to_content( $content ) {
        if ( is_singular() && ! is_admin() ) {
            $forms = get_option( 'cfp_forms', [] );
            $page_id = get_queried_object_id();
            if ( isset( $forms['default'] ) || isset( $forms[ $page_id ] ) ) {
                $content .= do_shortcode( '[cfp_form]' );
            }
        }
        return $content;
    }
}

new CFP_Plugin();
