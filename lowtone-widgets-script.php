<?php
/*
 * Plugin Name: Script
 * Plugin URI: http://wordpress.lowtone.nl/plugins/widgets-script/
 * Description: Execute a PHP script in a widget.
 * Version: 1.0
 * Author: Lowtone <info@lowtone.nl>
 * Author URI: http://lowtone.nl
 * License: http://wordpress.lowtone.nl/license
 */
/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\plugins\lowtone\widgets\script
 */

namespace lowtone\widgets\script {

	use lowtone\Util,
		lowtone\content\packages\Package;

	// Includes
	
	if (!include_once WP_PLUGIN_DIR . "/lowtone-content/lowtone-content.php") 
		return trigger_error("Lowtone Content plugin is required", E_USER_ERROR) && false;

	$__i = Package::init(array(
			Package::INIT_PACKAGES => array("lowtone", "lowtone\\wp"),
			Package::INIT_MERGED_PATH => __NAMESPACE__,
			Package::INIT_SUCCESS => function() {

				// Register widget

				add_action("widgets_init", function() {
					register_widget("lowtone\\widgets\\script\\Widget");
				});

				return true;
			}
		));

	if (true !== $__i)
		return false;

	class Widget extends \WP_Widget {

		public function __construct() {
			parent::__construct(
				"lowtone_widgets_script", 
				__("Script"), 
				array(
					"classname" => "widget_script", 
					"description" => __("Execute a script")
				)
			);
		}

		public function widget($args, $instance) {
			extract($args);

			$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
			$text = apply_filters('widget_text', empty($instance['text']) ? '' : $instance['text'], $instance);
			
			echo $before_widget;
			
			if (!empty($title)) 
				echo $before_title . $title . $after_title;

			echo '<div class="textwidget">' .
				Util::catchOutput(function() use ($text) {
					eval("?>" . $text);
				}) .
				'</div>';
			
			echo $after_widget;
		}

		function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
			$title = strip_tags($instance['title']);
			$text = esc_textarea($instance['text']);

			echo '<p><label for="'. $this->get_field_id('title') . '">' . 
				__('Title:') .
				'</label>' . 
				'<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '" /></p>' . 
				'<textarea class="widefat" rows="16" cols="20" id="' . $this->get_field_id('text') . '" name="' . $this->get_field_name('text') . '">' .  $text . '</textarea>';
		}

	}

}