<?php

add_action('init','of_options');

if (!function_exists('of_options')) {
function of_options(){

//Theme Shortname
$shortname = "mvp";

//Populate the options array
global $tt_options;
$tt_options = get_option('of_options');

if ( is_admin() ) {

//Access the WordPress Tags via an Array
$tt_tags = array();
$tt_tags_obj = get_tags('hide_empty=0');
foreach ($tt_tags_obj as $tt_tag) {
$tt_tags[$tt_tag->slug] = $tt_tag->slug;}
$tags_tmp = array_unshift($tt_tags, "Select a tag:");

//Access the WordPress Categories via an Array
$tt_categories = array();  
$tt_categories_obj = get_categories('hide_empty=0');
foreach ($tt_categories_obj as $tt_cat) {
$tt_categories[$tt_cat->cat_ID] = $tt_cat->cat_name;}
$categories_tmp = array_unshift($tt_categories, "Select a category:");

$home_layout = array("Blog","Widgets");

$logo_loc = array("Small in navigation","Left of leaderboard","Wide below leaderboard");

$feat_post = array("Featured Posts 1","Featured Posts 2","Featured Posts 3");

$admin_images = get_template_directory_uri() . '/admin/images/';

$google_fonts = array("ABeeZee","Abel","Abril Fatface","Aclonica","Acme","Actor","Adamina","Advent Pro","Aguafina Script","Akronim","Aladin","Aldrich","Alegreya","Alegreya SC","Alex Brush","Alfa Slab One","Alice","Alike","Alike Angular","Allan","Allerta","Allerta Stencil","Allura","Almendra","Almendra Display","Almendra SC","Amarante","Amaranth","Amatic SC","Amethysta","Anaheim","Andada","Andika","Angkor","Annie Use Your Telescope","Anonymous Pro","Antic","Antic Didone","Antic Slab","Anton","Arapey","Arbutus","Arbutus Slab","Architects Daughter","Archivo Black","Archivo Narrow","Arimo","Arizonia","Armata","Artifika","Arvo","Asap","Asset","Astloch","Asul","Atomic Age","Aubrey","Audiowide","Autour One","Average","Average Sans","Averia Gruesa Libre","Averia Libre","Averia Sans Libre","Averia Serif Libre","Bad Script","Balthazar","Bangers","Basic","Battambang","Baumans","Bayon","Belgrano","Belleza","BenchNine","Bentham","Berkshire Swash","Bevan","Bigelow Rules","Bigshot One","Bilbo","Bilbo Swash Caps","Bitter","Black Ops One","Bokor","Bonbon","Boogaloo","Bowlby One","Bowlby One SC","Brawler","Bree Serif","Bubblegum Sans","Bubbler One","Buda","Buenard","Butcherman","Butterfly Kids","Cabin","Cabin Condensed","Cabin Sketch","Caesar Dressing","Cagliostro","Calligraffitti","Cambo","Candal","Cantarell","Cantata One","Cantora One","Capriola","Cardo","Carme","Carrois Gothic","Carrois Gothic SC","Carter One","Caudex","Cedarville Cursive","Ceviche One","Changa One","Chango","Chau Philomene One","Chela One","Chelsea Market","Chenla","Cherry Cream Soda","Cherry Swash","Chewy","Chicle","Chivo","Cinzel","Cinzel Decorative","Clicker Script","Coda","Coda Caption","Codystar","Combo","Comfortaa","Coming Soon","Concert One","Condiment","Content","Contrail One","Convergence","Cookie","Copse","Corben","Courgette","Cousine","Coustard","Covered By Your Grace","Crafty Girls","Creepster","Crete Round","Crimson Text","Croissant One","Crushed","Cuprum","Cutive","Cutive Mono","Damion","Dancing Script","Dangrek","Dawning of a New Day","Days One","Delius","Delius Swash Caps","Delius Unicase","Della Respira","Denk One","Devonshire","Didact Gothic","Diplomata","Diplomata SC","Domine","Donegal One","Doppio One","Dorsa","Dosis","Dr Sugiyama","Droid Sans","Droid Sans Mono","Droid Serif","Duru Sans","Dynalight","Eagle Lake","Eater","EB Garamond","Economica","Electrolize","Elsie","Elsie Swash Caps","Emblema One","Emilys Candy","Engagement","Englebert","Enriqueta","Erica One","Esteban","Euphoria Script","Ewert","Exo","Expletus Sans","Fanwood Text","Fascinate","Fascinate Inline","Faster One","Fasthand","Federant","Federo","Felipa","Fenix","Finger Paint","Fjalla One","Fjord One","Flamenco","Flavors","Fondamento","Fontdiner Swanky","Forum","Francois One","Freckle Face","Fredericka the Great","Fredoka One","Freehand","Fresca","Frijole","Fruktur","Fugaz One","Gabriela","Gafata","Galdeano","Galindo","Gentium Basic","Gentium Book Basic","Geo","Geostar","Geostar Fill","Germania One","GFS Didot","GFS Neohellenic","Gilda Display","Give You Glory","Glass Antiqua","Glegoo","Gloria Hallelujah","Goblin One","Gochi Hand","Gorditas","Goudy Bookletter 1911","Graduate","Grand Hotel","Gravitas One","Great Vibes","Griffy","Gruppo","Gudea","Habibi","Hammersmith One","Hanalei","Hanalei Fill","Handlee","Hanuman","Happy Monkey","Headland One","Henny Penny","Herr Von Muellerhoff","Holtwood One SC","Homemade Apple","Homenaje","Iceberg","Iceland","IM Fell Double Pica","IM Fell Double Pica SC","IM Fell DW Pica","IM Fell DW Pica SC","IM Fell English","IM Fell English SC","IM Fell French Canon","IM Fell French Canon SC","IM Fell Great Primer","IM Fell Great Primer SC","Imprima","Inconsolata","Inder","Indie Flower","Inika","Irish Grover","Istok Web","Italiana","Italianno","Jacques Francois","Jacques Francois Shadow","Jim Nightshade","Jockey One","Jolly Lodger","Josefin Sans","Josefin Slab","Joti One","Judson","Julee","Julius Sans One","Junge","Jura","Just Another Hand","Just Me Again Down Here","Kameron","Karla","Kaushan Script","Kavoon","Keania One","Kelly Slab","Kenia","Khmer","Kite One","Knewave","Kotta One","Koulen","Kranky","Kreon","Kristi","Krona One",
"La Belle Aurore","Lancelot","Lato","League Script","Leckerli One","Ledger","Lekton","Lemon","Libre Baskerville","Life Savers","Lilita One","Limelight","Linden Hill","Lobster","Lobster Two","Londrina Outline","Londrina Shadow","Londrina Sketch","Londrina Solid","Lora","Love Ya Like A Sister","Loved by the King","Lovers Quarrel","Luckiest Guy","Lusitana","Lustria","Macondo","Macondo Swash Caps","Magra","Maiden Orange","Mako","Marcellus","Marcellus SC","Marck Script","Margarine","Marko One","Marmelad","Marvel","Mate","Mate SC","Maven Pro","McLaren","Meddon","MedievalSharp","Medula One","Megrim","Meie Script","Merienda","Merienda One","Merriweather","Merriweather Sans","Metal","Metal Mania","Metamorphous","Metrophobic","Michroma","Milonga","Miltonian","Miltonian Tattoo","Miniver","Miss Fajardose","Modern Antiqua","Molengo","Molle","Monda","Monofett","Monoton","Monsieur La Doulaise","Montaga","Montez","Montserrat","Montserrat Alternates","Montserrat Subrayada","Moul","Moulpali","Mountains of Christmas","Mouse Memoirs","Mr Bedfort","Mr Dafoe","Mr De Haviland","Mrs Saint Delafield","Mrs Sheppards","Muli","Mystery Quest","Neucha","Neuton","New Rocker","News Cycle","Niconne","Nixie One","Nobile","Nokora","Norican","Nosifer","Nothing You Could Do","Noticia Text","Noto Sans","Noto Serif","Nova Cut","Nova Flat","Nova Mono","Nova Oval","Nova Round","Nova Script","Nova Slim","Nova Square","Numans","Nunito","Odor Mean Chey","Offside","Old Standard TT","Oldenburg","Oleo Script","Oleo Script Swash Caps","Open Sans","Open Sans Condensed","Oranienbaum","Orbitron","Oregano","Orienta","Original Surfer","Oswald","Over the Rainbow","Overlock","Overlock SC","Ovo","Oxygen","Oxygen Mono","Pacifico","Paprika","Parisienne","Passero One","Passion One","Patrick Hand","Patrick Hand SC","Patua One","Paytone One","Peralta","Permanent Marker","Petit Formal Script","Petrona","Philosopher","Piedra","Pinyon Script","Pirata One","Plaster","Play","Playball","Playfair Display","Playfair Display SC","Podkova","Poiret One","Poller One","Poly","Pompiere","Pontano Sans","Port Lligat Sans","Port Lligat Slab","Prata","Preahvihear","Press Start 2P","Princess Sofia","Prociono","Prosto One","PT Mono","PT Sans","PT Sans Caption","PT Sans Narrow","PT Serif","PT Serif Caption","Puritan","Purple Purse","Quando","Quantico","Quattrocento","Quattrocento Sans","Questrial","Quicksand","Quintessential","Qwigley","Racing Sans One","Radley","Raleway","Raleway Dots","Rambla","Rammetto One","Ranchers","Rancho","Rationale","Redressed","Reenie Beanie","Revalia","Ribeye","Ribeye Marrow","Righteous","Risque","Roboto","Roboto Condensed","Roboto Slab","Rochester","Rock Salt","Rokkitt","Romanesco","Ropa Sans","Rosario","Rosarivo","Rouge Script","Ruda","Rufina","Ruge Boogie","Ruluko","Rum Raisin","Ruslan Display","Russo One","Ruthie","Rye","Sacramento","Sail","Salsa","Sanchez","Sancreek","Sansita One","Sarina","Satisfy","Scada","Schoolbell","Seaweed Script","Sevillana","Seymour One","Shadows Into Light","Shadows Into Light Two","Shanti","Share","Share Tech","Share Tech Mono","Shojumaru","Short Stack","Siemreap","Sigmar One","Signika","Signika Negative","Simonetta","Sintony","Sirin Stencil","Six Caps","Skranji","Slackey","Smokum","Smythe","Sniglet","Snippet","Snowburst One","Sofadi One","Sofia","Sonsie One","Sorts Mill Goudy","Source Code Pro","Source Sans Pro","Special Elite","Spicy Rice","Spinnaker","Spirax","Squada One","Stalemate","Stalinist One","Stardos Stencil","Stint Ultra Condensed","Stint Ultra Expanded","Stoke","Strait","Sue Ellen Francisco","Sunshiney","Supermercado One","Suwannaphum","Swanky and Moo Moo","Syncopate","Tangerine","Taprom","Tauri","Telex","Tenor Sans","Text Me One","The Girl Next Door","Tienne","Tinos","Titan One","Titillium Web","Trade Winds","Trocchi","Trochut","Trykker","Tulpen One","Ubuntu","Ubuntu Condensed","Ubuntu Mono","Ultra","Uncial Antiqua","Underdog","Unica One","UnifrakturCook","UnifrakturMaguntia","Unkempt","Unlock","Unna","Vampiro One","Varela","Varela Round","Vast Shadow","Vibur","Vidaloka","Viga","Voces","Volkhov","Vollkorn","Voltaire","VT323","Waiting for the Sunrise","Wallpoet","Walter Turncoat","Warnes","Wellfleet","Wendy One","Wire One","Yanone Kaffeesatz","Yellowtail","Yeseva One","Yesteryear","Zeyada");

}

/*-----------------------------------------------------------------------------------*/
/* Create The Custom Site Options Panel
/*-----------------------------------------------------------------------------------*/
$options = array(); // do not delete this line - sky will fall

/* General Settings */
$options[] = array( "name" => __('General','framework_localize'),
			"type" => "heading");

if (isset($logo_loc)) {
$options[] = array( "name" => __('Logo Location','framework_localize'),
			"desc" => __('Set the location of your logo.','framework_localize'),
			"id" => $shortname."_logo_loc",
			"std" => "Small in navigation",
			"type" => "select",
			"options" => $logo_loc);
}

$options[] = array( "name" => __('Logo','framework_localize'),
			"desc" => __('Select a file to appear as the logo for your site.','framework_localize'),
			"id" => $shortname."_logo",
			"std" => "",
			"type" => "upload");

$options[] = array( "name" => __('Custom Favicon','framework_localize'),
			"desc" => __('Upload a 16x16px PNG/GIF image that will represent your website\'s favicon.','framework_localize'),
			"id" => $shortname."_favicon",
			"std" => "",
			"type" => "upload");


$options[] = array( "name" => __('Tracking Code','framework_localize'),
			"desc" => "Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.",
			"id" => $shortname."_tracking",
			"std" => "",
			"type" => "textarea");

$options[] = array( "name" => __('Custom CSS','framework_localize'),
			"desc" => "Enter your custom CSS here. You will not lose any of the CSS you enter here if you update the theme to a new version.",
			"id" => $shortname."_customcss",
			"std" => "",
			"type" => "textarea");

$options[] = array( "name" => __('Number of posts per page','framework_localize'),
			"desc" => "Set the number of posts per page that you want displayed on the Homepage Blog and the Latest News Template.",
			"id" => $shortname."_posts_num",
			"std" => "10",
			"type" => "text");

$options[] = array( "name" => __('Toggle Responsiveness','framework_localize'),
			"desc" => "Uncheck this box if you would like to remove the responsiveness of the theme.",
			"id" => $shortname."_respond",
			"std" => "true",
			"type" => "checkbox");

$options[] = array( "name" => __('Toggle Sticky Sidebar','framework_localize'),
			"desc" => "Uncheck this box if you would like to remove the sticky sidebar feature.",
			"id" => $shortname."_sticky_sidebar",
			"std" => "true",
			"type" => "checkbox");

$options[] = array( "name" => __('Toggle Infinite Scroll','framework_localize'),
			"desc" => "Uncheck this box if you would like to remove the sticky sidebar feature.",
			"id" => $shortname."_infinite_scroll",
			"std" => "true",
			"type" => "checkbox");


/* Theme Color Settings */
$options[] = array( "name" => __('Colors','framework_localize'),
			"type" => "heading");

$options[] = array( "name" => __('Primary Theme Color','framework_localize'),
			"desc" => __('Primary color for the site.','framework_localize'),
			"id" => $shortname."_primary_theme",
			"std" => "#f00a71",
			"type" => "color");

$options[] = array( "name" => __('Main Menu Background Color','framework_localize'),
			"desc" => __('The background color of the main menu.','framework_localize'),
			"id" => $shortname."_menu_color",
			"std" => "#ffffff",
			"type" => "color");

$options[] = array( "name" => __('Primary Link Color','framework_localize'),
			"desc" => __('Primary link color for the site.','framework_localize'),
			"id" => $shortname."_link_color",
			"std" => "#f00a71",
			"type" => "color");

/* Font Settings */
$options[] = array( "name" => __('Fonts','framework_localize'),
			"type" => "heading");

if (isset($google_fonts)) {
$options[] = array( "name" => __('Featured Slider Headline Font','framework_localize'),
			"desc" => __('Select the font for the main headline in the Featured Slider section on the homepage and category pages.','framework_localize'),
			"id" => $shortname."_slider_headline",
			"std" => "Vidaloka",
			"type" => "select",
			"options" => $google_fonts);
}

if (isset($google_fonts)) {
$options[] = array( "name" => __('Main Menu Font','framework_localize'),
			"desc" => __('Select the font for the main menu.','framework_localize'),
			"id" => $shortname."_menu_font",
			"std" => "Raleway",
			"type" => "select",
			"options" => $google_fonts);
}

if (isset($google_fonts)) {
$options[] = array( "name" => __('General Headline Font','framework_localize'),
			"desc" => __('Select the font for the general headline font used in widgets, archive pages and page/post titles.','framework_localize'),
			"id" => $shortname."_headline_font",
			"std" => "Playfair Display",
			"type" => "select",
			"options" => $google_fonts);
}

if (isset($google_fonts)) {
$options[] = array( "name" => __('Header Font','framework_localize'),
			"desc" => __('Select the font for the headers around the site.','framework_localize'),
			"id" => $shortname."_header_font",
			"std" => "Oswald",
			"type" => "select",
			"options" => $google_fonts);
}


/* Featured Slider Settings */
$options[] = array( "name" => __('Home Featured Slider','framework_localize'),
			"type" => "heading");

$options[] = array( "name" => __('Attention','framework_localize'),
			"desc" => "",
			"id" => $shortname."_attention_home_slider",
			"std" => "In order to utilize these functions, you will have to set up your homepage as a static page. Please refer to the Installing Demo Data section of the documentation for more information.",
			"type" => "info");

$options[] = array( "name" => __('Show Featured Slider?','framework_localize'),
			"desc" => "Uncheck this box if you would like to remove the Featured Slider from the homepage.",
			"id" => $shortname."_slider",
			"std" => "true",
			"type" => "checkbox");

if (isset($tt_tags)) {
$options[] = array( "name" => __('Featured Slider Tag Slug','framework_localize'),
			"desc" => __('Posts with this Tag will be displayed in the Featured Slider at the top of the homepage.','framework_localize'),
			"id" => $shortname."_slider_tags",
			"std" => "featured",
			"type" => "select",
			"options" => $tt_tags);
}

$options[] = array( "name" => __('Maximum Featured Slider Items','framework_localize'),
			"desc" => "Set the maximum number of items (posts) to appear in the Featured Slider.",
			"id" => $shortname."_slider_num",
			"std" => "6",
			"type" => "text");


/* Homepage Featured Settings */
$options[] = array( "name" => __('Home Featured Posts','framework_localize'),
			"type" => "heading");

$options[] = array( "name" => __('Attention','framework_localize'),
			"desc" => "",
			"id" => $shortname."_attention_home_featured",
			"std" => "In order to utilize these functions, you will have to set up your homepage as a static page. Please refer to the Installing Demo Data section of the documentation for more information.",
			"type" => "info");

$options[] = array( "name" => __('Show Featured Posts Section?','framework_localize'),
			"desc" => "Uncheck this box if you would like to remove the Featured Posts Section from the homepage.",
			"id" => $shortname."_featured_posts",
			"std" => "true",
			"type" => "checkbox");

if (isset($feat_post)) {
$options[] = array( "name" => __('Featured Posts Layout','framework_localize'),
			"desc" => __('Choose your layout for the Featured Posts section.','framework_localize'),
			"id" => $shortname."_feat_post",
			"std" => "Featured Posts 1",
			"type" => "select",
			"options" => $feat_post);
}

if (isset($tt_categories)) {
$options[] = array( "name" => __('Featured Posts 1 Left Category','framework_localize'),
			"desc" => __('If you are using the Featured Posts 1 option, posts with this Category will be displayed to the left of the Featured Post on the homepage.','framework_localize'),
			"id" => $shortname."_featured_left",
			"std" => "Fashion",
			"type" => "select",
			"options" => $tt_categories);
}

if (isset($tt_categories)) {
$options[] = array( "name" => __('Featured Posts 1 Right Category','framework_localize'),
			"desc" => __('If you are using the Featured Posts 1 option, posts with this Category will be displayed to the right of the Featured Post on the homepage.','framework_localize'),
			"id" => $shortname."_featured_right",
			"std" => "Beauty",
			"type" => "select",
			"options" => $tt_categories);
}


/* Homepage Body Settings */
$options[] = array( "name" => __('Home Body Layout','framework_localize'),
			"type" => "heading");

$options[] = array( "name" => __('Attention','framework_localize'),
			"desc" => "",
			"id" => $shortname."_attention_home_body",
			"std" => "In order to utilize these functions, you will have to set up your homepage as a static page. Please refer to the Installing Demo Data section of the documentation for more information.",
			"type" => "info");

if (isset($home_layout)) {
$options[] = array( "name" => __('Homepage Layout','framework_localize'),
			"desc" => __('Select your layout for the body of the homepage.','framework_localize'),
			"id" => $shortname."_home_layout",
			"std" => "1",
			"type" => "select",
			"options" => $home_layout);
}

$options[] = array( "name" => __('Homepage Blog Heading','framework_localize'),
			"desc" => "Set the heading above the blog layout on the homepage.",
			"id" => $shortname."_blog_header",
			"std" => "The Latest",
			"type" => "text");

if (isset($admin_images)) {
$options[] = array( "name" => __('Homepage Blog Layout','framework_localize'),
			"desc" => __('If you chose the Blog-style homepage layout, you can choose between three different layout options: The large image, the horizontal list, or the two columns.','framework_localize'),
			"id" => $shortname."_blog_layout",
			"std" => "large",
			"type" => "images",
			"options" => array(
				'large' => $admin_images . 'large.gif',
				'list' => $admin_images . 'list.gif',
				'columns' => $admin_images . 'columns.gif'
				));
}


/* Article Settings */
$options[] = array( "name" => __('Article Settings','framework_localize'),
			"type" => "heading");

$options[] = array( "name" => __('Show Featured Image In Posts?','framework_localize'),
			"desc" => __('Uncheck this box if you would like to remove the featured image thumbnail from all posts.','framework_localize'),
			"id" => $shortname."_featured_img",
			"std" => "true",
			"type" => "checkbox");

$options[] = array( "name" => __('Show Social Sharing Buttons?','framework_localize'),
			"desc" => "Uncheck this box if you would like to remove the social sharing buttons from all posts and pages.",
			"id" => $shortname."_social_box",
			"std" => "true",
			"type" => "checkbox");

$options[] = array( "name" => __('Show Author Info?','framework_localize'),
			"desc" => "Uncheck this box if you would like to remove the author info box from the bottom of the posts.",
			"id" => $shortname."_author_box",
			"std" => "true",
			"type" => "checkbox");

$options[] = array( "name" => __('Show Previous/Next Post Links?','framework_localize'),
			"desc" => "Uncheck this box if you would like to remove the links to the previous/next posts below each article.",
			"id" => $shortname."_prev_next",
			"std" => "true",
			"type" => "checkbox");


/* Article Settings */
$options[] = array( "name" => __('Category Pages','framework_localize'),
			"type" => "heading");

$options[] = array( "name" => __('Show Featured Slider','framework_localize'),
			"desc" => "Uncheck this box if you would like to remove the Featured Slider from the category pages.",
			"id" => $shortname."_slider_cat",
			"std" => "true",
			"type" => "checkbox");

$options[] = array( "name" => __('Maximum Featured Slider Items','framework_localize'),
			"desc" => "Set the maximum number of items (posts) to appear in the Featured Slider.",
			"id" => $shortname."_slider_cat_num",
			"std" => "3",
			"type" => "text");

if (isset($admin_images)) {
$options[] = array( "name" => __('Category Body Layout','framework_localize'),
			"desc" => __('Choose between three different layout options for your category pages: The large image, the horizontal list, or the two columns.','framework_localize'),
			"id" => $shortname."_category_layout",
			"std" => "list",
			"type" => "images",
			"options" => array(
				'large' => $admin_images . 'large.gif',
				'list' => $admin_images . 'list.gif',
				'columns' => $admin_images . 'columns.gif'
				));
}


/* Social Media Settings */
$options[] = array( "name" => __('Social Media','framework_localize'),
			"type" => "heading");

$options[] = array( "name" => __('Attention','framework_localize'),
			"desc" => "",
			"id" => $shortname."_attention_ad",
			"std" => "While most fields require just the username, Google Plus requires the full URL to your Google Plus Page.",
			"type" => "info");

$options[] = array( "name" => __('Facebook','framework_localize'),
			"desc" => "Enter your Facebook Page username here.",
			"id" => $shortname."_facebook",
			"std" => "",
			"type" => "text");

$options[] = array( "name" => __('Twitter','framework_localize'),
			"desc" => "Enter your Twitter username here.",
			"id" => $shortname."_twitter",
			"std" => "",
			"type" => "text");

$options[] = array( "name" => __('Pinterest','framework_localize'),
			"desc" => "Enter your Pinterest username here.",
			"id" => $shortname."_pinterest",
			"std" => "",
			"type" => "text");

$options[] = array( "name" => __('Instagram','framework_localize'),
			"desc" => "Enter your Instagram username here.",
			"id" => $shortname."_instagram",
			"std" => "",
			"type" => "text");

$options[] = array( "name" => __('Google Plus','framework_localize'),
			"desc" => "Enter your full Google Plus URL here.",
			"id" => $shortname."_google",
			"std" => "",
			"type" => "text");

$options[] = array( "name" => __('Youtube','framework_localize'),
			"desc" => "Enter your Youtube username here.",
			"id" => $shortname."_youtube",
			"std" => "",
			"type" => "text");

$options[] = array( "name" => __('Linkedin','framework_localize'),
			"desc" => "Enter your Linkedin username here.",
			"id" => $shortname."_linkedin",
			"std" => "",
			"type" => "text");

$options[] = array( "name" => __('Custom RSS Link','framework_localize'),
			"desc" => "If you want to replace the default RSS link with a custom RSS link (like Feedburner), enter the URL here.",
			"id" => $shortname."_rss",
			"std" => "",
			"type" => "text");


/* Ad Management Settings */
$options[] = array( "name" => __('Ad Management','framework_localize'),
			"type" => "heading");

$options[] = array( "name" => __('Attention','framework_localize'),
			"desc" => "",
			"id" => $shortname."_attention_ad",
			"std" => "The 300x250 ads are controlled via a Widget.",
			"type" => "info");

$options[] = array( "name" => __('Header Leaderboard Ad Code','framework_localize'),
			"desc" => "Enter your ad code (Eg. Google Adsense) for the 970x90 ad area. You can also place a 728x90 ad in this spot.",
			"id" => $shortname."_header_leader",
			"std" => "",
			"type" => "textarea");

$options[] = array( "name" => __('Footer Leaderboard Ad Code','framework_localize'),
			"desc" => "Enter your ad code (Eg. Google Adsense) for the 970x90 ad area. You can also place a 728x90 ad in this spot.",
			"id" => $shortname."_footer_leader",
			"std" => "",
			"type" => "textarea");

$options[] = array( "name" => __('Wallpaper Ad Image URL','framework_localize'),
			"desc" => "Enter the URL for your wallpaper ad image. Wallpaper ad code should be a minimum of 1280px wide. Please see the theme documentation for more on wallpaper ad specifications.",
			"id" => $shortname."_wall_ad",
			"std" => "",
			"type" => "text");

$options[] = array( "name" => __('Wallpaper Ad URL','framework_localize'),
			"desc" => "Enter the URL for your wallpaper ad click-through.",
			"id" => $shortname."_wall_url",
			"std" => "",
			"type" => "text");


/* Footer Settings */
$options[] = array( "name" => __('Footer Info','framework_localize'),
			"type" => "heading");

$options[] = array( "name" => __('Show Footer Info Box?','framework_localize'),
			"desc" => "Uncheck this box if you would like to remove the Footer Info Box.",
			"id" => $shortname."_footer_info",
			"std" => "true",
			"type" => "checkbox");

$options[] = array( "name" => __('Footer Logo','framework_localize'),
			"desc" => __('Select a file to appear as the logo in the Footer Info Box.','framework_localize'),
			"id" => $shortname."_logo_footer",
			"std" => "",
			"type" => "upload");

$options[] = array( "name" => __('Footer Info Text','framework_localize'),
			"desc" => "Enter any text to display in the Footer Info Box.",
			"id" => $shortname."_footer_text",
			"std" => "<p>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem?</p><p>Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet.</p>",
			"type" => "textarea");

$options[] = array( "name" => __('Copyright Text','framework_localize'),
			"desc" => "Here you can enter any text you want (eg. copyright text)",
			"id" => $shortname."_copyright",
			"std" => "Copyright &copy; 2013 Braxton Theme. Theme by MVP Themes, powered by Wordpress.",
			"type" => "textarea");



update_option('of_template',$options);

update_option('of_shortname',$shortname);

}
}
?>