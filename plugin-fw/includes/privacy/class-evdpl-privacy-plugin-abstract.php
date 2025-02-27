<?php

/**
 * EVDPL Privacy Abstract Class
 * abstract class to handle privacy in plugins
 *
 * @class   EVDPL_Privacy_Plugin_Abstract
 * @package EVDPL\PluginFramework\Classes
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

if (!class_exists('EVDPL_Privacy_Plugin_Abstract')) {

    /**
     * Class EVDPL_Privacy_Plugin_Abstract
     */
    class EVDPL_Privacy_Plugin_Abstract {

        /**
         * The plugin name.
         *
         * @var string
         */
        private $plugin_name;

        /**
         * EVDPL_Privacy_Plugin_Abstract constructor.
         *
         * @param string $plugin_name The plugin name.
         */
        public function __construct($plugin_name) {
            $this->plugin_name = $plugin_name;
            $this->init();
        }

        /**
         * Let's initialize the privacy.
         */
        protected function init() {
            add_filter('evdpl_plugin_fw_privacy_guide_content', array($this, 'add_message_in_section'), 10, 2);
        }

        /**
         * Add message in a specific section.
         *
         * @param string $html    The HTML of the section.
         * @param string $section The section.
         *
         * @return string
         */
        public function add_message_in_section($html, $section) {
            $message = $this->get_privacy_message($section);
            if ($message) {
                $html .= "<p class='privacy-policy-tutorial'><strong>{$this->plugin_name}</strong></p>";
                $html .= $message;
            }

            return $html;
        }

        /**
         * Retrieve the privacy message.
         * Override me to customize the messages for each section.
         *
         * @param string $section The section.
         *
         * @return string
         */
        public function get_privacy_message($section) {
            return '';
        }

    }

}
