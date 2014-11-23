<?php
/*
 * Copyright 2012, Theia Post Slider, Liviu Cristian Mirea Ghiban.
 */

class TpsAdmin_About {
	public $showPreview = false;
	
	public function echoPage() {
		?>
		<h3><?php _e("About", 'theia-post-slider'); ?></h3>
		<p>
			Theia Post Slider version <?=TPS_VERSION?>
		</p>
		<p>
			Developed by Liviu Cristian Mirea Ghiban
		</p>
		<p>
			Website: <a href="http://liviucmg.com">liviucmg.com</a>
		</p>
		<p>
			Email: <a href="mailto:contact@liviucmg.com">contact@liviucmg.com</a>
		</p>


		<h3><?php _e("Credits", 'theia-post-slider'); ?></h3>
		Many thanks go out to the following:
		<ul>
			<li><a href="http://www.doublejdesign.co.uk/products-page/icons/super-mono-icons/">Super Mono Icons</a> by <a href="http://www.doublejdesign.co.uk/">Double-J Design</a></li>
			<li><a href="http://p.yusukekamiyamane.com/">Fugue Icons</a> by <a href="http://yusukekamiyamane.com/">Yusuke Kamiyamane</a></li>
			<li><a href="http://www.brightmix.com/blog/brightmix-icon-set-free-for-all/">Brightmix icon set</a> by <a href="http://www.brightmix.com">Brightmix</a></li>
			<li><a href="http://freebiesbooth.com/hand-drawn-web-icons">Hand Drawn Web icons</a> by <a href="http://highonpixels.com/">Pawel Kadysz</a></li>
			<li><a href="http://icondock.com/free/20-free-marker-style-icons">20 Free Marker-Style Icons</a> by <a href="http://icondock.com">IconDock</a></li>
			<li><a href="http://taytel.deviantart.com/art/ORB-Icons-87934875">ORB Icons</a> by <a href="http://taytel.deviantart.com">~taytel</a></li>
			<li><a href="http://www.visualpharm.com/must_have_icon_set/">Must Have Icon Set</a> by <a href="http://www.visualpharm.com">VisualPharm</a></li>
            <li><a href="http://github.com/balupton/History.js/">The History.js project</a></li>
            <li><a href="http://jquery.com/">The jQuery.js project</a></li>
		</ul>
		<?php
	}
}