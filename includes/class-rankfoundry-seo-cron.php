<?php

/**
 * RankFoundry SEO CronJob Class
 */

 if (!defined('WPINC')) {
    die;
}

class RankFoundry_SEO_Cron {

    /**
     * Schedule our cron jobs.
     */
    public static function schedule_cron_jobs() {
        error_log("Inside schedule_cron_jobs");
        if (!wp_next_scheduled('rankfoundry_seo_sync')) {
            error_log("Scheduling the cron job");
            wp_schedule_event(time(), 'rankfoundry_seo_hourly', 'rankfoundry_seo_sync');
        }
    }

    /**
     * Remove scheduled cron jobs.
     */
    public static function unschedule_cron_jobs() {
        wp_clear_scheduled_hook('rankfoundry_seo_sync');
        update_option('rankfoundry_seo_sync_activation', '0');
    }

    /**
     * Add custom cron schedules.
     */
    public static function custom_cron_schedules($schedules) {
        
        $schedules['rankfoundry_seo_5'] = array(
            'interval' => 300,
            'display'  => __('Every Five Minutes', 'rankfoundry-seo')
        );

        $schedules['rankfoundry_seo_hourly'] = array(
            'interval' => 3600,
            'display'  => __('Every Hour', 'rankfoundry-seo')
        );
        
        return $schedules;
    }

    public static function get_custom_schedules() {
        return wp_get_schedules();
    }

    public static function get_scheduled_events() {
        $events = array();
        $crons = _get_cron_array();

        if (empty($crons)) return array();
    
        foreach ( $crons as $time => $cron ) {
            foreach ( $cron as $hook => $dings ) {
                foreach ( $dings as $sig => $data ) {
    
                    $events[ "$hook-$sig-$time" ] = (object) array(
                        'hook'     => $hook,
                        'timestamp' => $time, // UTC
                        'sig'      => $sig,
                        'args'     => $data['args'],
                        'schedule' => $data['schedule'],
                        'interval' => isset( $data['interval'] ) ? $data['interval'] : null,
                    );
    
                }
            }
        }
    
        return $events;
    }
}