<?php

// includes/class-mantracal-deactivator.php
class MantraCal_Deactivator {
    public static function deactivate() {
        flush_rewrite_rules();
    }
}