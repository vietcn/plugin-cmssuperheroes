<?php
vc_map(
	array(
		"name" => __("CMS Carousel", CMS_NAME),
	    "base" => "cms_carousel",
	    "class" => "vc-cms-carousel",
	    "category" => __("CmsSuperheroes Shortcodes", CMS_NAME),
	    "params" => array(
	    	array(
	            "type" => "loop",
	            "heading" => __("Source",CMS_NAME),
	            "param_name" => "source",
	            'settings' => array(
	                'size' => array('hidden' => false, 'value' => 10),
	                'order_by' => array('value' => 'date')
	            ),
	            "group" => __("Source Settings", CMS_NAME),
	        ),
	        array(
	            "type" => "dropdown",
	            "heading" => __("XSmall Devices",CMS_NAME),
	            "param_name" => "xsmall_items",
	            "edit_field_class" => "vc_col-sm-3 vc_carousel_item",
	            "value" => array(1,2,3,4,5,6),
	            "std" => 1,
	            "group" => __("Carousel Settings", CMS_NAME)
	        ),
	    	array(
	            "type" => "dropdown",
	            "heading" => __("Small Devices",CMS_NAME),
	            "param_name" => "small_items",
	            "edit_field_class" => "vc_col-sm-3 vc_carousel_item",
	            "value" => array(1,2,3,4,5,6),
	            "std" => 2,
	            "group" => __("Carousel Settings", CMS_NAME)
	        ),
	        array(
	            "type" => "dropdown",
	            "heading" => __("Medium Devices",CMS_NAME),
	            "param_name" => "medium_items",
	            "edit_field_class" => "vc_col-sm-3 vc_carousel_item",
	            "value" => array(1,2,3,4,5,6),
	            "std" => 3,
	            "group" => __("Carousel Settings", CMS_NAME)
	        ),
	        array(
	            "type" => "dropdown",
	            "heading" => __("Large Devices",CMS_NAME),
	            "param_name" => "large_items",
	            "edit_field_class" => "vc_col-sm-3 vc_carousel_item",
	            "value" => array(1,2,3,4,5,6),
	           	"std" => 4,
	            "group" => __("Carousel Settings", CMS_NAME)
	        ),
	        array(
	            "type" => "textfield",
	            "heading" => __("Margin Items",CMS_NAME),
	            "param_name" => "margin",
	            "value" => "10",
	            "description" => __("",CMS_NAME),
	            "group" => __("Carousel Settings", CMS_NAME)
	        ),
	        array(
	            "type" => "dropdown",
	            "heading" => __("Loop Items",CMS_NAME),
	            "param_name" => "loop",
	            "value" => array(
	            	"True" => "true",
	            	"False" => "false"
	            	),
	            "group" => __("Carousel Settings", CMS_NAME)
	        ),
	        array(
	            "type" => "dropdown",
	            "heading" => __("Mouse Drag",CMS_NAME),
	            "param_name" => "mousedrag",
	            "value" => array(
	            	"True" => "true",
	            	"False" => "false"
	            	),
	            "group" => __("Carousel Settings", CMS_NAME)
	        ),
	        array(
	            "type" => "dropdown",
	            "heading" => __("Show Dots",CMS_NAME),
	            "param_name" => "dots",
	            "value" => array(
	            	"True" => "true",
	            	"False" => "false"
	            	),
	            "group" => __("Carousel Settings", CMS_NAME)
	        ),
	        array(
	            "type" => "dropdown",
	            "heading" => __("Auto Play",CMS_NAME),
	            "param_name" => "autoplay",
	            "value" => array(
	            	"True" => "true",
	            	"False" => "false"
	            	),
	            "group" => __("Carousel Settings", CMS_NAME)
	        ),
	        array(
	            "type" => "textfield",
	            "heading" => __("Extra Class",CMS_NAME),
	            "param_name" => "class",
	            "value" => "",
	            "description" => __("",CMS_NAME),
	            "group" => __("Carousel Settings", CMS_NAME)
	        ),
	    	array(
	            "type" => "cms_template",
	            "param_name" => "cms_template",
	            "shortcode" => "cms_carousel",
	            "admin_label" => true,
	            "heading" => __("Shortcode Template",CMS_NAME),
	            "group" => __("Template", CMS_NAME),
	        )
	    )
	)
);
global $cms_carousel;
$cms_carousel = array();
class WPBakeryShortCode_cms_carousel extends CmsShortCode{
	protected function content($atts, $content = null){
		//default value
		$atts_extra = shortcode_atts(array(
			'source' => '',
			'xsmall_items' => 1,
			'small_items' => 2,
			'medium_items' => 3,
			'large_items' => 4,
			'margin' => 10,
			'loop' => 'true',
			'nav' => 'true',
			'dots' => 'true',
			'autoplay' => 'true',
			'cms_template' => 'cms_carousel.php',
			'not__in'=> 'false', 
			'class' => '',
			    ), $atts);

		$atts = array_merge($atts_extra,$atts);
		global $cms_carousel;

		if(file_exists(get_template_directory().'/assets/js/owl.carousel.min.js')){
			// do nothing
		}else{
            wp_enqueue_style('owl-carousel',CMS_CSS.'owl.carousel.css','','2.2.1','all');
            wp_enqueue_script('owl-carousel',CMS_JS.'owl.carousel.min.js',array('jquery'),'2.2.1',true);
			wp_enqueue_script('owl-carousel-cms',CMS_JS.'owl.carousel.cms.js',array('jquery'),'1.0.0',true);
		}
		$source = $atts['source'];
        if(isset($atts['not__in']) and $atts['not__in'] == 'true'){
        	list($args, $post) = vc_build_loop_query($source, get_the_ID());
        	
        }else{
        	list($args, $post) = vc_build_loop_query($source);
        }
        $atts['posts'] = $post;
        $html_id = cmsHtmlID('cms-carousel');
        $atts['template'] = 'template-'.str_replace('.php','',$atts['cms_template']). ' '. $atts['class'];
        $atts['html_id'] = $html_id;
		return parent::content($atts, $content);
	}
}

?>