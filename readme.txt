=== Block disposable emails - BDEmails ===
Contributors: BDEmails
Donate link: https://bdemails.com
Tags: block disposable emails, prevent fake users, block spam, disposable email, fake email, temporary email, spam
Requires at least: 3.3.1
Tested up to: 4.8
Stable tag: 2.2
Donate Link: https://bdemails.com
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The one plugin you need to block disposable/fake emails.

== Description ==

This plugin prevents people from registering with disposable email addresses like the ones provided by mailinator (also known as throw-away email, one-time email). It protects your most important asset, your registered user base, by preventing contamination by fake accounts. This plugin working principle is similar to spam blacklists.

It hooks in the wordpress function is_email() so it will extend the known email validation of wordpress to detect fake/disposable domains.

The plugin itself does not contain a list of domains to block. Instead of local maintenance the plugin uses the service of https://bdemails.com. This is a very accurate free service for at least 50 API requests per month.

= Key features =
A list with some of the features that you'll have with us:

* Absolutely FREE
* 50 API requests per hour (minimum)
* Instant access to our API after registration
* Posibility to block free email service providers (only if you want)
* Allow your own list of domains (e.g.: gmail.com if you choose to block free email service providers)
* Check up to 200 domains per month on the browser
* Statistics about what happend with your requests (via browsable or API)

= Why choose us =
We are the best (and we are ABSOLUTELY FREE)

* Improve the percentage of emails that make it to the inbox
* Reduce the bounce rate of your mailing campaigns
* Improve delivery rates and your reputation
* Keep a clean DataBase without fake/disposable emails
* We have more than 23k fake/disposable domains checked manually

== Installation ==

1. Download the plugin
2. Access your WordPress management interface logged in as administrator.
3. Go to Plugins/Add new section.
4. Upload and activate the plugin.
5. After activation, access Settings/BDEmails section and fill in with your API Key

== Frequently Asked Questions ==

= What about privacy =

This plugin does NOT submit email addresses. I do not run this service to collect your subscribers data.
Before the data is sent to https://bdemails.com the domain part is separated. So only the domain part is sent. For further information see the mentioned website.

= What happens if the service is down =

Our team is working hard to keep the servers up and running all the time.
Even if the service is down your users are able to leave comments and/or register.

= Is this service FREE =

Yes, it is. Our service it is ABSOLUTELY FREE. There are no costs at all.
If you like our service you can donate.

= I need more than 50 API requests per our =

If you need more than 50 requests per hour please send us an email at: requests   [at]   bdemails   [dot]   com
Let us know more about your service as we give to our users more than 50 requests if they need.

= Why should I use your service =

It is bad to have such addresses in your userbase for several reasons. And yes, it is easy to block *.ru addresses, known domains as trashmail.net, and some others – but they are changing continuously. This service currently detects more than 23k domains. So maybe it is a good alternative for your manually maintained blocklist.

= Can I prevent or report false positive =

This should be happen very rarely. In this case I kindly ask you to report this mistake at: report   [at]   bdemails   [dot]   com

== Screenshots ==

1. Dashboard: Statistics about your requests ...
2. API: Have an ideea if you'll need more than 50 API requests per hour
3. Browsable: Check how many requests you made from the browser in the last 7 months
4. Example: An example on how this plugin works
5. Plugin: What you'll see after plugin installation
6. Plugin: What you'll see (depending on your options) after you'll activate the plugin with a valid API

== Changelog ==

= 1.0 =

* Release date: July 4th, 2017
* First release
