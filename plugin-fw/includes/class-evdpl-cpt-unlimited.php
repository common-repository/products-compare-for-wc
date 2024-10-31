<?php

/**
 * EVDPL Custom-Post-Type Unlimited Class.
 * Deprecated! Kept only to prevent fatal errors if someone is using it.
 *
 * @class      EVDPL_CPT_Unlimited
 * @package    EVDPL\PluginFramework\Classes
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

if (!class_exists('EVDPL_CPT_Unlimited')) {

    /**
     * Class EVDPL_CPT_Unlimited
     *
     */
    class EVDPL_CPT_Unlimited {

        /**
         * EVDPL_CPT_Unlimited constructor.
         * @param array $args Configuration arguments of post type.
         */
        public function __construct($args = array()) {
            
        }

        /**
         * Avoid issues when calling a non-defined method
         * @param string $name      Name of the missing method.
         * @param array  $arguments Arguments.
         * @return bool
         */
        public function __call($name, $arguments) {
            return false;
        }

        /**
         * Avoid issues when calling a non-defined attribute
         * @param string $key Name of the missing attribute.
         * @return bool
         */
        public function __get($key) {
            return false;
        }

    }

}
