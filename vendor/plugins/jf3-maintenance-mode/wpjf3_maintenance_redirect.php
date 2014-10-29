<?php
/*
Plugin Name: JF3 Maintenance Redirect
Plugin URI:  http://www.hooziewhats.com/wpjf3_maintenance_mode
Description: This plugin allows you to specify a maintenance mode message / page for your site as well as configure settings to allow specific users to bypass the maintenance mode functionality in order to preview the site prior to public launch, etc. FIXED REDIRECT ISSUE!
Version:     2.0
Author:      Jack Finch
*/

/*  Copyright 2010-2012  Jack Finch  (email : jack@hooziewhats.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if( !class_exists("wpjf3_maintenance_redirect") ) {
	class wpjf3_maintenance_redirect {
		
		var $admin_options_name    = "wpjf3_mr";
		var $maintenance_html_head = '<html><head><link href="[[WP_STYLE]]" rel="stylesheet" type="text/css" /><title>[[WP_TITLE]]</title></head><body><div style="margin-left:auto; margin-right: auto; width: 500px; border:1px solid #000; color: #000; background-color: #fff; padding: 10px; margin-top:200px">';
		var $maintenance_html_foot = '</div></body></html>';
		var $maintenance_html_body = 'This site is currently undergoing maintenance. Please check back later.';
		
		// (php) constructor.
		function wpjf3_maintenance_redirect() { }
		
		// (php) initialize.
		function init() {
			global $wpdb;
			
			// create keys table if needed.
			$tbl = $wpdb->prefix . $this->admin_options_name . "_access_keys";
    	if( $wpdb->get_var( "SHOW TABLES LIKE '$tbl'" ) != $tbl ) {
				$sql = "create table $tbl ( id int auto_increment primary key, name varchar(100), access_key varchar(20), email varchar(100), created_at datetime not null default '0000-00-00 00:00:00', active int(1) not null default 1 )";
				$wpdb->query($sql);
			}
			
			// create IPs table if needed
			$tbl = $wpdb->prefix . $this->admin_options_name . "_unrestricted_ips";
    	if( $wpdb->get_var( "SHOW TABLES LIKE '$tbl'" ) != $tbl ) {
				$sql = "create table $tbl ( id int auto_increment primary key, name varchar(100), ip_address varchar(20), created_at datetime not null default '0000-00-00 00:00:00', active int(1) not null default 1 )";
				$wpdb->query($sql);
			}
			
			// setup options
			add_option("wpjf3_maintenance_redirect_version", "1.3");
			$tmp_opt = $this->get_admin_options();	
		}
		
		// (php) find user IP.
		function get_user_ip(){
			$ip = ( $_SERVER['HTTP_X_FORWARD_FOR'] ) ? $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
			return $ip;
		}

		// (php) determine user class c
		function get_user_class_c(){
			$ip = $this->get_user_ip();
			$ip_parts = explode( '.', $ip );
			$class_c = $ip_parts[0] . '.' . $ip_parts[1] . '.' .$ip_parts[2] . '.*';
			return $class_c;
		}
		
		// (php) get and return an array of admin options. if no options set, initialize.
		function get_admin_options() {
			$wpjf3_mr_options = array(
				'enable_redirect'  => 'no',
				'method'           => 'message',
				'maintenance_html' => $this->default_maintenance_html,
				'static_page'      => ''
			);
			
			$wpjf3_mr_saved_options = get_option($this->admin_options_name);
			
			if( !empty($wpjf3_mr_saved_options) ) {
				foreach($wpjf3_mr_saved_options as $key => $option)
					$wpjf3_mr_options[$key] = $option;
			}else{
				update_option($this->admin_options_name, $wpjf3_mr_options);
			}
			return $wpjf3_mr_options;
		}
		
		// (php) generate key
		function alphastring( $len = 20, $valid_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' ){
			$str  = '';
			$chrs = explode( ' ', $valid_chars );
			for( $i=0; $i<$len; $i++ ){
				$str .= $valid_chars[ rand( 1, strlen( $valid_chars ) - 1 ) ];
			}
			return $str;
		}
		
		// (php) generate maintenance page
		function generate_maintenance_page( $msg_override = '', $skip_header_footer = false ){
			if( $skip_header_footer ){
				$html  = ( $msg_override != '' ) ? stripslashes( $msg_override ) : $this->maintenance_html_body;
			}else{
				$html  = $this->maintenance_html_head;
				$html  = str_replace( '[[WP_TITLE]]', get_bloginfo('name'), $html );
				$html  = str_replace( '[[WP_STYLE]]', get_bloginfo('stylesheet_url'), $html );
				$html .= ( $msg_override != '' ) ? stripslashes( $msg_override ) : $this->maintenance_html_body;
				$html .= $this->maintenance_html_foot;
			}		
			header('HTTP/1.1 503 Service Temporarily Unavailable');
			header('Status: 503 Service Temporarily Unavailable');
			header('Retry-After: 600');
			echo $html;
			exit();
		}
		
		// (php) find out if we need to redirect or not.
		function process_redirect() {
			global $wpdb;
			$valid_ips      = array();
			$valid_class_cs = array();
			$valid_aks      = array();
			$wpjf3_matches  = array();
      
			// set cookie if needed
			if ( trim( $_GET['wpjf3_mr_temp_access_key'] ) != '' ) {
				// get valid access keys
				$sql = "select access_key from " . $wpdb->prefix . $this->admin_options_name . "_access_keys where active = 1";
				$aks = $wpdb->get_results($sql, OBJECT);
				if( $aks ){
					foreach( $aks as $ak ){
						$valid_aks[] = $ak->access_key;
					}
				}
				
				// set cookie if there's a match
				if( in_array( $_GET['wpjf3_mr_temp_access_key'], $valid_aks ) ){
					$wpjf3_mr_cookie_time = time()+(60*60*24*365);
					setcookie( 'wpjf3_mr_access_key', $_GET['wpjf3_mr_temp_access_key'], $wpjf3_mr_cookie_time, '/' );
					$_COOKIE['wpjf3_mr_access_key'] = $_GET['wpjf3_mr_temp_access_key'];
				}
			}
			
			// get plugin options
			$wpjf3_mr_options = $this->get_admin_options();
			
			// skip admin pages by default
			$url_parts = explode( '/', $_SERVER['REQUEST_URI'] );
			if( in_array( 'wp-admin', $url_parts ) ) {
				$wpjf3_matches[] = "<!-- WPJF_MR: SKIPPING ADMIN -->";
			}else{
				// determine if user is admin.. if so, bypass all of this.
				if( current_user_can('manage_options') ) {
					$wpjf3_matches[] = "<!-- WPJF_MR: USER IS ADMIN -->";
				}else{
					if( $wpjf3_mr_options['enable_redirect'] == "YES" ){
						// get valid unrestricted IPs
						$sql = "select ip_address from " . $wpdb->prefix . $this->admin_options_name . "_unrestricted_ips where active = 1";
						$ips = $wpdb->get_results($sql, OBJECT);
						if( $ips ){
							foreach( $ips as $ip ){
								$ip_parts = explode( '.', $ip->ip_address );
								if( $ip_parts[3] == '*' ){
									$valid_class_cs[] = $ip_parts[0] . '.' . $ip_parts[1] . '.' . $ip_parts[2];
								}else{
									$valid_ips[] = $ip->ip_address;
								}
							}
						}
						
						// get valid access keys
						$valid_aks = array();
						$sql = "select access_key from " . $wpdb->prefix . $this->admin_options_name . "_access_keys where active = 1";
						$aks = $wpdb->get_results($sql, OBJECT);
						if( $aks ){
							foreach( $aks as $ak ){
								$valid_aks[] = $ak->access_key;
							}
						}
						
						// manage cookie filtering
						if( $_COOKIE['wpjf3_mr_access_key'] != '' ){
							// check versus active codes
							if( in_array( $_COOKIE['wpjf3_mr_access_key'], $valid_aks ) ){
								$wpjf3_matches[] = "<!-- WPJF_MR: COOKIE MATCH -->";
							}
						}
						
						// manage ip filtering 
						if( in_array( $this->get_user_ip(), $valid_ips ) ) {
							$wpjf3_matches[] = "<!-- WPJF_MR: IP MATCH -->";
						}else{
							// check for partial ( class c ) match
							$ip_parts     = explode( '.', $this->get_user_ip() );
							$user_class_c = $ip_parts[0] . '.' . $ip_parts[1] . '.' . $ip_parts[2];
							if( in_array( $user_class_c, $valid_class_cs ) ) {
								$wpjf3_matches[] = "<!-- WPJF_MR: CLASS C MATCH -->";
							}
						}
						
						if( count( $wpjf3_matches ) == 0 ) {
							// no match found. show maintenance page / message
							if( $wpjf3_mr_options['method'] == 'redirect' ){
								// redirect
								//header('HTTP/1.1 503 Service Temporarily Unavailable');
								//header('Status: 503 Service Temporarily Unavailable');
								//header('Retry-After: 600');
								//header ( 'Location:'.$wpjf3_mr_options['static_page'] );
                wp_redirect( $wpjf3_mr_options['static_page'] );
								exit();
							}else	if( $wpjf3_mr_options['method'] == 'html' ){
									// html entered only. do not wrap with header or footer
									$this->generate_maintenance_page( $wpjf3_mr_options['maintenance_html'], true );
							}else{
								// message
								$this->generate_maintenance_page( $wpjf3_mr_options['maintenance_html'] );
							}
						}
					}else{
						$wpjf3_matches[] = "<!-- WPJF_MR: REDIR DISABLED -->";
					}
				}
			}
		}
		
		// (php) add new IP
		function add_new_ip() {
			global $wpdb;
			$tbl        = $wpdb->prefix . $this->admin_options_name . '_unrestricted_ips';
			$name       = $wpdb->escape( stripslashes( $_POST['wpjf3_mr_ip_name'] ) );
			$ip_address = $wpdb->escape( stripslashes( trim( $_POST['wpjf3_mr_ip_ip'] ) ) );
			$sql        = "insert into $tbl ( name, ip_address, created_at ) values ( '$name', '$ip_address', NOW() )";
			$rs         = $wpdb->query( $sql );
			if( $rs ){
				// send table data
				$this->print_unrestricted_ips();
			}else{
				echo 'Unable to add IP because of a database error. Please reload the page.';
			}
			die();
		}
		
		// (php) toggle IP status
		function toggle_ip_status(){
			global $wpdb;
			$tbl       = $wpdb->prefix . $this->admin_options_name . '_unrestricted_ips';
			$ip_id     = $wpdb->escape( $_POST['wpjf3_mr_ip_id'] );
			$ip_active = ( $_POST['wpjf3_mr_ip_active'] == 1 ) ? 1 : 0;
			$sql       = "update $tbl set active = '$ip_active' where id = '$ip_id'";
			$rs        = $wpdb->query( $sql );
			if( $rs ){
				// $this->print_unrestricted_ips();
				echo 'SUCCESS|' . $ip_id . '|' . $ip_active;
			}else{
				// echo 'There was an unknown database error. Please reload the page.';
				echo 'ERROR';
			}
			die();
		}
		
		// (php) delete IP
		function delete_ip(){
			global $wpdb;
			$tbl       = $wpdb->prefix . $this->admin_options_name . '_unrestricted_ips';
			$ip_id     = $wpdb->escape( $_POST['wpjf3_mr_ip_id'] );
			$sql       = "delete from $tbl where id = '$ip_id'";
			$rs        = $wpdb->query( $sql );
			if( $rs ){
				$this->print_unrestricted_ips();
			}else{
				echo 'Unable to delete IP because of a database error. Please reload the page.';
			}
			die();
		}
		
		// (php) add new Access Key
		function add_new_ak() {
			global $wpdb;
			$tbl        = $wpdb->prefix . $this->admin_options_name . '_access_keys';
			$name       = $wpdb->escape( stripslashes( $_POST['wpjf3_mr_ak_name'] ) );
			$email      = $wpdb->escape( stripslashes( $_POST['wpjf3_mr_ak_email'] ) );
			$access_key = $wpdb->escape( $this->alphastring(20) );
			$sql        = "insert into $tbl ( name, email, access_key, created_at ) values ( '$name', '$email', '$access_key', NOW() )";
			$rs         = $wpdb->query( $sql );
			if( $rs ){
				// email user
				$subject    = 'Access Key Link for ' . get_bloginfo();
				$full_msg   = 'The following link will provide you temporary access to ' . get_bloginfo() .':'."\n\n";
				$full_msg  .= get_bloginfo('url') . '?wpjf3_mr_temp_access_key=' . $access_key;
				$mail_sent  = wp_mail( $email, $subject, $full_msg );
				echo ( $mail_sent ) ? '<!-- SEND_SUCCESS -->' : '<!-- SEND_FAILURE -->';
				// send table data
				$this->print_access_keys();
			}else{
				echo 'Unable to add Access Key because of a database error. Please reload the page.';
			}
			die();
		}
		
		// (php) toggle Access Key status
		function toggle_ak_status(){
			global $wpdb;
			$tbl       = $wpdb->prefix . $this->admin_options_name . '_access_keys';
			$ak_id     = $wpdb->escape( $_POST['wpjf3_mr_ak_id'] );
			$ak_active = ( $_POST['wpjf3_mr_ak_active'] == 1 ) ? 1 : 0;
			$sql       = "update $tbl set active = '$ak_active' where id = '$ak_id'";
			$rs        = $wpdb->query( $sql );
			if( $rs ){
				// $this->print_access_keys();
				echo 'SUCCESS|' . $ak_id . '|' . $ak_active;
			}else{
				// echo 'There was an unknown database error. Please reload the page.';
				echo 'ERROR';
			}
			die();
		}
		
		// (php) delete Access Key
		function delete_ak(){
			global $wpdb;
			$tbl       = $wpdb->prefix . $this->admin_options_name . '_access_keys';
			$ak_id     = $wpdb->escape( $_POST['wpjf3_mr_ak_id'] );
			$sql       = "delete from $tbl where id = '$ak_id'";
			$rs        = $wpdb->query( $sql );
			if( $rs ){
				$this->print_access_keys();
			}else{
				echo 'Unable to delete Access Key because of a database error. Please reload the page.';
			}
			die();
		}
		
		// (php) resend Access Key email
		function resend_ak(){
			global $wpdb;
			$tbl       = $wpdb->prefix . $this->admin_options_name . '_access_keys';
			$ak_id     = $wpdb->escape( $_POST['wpjf3_mr_ak_id'] );
			$sql       = "select * from $tbl where id = '$ak_id'";
			$ak        = $wpdb->get_row( $sql );
			if( $ak ){
				$subject    = 'Access Key Link for ' . get_bloginfo();
				$full_msg   = 'The following link will provide you temporary access to ' . get_bloginfo() .':'."\n\n";
				$full_msg  .= get_bloginfo('url') . '?wpjf3_mr_temp_access_key=' . $ak->access_key;
				$mail_sent  = wp_mail( $ak->email, $subject, $full_msg );
				echo ( $mail_sent ) ? 'SEND_SUCCESS' : 'SEND_FAILURE';
			}else{
				echo 'ERROR';
			}
			die();
		}
		
		// (php) generate IP table data 
		function print_unrestricted_ips( ){
			global $wpdb;
			?>
			<table class="widefat fixed" cellspacing="0">
				<thead>
					<tr>
						<th class="column-wpjf3-ip-name"  >Name</th>
						<th class="column-wpjf3-ip-ip"    >IP</th>
						<th class="column-wpjf3-ip-active">Active</th>
						<th class="column-wpjf3-actions"  >Actions</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th class="column-wpjf3-ip-name"  >Name</th>
						<th class="column-wpjf3-ip-ip"    >IP</th>
						<th class="column-wpjf3-ip-active">Active</th>
						<th class="column-wpjf3-actions"  >Actions</th>
					</tr>
				</tfoot>

				<tbody>
					<?php
					$sql = "select * from " . $wpdb->prefix . $this->admin_options_name . "_unrestricted_ips order by name";
					$ips = $wpdb->get_results($sql, OBJECT);
					$ip_row_class = 'alternate';
					if( $ips ){
						foreach( $ips as $ip ){
							?>
							<tr id="wpjf-ip-<?php echo $ip->id; ?>" valign="middle"  class="<?php echo $ip_row_class; ?>">
								<td class="column-wpjf3-ip-name"><?php echo $ip->name; ?></td>
								<td class="column-wpjf3-ip-ip"><?php echo $ip->ip_address; ?></td>
								<td class="column-wpjf3-ip-active" id="wpjf3_mr_ip_status_<?php echo $ip->id; ?>" ><?php echo ( $ip->active == 1) ? 'Yes' : 'No'; ?></td>
								<td class="column-wpjf3-actions">
									<span class='edit' id="wpjf3_mr_ip_status_<?php echo $ip->id; ?>_action">
										<?php if( $ip->active == 1 ){ ?>
											<a href="javascript:wpjf3_mr_toggle_ip( 0, <?php echo $ip->id; ?> );">Disable</a> | 
										<?php }else{ ?>
											<a href="javascript:wpjf3_mr_toggle_ip( 1, <?php echo $ip->id; ?> );">Enable</a> | 
										<?php } ?>
									</span>
									<span class='delete'>
										<a class='submitdelete' href="javascript:wpjf3_mr_delete_ip( <?php echo $ip->id ?>, '<?php echo addslashes( $ip->ip_address ) ?>' );" >Delete</a>
									</span>
								</td>
							</tr>
							<?php
							$ac_row_class = ( $ac_row_class == '' ) ? 'alternate' : '';
						}
					}
					?>
					
					<tr id="wpjf-ip-NEW" valign="middle"  class="<?php echo $ip_row_class; ?>">
						<td class="column-wpjf3-ip-name">
							<input class="wpjf3_mr_disabled_field" type="text" id="wpjf3_mr_new_ip_name" name="wpjf3_mr_new_ip_name" value="Enter Name:" onfocus="wpjf3_mr_undim_field('wpjf3_mr_new_ip_name','Enter Name:');" onblur="wpjf3_mr_dim_field('wpjf3_mr_new_ip_name','Enter Name:');">
						</td>
						<td class="column-wpjf3-ip-ip">
							<input class="wpjf3_mr_disabled_field" type="text" id="wpjf3_mr_new_ip_ip" name="wpjf3_mr_new_ip_ip" value="Enter IP:" onfocus="wpjf3_mr_undim_field('wpjf3_mr_new_ip_ip','Enter IP:');" onblur="wpjf3_mr_dim_field('wpjf3_mr_new_ip_ip','Enter IP:');">
						</td>
						<td class="column-wpjf3-ip-active">&nbsp;</td>
						<td class="column-wpjf3-actions">
							<span class='edit' id="wpjf3_mr_add_ip_link">
								<a href="javascript:wpjf3_mr_add_new_ip( );">Add New IP</a>
							</span>
						</td>
					</tr>
					
				</tbody>
			</table>
			<?php
		}
		
		// (php) genereate Access Key table data
		function print_access_keys(){
			global $wpdb;
			?>
			<table class="widefat fixed" cellspacing="0">
				<thead>
					<tr>
						<th class="column-wpjf3-ak-name"  >Name</th>
						<th class="column-wpjf3-ak-email" >Email</th>
						<th class="column-wpjf3-ak-key"   >Access Key</th>
						<th class="column-wpjf3-ak-active">Active</th>
						<th class="column-wpjf3-actions"  >Actions</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th class="column-wpjf3-ak-name"  >Name</th>
						<th class="column-wpjf3-ak-email" >Email</th>
						<th class="column-wpjf3-ak-key"   >Access Key</th>
						<th class="column-wpjf3-ak-active">Active</th>
						<th class="column-wpjf3-actions"  >Actions</th>
					</tr>
				</tfoot>
				
				<tbody>
					<?php
					$sql   = "select * from " . $wpdb->prefix . $this->admin_options_name . "_access_keys order by name";
					$codes = $wpdb->get_results($sql, OBJECT);
					$ak_row_class = 'alternate';
					if( $codes ){
						foreach( $codes as $code ){
							?>
							<tr id="wpjf-ak-<?php echo $code->id; ?>" valign="middle"  class="<?php echo $ak_row_class; ?>">
								<td class="column-wpjf3-ak-name"><?php echo $code->name; ?></td>
								<td class="column-wpjf3-ak-email"><a href="mailto:<?php echo $code->email; ?>" title="email <?php echo $code->email; ?>"><?php echo $code->email; ?></a></td>
								<td class="column-wpjf3-ak-key"><?php echo $code->access_key; ?></td>
								<td class="column-wpjf3-ak-active" id="wpjf3_mr_ak_status_<?php echo $code->id; ?>" ><?php echo ( $code->active == 1) ? 'Yes' : 'No'; ?></td>
								<td class="column-wpjf3-actions">
									<span class='edit' id="wpjf3_mr_ak_status_<?php echo $code->id; ?>_action">
										<?php if( $code->active == 1 ){ ?>
											<a href="javascript:wpjf3_mr_toggle_ak( 0, <?php echo $code->id; ?> );">Disable</a> | 
										<?php }else{ ?>
											<a href="javascript:wpjf3_mr_toggle_ak( 1, <?php echo $code->id; ?> );">Enable</a> | 
										<?php } ?>
									</span>
									<span class='resend'>
										<a class='submitdelete' href="javascript:wpjf3_mr_resend_ak( <?php echo $code->id ?>, '<?php echo addslashes( $code->name ) ?>', '<?php echo addslashes( $code->email ) ?>' );" >Resend Code</a> | 
									</span>
									<span class='delete'>
										<a class='submitdelete' href="javascript:wpjf3_mr_delete_ak( <?php echo $code->id ?>, '<?php echo addslashes( $code->name ) ?>' );" >Delete</a>
									</span>
								</td>
							</tr>
							<?php
							$ak_row_class = ( $ak_row_class == '' ) ? 'alternate' : '';
						}
					}
					/*
					?>
					<tr id="wpjf-ak-NONE" valign="middle"  class="<?php echo $ak_row_class; ?>">
						<td colspan="5">Enter a New Access Code</td>
					</tr>
					<?php
					$ak_row_class = ( $ak_row_class == '' ) ? 'alternate' : '';
					*/
					?>
					<tr id="wpjf-ak-NEW" valign="middle"  class="<?php echo $ak_row_class; ?>">
						<td class="column-wpjf3-ak-name">
							<input class="wpjf3_mr_disabled_field" type="text" id="wpjf3_mr_new_ak_name" name="wpjf3_mr_new_ak_name" value="Enter Name:" onfocus="wpjf3_mr_undim_field('wpjf3_mr_new_ak_name','Enter Name:');" onblur="wpjf3_mr_dim_field('wpjf3_mr_new_ak_name','Enter Name:');">
						</td>
						<td class="column-wpjf3-ak-email">
							<input class="wpjf3_mr_disabled_field" type="text" id="wpjf3_mr_new_ak_email" name="wpjf3_mr_new_ak_email" value="Enter Email:" onfocus="wpjf3_mr_undim_field('wpjf3_mr_new_ak_email','Enter Email:');" onblur="wpjf3_mr_dim_field('wpjf3_mr_new_ak_email','Enter Email:');">
						</td>
						<td class="column-wpjf3-ak-key">&nbsp;</td>
						<td class="column-wpjf3-ak-active">&nbsp;</td>
						<td class="column-wpjf3-actions">
							<span class='edit' id="wpjf3_mr_add_ak_link">
								<a href="javascript:wpjf3_mr_add_new_ak( );">Add New Access Key</a>
							</span>
						</td>
					</tr>
					
				</tbody>
			</table>
			<?php
		}
		
		// (php) display redirect status if active
		function display_status_if_active(){
			global $wpdb;
			$wpjf3_mr_options = $this->get_admin_options();
			$show_notice      = false;
			
			if( $wpjf3_mr_options['enable_redirect'] == 'YES' ) $show_notice = true;
			if ( trim($_POST['update_wp_maintenance_redirect_settings']) != '' && $_POST['wpjf3_mr_enable_redirect'] == 'YES' ) $show_notice = true;
			if ( trim($_POST['update_wp_maintenance_redirect_settings']) != '' && $_POST['wpjf3_mr_enable_redirect'] == 'NO'  ) $show_notice = false;
			
			if( $show_notice ){
				echo '<div class="error" id="wpjf3_mr_enabled_notice"><p><strong>JF Maintenance Redirect</strong> is <strong>Enabled</strong></p></div>';
			}
		}
				
		// (php) create the admin page
		function print_admin_page() {
			global $wpdb;
			$wpjf3_mr_options = $this->get_admin_options();
			
			// process update
			if ( trim($_POST['update_wp_maintenance_redirect_settings']) != '' ) {
				// prepare options
				$wpjf3_mr_options['enable_redirect']  = trim( $_POST['wpjf3_mr_enable_redirect'] );
				$wpjf3_mr_options['method']           = trim( $_POST['wpjf3_mr_method'] );
				$wpjf3_mr_options['static_page']      = trim( $_POST['wpjf3_mr_static_page'] );
				$wpjf3_mr_options['maintenance_html'] = trim( $_POST['wpjf3_mr_maintenance_html'] );
				
				// update options
				update_option($this->admin_options_name, $wpjf3_mr_options);
				?>
				<div class="updated"><p><strong><?php _e("Settings Updated.", "wpjf3_maintenance_redirect");?></strong></p></div>
				<?php
			} ?>
			
			<script type="text/javascript" charset="utf-8">
				// bind actions
				jQuery(document).ready(function() {
					// enable disable toggle
					jQuery( '#wpjf3_mr_enable_redirect' ).change( function(){ wpjf3_mr_toggle_main_options(); });
					// method mode toggle
					jQuery( '#wpjf3_mr_method' ).change( function(){ wpjf3_mr_toggle_method_options(); });
				});
				
				// (js) update form layout based on main option
				function wpjf3_mr_toggle_main_options () {
					if( jQuery('#wpjf3_mr_enable_redirect').val() == 'YES' ){
						jQuery('#wpjf3_main_options').slideDown('fast');
					}else{
						jQuery('#wpjf3_main_options').slideUp('fast');
					}
				}
				
				// (js) update form layout based on method option
				function wpjf3_mr_toggle_method_options () {
					if( jQuery('#wpjf3_mr_method').val() == 'redirect' ){
						jQuery('#wpjf3_method_message' ).hide();
						jQuery('#wpjf3_method_redirect').show();
					}else{
						jQuery('#wpjf3_method_redirect').hide();
						jQuery('#wpjf3_method_message' ).show();
					}
				}
				
				// (js) undim field
				function wpjf3_mr_undim_field( field_id, default_text ) {
					if( jQuery('#'+field_id).val() == default_text ) jQuery('#'+field_id).val('');
					jQuery('#'+field_id).css('color','#000');
				}
				// (js) dim field
				function wpjf3_mr_dim_field( field_id, default_text ) {
					if( jQuery('#'+field_id).val() == '' ) {
						jQuery('#'+field_id).val(default_text);
						jQuery('#'+field_id).css('color','#888');
					}
				}
				
				// (js) add new IP
				function wpjf3_mr_add_new_ip () {
					// validate entries before posting ajax call
					var error_msg = '';
					if( jQuery('#wpjf3_mr_new_ip_name').val() == ''            ) error_msg += 'You must enter a Name.\n';
					if( jQuery('#wpjf3_mr_new_ip_name').val() == 'Enter Name:' ) error_msg += 'You must enter an Name.\n';
					if( jQuery('#wpjf3_mr_new_ip_ip'  ).val() == ''            ) error_msg += 'You must enter an IP.\n';
					if( jQuery('#wpjf3_mr_new_ip_ip'  ).val() == 'Enter IP:'   ) error_msg += 'You must enter an IP.\n';
					if( error_msg != '' ){
						alert( 'There are problems with the information you have entered.\n\n' + error_msg );
					}else{
						// prepare ajax data
						var data = {
							action:          'wpjf3_mr_add_ip',
							wpjf3_mr_ip_name: jQuery('#wpjf3_mr_new_ip_name').val(),
							wpjf3_mr_ip_ip:   jQuery('#wpjf3_mr_new_ip_ip').val() 
						};
						
						// set section to loading img
						var img_url = '<?php echo plugins_url( 'images/ajax_loader_16x16.gif', __FILE__ ); ?>';
						jQuery( '#wpjf3_mr_ip_tbl_container' ).html('<img src="' + img_url + '">');
						
						// send ajax request
						jQuery.post( ajaxurl, data, function(response) {
							jQuery('#wpjf3_mr_ip_tbl_container').html( response );
						});
					}
				}
				
				// (js) toggle IP status
				function wpjf3_mr_toggle_ip ( status, ip_id ) {
					// prepare ajax data
					var data = {
						action:             'wpjf3_mr_toggle_ip',
						wpjf3_mr_ip_active: status,
						wpjf3_mr_ip_id:     ip_id 
					};
					
					// (js) set status to loading img
					var img_url = '<?php echo plugins_url( 'images/ajax_loader_16x16.gif', __FILE__ ); ?>';
					jQuery( '#wpjf3_mr_ip_status_' + ip_id ).html('<img src="' + img_url + '">');
					
					// send ajax request
					jQuery.post( ajaxurl, data, function(response) {
						var split_response = response.split('|');
						if( split_response[0] == 'SUCCESS' ){
							var ip_id     = split_response[1];
							var ip_active = split_response[1];
							// update divs / 1 = id / 2 = status
							if( split_response[2] == '1' ){
								// active
								jQuery('#wpjf3_mr_ip_status_' + split_response[1] ).html( 'Yes' );
								jQuery('#wpjf3_mr_ip_status_' + split_response[1] + '_action' ).html( '<a href="javascript:wpjf3_mr_toggle_ip( 0, ' + split_response[1] + ' );">Disable</a> | ' );
							}else{
								// disabled
								jQuery('#wpjf3_mr_ip_status_' + split_response[1] ).html( 'No' );
								jQuery('#wpjf3_mr_ip_status_' + split_response[1] + '_action' ).html( '<a href="javascript:wpjf3_mr_toggle_ip( 1, ' + split_response[1] + ' );">Enable</a> | ' );
							} 
						}else{
							alert( 'There was a database error. Please reload this page' );
						}
					});
				}
				
				// (js) delete IP
				function wpjf3_mr_delete_ip ( ip_id, ip_addr ) {
					if ( confirm('You are about to delete the IP \n\n\'' + ip_addr + '\'\n\n') ) {
						// prepare ajax data
						var data = {
							action:          'wpjf3_mr_delete_ip',
							wpjf3_mr_ip_id:   ip_id
						};
						
						// set section to loading img
						var img_url = '<?php echo plugins_url( 'images/ajax_loader_16x16.gif', __FILE__ ); ?>';
						jQuery( '#wpjf3_mr_ip_tbl_container' ).html('<img src="' + img_url + '">');
						
						// send ajax request
						jQuery.post( ajaxurl, data, function(response) {
							jQuery('#wpjf3_mr_ip_tbl_container').html( response );
						});
					}
				}
				
				// (js) add new Access Key
				function wpjf3_mr_add_new_ak () {
					// validate entries before posting ajax call
					var error_msg = '';
					if( jQuery('#wpjf3_mr_new_ak_name' ).val() == ''            ) error_msg += 'You must enter a Name.\n';
					if( jQuery('#wpjf3_mr_new_ak_name' ).val() == 'Enter Name:' ) error_msg += 'You must enter an Name.\n';
					if( jQuery('#wpjf3_mr_new_ak_email').val() == ''            ) error_msg += 'You must enter an Email.\n';
					if( jQuery('#wpjf3_mr_new_ak_email').val() == 'Enter Email:') error_msg += 'You must enter an Email.\n';
					if( error_msg != '' ){
						alert( 'There are problems with the information you have entered.\n\n' + error_msg );
					}else{
						// prepare ajax data
						var data = {
							action:          'wpjf3_mr_add_ak',
							wpjf3_mr_ak_name:  jQuery('#wpjf3_mr_new_ak_name').val(),
							wpjf3_mr_ak_email: jQuery('#wpjf3_mr_new_ak_email').val() 
						};

						// set section to loading img
						var img_url = '<?php echo plugins_url( 'images/ajax_loader_16x16.gif', __FILE__ ); ?>';
						jQuery( '#wpjf3_mr_ak_tbl_container' ).html('<img src="' + img_url + '">');

						// send ajax request
						jQuery.post( ajaxurl, data, function(response) {
							jQuery('#wpjf3_mr_ak_tbl_container').html( response );
						});
					}
				}

				// (js) toggle Access Key status
				function wpjf3_mr_toggle_ak ( status, ak_id ) {
					// prepare ajax data
					var data = {
						action:             'wpjf3_mr_toggle_ak',
						wpjf3_mr_ak_active: status,
						wpjf3_mr_ak_id:     ak_id 
					};

					// set status to loading img
					var img_url = '<?php echo plugins_url( 'images/ajax_loader_16x16.gif', __FILE__ ); ?>';
					jQuery( '#wpjf3_mr_ak_status_' + ak_id ).html('<img src="' + img_url + '">');

					// send ajax request
					jQuery.post( ajaxurl, data, function(response) {
						var split_response = response.split('|');
						if( split_response[0] == 'SUCCESS' ){
							var ak_id     = split_response[1];
							var ak_active = split_response[1];
							// update divs / 1 = id / 2 = status
							if( split_response[2] == '1' ){
								// active
								jQuery('#wpjf3_mr_ak_status_' + split_response[1] ).html( 'Yes' );
								jQuery('#wpjf3_mr_ak_status_' + split_response[1] + '_action' ).html( '<a href="javascript:wpjf3_mr_toggle_ak( 0, ' + split_response[1] + ' );">Disable</a> | ' );
							}else{
								// disabled
								jQuery('#wpjf3_mr_ak_status_' + split_response[1] ).html( 'No' );
								jQuery('#wpjf3_mr_ak_status_' + split_response[1] + '_action' ).html( '<a href="javascript:wpjf3_mr_toggle_ak( 1, ' + split_response[1] + ' );">Enable</a> | ' );
							} 
						}else{
							alert( 'There was a database error. Please reload this page' );
						}
					});
				}

				// (js) delete Access Key
				function wpjf3_mr_delete_ak ( ak_id, ak_name ) {
					if ( confirm('You are about to delete the Access Key \n\n\'' + ak_name + '\'\n\n') ) {
						// prepare ajax data
						var data = {
							action:          'wpjf3_mr_delete_ak',
							wpjf3_mr_ak_id:   ak_id
						};

						// set section to loading img
						var img_url = '<?php echo plugins_url( 'images/ajax_loader_16x16.gif', __FILE__ ); ?>';
						jQuery( '#wpjf3_mr_ak_tbl_container' ).html('<img src="' + img_url + '">');

						// send ajax request
						jQuery.post( ajaxurl, data, function(response) {
							jQuery('#wpjf3_mr_ak_tbl_container').html( response );
						});
					}
				}
				
				// (js) re-send Access Key
				function wpjf3_mr_resend_ak ( ak_id, ak_name, ak_email ) {
					if ( confirm('You are about to email an Access Key link for \n\n\'' + ak_name + '\'\n\nto \n\n\'' + ak_email + '\'\n\n') ) {
						// prepare ajax data
						var data = {
							action:          'wpjf3_mr_resend_ak',
							wpjf3_mr_ak_id:   ak_id
						};
						
						// send ajax request
						jQuery.post( ajaxurl, data, function(response) {
							if( response == 'SEND_SUCCESS' ){
								alert( 'Notification Sent' );
							}else{
								alert( 'Notification Failure. Please check your server settings.' );
							}
						});
					}
				}
			
			</script>
			
			<style type="text/css" media="screen">
				.wpjf3_mr_admin_section  { border: 1px solid #ddd; padding: 10px; padding-top: 0px; }
				.wpjf3_mr_disabled_field { color: #888;	}
				.wpjf3_mr_small_dim      { font-size: 11px; font-weight: normal; color: #444; }
			</style>
			
			<div class=wrap>
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" onsubmit="return wpjf3_mr_validate_form();">
					<h2>JF3 Maintenance Redirect</h2>
					
					<p>This plugin is intended primarily for designers / developers that need to allow clients to preview sites before being available to the general public. 
						
						Any logged in user with WordPress administrator privileges will be allowed to view the site regardless of the settings below.</p>
					
					<h3>Enable Maintenance Mode:</h3>
					<select name="wpjf3_mr_enable_redirect" id="wpjf3_mr_enable_redirect" style="width:30%" >
						<option value="NO"  >No - Do not use maintenance mode  </option>
						<option value="YES" <?php if( $wpjf3_mr_options['enable_redirect'] == "YES" ) echo "selected"; ?>>Yes - Use maintenance mode  </option>
					</select>
					
					<div id="wpjf3_main_options" <?php if( $wpjf3_mr_options['enable_redirect'] == "NO" ) echo 'style="display:none;"'; ?> >
						
						<p><div class="wpjf3_mr_admin_section" >
							<h3>Unrestricted IPs: &nbsp; <span class="wpjf3_mr_small_dim">( Your IP address is: <?php echo $this->get_user_ip(); ?> - Your Class C is: <?php echo $this->get_user_class_c(); ?> )</span></h3>
							<p>Users with unrestricted IP addresses will bypass maintenance mode entirely. Using this option is useful to an entire office of clients to view the site without needing to jump through any extra hoops.</p> 
							
							<div id="wpjf3_mr_ip_tbl_container">
								<?php $this->print_unrestricted_ips(); ?>
							</div>
						</div></p>
						
						<p><div class="wpjf3_mr_admin_section">
							<h3>Access Keys:</h3>
							<p>You can allow users temporary access by sending them the access key. When a new key is created, a link to create the access key cookie will be emailed to the email address provided. Access can then be revoked either by disabling or deleting the key.</p>
							
							<div id="wpjf3_mr_ak_tbl_container">
								<?php $this->print_access_keys(); ?>
							</div>
						</div></p>
						
						<p><div class="wpjf3_mr_admin_section">	
							<h3>Maintenance Message:</h3>
							<p>You have two options for how to specify what you want to show users when your site is in maintenance mode. You can display a message or redirect to a static page.</p>
							<p><select name="wpjf3_mr_method" id="wpjf3_mr_method" style="width:30%" >
								<option value="message"  >Message Only - The easy way  </option>
								<option value="html" <?php if( $wpjf3_mr_options['method'] == "html" ) echo "selected"; ?> >HTML Entered Here - A little harder  </option>
								<option value="redirect" <?php if( $wpjf3_mr_options['method'] == "redirect" ) echo "selected"; ?> >Redirect - A little harder  </option>
							</select></p>

							<div id="wpjf3_method_message" style="<?php if( $wpjf3_mr_options['method'] == "redirect" ) echo "display:none;"; ?>" >
								<strong>Maintenance Mode Message:</strong>
								<p>This is the message that will be displayed while your site is in maintenance mode.</p>
								<p style="margin-bottom: 0;"><textarea name="wpjf3_mr_maintenance_html" rows="3" style="width:60%"><?php echo stripslashes( $wpjf3_mr_options['maintenance_html'] ); ?></textarea></p>
							</div>
						
							<div id="wpjf3_method_redirect" style="<?php if( $wpjf3_mr_options['method'] == "message" || $wpjf3_mr_options['method'] == "html" ) echo "display:none;"; ?>" >
								<strong>Static Maintenance Page:</strong>
								<p>To use this method you need to upload a static HTML page to your site and enter it's URL below.</p>
								<p><input type="text" name="wpjf3_mr_static_page" value="<?php echo $wpjf3_mr_options['static_page']; ?>" id="wpjf3_mr_static_page" style="width:60%"></p>
							</div>
						</div></p>
						
					</div>
					
					<div class="submit">
						<input type="submit" name="update_wp_maintenance_redirect_settings" value="<?php _e('Update Settings', 'wp_maintenance_redirect') ?>" />
					</div>
				</form>
			</div>
				
			<?php
		} // end function print_admin_page()
	} // end class wpjf3_maintenance_redirect
}

if (class_exists("wpjf3_maintenance_redirect")) {
	$my_wpjf3_maintenance_redirect = new wpjf3_maintenance_redirect();
}

// initialize the admin and users panel
if (!function_exists("wpjf3_maintenance_redirect_ap")) {
	function wpjf3_maintenance_redirect_ap() {
		global $my_wpjf3_maintenance_redirect;
		if( !isset($my_wpjf3_maintenance_redirect) ) return;
		
		if (function_exists('add_options_page')) {
			add_options_page('JF3 Maintenance Redirect Options', 'JF3 Maint Redirect', 1, 'JF3_Maint_Redirect', array( $my_wpjf3_maintenance_redirect, 'print_admin_page' ));
		}
	}
}

// actions and filters	
if( isset( $my_wpjf3_maintenance_redirect ) ) {
	// actions
	add_action( 'admin_menu',    'wpjf3_maintenance_redirect_ap' );
	add_action( 'send_headers',  array( $my_wpjf3_maintenance_redirect, 'process_redirect'), 1 );
	add_action( 'admin_notices', array( $my_wpjf3_maintenance_redirect, 'display_status_if_active' ) );
	
	// ajax actions
	add_action('wp_ajax_wpjf3_mr_add_ip',    array( $my_wpjf3_maintenance_redirect, 'add_new_ip'       ) );
	add_action('wp_ajax_wpjf3_mr_toggle_ip', array( $my_wpjf3_maintenance_redirect, 'toggle_ip_status' ) );
	add_action('wp_ajax_wpjf3_mr_delete_ip', array( $my_wpjf3_maintenance_redirect, 'delete_ip'        ) );
	add_action('wp_ajax_wpjf3_mr_add_ak',    array( $my_wpjf3_maintenance_redirect, 'add_new_ak'       ) );
	add_action('wp_ajax_wpjf3_mr_toggle_ak', array( $my_wpjf3_maintenance_redirect, 'toggle_ak_status' ) );
	add_action('wp_ajax_wpjf3_mr_delete_ak', array( $my_wpjf3_maintenance_redirect, 'delete_ak'        ) );
	add_action('wp_ajax_wpjf3_mr_resend_ak', array( $my_wpjf3_maintenance_redirect, 'resend_ak'        ) );
	
	// activation ( deactivation is later enhancement... )
	register_activation_hook( __FILE__, array( $my_wpjf3_maintenance_redirect, 'init' ) );
}
?>