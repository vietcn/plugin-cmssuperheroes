<?php
extract($atts);
?>
<div class="cms-carousel cms-carousel-layout1 owl-carousel owl-theme <?php echo esc_attr($el_class); ?>" data-margin="<?php echo esc_attr($margin); ?>" data-loop="<?php echo esc_attr($loop); ?>" data-nav="<?php echo esc_attr($nav); ?>" data-dots="<?php echo esc_attr($dots); ?>" data-autoplay="<?php echo esc_attr($autoplay); ?>" data-large-items="<?php echo esc_attr($large_items); ?>" data-medium-items="<?php echo esc_attr($medium_items); ?>" data-small-items="<?php echo esc_attr($small_items); ?>" data-xsmall-items="<?php echo esc_attr($xsmall_items); ?>">
    <?php
    if (is_array($contents)):
        foreach ($contents as $shortcode) {
            ?>
            <div class="cms-carousel-item">
                <?php echo cms_do_the_content($shortcode) ?>
            </div>
            <?php
        }
    endif;
    ?>
</div>