<?php
/**
 * Common admin class.
 *
 * @since 2.0.0
 *
 * @package Optin_Monster
 * @author  Thomas Griffin
 */
class Optin_Monster_Common_Admin {

    /**
     * Holds the class object.
     *
     * @since 2.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 2.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 2.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Primary class constructor.
     *
     * @since 2.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Optin_Monster::get_instance();
        
        // Ensure the preview page can't be tampered with.
        add_action( 'admin_head', array( $this, 'preview_lockdown' ) );
        add_filter( 'page_row_actions', array( $this, 'row_actions' ), 10, 2 );

        // Handle any admin notices.
        add_action( 'admin_notices', array( $this, 'notices' ) );

        // Delete any optin association on attachment deletion. Also delete any extra cropped images.
        add_action( 'delete_attachment', array( $this, 'delete_optin_association' ) );
        add_action( 'delete_attachment', array( $this, 'delete_cropped_image' ) );

        // Ensure optin display is correct when trashing/untrashing optins.
        add_action( 'wp_trash_post', array( $this, 'trash_optin' ) );
        add_action( 'untrash_post', array( $this, 'untrash_optin' ) );

    }
    
    /**
     * Locks down the preview page so that it cannot be "accidentally" removed.
     *
     * @since 2.0.2
     */
    public function preview_lockdown() {
	    
	    if ( isset( get_current_screen()->post_type ) && 'page' == get_current_screen()->post_type ) {
	        $preview_id = get_option( 'optin_monster_preview_page' );
	        ?>
	        <script type="text/javascript">
	            jQuery(document).ready(function($){
	                $('#post-<?php echo $preview_id; ?> .check-column, #post-<?php echo $preview_id; ?> .column-shortcode, #post-<?php echo $preview_id; ?> .column-template, #post-<?php echo $preview_id; ?> .column-images').empty();
	                $('#post-<?php echo $preview_id; ?> .row-title').contents().unwrap();
	            });
	        </script>
	        <?php
	    }
	    
    }
    
    /**
     * Filter out unnecessary row actions from the Preview frame page.
     *
     * @since 1.0.0
     *
     * @param array $actions  Default row actions.
     * @param object $post    The current post object.
     * @return array $actions Amended row actions.
     */
    public function row_actions( $actions, $post ) {

        if ( isset( get_current_screen()->post_type ) && 'page' == get_current_screen()->post_type ) {
        	$preview_id = get_option( 'optin_monster_preview_page' );
        	if ( $post->ID == $preview_id ) {
        		unset( $actions['edit'] );
            	unset( $actions['inline hide-if-no-js'] );
            	unset( $actions['trash'] );
            	unset( $actions['view'] );
            }
        }

        return $actions;

    }

    /**
     * Outputs any OptinMonster admin notices not tied to optin actions.
     *
     * @since 2.0.0
     */
    public function notices() {

        if ( isset( $_GET['optin-monster-updated'] ) && $_GET['optin-monster-updated'] ) {
            echo '<div class="updated"><p>' . __( 'You have saved your OptinMonster settings successfully!', 'optin-monster' ) . '</p></div>';
        }

        if ( isset( $_GET['om_saved'] ) && $_GET['om_saved'] ) {
            echo '<div class="updated"><p>' . __( 'You have saved your optin campaign successfully!', 'optin-monster' ) . '</p></div>';
        }

        if ( isset( $_GET['om_primary'] ) && $_GET['om_primary'] ) {
            echo '<div class="updated"><p>' . sprintf( __( 'You have made %s the primary optin successfully!', 'optin-monster' ), '<strong>' . urldecode( $_GET['om_title'] ) . '</strong>' ) . '</p></div>';
        }

    }

    /**
     * Deletes the Optin_Monster optin association for the image being deleted.
     *
     * @since 2.0.0
     *
     * @param int $attach_id The attachment ID being deleted.
     */
    public function delete_optin_association( $attach_id ) {

        $has_optin = get_post_meta( $attach_id, '_om_has_optin', true );

        // Only proceed if the image is attached to any Optin_Monster optins.
        if ( ! empty( $has_optin ) ) {
            foreach ( (array) $has_optin as $post_id ) {
                // Remove the in_optin association.
                $in_optin = get_post_meta( $post_id, '_om_in_optin', true );
                if ( ! empty( $in_optin ) ) {
                    if ( ( $key = array_search( $attach_id, (array) $in_optin ) ) !== false ) {
                        unset( $in_optin[$key] );
                    }
                }

                update_post_meta( $post_id, '_om_in_optin', $in_optin );

                // Remove the image from the optin altogether.
                $optin_data = get_post_meta( $post_id, '_om_optin_data', true );
                if ( ! empty( $optin_data['optin'] ) ) {
                    unset( $optin_data['optin'][$attach_id] );
                }

                // Update the post meta for the optin.
                update_post_meta( $post_id, '_om_optin_data', $optin_data );

                // Flush necessary optin caches.
                Optin_Monster_Common::get_instance()->flush_optin_caches( $post_id, ( ! empty( $optin_data['config']['slug'] ) ? $optin_data['config']['slug'] : '' ) );
            }
        }

    }

    /**
     * Removes any extra cropped images when an attachment is deleted.
     *
     * @since 2.0.0
     *
     * @param int $post_id The post ID.
     * @return null        Return early if the appropriate metadata cannot be retrieved.
     */
    public function delete_cropped_image( $post_id ) {

        // Get attachment image metadata.
        $metadata = wp_get_attachment_metadata( $post_id );

        // Return if no metadata is found.
        if ( ! $metadata ) {
            return;
        }

        // Return if we don't have the proper metadata.
        if ( ! isset( $metadata['file'] ) || ! isset( $metadata['image_meta']['resized_images'] ) ) {
            return;
        }

        // Grab the necessary info to removed the cropped images.
        $wp_upload_dir  = wp_upload_dir();
        $pathinfo       = pathinfo( $metadata['file'] );
        $resized_images = $metadata['image_meta']['resized_images'];

        // Loop through and deleted and resized/cropped images.
        foreach ( $resized_images as $dims ) {
            // Get the resized images filename and delete the image.
            $file = $wp_upload_dir['basedir'] . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $dims . '.' . $pathinfo['extension'];

            // Delete the resized image.
            if ( file_exists( $file ) ) {
                @unlink( $file );
            }
        }

    }

    /**
     * Trash a optin when the optin post type is trashed.
     *
     * @since 2.0.0
     *
     * @param $id   The post ID being trashed.
     * @return null Return early if no optin is found.
     */
    public function trash_optin( $id ) {

        $optin = get_post( $id );

        // Flush necessary optin caches to ensure trashed optins are not showing.
        Optin_Monster_Common::get_instance()->flush_optin_caches( $id );

        // Return early if not an Optin_Monster optin.
        if ( 'optin-monster' !== $optin->post_type ) {
            return;
        }

        // Set the optin status to inactive.
        $optin_data = get_post_meta( $id, '_om_optin_data', true );
        if ( empty( $optin_data ) ) {
            return;
        }

        $optin_data['status'] = 'inactive';
        update_post_meta( $id, '_om_optin_data', $optin_data );

    }

    /**
     * Untrash a optin when the optin post type is untrashed.
     *
     * @since 2.0.0
     *
     * @param $id   The post ID being untrashed.
     * @return null Return early if no optin is found.
     */
    public function untrash_optin( $id ) {

        $optin = get_post( $id );

        // Flush necessary optin caches to ensure untrashed optins are showing.
        Optin_Monster_Common::get_instance()->flush_optin_caches( $id );

        // Return early if not an Optin_Monster optin.
        if ( 'optin-monster' !== $optin->post_type ) {
            return;
        }

        // Set the optin status to inactive.
        $optin_data = get_post_meta( $id, '_om_optin_data', true );
        if ( empty( $optin_data ) ) {
            return;
        }

        if ( isset( $optin_data['status'] ) ) {
            unset( $optin_data['status'] );
        }

        update_post_meta( $id, '_om_optin_data', $optin_data );

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 2.0.0
     *
     * @return object The Optin_Monster_Common_Admin object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Optin_Monster_Common_Admin ) ) {
            self::$instance = new Optin_Monster_Common_Admin();
        }

        return self::$instance;

    }

}

// Load the common admin class.
$optin_monster_common_admin = Optin_Monster_Common_Admin::get_instance();