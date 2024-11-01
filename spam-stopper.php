<?php
/*
Plugin Name: spam-stopper
Plugin URI: http://wordpress.org/extend/plugins/spam-stopper/
Description: Protect your blog from comments spam with a simple validation question. To config the spam question or disable adding the form field automatically, go to <code>Tools &rsaquo; Spam Stopper</code>.
Version: 3.1.3
Author: Mike Jolley
Author URI: http://blue-anvil.com
*/

// Pre 2.6 compatibility (BY Stephen Rider)
if ( ! defined( 'WP_CONTENT_URL' ) ) {
	if ( defined( 'WP_SITEURL' ) ) define( 'WP_CONTENT_URL', WP_SITEURL . '/wp-content' );
	else define( 'WP_CONTENT_URL', get_option( 'url' ) . '/wp-content' );
}
if ( ! defined( 'WP_CONTENT_DIR' ) ) define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) ) define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) ) define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

load_plugin_textdomain('spamstopper', WP_PLUGIN_URL.'/spam-stopper/langs/', 'spam-stopper/langs/');

function wp_spamstopper_menu() {
	add_management_page(__('Spam Stopper','spamstopper'), __('Spam Stopper','spamstopper'), 'manage_options', 'spamstopper', 'wp_spamstopper_admin');
}
add_action('admin_menu', 'wp_spamstopper_menu');

// Admin Interface
function wp_spamstopper_admin(){
	// Update options
	if ($_POST) {
		update_option('spamstopper_spam_question', 	stripslashes($_POST['spamstopper_spam_question']));
		update_option('spamstopper_field_format', 	stripslashes($_POST['spamstopper_field_format']));
		update_option('spamstopper_spam_answer', 	stripslashes($_POST['spamstopper_spam_answer']));
		update_option('spamstopper_show_field', 	stripslashes($_POST['spamstopper_show_field']));
		echo '<div id="message"class="updated fade"><p>'.__('Changes saved',"spamstopper").'</p></div>';
	}
	// Get options
	$spamstopper_spam_question = 					get_option('spamstopper_spam_question');
	$spamstopper_spam_answer = 						get_option('spamstopper_spam_answer');
	$spamstopper_show_field = 						get_option('spamstopper_show_field');
	$spamstopper_field_format = 					get_option('spamstopper_field_format');
	?>
	<div class="wrap alternate">
        <h2><?php _e('Spam-Stopper',"spamstopper"); ?></h2>
        <br class="a_break" style="clear: both;"/>
        <form action="?page=spamstopper" method="post">
            <table class="niceblue form-table">
                <tr>
                    <th scope="col"><?php _e('Anti-spam Question',"spamstopper"); ?>:</th>
                    <td><input type="text" name="spamstopper_spam_question" value="<?php echo $spamstopper_spam_question; ?>" /> <span class="setting-description"><?php _e('Question for the antispam field. Don\'t make this too hard!','spamstopper'); ?></span></td>
                </tr>
                <tr>
                    <th scope="col"><?php _e('Anti-spam Answer',"spamstopper"); ?>:</th>
                    <td><input type="text" name="spamstopper_spam_answer" value="<?php echo $spamstopper_spam_answer; ?>" /> <span class="setting-description"><?php _e('Answer for the antispam field.','spamstopper'); ?></span></td>
                </tr>
                <tr>
                    <th scope="col"><?php _e('Automatically show field in comment form?',"spamstopper"); ?>:</th>
                    <td><select name="spamstopper_show_field">
                    	<option <?php if ($spamstopper_show_field=='yes') echo 'selected="selected"'; ?> value="yes"><?php _e('Yes','spamstopper'); ?></option>
                    	<option <?php if ($spamstopper_show_field=='no') echo 'selected="selected"'; ?> value="no"><?php _e('No','spamstopper'); ?></option>
                    </select> <span class="setting-description"><?php _e('Turn this option on to insert the antispam field into your comment form. If you have inserted the field manually you should disable this option.','spamstopper'); ?></span></td>
                </tr>
                <tr>
                    <th scope="col"><?php _e('Field Format',"spamstopper"); ?>:</th>
                    <td><select name="spamstopper_field_format">
                    	<option <?php if ($spamstopper_field_format=='left') echo 'selected="selected"'; ?> value="left"><?php _e('Label on left of input','spamstopper'); ?></option>
                    	<option <?php if ($spamstopper_field_format=='right') echo 'selected="selected"'; ?> value="right"><?php _e('Label on right of input','spamstopper'); ?></option>
                    </select> <span class="setting-description"><?php _e('This option determines how the field is outputted.','spamstopper'); ?></span></td>
                </tr>
            </table>
            <p class="submit"><input type="submit" value="<?php _e('Save Changes',"spamstopper"); ?>" /></p>
        </form>
    </div>
    <?php
}


// Init
function wp_spamstopper_init() {

	// Defaults
	add_option("spamstopper_spam_question", 'Is Fire hot or cold?');
	add_option("spamstopper_spam_answer", 'Hot');
	add_option("spamstopper_show_field", 'yes');
	
	if (strstr(get_bloginfo('template_directory'), "default")) {
		add_option("spamstopper_field_format", 'right');
	} else {
		add_option("spamstopper_field_format", 'left');
	}
	
	// Validation uses jquery
	if (function_exists('wp_enqueue_script')) wp_enqueue_script('jquery');
	
}
add_action('init', 'wp_spamstopper_init');

// Head
function wp_spamstopper_head() {		
		
		echo '<link href="'.WP_PLUGIN_URL.'/spam-stopper/spam-stopper.css" rel="stylesheet" type="text/css" />';

		global $user_ID;
		
		if (!isset($user_ID) || $user_ID==0) {
		
			$aspamq = get_option('spamstopper_spam_question');
			$aspama = get_option('spamstopper_spam_answer');	
	
			?>
			<script type="text/javascript">
			/* <![CDATA[ */
				if (typeof jQuery == 'undefined') {  
	   				 // jQuery is not loaded  
				} else {
					jQuery(function(){
						jQuery('#commentform').submit(function(){
								if (jQuery('#author').val()=="") {
									alert("<?php _e('Please input your name',"spamstopper"); ?>");
									return false;     		
								}
								var email_field = jQuery('#email');
								var emailFilter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
								if (!emailFilter.test(email_field.val())) {
									alert("<?php _e('Please input a valid email',"spamstopper"); ?>");
									return false;
								}
								if (jQuery('#spamq').val()=="") {
									alert("<?php _e('Please answer the anti-spam question',"spamstopper"); ?>");
									return false;
								}
								if (jQuery('#comment').val()=="") {
									alert("<?php _e('Please input a comment',"spamstopper"); ?>");
									return false;
								}
								var spamq = jQuery('#spamq').val();			
								if (spamq.toUpperCase()!=='<?php echo strtoupper($aspama); ?>') {
									alert("<?php _e('Incorrect anti-spam answer. Please re-answer the question! The correct answer is ',"spamstopper"); echo '\"'.$aspama.'\"'; ?>");
									return false;
								}
								return true;
						});
					});
				}
			/* ]]> */
			</script>
			<?php
		}
}
add_action('wp_head', 'wp_spamstopper_head');

function wp_spamstopper_comment_form() {
				
	global $user_ID;
	
	if (isset($user_ID) && $user_ID >0) {						
		//logged in so no need for form
		return;
	} else {
		$aspamq = get_option('spamstopper_spam_question');
		$aspama = get_option('spamstopper_spam_answer');
		$spamstopper_field_format = get_option('spamstopper_field_format');
		
		echo '<!--Spam-Stopper for Wordpress by Mike Jolley @ http://blue-anvil.com-->';
		
		if ($spamstopper_field_format == 'right') {
		?>
			<!-- Required anti spam confirmation -->
			<p id="aspamquestion">										
			<input type="text" name="spamq" id="spamq" value="" size="15" maxlength="30" tabindex="3" /> <label for="spamq"><small><?php echo $aspamq; ?> (<?php _e('required',"spamstopper"); ?>) - <span class="whyask" title="<?php _e('This confirms you are a human user!',"spamstopper"); ?>" style="cursor:help;"><?php _e('Why ask?',"spamstopper"); ?></span></small></label>
			</p>
		<?php									
		} else {
		?>
			<!-- Required anti spam confirmation -->
			<p id="aspamquestion">
			<label for="spamq"><small><?php echo $aspamq; ?> (<?php _e('required',"spamstopper"); ?>)</small></label>
			<input type="text" name="spamq" id="spamq" size="15" maxlength="30" tabindex="3" /> <small><span class="whyask" title="<?php _e('This confirms you are a human user!',"spamstopper"); ?>" style="cursor:help;"><?php _e('Why ask?',"spamstopper"); ?></span></small>
			</p>
		<?php
		}
		?>	
		<!-- cookie script -->								
		<script type="text/javascript">
		/* <![CDATA[ */
			if (typeof jQuery == 'undefined') {  
   				 // jQuery is not loaded  
			} else {
				jQuery(function(){
					// Reorder fields 
					jQuery('#email').parent().after(jQuery('#aspamquestion'));
				});
			}					
			/* ]]> */
		</script>
		<?php
		return;
	}
}

function wp_spamstopper_comment_form_required() {

	global $user_ID;
	
	if (isset($user_ID) && $user_ID >0) {							
		//logged in so no need for form
		return;
	} else {
		?>
		<!-- Other anti spam measures -->
		<div style="position: absolute; left: -9000px;" class="spam-offset">
  			<label for="honeypot"><?php _e('Leave this anti-spam trap empty') ?></label><input type="text" name="honeypot" value="" size="32" maxlength="255" id="honeypot" />
		    <?php  		
				$form_id  =  "ID".trim(strtoupper(md5(md5(get_bloginfo('name').get_bloginfo('wpurl')))))."SS"; 			
		        echo '<input type="hidden" name="ss_hidden" value="'.$form_id.'" id="hidden" alt="hidden" />';
		    ?>
    	</div>
		<script type="text/javascript">
		/* <![CDATA[ */
			if (typeof jQuery == 'undefined') {  
   				 // jQuery is not loaded  
			} else {
				jQuery(function(){
					function getCookieVal (offset) {
					  var endstr = document.cookie.indexOf (";", offset);
					  if (endstr == -1) { endstr = document.cookie.length; }
					  return unescape(document.cookie.substring(offset, endstr));
					}
					function GetCookie (name) {
					  var arg = name + "=";
					  var alen = arg.length;
					  var clen = document.cookie.length;
					  var i = 0;
					  while (i < clen) {
						var j = i + alen;
						if (document.cookie.substring(i, j) == arg) {
						  return getCookieVal (j);
						  }
						i = document.cookie.indexOf(" ", i) + 1;
						if (i == 0) break; 
						}
					  return null;
					}
					function deleteCookie(name, path){
					    if(GetCookie(name)){
					        setCookie(name, '', -30, path);
					    }
					}
					function setCookie(name, value, expires, path){
					    var today = new Date();
					    if(expires){
					        expires = expires * 1000 * 3600 * 24;
					    }
					    document.cookie = name+'='+escape(value) +
					        ((expires) ? ';expires=' + new Date(today.getTime() + expires).toGMTString() : '') +
					        ((path) ? ';path=' + path : '');
					} 					
					// Set comment data if cookie exists
					if (GetCookie('cached_error_comment') == null) {} else {					
						jQuery('#comment').val(unescape(GetCookie('cached_error_comment')));
						deleteCookie('cached_error_comment', '/');
					}
					if (GetCookie('cached_comment_author_url') == null) {} else {					
						jQuery('input#url').val(unescape(GetCookie('cached_comment_author_url')));
						deleteCookie('cached_comment_author_url', '/');
					}
					if (GetCookie('cached_comment_author_email') == null) {} else {					
						jQuery('input#email').val(unescape(GetCookie('cached_comment_author_email')));
						deleteCookie('cached_comment_author_email', '/');
					}
					if (GetCookie('cached_comment_author') == null) {} else {					
						jQuery('input#author').val(unescape(GetCookie('cached_comment_author')));
						deleteCookie('cached_comment_author', '/');
					}
				});
				}					
			/* ]]> */
		</script>
    	<?php
	}
	
}
				
function wp_spamstopper_check_comment($comment_data) {
	global $user_ID, $wpdb, $comment_type;
	
	get_currentuserinfo();
	
	$aspamq = get_option('spamstopper_spam_question');
	$aspama = get_option('spamstopper_spam_answer');
	
	$input = $_POST['spamq'];					
	// If posting a comment (not trackback etc) and not logged in
	if (  (!isset($user_ID) || $user_ID==0) && ($comment_data['comment_type']== '')) {
	
		// Form ID Check
		$form_id  =  "ID".trim(strtoupper(md5(md5(get_bloginfo('name').get_bloginfo('wpurl')))))."SS";
		$posted_id = trim(stripslashes($_POST['ss_hidden']));
		if ($form_id != $posted_id) {
			// Cache the comment
			setcookie("cached_error_comment", $comment_data['comment_content'], time()+3600, '/');
			setcookie("cached_comment_author_url", $comment_data['comment_author_url'], time()+3600, '/');
			setcookie("cached_comment_author_email", $comment_data['comment_author_email'], time()+3600, '/');
			setcookie("cached_comment_author", $comment_data['comment_author'], time()+3600, '/');
			// Echo error
			wp_die( __('Error! There was a form ID mis-match indicative of remote posting.',"spamstopper") );
			exit;
		}
		
		// Honeypot trap
		$honey = trim(stripslashes($_POST['honeypot']));
		if ($honey != "") {
			// Cache the comment
			setcookie("cached_error_comment", $comment_data['comment_content'], time()+3600, '/');
			setcookie("cached_comment_author_url", $comment_data['comment_author_url'], time()+3600, '/');
			setcookie("cached_comment_author_email", $comment_data['comment_author_email'], time()+3600, '/');
			setcookie("cached_comment_author", $comment_data['comment_author'], time()+3600, '/');
			// Echo error
			wp_die( __('Error! A hidden empty input had data. If you are human, try again.',"spamstopper") );
			exit;	
		}		
		
		// Input missing		
		if ( empty($input) ) {
			// Cache the comment
			setcookie("cached_error_comment", $comment_data['comment_content'], time()+3600, '/');
			setcookie("cached_comment_author_url", $comment_data['comment_author_url'], time()+3600, '/');
			setcookie("cached_comment_author_email", $comment_data['comment_author_email'], time()+3600, '/');
			setcookie("cached_comment_author", $comment_data['comment_author'], time()+3600, '/');
			// Echo error
			wp_die( __('Error: please answer the anti-spam question.',"spamstopper") );
			exit;
		}
		
		// Wrong answer
		if ( strtolower($input) !== strtolower($aspama) ) {
			// Cache the comment
			setcookie("cached_error_comment", $comment_data['comment_content'], time()+3600, '/');
			setcookie("cached_comment_author_url", $comment_data['comment_author_url'], time()+3600, '/');
			setcookie("cached_comment_author_email", $comment_data['comment_author_email'], time()+3600, '/');
			setcookie("cached_comment_author", $comment_data['comment_author'], time()+3600, '/');
			// Echo error
			wp_die( __('Invalid anti-spam answer. Press your browsers back button and try again. The correct answer is ',"spamstopper").'"'.$aspama.'".' );
			exit;
		}				
	}
	setcookie("cached_error_comment", "", time()-3600, '/');
	setcookie("cached_comment_author_url", "", time()-3600, '/');
	setcookie("cached_comment_author_email", "", time()-3600, '/');
	setcookie("cached_comment_author", "", time()-3600, '/');
	return $comment_data;
}

// Filters
if (get_option('spamstopper_show_field')=='yes') {
	add_action('comment_form', 'wp_spamstopper_comment_form');
}
add_action('comment_form', 'wp_spamstopper_comment_form_required');
add_filter('preprocess_comment', 'wp_spamstopper_check_comment',0);	
?>