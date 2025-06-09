=== Lee's Membership Hierarchy ===
Contributors: leethomas
Donate link: https://buymeacoffee.com/leethomastech
Tags: membership, organisation, status, expiry, user-management
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html


A custom membership hierarchy plugin allowing you to group users by organisation, assign roles, set statuses, and manage expiry dates.

== Description ==

This plugin lets site administrators assign users to custom organisations with additional properties like:

- Membership Type (e.g., Member, Admin, Delegate)
- Expiry Dates (organisation-wide or user-specific)
- Status control (Active, Inactive, or custom labels)

Features:
- Admin interface for managing organisations and their statuses
- Optional inheritance of expiry/status from organisations to users
- User profile extensions for membership assignment
- Organisation and user lists with status highlighting
- Customisable labels and dropdowns for membership types and statuses

Ideal for associations, federated groups, or multi-tiered membership setups.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Settings > Membership Hierarchy** to configure labels, membership types, and status options.
4. Use **Users > Membership List** to view and manage assigned users.
5. Edit individual user profiles to assign them to an organisation and set membership details.

== Frequently Asked Questions ==

= Can I define custom statuses or membership types? =

Yes, under **Settings > Membership Hierarchy**, you can add/remove values for both.

= Can expiry dates be inherited from the organisation? =

Yes, you can choose whether expiry is user-specific or taken from their organisation.

= Can I control whether status is inherited from organisation? =

Yes, status inheritance is optional and configurable.

= Will this plugin affect WordPress roles or capabilities? =

No, this is a standalone metadata system that works alongside existing roles.

== Screenshots ==

1. Admin view of plugin options.
2. Organisation management screen.
3. User profile enhancements.
4. Membership list with expiry and status.

== Changelog ==

= 1.0.0 =
* Initial public release.
* Organisation management, user profile enhancements, expiry/status settings, and admin listing pages.

== Upgrade Notice ==

= 1.0.0 =
First stable release.

== License ==

This plugin is released under the GPLv2 or later. See https://www.gnu.org/licenses/gpl-2.0.html
