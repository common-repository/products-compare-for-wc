/**
 * Actions to jQuery events
 */

/**
 * WordPress dependencies
 */
import { addAction } from '@wordpress/hooks';
const actions = [
    {key: 'evdpl_plugin_fw_gutenberg_before_do_shortcode', delay: 0},
    {key: 'evdpl_plugin_fw_gutenberg_success_do_shortcode', delay: 200},
    {key: 'evdpl_plugin_fw_gutenberg_after_do_shortcode', delay: 200}
];

for (const action of actions) {
    addAction(
            action.key,
            'evdpl-plugin-fw/jquery-events',
            (...params) => {
        if ('jQuery' in window) {
            if (action.delay) {
                setTimeout(() => {
                    jQuery(document).trigger(action.key, Object.values(params));
                }, action.delay);
            } else {
                jQuery(document).trigger(action.key, Object.values(params));
            }
        }
    }
    );
}