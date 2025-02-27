/**
 * Handle EVDPL Gutenberg Blocks
 *
 * @var {Object} evdplGutenbergBlocks The Gutenberg blocks object.
 */

/**
 * External dependencies
 */
import React                            from 'react';
import md5                              from 'md5';

/**
 * WordPress dependencies
 */
import { registerBlockType }            from '@wordpress/blocks';
import { RawHTML }                      from '@wordpress/element';

/**
 * Internal dependencies
 */
import { evdpl_icon, generateShortcode } from './common';
import { createEditFunction }           from './edit';
import './common/actions-to-jquery-events';

for (const [blockName, blockArgs] of Object.entries(evdplGutenbergBlocks)) {
    registerBlockType('evdpl/' + blockName, {
        title: blockArgs.title,
        description: blockArgs.description,
        category: blockArgs.category,
        attributes: blockArgs.attributes,
        icon: typeof blockArgs.icon !== 'undefined' ? blockArgs.icon : evdpl_icon,
        keywords: blockArgs.keywords,
        edit: createEditFunction(blockName, blockArgs),
        save: ({ attributes }) => {
            return generateShortcode(blockArgs, attributes);
        },
        deprecated: [
            {
                attributes: blockArgs.attributes,
                save: ({ attributes }) => {
                    const shortcode = generateShortcode(blockArgs, attributes);
                    const blockHash = md5(shortcode);
                    const shortcodeSpan = '<span class="evdpl_block_' + blockHash + '">' + shortcode + '</span>';

                    return (
                            <RawHTML>{shortcodeSpan}</RawHTML>
                            )
                }
            }
        ]
    });
}