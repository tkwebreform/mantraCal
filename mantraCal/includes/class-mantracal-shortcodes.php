<?php

// includes/class-mantracal-shortcodes.php

class MantraCal_Shortcodes {
    public static function register() {
        add_shortcode('mantracal_events', array(__CLASS__, 'events_shortcode'));
        add_shortcode('mantracal_month_view', array(__CLASS__, 'month_view_shortcode'));
    }

    public static function events_shortcode($atts) {
        $atts = shortcode_atts(array(
            'number' => 5,
        ), $atts, 'mantracal_events');

        $args = array(
            'post_type' => 'mantracal_event',
            'posts_per_page' => intval($atts['number']),
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            $output = '<ul class="mantracal-events">';
            while ($query->have_posts()) {
                $query->the_post();
                $date = get_post_meta(get_the_ID(), '_mantracal_event_date', true);
                $time = get_post_meta(get_the_ID(), '_mantracal_event_time', true);
                $location = get_post_meta(get_the_ID(), '_mantracal_event_location', true);
                $recurrence = get_post_meta(get_the_ID(), '_mantracal_event_recurrence', true);

                $output .= '<li>';
                $output .= '<h3>' . get_the_title() . '</h3>';
                $output .= '<p>' . __('Datum:', 'mantracal') . ' ' . esc_html($date) . '</p>';
                $output .= '<p>' . __('Uhrzeit:', 'mantracal') . ' ' . esc_html($time) . '</p>';
                $output .= '<p>' . __('Ort:', 'mantracal') . ' ' . esc_html($location) . '</p>';
                if ($recurrence != 'none') {
                    $output .= '<p>' . __('Wiederholung:', 'mantracal') . ' ' . esc_html($recurrence) . '</p>';
                }
                $output .= '<p>' . get_the_excerpt() . '</p>';
                $output .= '</li>';
            }
            $output .= '</ul>';

            wp_reset_postdata();
        } else {
            $output = '<p>' . __('Keine Ereignisse gefunden.', 'mantracal') . '</p>';
        }

        return $output;
    }

    public static function month_view_shortcode() {
        // Abfrage aller Ereignisse
        $args = array(
            'post_type' => 'mantracal_event',
            'posts_per_page' => -1, // Alle Ereignisse abrufen
        );

        $query = new WP_Query($args);

        $events = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $date = get_post_meta(get_the_ID(), '_mantracal_event_date', true);
                $time = get_post_meta(get_the_ID(), '_mantracal_event_time', true);
                $location = get_post_meta(get_the_ID(), '_mantracal_event_location', true);
                $recurrence = get_post_meta(get_the_ID(), '_mantracal_event_recurrence', true);

                $events[] = array(
                    'title' => get_the_title(),
                    'date' => $date,
                    'time' => $time,
                    'location' => $location,
                    'recurrence' => $recurrence,
                );
            }
            wp_reset_postdata();
        }

        $output = '<div class="mantracal-month-view">';
        $output .= '<h2>' . __('Monatsansicht', 'mantracal') . '</h2>';

        $output .= '<table class="mantracal-calendar">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th>' . __('Sonntag', 'mantracal') . '</th>';
        $output .= '<th>' . __('Montag', 'mantracal') . '</th>';
        $output .= '<th>' . __('Dienstag', 'mantracal') . '</th>';
        $output .= '<th>' . __('Mittwoch', 'mantracal') . '</th>';
        $output .= '<th>' . __('Donnerstag', 'mantracal') . '</th>';
        $output .= '<th>' . __('Freitag', 'mantracal') . '</th>';
        $output .= '<th>' . __('Samstag', 'mantracal') . '</th>';
        $output .= '</tr>';
        $output .= '</thead>';
        $output .= '<tbody>';

        // Dynamische Monatsansicht
        $days_in_month = date('t');
        $first_day_of_month = date('w', strtotime(date('Y-m-01')));
        $current_day = 1;

        for ($week = 0; $week < 6; $week++) {
            $output .= '<tr>';
            for ($day = 0; $day < 7; $day++) {
                if ($week == 0 && $day < $first_day_of_month) {
                    $output .= '<td></td>';
                } elseif ($current_day > $days_in_month) {
                    $output .= '<td></td>';
                } else {
                    $output .= '<td>' . $current_day;

                    // Ereignisse für den aktuellen Tag anzeigen
                    foreach ($events as $event) {
                        if ($event['date'] == date('Y-m-d', strtotime(date('Y-m') . '-' . $current_day))) {
                            $output .= '<p>' . esc_html($event['title']) . '</p>';
                        }
                    }

                    $output .= '</td>';
                    $current_day++;
                }
            }
            $output .= '</tr>';
            if ($current_day > $days_in_month) {
                break;
            }
        }

        $output .= '</tbody>';
        $output .= '</table>';
        $output .= '</div>';

        return $output;
    }
}