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
        if (!wp_next_scheduled('rankfoundry_seo_sync')) {
            wp_schedule_event(time(), 'rankfoundry_seo_hourly', 'rankfoundry_seo_sync');
        }
    }

    /**
     * Remove scheduled cron jobs.
     */
    public static function unschedule_cron_jobs() {
        wp_clear_scheduled_hook('rankfoundry_seo_sync');
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
}