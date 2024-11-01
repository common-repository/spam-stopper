=== Plugin Name ===
Contributors: jolley_small
Donate link: http://blue-anvil.com/archives/wordpress-comment-spam-stopper-plugin
Tags: spam, spam stopper, captcha, anti-spam, comment, comments, form, antispam
Requires at least: 2.0
Tested up to: 3.0
Stable tag: 3.1.3

The whole idea to this plugin is to keep spammer robots from posting on your blog, reducing the space taken by spam messages in the database, and preventing your blog from being a spam magnet.

== Description ==

The whole idea to this plugin is to keep spammer robots from posting on your blog, reducing the space taken by spam messages in the database, and preventing your blog from being a spam magnet.

You can ask anything, keep it simple and obvious, e.g. Is the sky Green? NO!. Doing this will stop stupid bots from being able to post.

It only shows up when you are logged out, so you dont have to fill it in if your an admin. On top of this, it also adds javascript validation to the form to ensure the required fields are filled in.

You can configure the plugin in <code>Admin > Tools > Spam Stopper</code> after installing it.

== Installation ==

= Installation instructions =

   1. Unzip and upload the php file to your wordpress plugin directory
   2. Activate the plugin
   
   In older version of wordpress (that don't support wp_enqueue_script) you may need to include jquery in your themes header.php manually in order for the client side validation to function.
   
== Screenshots ==

1. Form Field Screenshot

== Changelog ==

= 3.1.3 = 
* WordPress 3.0 Compatibility

= 3.1.2 =
*	Added Persian translation by Mostafa Soufi
*	Added class to offset spam trap (spam-offset) 

= 3.1.1 =
*	Option to have label on either side of input - makes theming easier.

= 3.1 =
*	Added changelog to readme.
*	Email validation bug squashed
*	Cached comments now work; if user forgets to fill in antispam or makes a mistake (and the JS does not catch it) the users comment will not be lost.
*	Redone entire code to make it more efficient
*	Admin section added for changing the antispam question
*	Form ID and honeypot trap added to form