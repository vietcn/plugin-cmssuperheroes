<?php
/**
 * @Template: cms_carousel.php
 * @since: 1.0.0
 * @author: KP
 * @descriptions:
 * @create: 01-Feb-18
 */
extract($atts);
?>
<div class="carousel-portfolio carousel-portfolio-layout1 <?php echo esc_attr($el_class); ?>">
    <div class="cms-carousel owl-carousel owl-theme cms-carousel-portfolio-layout1" data-margin="<?php echo esc_attr($margin); ?>" data-loop="<?php echo esc_attr($loop); ?>" data-nav="<?php echo esc_attr($nav); ?>" data-dots="<?php echo esc_attr($dots); ?>" data-autoplay="<?php echo esc_attr($autoplay); ?>" data-large-items="<?php echo esc_attr($large_items); ?>" data-medium-items="<?php echo esc_attr($medium_items); ?>" data-small-items="<?php echo esc_attr($small_items); ?>" data-xsmall-items="<?php echo esc_attr($xsmall_items); ?>">
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
</div>
