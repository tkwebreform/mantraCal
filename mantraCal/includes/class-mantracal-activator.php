<?php

// includes/class-mantracal-activator.php
class MantraCal_Activator {
    public static function activate() {
        // Ensure the CPT class is included before calling it
        require_once plugin_dir_path(__FILE__) . 'class-mantracal-cpt.php';
        
        // Register the custom post type
        MantraCal_CPT::register();
        flush_rewrite_rules();
    }
}