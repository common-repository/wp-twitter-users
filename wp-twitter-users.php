<?php
/*
Plugin Name: WP Twitter Users
Version: 2.0.0
Plugin URI:  http://0xtc.com/2009/09/10/wp-twitter-users.xhtml
Description: Enhances Twitter usernames by allowing you to quickly create badges using a shortcode. It fully supports @Anywhere and can be customized with templates.
Contributors: 0xtc
Author: Tanin Ehrami
Author URI: http://0xtc.com/
Stable tag: trunk
*/

/*
																									ini_set("display_errors","0");
																									ERROR_REPORTING(E_NONE);
*/

if (!class_exists("wpFollowFriday")) {
	class wpFollowFriday {
		var $OptionsNamePre = "wpFollowFridayAdminOptions";
		var $prefs = NULL;
		function wpFollowFriday() {
			$this->prefs = $this->getAdminOptions();
			add_shortcode ('ffjustlink',	array(&$this, 'wp_just_link_to_user'));
			add_shortcode ('ff', 			array(&$this, 'wp_follow_friday_exec'));
			add_shortcode ('#ff', 			array(&$this, 'wp_follow_friday_exec'));
			add_shortcode ('#followfriday',	array(&$this, 'wp_follow_friday_exec'));
			add_shortcode ('followfriday',	array(&$this, 'wp_follow_friday_exec'));
			add_shortcode ('twitterusers',	array(&$this, 'wp_follow_friday_exec'));
		}

		function init() {
			$this->prefs = $this->getAdminOptions();
		}

		function getAdminOptions() {
			$wpFollowFridayAdminOptions = array('template' => 'profile.tidy','CustomTemplate'=>null,'wpTWUTw3rdSRV'=>'awesome','TwitterAPIKey'=>'','autolink_anywhere'=>'no','create_hovercards'=>'no');
			$ffrOptions = get_option($this->OptionsNamePre);
			if (!empty($ffrOptions)) {
				foreach ($ffrOptions as $key => $option)
					$wpFollowFridayAdminOptions[$key] = $option;
			}				
			update_option($this->OptionsNamePre, $wpFollowFridayAdminOptions);
			return $wpFollowFridayAdminOptions;
		}

		function wp_follow_friday_add_header() {
			$ffrOptions = $this->prefs;
			$jsinclude = null;
			if ($ffrOptions['template'] == $ffrOptions['CustomTemplate']){
				$templatefilepath = get_bloginfo('template_directory').'/wptu/'.$ffrOptions['CustomTemplate'].'/'.$ffrOptions['CustomTemplate'].'css';
			} else {			
				$templatefilepath = get_bloginfo('wpurl').'/wp-content/plugins/wp-twitter-users/templates/'.$ffrOptions['template'].'/'.$ffrOptions['template'].'.css';
			}
			
			if ($ffrOptions['TwitterAPIKey'] != ''){
				$jsinclude = '<script src="http://platform.twitter.com/anywhere.js?id='.$ffrOptions['TwitterAPIKey'].'&v=1" type="text/javascript"></script>'."\r\n";
				if ($ffrOptions['autolink_anywhere']=='yes'){
					$jsinclude .="\r\n\t<script type=\"text/javascript\">twttr.anywhere(function(twitter){twitter(\"body\").linkifyUsers();});</script>";
				}
				if ($ffrOptions['create_hovercards']=='yes'){
					$jsinclude .="\r\n\t<script type=\"text/javascript\">twttr.anywhere(function(twitter){twitter.hovercards();});</script>";
				}
			}

			$cssline = "\r\n\t<!-- WP Twitter Users -->\r\n\t<link rel=\"stylesheet\" href=\"".$templatefilepath."\" type=\"text/css\" media=\"screen\" />\r\n".$jsinclude;
			echo $cssline;
		}

		function printAdminPage() {
			$ffrOptions = $this->prefs;
			if (isset($_POST['update_wpFollowFridaySettings'])) {
				if (isset($_POST['wpFollowFridayTemplate'])) {
					if (strrpos($_POST['wpFollowFridayTemplate'],'wptu_custom_') !== false){
						echo 'Setting custom template';
						$ffrOptions['CustomTemplate'] = substr($_POST['wpFollowFridayTemplate'],12);
						$ffrOptions['template'] = substr($_POST['wpFollowFridayTemplate'],12);
					} else {
						$ffrOptions['template'] = $_POST['wpFollowFridayTemplate'];
						$ffrOptions['CustomTemplate'] = null;
					}
					
				}
				$ffrOptions['TwitterAPIKey'] = $_POST['wpTWUTweetTwitterAPIKey'];
				
				if ('OMG_OMG_AWESOME' == $_POST['wpTWUTweetUser3rdPartyImageSRV']){
					$ffrOptions['wpTWUTw3rdSRV']= 'awesome';
				} else {
					$ffrOptions['wpTWUTw3rdSRV']= 'not_so_awesome';
				}
				if ('yes'==$_POST['wpTWUTweetAnywhere']){
					$ffrOptions['autolink_anywhere']='yes';
				} else {
					$ffrOptions['autolink_anywhere']='no';
				}
				
				if ('yes'==$_POST['wpTWUTweetHovercards']){
					$ffrOptions['create_hovercards']='yes';
				} else {
					$ffrOptions['create_hovercards']='no';
				}
								
				
				update_option($this->OptionsNamePre, $ffrOptions);
				?>
					<div class="updated"><p><strong><?php _e("Settings Updated", "wpFollowFriday");?></strong></p></div>
				<?php
			}
			
			if (isset($_POST['wpFollowFridayClearCache'])) {
				if ($_POST['wpFollowFridayClearCache']=='please') {
						$cachedir = dirname(__FILE__).'/xmlcache/'; 
						if (file_exists(WP_CONTENT_DIR.'/cache/xmlcache/.')){
							$cachedir = WP_CONTENT_DIR.'/cache/xmlcache/'; 
						}
						$d = dir($cachedir); 
						while($entry = $d->read()) { 
							if ($entry!= "." && $entry != "..") { 
								unlink($cachedir.$entry);
							}
						}
						$d->close();
				}
				?>
					<div class="updated"><p><strong><?php _e("Cache cleared", "wpFollowFriday");?></strong></p></div>
				<?php
			}
			echo '
			<div class="wrap">
				<div id="icon-options-general" class="icon32"><br /></div> 
				<h2>WP Twitter Users</h2>
				<div id="poststuff" class="metabox-holder has-right-sidebar">
					<div id="post-body"> 
						<div id="post-body-content">';
							echo '
							<div id="templateselect" class="stuffbox" style="background:#fff;">
								<h3 class="hndle">Settings</h3>
								<div class="inside">
									<form method="post" action="'. $_SERVER["REQUEST_URI"].'">
										<h2>@Anywhere</h2>
										<div>
											<p>
											<label for="wpTWUTweetTwitterAPIKey">';
									if ('' != $ffrOptions['TwitterAPIKey']){
										$selected=$ffrOptions['TwitterAPIKey'];
									} else {
										$selected='';
									}
											echo '
												In order to take advantage of Twitter\'s @Anywhere services, you need an API key for your blog.<br />
												Enter your <a href="http://dev.twitter.com/apps/new/" rel="external" title="register an API key here">Twitter API key</a> here:<br />
												<input value="'.$selected.'" name="wpTWUTweetTwitterAPIKey" id="wpTWUTweetTwitterAPIKey" type="text" style="width:570px"/>
											</label>
											</p>
											<p>
											<label for="wpTWUTweetAnywhere">';
									if ('yes' == $ffrOptions['autolink_anywhere']){
										$selected=' checked="checked"';
									} else {
										$selected='';
									}
											echo '
												<input type="checkbox" '.$selected.' name="wpTWUTweetAnywhere" value="yes" id="wpTWUTweetAnywhere" /> Autolink @usernames to Twitter profiles.
											</label>
											</p>
											<p>
											<label for="wpTWUTweetHovercards">';
									if ('yes' == $ffrOptions['create_hovercards']){
										$selected=' checked="checked"';
									} else {
										$selected='';
									}
											echo '
												<input type="checkbox" '.$selected.' name="wpTWUTweetHovercards" value="yes" id="wpTWUTweetHovercards" /> Create hovercards for @usernames.
											</label>
											</p>
										</div>									
										<h2>Images</h2>
										<div>
											<p>
											<label for="wpTWUTweetUser3rdPartyImageSRV">';
									if ('awesome' == $ffrOptions['wpTWUTw3rdSRV']){
										$selected=' checked="checked"';
									} else {
										$selected='';
									}
											echo '
												<input type="checkbox" '.$selected.' name="wpTWUTweetUser3rdPartyImageSRV" value="OMG_OMG_AWESOME" id="wpTWUTweetUser3rdPartyImageSRV" /> Use <a href="http://tweetimag.es/" target="_blank">tweetimag.es</a> for avatar images (Reduces the risk of missing pictures).
											</label>
											</p>
										</div>
										<h2>Select a template</h2>';
										$dirPath = dirname(__FILE__).'/templates/';
										if (file_exists($dirPath)) if ($handle = opendir($dirPath)) {
											$filenamearr=Array();

											while (false !== ($file = readdir($handle))) {
												if ($file != "." && $file != "..") {
													$filenamearr[]=$file;
												}
											}
											closedir($handle);

											sort ($filenamearr);
											
											foreach ($filenamearr as $file){
												if (file_exists("$dirPath/$file/$file.template.html")) {
													if (($file == $ffrOptions['template'])&& !$ffrOptions['CustomTemplate']){
														$selected=' checked="checked"';
													} else {
														$selected='';
													}
													echo '
													<div style="border-bottom:1px solid #ddd;border-top:1px solid #fff;">
														<h4>'.$file.'</h4>
														<input type="radio"'.$selected.' value="'.$file.'" name="wpFollowFridayTemplate" /> 
														<img style="vertical-align:middle" src="'.WP_PLUGIN_URL.'/wp-twitter-users/templates/'.$file.'/'.$file.'.png" />
														<br />
														<p style="width:600px">';
															if (file_exists("$dirPath/$file/readme.txt")){
																echo file_get_contents("$dirPath/$file/readme.txt");
															}
															echo '<br />
														</p>
													</div>	
													';
												}
											}
										}
							
										$dirPath = (TEMPLATEPATH).'/wptu/';
										if (file_exists($dirPath)) if ($handle = opendir($dirPath)) {
											$filenamearr=Array();

											while (false !== ($file = readdir($handle))) {
												if ($file != "." && $file != "..") {
													$filenamearr[]=$file;
												}
											}
											closedir($handle);

											sort ($filenamearr);

											foreach ($filenamearr as $file){
												if (file_exists("$dirPath/$file/$file.template.html")) {
													if (($file == $ffrOptions['template']) && ($ffrOptions['CustomTemplate'] != '')){
														$selected=' checked="checked"';
													} else {
														$selected='';
													}
													echo '
													<div style="border-bottom:1px solid #ddd;border-top:1px solid #fff;">
														<h4>'.$file.'</h4>
														<input type="radio"'.$selected.' value="wptu_custom_'.$file.'" name="wpFollowFridayTemplate" />';
														if (file_exists((TEMPLATEPATH).'/wptu/'.$file.'/'. $file. '.png')){
															echo '<img style="vertical-align:middle" src="'.get_bloginfo('template_directory').'/wptu/'.$file.'/'.$file.'.png" />
															<br />';
														}
														echo '
														<p style="width:600px">';
															if (file_exists("$dirPath/$file/readme.txt")){
																echo file_get_contents("$dirPath/$file/readme.txt");
															}
															echo '<br />
														</p>
													</div>	
													';
												}
											}
										}
										echo '
										<div>
											<p>
											<label for="wpFollowFridayClearCache">
												<input type="checkbox" name="wpFollowFridayClearCache" value="please" id="wpFollowFridayClearCache" /> Clear the XML cache when I save.
											</label>
											</p>
										</div>
										<div class="submit" style="float:right;">
												<input type="submit" name="update_wpFollowFridaySettings" value="';
												_e('Update Settings', 'wpFollowFriday');
												echo '" />
										</div>
									</form>
									<br class="clear" />
								</div>
							</div>
						</div>
					</div>

					<div id="side-info-column" class="inner-sidebar"> 
						<div id="side-sortables" class="meta-box-sortables"> 
							<div id="wptuhelpdiv" class="postbox " > 
								<h3 class="hndle"><span>Just so you know....</span></h3> 
								<div class="inside"> 
									<h4>Templates</h4>
									<p>
										If you\'ve created your own custom template that matches your site and style, you can save its directory in a <code>wptu</code> subdirectory in your current theme\'s path. 
									</p>
									<p>
										For example: <code>/wp-content/themes/MyTheme/<strong>wptu</strong>/MyCustomTweetTemplate/</code>
									</p>
									<p>
										To learn more about custom templates, read the <a href="http://0xtc.com/2009/07/09/creating-a-wp-quote-tweets-template.xhtml" title="Make your own templates!" target="_blank">templating documentation</a> on 0xtc.com.
									</p>
									<h4>Caching</h4>
									<p>
										This plugin caches the information it gets from Twitter.com so your server plays nice and doesn\'t make too many requests.
									</p>
									<p>
										Should something ever go wrong and you need to clear the cache, check the checkbox at the bottom of this page and save your settings to delete all the cached requests.
									</p>
									<h4>Usage</h4>
									<p>
										The following shortcodes variations are valid:
									</p>
									<ul style="font-family:monospace">
										<li>[ff name1 name2...]</li>
										<li>[#ff name1 name2...]</li>
										<li>[#followfriday name1 name2...]</li>
										<li>[followfriday name1 name2...]</li>
										<li>[twitterusers name1 name2...]</li>
									</ul>
									<p>Names must be valid Twitter usernames written in one of the following formats:</p>
									<ol>
										<li>[#ff username]</li>
										<li>[#ff @username]</li>
										<li>[#ff http://twitter.com/username]</li>
									</ol>
								</div>
							</div>
						</div>
					</div>					
				</div>
			</div>';
		}

		function wp_follow_friday_exec($att,$content=null){
			$exLowerAs = array();
			$r_content=null;
			if (!is_array($att) || is_null($att)) return false;
			foreach ($att as $twitterUsername){
				$twitterUsername = strtolower(str_replace('@','',$twitterUsername));
				$twitterUsername = str_replace(' ','',$twitterUsername);
				$twitterUsername = str_replace(',','',$twitterUsername);
				$twitterUsername = str_replace('http://twitter.com/','',$twitterUsername);
				$twitterUsername = str_replace('https://twitter.com/','',$twitterUsername);
				if ( (!in_array($twitterUsername,$exLowerAs)) && (!strstr($twitterUsername,'#')) && ($twitterUsername!='')){
					$r_content  .= $this->wp_follow_friday_execProc($twitterUsername,$content);
					$exLowerAs[] = $twitterUsername;
				}
			}
			return $r_content;
		}

		function wp_just_link_to_user($att,$content=null){
			$exLowerAs = array();		// duplication prevention
			$r_content=null;
			foreach ($att as $twitterUsername){
				$twitterUsername = strtolower(str_replace('@','',$twitterUsername));
				$twitterUsername = str_replace(' ','',$twitterUsername);
				$twitterUsername = str_replace(',','',$twitterUsername);
				$twitterUsername = str_replace('http://twitter.com/','',$twitterUsername);
				$twitterUsername = str_replace('https://twitter.com/','',$twitterUsername);
				if ( (!in_array($twitterUsername,$exLowerAs)) && (!strstr($twitterUsername,'#')) && ($twitterUsername!='')){
					$r_content  .= ' <a href="http://twitter.com/'.$twitterUsername.'" title="'.$twitterUsername.' on Twitter" rel="external" class="twitter-anywhere-user">@'.$twitterUsername.'</a>';
					$exLowerAs[] = $twitterUsername;
				}
			}
			return $r_content;
		}

		function getTemplateFilepath(){
			$templatesDir = dirname(__FILE__).'/templates/';
			$templatePref = $this->prefs['template'];
			$templateFile = $templatePref.'.template.html';
			$templatefilepath = $templatesDir.$templatePref.'/'.$templateFile;
			if (is_feed()){
				$templatefilepath = $templatesDir.'/noborder.inline/noborder.inline.template.html';
			}
			if ($this->prefs['template'] == $this->prefs['CustomTemplate']) {
				$templatefilepath = (TEMPLATEPATH).'/wptu/'.$this->prefs['template'].'/'.$this->prefs['template'].'.template.html';
			}
			return $templatefilepath;
		}
		
		function templateUser($userinfo,$WPTUTemplate){
			$ffrOptions = $this->prefs;
			if (($userinfo==false) || $userinfo->id==''){
				return null;
			}
			if ($userinfo->profile_background_tile == 'true'){$tweetbgCSSprop = 'repeat-x';} else {$tweetbgCSSprop = 'no-repeat';}
			if ((strtolower($userinfo->profile_sidebar_fill_color) =='ffffff') || (strtolower($userinfo->profile_sidebar_fill_color) =='fff')){
				$userinfo->profile_sidebar_fill_color='f4f4f4';
			}

			if ((strtolower($userinfo->profile_background_color) =='ffffff') || (strtolower($userinfo->profile_background_color) =='fff')){
				$userinfo->profile_background_color='f0f0f0';
			}
			if (($userinfo->status->in_reply_to_user_id <> '') && ($userinfo->status->in_reply_to_screen_name <> '') && ($userinfo->status->in_reply_to_status_id <> '')){
				$replyToStr = ' in reply to <a href="http://twitter.com/'.$userinfo->status->in_reply_to_screen_name.'/status/'.$userinfo->status->in_reply_to_status_id.'" rel="external">@'.$userinfo->status->in_reply_to_screen_name.'</a>';
			} else {
				$replyToStr = '';
			}
			// template search strings.....
			$findArr = Array(
				'%USER_ID%','%USER_NAME%','%USER_SCREEN_NAME%','%USER_LOCATION%','%USER_DESCRIPTION%',
				'%USER_PROFILE_IMAGE_URL%','%USER_PROFILE_IMAGE_URL_MINI%','%USER_PROFILE_IMAGE_URL_BIGGER%','%USER_URL%','%USER_PROTECTED%',
				'%USER_FOLLOWERS_COUNT%','%USER_PROFILE_BACKGROUND_COLOR%','%USER_PROFILE_TEXT_COLOR%',
				'%USER_PROFILE_LINK_COLOR%','%USER_PROFILE_SIDEBAR_FILL_COLOR%','%USER_PROFILE_SIDEBAR_BORDER_COLOR%',
				'%USER_FRIENDS_COUNT%','%USER_CREATED_AT%','%USER_FAVORITES_COUNT%','%USER_UTC_OFFSET%','%USER_TIME_ZONE%',
				'%USER_PROFILE_BACKGROUND_IMAGE_URL%','%USER_PROFILE_BACKGROUND_TILE%','%USER_STATUSES_COUNT%','%USER_NOTIFICATIONS%',
				'%USER_VARIFIED%','%USER_FOLLOWING%','%USER_STATUS_CREATED_AT%','%USER_STATUS_ID%','%USER_STATUS_TEXT%','%USER_STATUS_SOURCE%',
				'%USER_STATUS_TRUNCATED%','%USER_STATUS_IN_REPLY_TO_STATUS_ID%','%USER_STATUS_IN_REPLY_TO_USER_ID%','%USER_STATUS_FAVORITED%',
				'%USER_STATUS_IN_REPLY_TO_SCREEN_NAME%','%USER_NICE_CREATED_AT%','%USER_STATUS_NICE_TEXT%','%USER_BACKGROUND_CSS_STRING%','%USER_PICTURE_LINK%',
				'%USER_PICTURE_LINK_MINI%','%USER_PICTURE_LINK_BIGGER%','%PROFILE_LINK%','%NICE_TIMESTAMP_LINK%','%IN_REPLY_TO_LINK%'
			);
			// ....replace with
			
			if ($ffrOptions['wpTWUTw3rdSRV']=='awesome') {
				$userinfo->profile_image_url = 'http://img.tweetimag.es/i/'.($userinfo->screen_name).'_n';
				$image_mini_url = 'http://img.tweetimag.es/i/'.($userinfo->screen_name).'_m';
				$image_bigger_url = 'http://img.tweetimag.es/i/'.($userinfo->screen_name).'_b';
			} else {
				$image_mini_url = str_replace('_normal.', '_mini.', $userinfo->profile_image_url);
				$image_bigger_url = str_replace('_normal.', '_bigger.', $userinfo->profile_image_url);
		
			}

			$replaceArr = Array(
				$userinfo->id,
				$userinfo->name,
				$userinfo->screen_name,
				$userinfo->location,
				$userinfo->description,
				$userinfo->profile_image_url,
				$image_mini_url,
				$image_bigger_url,
				$userinfo->url,
				$userinfo->protected,
				$userinfo->followers_count,
				$userinfo->profile_background_color,
				$userinfo->profile_text_color,
				$userinfo->profile_link_color,
				$userinfo->profile_sidebar_fill_color,
				$userinfo->profile_sidebar_border_color,
				$userinfo->friends_count,
				$userinfo->created_at,
				$userinfo->favourites_count,
				$userinfo->utc_offset,
				$userinfo->time_zone,
				$userinfo->profile_background_image_url,
				$userinfo->profile_background_tile,
				$userinfo->statuses_count,
				$userinfo->notifications,
				$userinfo->verified,
				$userinfo->following,
				$userinfo->status->created_at,
				$userinfo->status->id,
				$userinfo->status->text,
				$userinfo->status->source,
				$userinfo->status->truncated,
				$userinfo->status->in_reply_to_status_id,
				$userinfo->status->in_reply_to_user_id,
				$userinfo->status->favorited,
				$userinfo->status->in_reply_to_screen_name,
				$this->wp_follow_friday_niceTime(strtotime(str_replace("+0000", "", $userinfo->created_at))),
				$this->wp_follow_friday_formatTweetContent($userinfo->status->text)."\r\n",
				'background:#'.$userinfo->profile_background_color.' url('.$userinfo->profile_background_image_url.') top left '.$tweetbgCSSprop,
				'<a href="http://twitter.com/'.$userinfo->screen_name.'" title="'.$userinfo->name.'" class="quoting_pic" rel="external"><img src="'.$userinfo->profile_image_url.'" style="background-color:#'.$userinfo->profile_background_color.'" alt="'.$userinfo->screen_name.'" /></a>',
				'<a href="http://twitter.com/'.$userinfo->screen_name.'" title="'.$userinfo->name.'" class="quoting_pic" rel="external"><img src="'.str_replace('_normal.', '_mini.', $userinfo->profile_image_url).'" style="background-color:#'.$userinfo->profile_background_color.'" alt="'.$userinfo->screen_name.'" /></a>',
				'<a href="http://twitter.com/'.$userinfo->screen_name.'" title="'.$userinfo->name.'" class="quoting_pic" rel="external"><img src="'.str_replace('_normal.', '_bigger.', $userinfo->profile_image_url).'" style="background-color:#'.$userinfo->profile_background_color.'" alt="'.$userinfo->screen_name.'" /></a>',
				'<a href="http://twitter.com/'.$userinfo->screen_name.'" title="'.$userinfo->name.': '.$userinfo->description.'" class="twitter-anywhere-user" rel="external" style="color:#'.$userinfo->profile_link_color.';">'. $userinfo->screen_name.'</a>',
				'<a href="http://twitter.com/'.$userinfo->screen_name.'/status/'.$userinfo->status->id.'" rel="external">'.$this->wp_follow_friday_niceTime(strtotime(str_replace("+0000", "", $userinfo->status->created_at))).'</a>',
				$replyToStr
			);
			
			// if ($ffrOptions['create_hovercards']=='yes'){$WPTUTemplate=str_ireplace('%PROFILE_LINK%','%PROFILE_AUTOLINK%',$WPTUTemplate);}
			$r_content = str_ireplace($findArr, $replaceArr, $WPTUTemplate);		
			return $r_content;
		}

		function wp_follow_friday_execProc($twitterUsername,$content=null){
			$r_content = null;
			$templatefilepath = $this->getTemplateFilepath();
			if (file_exists($templatefilepath)) {
				$WPTUTemplate = file_get_contents($templatefilepath);
			} else {
				return '<p><tt>Could not find the following template file : '.$templatefilepath.'</tt></p>';
			}
			$userinfo = 	$this->wp_follow_friday_getTwitterUser($twitterUsername);
			
			$r_content = 	$this->templateUser($userinfo,$WPTUTemplate);
			return $r_content;
		}

		function wp_follow_friday_formatTweetContent ($tweet){
			$search = array('&'); 
			$replace = array('&amp;');
			$tweet = str_replace($search, $replace, $tweet);
			$tweet = preg_replace("/http:\/\/(.*?)[^ ]*/"	, 	'<a href="\\0" rel="external">\\0</a>', $tweet);
			$tweet = preg_replace("(@([a-zA-Z0-9_]+))"		,	"<a href=\"http://www.twitter.com/\\1\" rel=\"external\">\\0</a>", $tweet);
			$tweet = preg_replace('/\#(\w+)/'				, 	"<a href='http://search.twitter.com/search?q=$1' rel='external'>#$1</a>", $tweet);
			return $tweet;
		}

		function wp_follow_friday_niceTime($time) {
			$delta = time() - $time;
			if ($delta < 60) {return 'less than a minute ago.';
				} else if ($delta < 120) { return 'about a minute ago.';
				} else if ($delta < (45 * 60)) { return floor($delta / 60) . ' minutes ago.';
				} else if ($delta < (90 * 60)) { return 'about an hour ago.';
				} else if ($delta < (24 * 60 * 60)) { return 'about ' . floor($delta / 3600) . ' hours ago.';
				} else if ($delta < (48 * 60 * 60)) { return '1 day ago.';
				} else { return floor($delta / 86400) . ' days ago.';
			}
		}

		function wp_follow_friday_getTwitterUser($userName){
			$url = 'http://twitter.com/users/show/'.$userName.'.xml?ustring='.uniqid('s',true);
			$meta = '';
			$TwitterCacheDir = dirname(__FILE__).'/xmlcache/';
			if (file_exists(WP_CONTENT_DIR.'/cache/xmlcache/.')){
				$TwitterCacheDir = WP_CONTENT_DIR.'/cache/xmlcache/'; 
			}
			$filename = $TwitterCacheDir . 'twitterUser_'.$userName.'.xml';
			$metafilename = $TwitterCacheDir . 'twitterUser_'.$userName.'.xml.meta';
			$header = null;
			if (file_exists($filename)) {
				$content = file_get_contents($filename);
				if (stristr($content,'<!DOCTYPE HTML PUBLIC')){
					// "I'm afraid I can't do that Dave...."
					unlink($filename);
					// something went horribly wrong on twitter's end
					return false;
					// try again later...
				}
			} else {
				$curl_handle=curl_init();
				curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
				curl_setopt($curl_handle,CURLOPT_URL,$url);
				curl_setopt($curl_handle,CURLOPT_HEADER,$header);
				$content = curl_exec($curl_handle);
				$httpCode = curl_getinfo($curl_handle);
				$meta .= print_r($httpCode,true)."\r\n";
				curl_close($curl_handle);
				if (is_writable($TwitterCacheDir)){
				// The following line writes http headers to a file for diagnostics.
//					file_put_contents($metafilename, $meta);
				}
				if ($httpCode['http_code']==404) {return false;}
				if ($httpCode['http_code']==500) {return false;}
				if ($httpCode['http_code']==503) {return false;}
				
				if (stristr($content,'<!DOCTYPE HTML PUBLIC')){return false;}
				if (stristr($content,'<HTML>')){return false;}
				if (stristr($content,'<hash>')){return false;}

				if (is_writable($TwitterCacheDir)){
					file_put_contents($filename, $content);
				}
			}
			try {
				$userinfo = new SimpleXMLElement($content);
			} catch (exception $e) {
				return false;
			}
			if (isset($userinfo->error)){
				unlink($filename);
				return false;
			}
			return $userinfo;
		}
	}
}

if (class_exists("wpFollowFriday")) {
	$followfriday = new wpFollowFriday();
}

if (!function_exists("wpFollowFriday_ap")) {
	function wpFollowFriday_ap() {
		global $followfriday;
		if (!isset($followfriday)) {
			return;
		}
		if (function_exists('add_options_page')) {
			add_options_page('WP Twitter Users', 'WP Twitter Users', 9, basename(__FILE__), array(&$followfriday, 'printAdminPage'));
		}
	}	
}

if (isset($followfriday)) {
	add_action('admin_menu', 'wpFollowFriday_ap');
	add_action('wp_head', array(&$followfriday, 'wp_follow_friday_add_header'), 1);
	add_action('activate_wp-twitter-users/wp-twitter-users.php',  array(&$followfriday, 'init'));
}

?>