=== WP Twitter Users ===
Contributors: 0xTC
Tags: Twitter, anywhere, Users, FollowFriday, profiles, badges, user, lists, showcase
Requires at least: 2.0.0
Tested up to: 3.0
Stable tag: trunk
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6331357

Allows authors to showcase one or more Twitter accounts. Useful for creating Follow Friday lists in posts or pages. Now with @Anywhere support.

== Description ==

Allows authors to showcase Twitter users when written about on blogs or pages or create formatted lists of users shown in badges using a simple shortcode. The plugin can also be used to easily add Twitter @Anywhere enhancments to your blog posts or pages.

Templates can be used to manage formatting. You can select from 10 templates or create your own custom template and put it in your theme's /wptu/ subfolder.

**Please read the instructions as described in the installation manual and FAQ**

== Installation ==

1. Upload/extract the `wp-twitter-users` folder to your `/wp-content/plugins/` directory.
3. **Make sure the `/xmlcache/` sub folder is writable! (Permissions set to 766 seems to work best)**
4. Activate the plugin through the 'Plugins' menu in WordPress.

**It is recommended to create an external cache path in order to preserve cached data between updates. Read the FAQ on how to enable this feature.**

== Frequently Asked Questions ==

= So, how do I use it? =

Just enter one or more twitter users in the shortcode `[ff xxx...]`. Example:

`[ff tcorp theemperfect]`

This will create two user boxes for the Twitter users "tcorp" and "theemperfect".

You can list as many users as you wish, but keep in mind that Twitter has a 150 requests/hour limit! If you create lists that have more than 150 users, the plugin will only list the first 150 users. You can always refresh the page an hour later and see if more users have been added to your post/page. The shape of the user boxes depend on the template you have chosen in the admin panel.

= @Anywhere support =

As of version 2.0.0 this WP Twitter Users supports Twitter's @Anywhere platform. This means that you can enhance Twitter @usernames on your blog or site with the @Anywhere service using this plugin. No shortcode is needed. Simply configure the options in the admin panel.

In order to take advantage of this feature, you must first register an API key with Twitter. You can do this by going to the WP Twitter Users settings page and clicking on the link labeled "Twitter API key".

Once you have entered your valid API key, you must set the options you wish to use.

* `Autolink @usernames to Twitter profiles.`

 * This option will automatically link Twitter @usernames entered in blogs or pages to their respective pages on Twitter.com.

* `Create hovercards for @usernames.`

 * This option will create hovercards for @usernames entered in blogs or pages. The hovercards display information about users when the mouse is hovering over them.

= Shortcode aliases and alternative input =

Instead of `[ff ...]` you can also use the following variations:

* `[#ff ...]`
* `[#followfriday ...]`
* `[followfriday ...]`
* `[twitterusers ...]`

All of the above shortcodes have the same result.

Usernames can be entered in a number of ways, all valid to the plugin. For example for the twitter user "tcorp" you can enter one of the follow variations:

* [#ff tcorp]
* [#ff @tcorp]
* [#ff http://twitter.com/tcorp]

= How do I preserve the cache between plugin updates? =

If you wish to preserve your cached XML files due to Twitter limitations, create the following **writable** folder: **`/wp-content/cache/xmlcache/`**.

All the requested will now be cached in the **external cache path** if it is writable.

== Screenshots ==
1. This is what a hovercard looks like.
2. Another example of a hovercard.
3. Example of a template
4. more templates
5. How you enter values

== Changelog ==

= 1.0.0 =

* First build!

= 1.0.1 =

* Added Error capturing
* Prefered cache directory defined.
* Added option for external cache.

= 1.0.3 = 

* fixed typo

= 1.1.0 =

* Added support for tweetimag.es (3rd party twitter image provider)

= 1.1.1 =

* Added auto-cleanup feature for faulty requests that are already cached.
* Accommodates for "Twitter is Over Capacity" error.

= 2.0.0 =

* Twitter @Anywhere support added.

