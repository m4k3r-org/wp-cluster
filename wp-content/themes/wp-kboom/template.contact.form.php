<?php
/*
 * Template Name: Contact Form Page
*/
if(isset($_POST['submitted'])) {
		//Check to make sure that the name field is not empty
		if(trim($_POST['contactName']) === '') {
			$nameError = __("You forgot to enter your name.", "site5framework");
			$hasError = true;
		} else {
			$name = trim($_POST['contactName']);
		}

		//Check to make sure sure that a valid email address is submitted
		if(trim($_POST['email']) === '')  {
			$emailError = __("You forgot to enter your email address.", "site5framework");
			$hasError = true;
		} else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email']))) {
			$emailError = __("You entered an invalid email address.", "site5framework");
			$hasError = true;
		} else {
			$email = trim($_POST['email']);
		}

		//Check to make sure comments were entered
		if(trim($_POST['comments']) === '') {
			$commentError = __("You forgot to enter your comments.", "site5framework");
			$hasError = true;
		} else {
			if(function_exists('stripslashes')) {
				$comments = stripslashes(trim($_POST['comments']));
			} else {
				$comments = trim($_POST['comments']);
			}
		}

		//If there is no error, send the email
		if(!isset($hasError)) {
			$msg .= "------------User Info------------ \r\n"; //Title
			$msg .= "User IP : ".$_SERVER["REMOTE_ADDR"]."\r\n"; //Sender's IP
			$msg .= "Browser Info : ".$_SERVER["HTTP_USER_AGENT"]."\r\n"; //User agent
			$msg .= "User Come From : ".$_SERVER["HTTP_REFERER"]; //Referrer
			
			$emailTo = ''.of_get_option('sc_contact_email').'';
			$subject = 'Contact Form Submission From '.$name;
			$body = "Name: $name \n\nEmail: $email \n\nMessage: $comments \n\n $msg";
			$headers = 'From: '.get_bloginfo('name').' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;

			if(mail($emailTo, $subject, $body, $headers)) $emailSent = true;

	}
	 
}
get_header(); 
?>
        <div id="white-background">
            <!-- content -->
            <div id="content" class="container clearfix" style="padding-bottom: 0;">

                <div class="container">
                    <div class="page-title-heading">
                        <h2><?php the_title(); ?><?php if ( !get_post_meta($post->ID, 'snbpd_pagedesc', true)== '') { ?> / <?php }?> <span><?php echo get_post_meta($post->ID, 'snbpd_pagedesc', true); ?></span></h2>
                    </div>
                </div>
                <div class="title-border"></div>

                <?php of_get_option('sc_contact_map') ?>
                <!-- contact map -->
                <div id="contact-map">
                    <?php echo of_get_option('sc_contact_map') ?>

                </div>
                <!-- end contact map -->

				<!-- content -->
				<div class="container">
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">

						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                        <div class="container clearfix">
                            <div class="title-border" style="float: left;"></div>
                        </div>

                        <?php if(of_get_option('sc_contactcontent') == '1') { ?>
                            <div class="container clearfix">
								<?php the_content(); ?>
                                <div class="space"></div>
                                <div class="title-border" style="float: left;"></div>
                            </div>
                        <?php } ?>

							<div class="container clearfix">
								<div id="messages">
									<p class="simple-error error" <?php if($hasError != '') echo 'style="display:block;"'; ?>><?php _e('There was an error submitting the form.', 'site5framework'); ?></p>
				                    
				                    <p class="simple-success thanks"><?php _e('<strong>Thanks!</strong> Your email was successfully sent. We should be in touch soon.', 'site5framework'); ?></p>
			                	</div>
								
								<form id="contactForm" method="POST">
                                    <div class="one-half">
										<label for="nameinput"><?php _e("Your name*", "site5framework"); ?></label>
										<input type="text" id="nameinput" name="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" class="requiredField"/>

                                        <span class="error" <?php if($nameError != '') echo 'style="display:block;"'; ?>><?php _e("You forgot to enter your name.", "site5framework");?></span>
										<label for="emailinput"><?php _e("Your email*", "site5framework"); ?></label>
											<input type="text" id="emailinput" name="email" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="requiredField email"/>
										  <span class="error" <?php if($emailError != '') echo 'style="display:block;"'; ?>><?php _e("You forgot to enter your email address.", "site5framework");?></span>
				                    </div>

                                    <div class="one-half last" id="contact-info-wrapper">
                                        <?php if(of_get_option('sc_displaycaddress') == '1') { ?>
                                            <div class="caddress"><strong><?php _e('Address:', 'site5framework') ?></strong> <?php echo of_get_option('sc_contact_address') ?></div>
                                        <?php } ?>

                                        <?php if(of_get_option('sc_displaycphone') == '1') { ?>
                                            <div class="cphone"><strong><?php _e('Phone:', 'site5framework') ?></strong> <?php echo of_get_option('sc_contact_phone') ?></div>
                                        <?php } ?>

                                        <?php if(of_get_option('sc_displaycfax') == '1') { ?>
                                            <div class="cfax"><strong><?php _e('Fax:', 'site5framework') ?></strong> <?php echo of_get_option('sc_contact_fax') ?></div>
                                        <?php } ?>

                                        <?php if(of_get_option('sc_displaycemail') == '1') { ?>
                                            <div class="cemail"><strong><?php _e('E-mail:', 'site5framework') ?></strong> <?php echo of_get_option('sc_contact_email') ?></div>
                                        <?php } ?>
                                    </div>

                                    <div class="text-area-wrapper">
									<label for="Mymessage"><?php _e("Your message*", "site5framework"); ?></label>
										<textarea cols="20" rows="20" id="Mymessage" name="comments" class="requiredField"><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
										  <span class="error" <?php if($commentError != '') echo 'style="display:block;"'; ?>><?php _e("You forgot to enter your comments.", "site5framework");?></span>
                                    </div>
				                    <br class="clear" />
				                    <input type="hidden" name="submitted" id="submitted" value="true" />
									<button type="submit" id="submitbutton" class="button"><?php _e(' &nbsp;Send Message&nbsp; ', 'site5framework'); ?></button>
			               
								</form>
								
							</div>

						<?php endwhile; ?>		
					</article>

					<?php else : ?>
						
					<article id="post-not-found">
						<header>
							<h1><?php _e("Not Found", "site5framework"); ?></h1>
						</header>
						<section class="post_content">
							<p><?php _e("Sorry, but the requested resource was not found on this site.", "site5framework"); ?></p>
						</section>
						<footer>
						</footer>
					</article>
					
					<?php endif; ?>
				</div>
			</div> <!-- end content -->
        </div><!-- end #white-background -->

<?php get_footer(); ?>