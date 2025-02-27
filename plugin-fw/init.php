<?php

/**
 * Framework Name: EVDPL Plugin Framework
 * Version: 1.0.0
 * Author: wpevince
 * Text Domain: evdpl-plugin-fw
 * Domain Path: /languages/
 *
 * @author  wpevince
 * @version 1.0.0
 * @package EVDPL\PluginFramework
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

if (!function_exists('evdpl_maybe_plugin_fw_loader')) {

    /**
     * Load the framework if it's not yet loaded.
     *
     * @param string $plugin_path The plugin path.
     */
    function evdpl_maybe_plugin_fw_loader($plugin_path) {
        global $plugin_fw_data, $plugin_upgrade_fw_data;

        $default_headers = array(
            'Name' => 'Framework Name',
            'Version' => 'Version',
            'Author' => 'Author',
            'TextDomain' => 'Text Domain',
            'DomainPath' => 'Domain Path',
        );

        $plugin_path = trailingslashit($plugin_path);
        $framework_data = get_file_data($plugin_path . 'plugin-fw/init.php', $default_headers);
        $plugin_fw_main_file = $plugin_path . 'plugin-fw/evdpl-plugin.php';

        if (!empty($plugin_fw_data)) {
            foreach ($plugin_fw_data as $version => $path) {
                if (version_compare($version, $framework_data['Version'], '<')) {
                    $plugin_fw_data = array($framework_data['Version'] => $plugin_fw_main_file);
                }
            }
        } else {
            $plugin_fw_data = array($framework_data['Version'] => $plugin_fw_main_file);
        }

        // Check for license & upgrade classes.
        $upgrade_fw_init_file = $plugin_path . 'plugin-upgrade/init.php';
        $framework_data = file_exists($upgrade_fw_init_file) ? get_file_data($upgrade_fw_init_file, $default_headers) : $framework_data;
        $plugin_license_path = $plugin_path . 'plugin-upgrade';
        $plugin_upgrade_path = $plugin_path . 'plugin-upgrade';

        if (!file_exists($plugin_upgrade_path)) {
            // Check path for OLD plugin framework version.
            if (file_exists($plugin_path . 'plugin-fw/licence')) {
                $plugin_license_path = $plugin_path . 'plugin-fw/licence';
                $plugin_upgrade_path = $plugin_path . 'plugin-fw/';
            } else {
                $plugin_upgrade_path = false;
                $plugin_license_path = false;
            }
        }

        if (file_exists($plugin_upgrade_path)) {
            if (!empty($plugin_upgrade_fw_data)) {
                foreach ($plugin_upgrade_fw_data as $version => $files) {
                    if (version_compare($version, $framework_data['Version'], '<')) {
                        $plugin_upgrade_fw_data = array($framework_data['Version'] => evdpl_get_upgrade_files($plugin_license_path, $plugin_upgrade_path));
                    }
                }
            } else {
                $plugin_upgrade_fw_data = array($framework_data['Version'] => evdpl_get_upgrade_files($plugin_license_path, $plugin_upgrade_path));
            }
        }
    }

}

if (!function_exists('evdpl_get_upgrade_files')) {

    /**
     * Retrieve the core files to include to manage license and upgrade if exists.
     *
     * @param string $plugin_license_path The license path.
     * @param string $plugin_upgrade_path The upgrade path.
     *
     * @return array to files to include
     */
    function evdpl_get_upgrade_files($plugin_license_path, $plugin_upgrade_path = '') {
        $to_include = array();

        if (!!$plugin_license_path) {
            $plugin_upgrade_path = empty($plugin_upgrade_path) ? $plugin_license_path : $plugin_upgrade_path;
            $license_files = array(
                '%evdpl-license-path%/lib/evdpl-licence.php',
                '%evdpl-license-path%/lib/evdpl-plugin-licence.php',
                '%evdpl-license-path%/lib/evdpl-theme-licence.php',
            );

            $upgrade_files = array('%evdpl-upgrade-path%/lib/evdpl-plugin-upgrade.php');

            $to_include_license = str_replace('%evdpl-license-path%', $plugin_license_path, $license_files);
            $to_include_upgrade = str_replace('%evdpl-upgrade-path%', $plugin_upgrade_path, $upgrade_files);

            $to_include = array_merge($to_include_license, $to_include_upgrade);
        }

        return $to_include;
    }

}
