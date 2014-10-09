<?php
class TpsEnqueues {
	// Enqueue the required JavaScript for a given transition effect.
    public static function enqueueTransition($transition) {
        wp_register_script('theiaPostSlider-transition-' . $transition . '.js', TPS_PLUGINS_URL . 'js/tps-transition-' . $transition . '.js', array( 'jquery'), TPS_VERSION);
        wp_enqueue_script('theiaPostSlider-transition-' . $transition . '.js');
    }

    // Enqueue JavaScript and CSS.
    public static function wp_enqueue_scripts() {
	    // Do not load unless necessary.
		if (!is_admin() && !TpsMisc::isCompatiblePost()) {
			return;
		}

        // CSS
        if (TpsOptions::get('theme') != 'none') {
            wp_register_style('theiaPostSlider', TPS_PLUGINS_URL . 'css/' . TpsOptions::get('theme'), array(), TPS_VERSION);
            wp_enqueue_style('theiaPostSlider');
        }

        // jQuery
        //wp_register_script('jquery', TPS_PLUGINS_URL . 'js/jquery-1.8.0.min.js', '1.8.0');
        //wp_enqueue_script('jquery');

        // history.js
        wp_register_script('history.js', TPS_PLUGINS_URL . 'js/balupton-history.js/history.js', array('jquery'), '1.7.1');
        wp_enqueue_script('history.js');
        wp_register_script('history.adapter.jquery.js', TPS_PLUGINS_URL . 'js/balupton-history.js/history.adapter.jquery.js', array('jquery', 'history.js'), '1.7.1');
        wp_enqueue_script('history.adapter.jquery.js');

        // async.js
        wp_register_script('async.js', TPS_PLUGINS_URL . 'js/async.min.js', array(), '25.11.2012');
        wp_enqueue_script('async.js');

        // The slider
        wp_register_script('theiaPostSlider.js', TPS_PLUGINS_URL . 'js/tps.js', array('jquery'), TPS_VERSION, true);
        wp_enqueue_script('theiaPostSlider.js');

        // The selected transition effect
        self::enqueueTransition(TpsOptions::get('transition_effect'));
    }

    // Enqueue JavaScript and CSS for the admin interface.
    public static function admin_enqueue_scripts() {
        self::wp_enqueue_scripts();

	    // Enqueue all transition scripts for live preview.
        foreach (TpsOptions::getTransitionEffects() as $key => $value) {
            self::enqueueTransition($key);
        }

        // CSS, even if there is no theme, so we can change the path via JS.
        if (TpsOptions::get('theme') == 'none') {
            wp_register_style('theiaPostSlider', TPS_PLUGINS_URL . 'css/' . TpsOptions::get('theme'), TPS_VERSION);
            wp_enqueue_style('theiaPostSlider');
        }

        // Admin CSS
        wp_register_style('theiaPostSlider-admin', TPS_PLUGINS_URL . 'css/admin.css', array(), TPS_VERSION);
        wp_enqueue_style('theiaPostSlider-admin');
    }
}