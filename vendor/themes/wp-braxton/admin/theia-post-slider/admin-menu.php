<?php
/*
 * Copyright 2012, Theia Post Slider, Liviu Cristian Mirea Ghiban.
 */

add_action('admin_init', 'TpsMenu::admin_init');
add_action('admin_menu', 'TpsMenu::admin_menu');

class TpsMenu {
	public static function admin_init() {
		register_setting('tps_options_general', 'tps_general', 'TpsMenu::validate');
		register_setting('tps_options_nav', 'tps_nav', 'TpsMenu::validate');
		register_setting('tps_options_advanced', 'tps_advanced', 'TpsMenu::validate');
	}

	public static function admin_menu() {
		if (TPS_USE_AS_STANDALONE) {
			add_options_page('Theia Post Slider Settings', 'Theia Post Slider', 'manage_options', 'tps', 'TpsMenu::do_page');
		}
	}

	public static function do_page() {
		$tabs = array(
			'general' => array(
				'title' => __("General", 'theia-post-slider'),
				'class' => 'General'
			),
			'navigationBar' => array(
				'title' => __("Navigation Bar", 'theia-post-slider'),
				'class' => 'NavigationBar'
			),
			'advanced' => array(
				'title' => __("Advanced", 'theia-post-slider'),
				'class' => 'Advanced'
			),
			'about' => array(
				'title' => __("About & Credits", 'theia-post-slider'),
				'class' => 'About'
			)
		);
		if (array_key_exists('tab', $_GET) && array_key_exists($_GET['tab'], $tabs)) {
			$currentTab = $_GET['tab'];
		}
		else {
			$currentTab = 'general';
		}
		?>

		<div class="wrap" xmlns="http://www.w3.org/1999/html">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2>Theia Post Slider</h2>
			<h2 class="nav-tab-wrapper">
				<?php
				foreach ($tabs as $id => $tab) {
				    $class = 'nav-tab';
				    if ($id == $currentTab) {
				        $class .= ' nav-tab-active';
				    }
				    ?>
				    <a href="?page=tps&tab=<?php echo $id; ?>" class="<?php echo $class; ?>"><?php echo $tab['title']; ?></a>
				    <?php
				}
				?>
			</h2>
			<?php
			$class = 'TpsAdmin_' . $tabs[$currentTab]['class'];
			require $class  . '.php';
			$page = new $class;
			$page->echoPage();

			if ($page->showPreview == true) {
				?>
				<h3><?php _e("Live Preview", 'theia-post-slider'); ?></h3>
				<div class="theiaPostSlider_adminPreview">
					<?php
					echo TpsMisc::getNavigationBar(array(
						'currentSlide' => 1,
						'totalSlides' => 3,
						'id' => 'tps_nav_upper',
						'class' => '_upper',
						'style' => in_array(TpsOptions::get('nav_vertical_position'), array('top_and_bottom', 'top')) ? '' : 'display: none'
					));
					?>
					<div id="tps_dest" class="theiaPostSlider_slides"></div>
					<div id="tps_src" class="theiaPostSlider_slides">
						<?php include dirname(__FILE__) . '/preview-slider.php'; ?>
					</div>
					<?php
					echo TpsMisc::getNavigationBar(array(
						'currentSlide' => 1,
						'totalSlides' => 3,
						'id' => 'tps_nav_lower',
						'class' => '_lower',
						'style' => in_array(TpsOptions::get('nav_vertical_position'), array('top_and_bottom', 'bottom')) ? '' : 'display: none'
					));
					$sliderOptions = array(
						'src' => '#tps_src > div',
						'dest' => '#tps_dest',
						'nav' => array('#tps_nav_upper', '#tps_nav_lower'),
						'navText' => TpsOptions::get('navigation_text'),
	                    'helperText' => TpsOptions::get('helper_text'),
						'transitionEffect' => TpsOptions::get('transition_effect'),
						'transitionSpeed' => TpsOptions::get('transition_speed'),
						'keyboardShortcuts' => true,
	                    'prevText' => TpsOptions::get('prev_text'),
	                    'nextText' => TpsOptions::get('next_text'),
	                    'buttonWidth' => TpsOptions::get('button_width'),
						'numberOfSlides' => 3
					);
					?>
					<script type='text/javascript'>
						var slider, theme;
						jQuery(document).ready(function() {
							slider = new tps.createSlideshow(<?php echo json_encode($sliderOptions); ?>);
						});
					</script>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	}

	public static function validate($input) {
		return $input;
	}
}