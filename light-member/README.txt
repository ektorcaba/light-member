=== Light Member ===
Contributors: ektorcaba
Donate link: https://donate.stripe.com/6oEg2gcKo9v59he5kl
Tags: community, member, membership, user profile, User Registration, members-only, light member
Requires at least: 6.0
Tested up to: 6.4.2
Stable tag: 1.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Ultra low resources member plugin, turn your site in a membership site.

== Description ==

Offer exclusive content for members on your site with this plugin and charge for access through Stripe.

A very easy-to-configure plugin that doesn't require many resources, very lightweight, designed to do just what is necessary.

You won't need anything else to have your membership site.

I'm not happy with how plugins have been working lately. They say they're free, and then you have to pay for everything, making WordPress very heavy. I don't do that. This plugin is 100% free, but I ask that if it has helped you, please consider making a donation. As long as I receive donations, I'll make sure to keep this plugin alive.

What you can do with this plugin:

* Optional Frontend login/register/forgot
* Simple integration Stripe Payment
* Log Stripe request
* Mail templates
* Hide admin bar
* Disable default profile
* Disable wp-login.php and links
* Enable free user registration
* Customize the default email address and sender name for emails
* reCAPTCHA support
* Automatic member role
* Hide full content post/page/custom
* Hide partial content shortcode on post/page/custom
* Custom messages for hidden content to non-members
* Shortcode for invoices, profile, edit user
* Stripe invoices and Customer Portal access
* Highlighted hidden content for administrators
* Shortcodes/Filters/Hooks

Included languages:

* English
* Spanish
* .pot file included

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload plugin to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Create a product in Stripe with a recurring price.
4. Create a payment link in Stripe from your product.
5. Place your payment link where do you want in your site.
6. Activate Stripe Customer portal.
7. Paste Stripe Customer Portal link in plugin settings.
8. Copy your Endpoint Webhook from plugin settings at bottom.
9. Inside your Stripe account, go to developers/webhooks, create new endpoint, paste the link and select this events:
    * checkout.session.completed
    * charge.refunded
    * customer.subscription.updated
    * customer.subscription.deleted
    * invoice.paid
    * invoice.payment_succeeded
10. Copy Secret Sign from your Endpoint Webhook and paste in the plugin settings field.
11. Go to Stripe developers/API keys, copy & paste Secret Key to the plugin settings field.
12. Create a new page in WordPress and place it this shortcode [lm_member_profile_page]
13. Set this page in plugin settings at Member Page field.
14. Set your free and member role, by default the plugin create a Member role for you, you can select Subscriber as free member and Member role as pay member.

== Frequently Asked Questions ==

= Is the plugin free? =

Yes, completely free. You won't have to pay for any additional add-ons. If this plugin has helped you, I ask that you help me with a donation to continue maintaining it.

== Screenshots ==

1. Plugin Settings
2. Login form
3. Register form
4. Forgot your password form
5. Non-member view of partial hidden content
6. Administrator view of partial hidden content

== Changelog ==

= 1.4 =
* Documentation updated
* Bug fixes

= 1.3 =
* Spanish language added
* Bug fixes

= 1.2 =
* Added shortcode lm_login_button
* Bug fixes

= 1.1 =
* Replace default login_url when wp-login.php is disabled.

= 1.0 =
* Initial release
