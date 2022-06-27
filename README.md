Aligent OroCommerce Announcement Bundle
==============================
This bundle adds the ability to display a custom Announcement Message on the OroCommerce storefront.

<img src="src/Aligent/AnnouncementBundle/Resources/doc/img/sample-announcement.png" alt="Sample Announcement">

### Example Use Cases
- Shipping Delays
- Public Holiday closures
- Promotional Messages
- Upcoming Website Maintenance

### Features
- Select custom Background Colour for Announcement
- Set optional Start/End Date for Announcement
- Restrict Announcement to one or more Customer Groups 

Requirements
-------------------
- OroCommerce 5.0

Installation and Usage
-------------------
**NOTE: Adjust instructions as needed for your local environment**

### Installation
*Currently, this Bundle is not available via Composer and must be installed manually into the application*

Once installed, clear Oro cache to load bundle, then run a [full asset install](https://doc.oroinc.com/bundles/platform/AssetBundle/commands/#oro-assets-install) to ensure assets are recompiled.


### Configuration Settings

<img src="src/Aligent/AnnouncementBundle/Resources/doc/img/configuration-options.png" alt="Configuration Options">

| Setting                     | Description                                                                                                                |
|-----------------------------|----------------------------------------------------------------------------------------------------------------------------|
| **Block Background Colour** | Select the background colour for the Announcement on the storefront (Colour picker)                                        |
| **Start Date**              | Date to start displaying the Announcement (Use Default for 'immediately')                                                  |
| **End Date**                | Date to stop displaying the Announcement (Use Default for 'forever')                                                       |
| **Content Block**           | Select the Content Block containing the message to display                                                                 |
| **Allowed Customer Groups** | If enabled, only these Customer Groups will see the announcement message. (NOTE: **Ctrl+Click** to select multiple Groups) |

Database Modifications
-------------------
*This Bundle does not directly modify the database schema in any way*

All configuration is stored in System Configuration (`oro_config_value`).

Templates
-------------------
`Resources/views/layouts/default/page/alert_bar.html.twig`

This includes a single `_alert_bar_widget` block which can be customized/overridden in OroCommerce themes
if needed.

Roadmap / Remaining Tasks
-------------------
- [x] Ability to restrict Announcement to one or more Customer Groups
- [x] OroCommerce 5.0 Support
- [ ] Implement Unit Tests
- [ ] Reset `hideAlert` session variable when new Announcements are added
- [ ] Ability to block dismissal of Announcement Message (hides the 'X' button)
- [ ] Ability to only display on Homepage
- [ ] Ability to configure multiple messages for different scenarios
- [ ] (TBC) Move away from Content Blocks to WYSIWYG configuration fields
