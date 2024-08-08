<?php

// includes/class-mantracal-cpt.php

class MantraCal_CPT {
    public static function register() {
        add_action('init', array(__CLASS__, 'register_post_type'));
        add_action('add_meta_boxes', array(__CLASS__, 'add_event_metaboxes'));
        add_action('save_post', array(__CLASS__, 'save_event_details'));
    }

    public static function register_post_type() {
        $labels = array(
            'name'                  => _x('Events', 'Post Type General Name', 'mantracal'),
            'singular_name'         => _x('Event', 'Post Type Singular Name', 'mantracal'),
            'menu_name'             => __('Events', 'mantracal'),
            'name_admin_bar'        => __('Event', 'mantracal'),
            'archives'              => __('Event Archives', 'mantracal'),
            'attributes'            => __('Event Attributes', 'mantracal'),
            'parent_item_colon'     => __('Parent Event:', 'mantracal'),
            'all_items'             => __('All Events', 'mantracal'),
            'add_new_item'          => __('Add New Event', 'mantracal'),
            'add_new'               => __('Add New', 'mantracal'),
            'new_item'              => __('New Event', 'mantracal'),
            'edit_item'             => __('Edit Event', 'mantracal'),
            'update_item'           => __('Update Event', 'mantracal'),
            'view_item'             => __('View Event', 'mantracal'),
            'view_items'            => __('View Events', 'mantracal'),
            'search_items'          => __('Search Event', 'mantracal'),
            'not_found'             => __('Not found', 'mantracal'),
            'not_found_in_trash'    => __('Not found in Trash', 'mantracal'),
            'featured_image'        => __('Featured Image', 'mantracal'),
            'set_featured_image'    => __('Set featured image', 'mantracal'),
            'remove_featured_image' => __('Remove featured image', 'mantracal'),
            'use_featured_image'    => __('Use as featured image', 'mantracal'),
            'insert_into_item'      => __('Insert into event', 'mantracal'),
            'uploaded_to_this_item' => __('Uploaded to this event', 'mantracal'),
            'items_list'            => __('Events list', 'mantracal'),
            'items_list_navigation' => __('Events list navigation', 'mantracal'),
            'filter_items_list'     => __('Filter events list', 'mantracal'),
        );
        $args = array(
            'label'                 => __('Event', 'mantracal'),
            'description'           => __('Simple event calendar', 'mantracal'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'excerpt', 'custom-fields', 'thumbnail'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
        );
        register_post_type('mantracal_event', $args);
    }

    public static function add_event_metaboxes() {
        add_meta_box(
            'mantracal_event_details',
            __('Event Details', 'mantracal'),
            array(__CLASS__, 'render_event_details_metabox'),
            'mantracal_event',
            'side',
            'default'
        );
    }

    public static function render_event_details_metabox($post) {
        // Security nonce field
        wp_nonce_field('mantracal_save_event_details', 'mantracal_event_details_nonce');

        $start_date = get_post_meta($post->ID, '_mantracal_event_start_date', true);
        $end_date = get_post_meta($post->ID, '_mantracal_event_end_date', true);
        $time = get_post_meta($post->ID, '_mantracal_event_time', true);
        $location = get_post_meta($post->ID, '_mantracal_event_location', true);
        $recurrence = get_post_meta($post->ID, '_mantracal_event_recurrence', true);

        echo '<p><label for="mantracal_event_start_date">' . __('Start Date', 'mantracal') . '</label></p>';
        echo '<p><input type="date" id="mantracal_event_start_date" name="mantracal_event_start_date" value="' . esc_attr($start_date) . '" class="widefat" /></p>';

        echo '<p><label for="mantracal_event_end_date">' . __('End Date', 'mantracal') . '</label></p>';
        echo '<p><input type="date" id="mantracal_event_end_date" name="mantracal_event_end_date" value="' . esc_attr($end_date) . '" class="widefat" /></p>';

        echo '<p><label for="mantracal_event_time">' . __('Time', 'mantracal') . '</label></p>';
        echo '<p><input type="time" id="mantracal_event_time" name="mantracal_event_time" value="' . esc_attr($time) . '" class="widefat" /></p>';

        echo '<p><label for="mantracal_event_location">' . __('Location', 'mantracal') . '</label></p>';
        echo '<p><input type="text" id="mantracal_event_location" name="mantracal_event_location" value="' . esc_attr($location) . '" class="widefat" /></p>';

        echo '<p><label for="mantracal_event_recurrence">' . __('Recurrence', 'mantracal') . '</label></p>';
        echo '<p>';
        echo '<select id="mantracal_event_recurrence" name="mantracal_event_recurrence" class="widefat">';
        echo '<option value="none" ' . selected($recurrence, 'none', false) . '>' . __('None', 'mantracal') . '</option>';
        echo '<option value="daily" ' . selected($recurrence, 'daily', false) . '>' . __('Daily', 'mantracal') . '</option>';
        echo '<option value="weekly" ' . selected($recurrence, 'weekly', false) . '>' . __('Weekly', 'mantracal') . '</option>';
        echo '<option value="monthly" ' . selected($recurrence, 'monthly', false) . '>' . __('Monthly', 'mantracal') . '</option>';
        echo '</select>';
        echo '</p>';
    }

    public static function save_event_details($post_id) {
        // Verify nonce
        if (!isset($_POST['mantracal_event_details_nonce']) || !wp_verify_nonce($_POST['mantracal_event_details_nonce'], 'mantracal_save_event_details')) {
            return;
        }

        // Check for autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save event details
        if (isset($_POST['mantracal_event_start_date'])) {
            update_post_meta($post_id, '_mantracal_event_start_date', sanitize_text_field($_POST['mantracal_event_start_date']));
        }

        if (isset($_POST['mantracal_event_end_date'])) {
            update_post_meta($post_id, '_mantracal_event_end_date', sanitize_text_field($_POST['mantracal_event_end_date']));
        }

        if (isset($_POST['mantracal_event_time'])) {
            update_post_meta($post_id, '_mantracal_event_time', sanitize_text_field($_POST['mantracal_event_time']));
        }

        if (isset($_POST['mantracal_event_location'])) {
            update_post_meta($post_id, '_mantracal_event_location', sanitize_text_field($_POST['mantracal_event_location']));
        }

        if (isset($_POST['mantracal_event_recurrence'])) {
            update_post_meta($post_id, '_mantracal_event_recurrence', sanitize_text_field($_POST['mantracal_event_recurrence']));
        }
    }
}