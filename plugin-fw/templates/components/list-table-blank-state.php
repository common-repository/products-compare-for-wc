<?php
/**
 * Template for displaying the list-table-blank-state component
 *
 * @var array $component The component.
 * @package EVDPL\PluginFramework\Templates\Components
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

list ( $component_id, $class, $icon, $icon_class, $icon_url, $message, $cta, $html_attributes, $html_data ) = evdpl_plugin_fw_extract($component, 'id', 'class', 'icon', 'icon_class', 'icon_url', 'message', 'cta', 'html_attributes', 'html_data');
?>
<div id="<?php echo esc_attr($component_id); ?>"
     class="evdpl-plugin-fw__list-table-blank-state <?php echo esc_attr($class); ?>"
     <?php echo wp_kses_post($html_attributes); ?>
     <?php echo wp_kses_post($html_data); ?>
     >
         <?php if ($icon) : ?>
        <i class="evdpl-plugin-fw__list-table-blank-state__icon evdpl-icon evdpl-icon-<?php echo esc_attr($icon); ?>"></i>
    <?php elseif ($icon_class) : ?>
        <i class="evdpl-plugin-fw__list-table-blank-state__icon <?php echo esc_attr($icon_class); ?>"></i>
    <?php elseif ($icon_url) : ?>
        <img class="evdpl-plugin-fw__list-table-blank-state__icon" src="<?php echo esc_url($icon_url); ?>"/>
    <?php endif; ?>
    <div class="evdpl-plugin-fw__list-table-blank-state__message"><?php echo wp_kses_post($message); ?></div>
    <?php if ($cta && !empty($cta['title'])) : ?>
        <?php
        $cta_url = !empty($cta['url']) ? $cta['url'] : '';
        $cta_classes = array('evdpl-plugin-fw__list-table-blank-state__cta', 'evdpl-plugin-fw__button--primary', 'evdpl-plugin-fw__button--xxl');
        if (!empty($cta['class'])) {
            $cta_classes[] = $cta['class'];
        }
        if (!empty($cta['icon'])) {
            $cta_classes[] = 'evdpl-plugin-fw__button--with-icon';
        }
        $cta_classes = implode(' ', $cta_classes);
        ?>
        <div class="evdpl-plugin-fw__list-table-blank-state__cta-wrapper">
            <a href="<?php echo esc_url($cta_url); ?>" class="<?php echo esc_attr($cta_classes); ?>">
                <?php if (!empty($cta['icon'])) : ?>
                    <i class="evdpl-icon evdpl-icon-<?php echo esc_attr($cta['icon']); ?>"></i>
                <?php endif; ?>
                <?php echo esc_html($cta['title']); ?>
            </a>
        </div>
    <?php endif; ?>
</div>
