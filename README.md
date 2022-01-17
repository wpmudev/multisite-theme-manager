# Multisite Theme Manager

**INACTIVE NOTICE: This plugin is unsupported by WPMUDEV, we've published it here for those technical types who might want to fork and maintain it for their needs.**

## Translations

Translation files can be found at https://github.com/wpmudev/translations

## Give the WordPress theme selector on your network a theme shop style makeover with custom images, titles, descriptions, labels, CSS styling and category sorting.

Customize each individual theme and the overall theme selector from one simple interface. Create a better theme shopping experience for Multisite with Multisite Theme Manager.

### Theme Browsing With Style

Give the overall look and feel of Theme browsing on your network a makeover. Hide tags and author information to streamline design and white label presentation. Rename the theme page and add custom copy without adjusting core code. Plus, create your own stylesheets for a fresh design that won’t break when updating your network. 

### Custom Branding For Every Theme

Rebrand any theme on your network with a new name and description. Improve design and create a more uniform look with custom featured images. Link themes to your support page for in-house client support and build brand loyalty.

![custom-image](https://premium.wpmudev.org/wp-content/uploads/2014/04/custom-image-583x373.jpg)

 Create custom images for every theme.

 

![prosites](https://premium.wpmudev.org/wp-content/uploads/2014/04/prosites-583x373.jpg)

 Upsell themes on your network with Prosites.

### Upsell Themes With Prosites

Use Prosites to create and monetize your own WordPress.com or Edublogs.org type network. Offer premium themes as a paid upgrade with Prosites and use Multisite Theme Manager to build and manage your theme shop. Build a network with WordPress SuperPowers when pairing WPMU DEV plugins.

 

### Give Theme Search A Boost

Make it faster for users to find the themes they like with sortable categories. Give users the ability to eliminate themes that don’t fit their needs with a click. Sort by free, premium, style, framework (like Upfront), or anything else that will make your themes easier to navigate.

![categories](https://premium.wpmudev.org/wp-content/uploads/2014/04/categories-583x373.jpg)

 Use categories to make theme navigation simple.

 

![export-import](https://premium.wpmudev.org/wp-content/uploads/2014/04/export-import-583x373.jpg)

 Import and Export makes moving and sharing settings simple.

### Setup, Import And Export

With Setup Mode, you can configure and test your new theme shop before pushing it live to other users on your network. Built-in Import/Export makes it easy to move or share style settings across multiple networks and to create a consistent professional look on any network you manage.

  

### Simple Setup and Activation

You’ll never get lost with our easy-to-follow configuration tips. After you install and activate Multisite Theme Manager, head to the plugin’s settings page for easy instructions.

## Usage

### To Get Started:

Start by reading the [Installing Plugins](https://wpmudev.com/docs/using-wordpress/installing-wordpress-plugins/) section in our comprehensive [WordPress and WordPress Multisite Manual](https://premium.wpmudev.org/wpmu-manual/) if you are new to WordPress. Once installed and network-activated, you'll find a new menu item in your network admin under Settings > Multisite Theme Manager. 

![Multisite Theme Manager Network](https://premium.wpmudev.org/wp-content/uploads/2014/04/multisite-theme-manager-1000-menu-network1.png)

### Configuring the Network Settings

The settings on this screen enable you to set certain defaults, and configure how the Themes page will display on all sites in your network. Let's take a closer look at that right now, shall we? 

![Multisite Theme Manager Network](https://premium.wpmudev.org/wp-content/uploads/2014/04/multisite-theme-manager-1000-settings-network.png)

 1\. Toggle Setup Mode on/off.  
2\. Select the admin theme to use for the "Themes" screen.  
3\. Select what to display for each theme.  
4\. Select how to load theme images.  
5\. Customize the "Themes" menu item.  
6\. Enter a description for your "Themes" page.  
7\. Enter a default label for your custom link.

 1\. Enable _Setup Mode_ to fine-tune all the settings for the plugin, and all your themes. Once you're satisfied with how everything looks, make everything live and visible on all sites in your network by disabling Setup Mode.

*   While in setup mode, the Themes page on your main site will reflect any changes you make in the settings.

2\. The _Select Theme For Theme Page_ setting allows you to select the admin theme you want to use to display your available themes. A bit confusing? Don't worry, the theme you select here will only affect the "Themes" screen in the admin, not the front-end of any site. :) The plugin comes with a default theme for this purpose. But you can create your own custom theme(s) if you like.

*   You can upload any custom themes you make to the _wp-content/uploads/multisite-theme-manager_ folder. They will then be available for selection here.
*   Tip: to make your customization easier, start by duplicating the default theme from _multisite-theme-manager/multisite-theme-manager-files/themes_. You can then edit whatever you need.

3\. To _Select details that you want to show for themes_, simply check the box next to each feature you want to display. You can choose to show or hide the Author link that usually displays for each theme, a Custom Link that you can add if you like, the theme Tags & the Version number. 4\. The _Auto Load Screenshot With Correct Name_ setting gives you the option of uploading default images to use for theme screenshots. Simply upload any images you want to use to _wp-content/uploads/multisite-theme-manager/screenshots/_ and they will automatically display for the corresponding themes if this is set to "True".

*   The screenshots you upload must be correctly named for this to work. For example, if you want to upload a screenshot for the Twenty-Thirteen theme located at _wp-content/themes/twentythirteen_, then your screenshot should be named _twentythirteen.png_
*   Note that only png images will work with this method.
*   Even if you do upload default images, you can still override them for any theme when editing the theme details (see below).

5\. The _Theme Page Title_ you enter will display at the top of the Themes page on all sites. It will also replace the "Themes" menu label under "Appearance". 6\. You can also enter a _Theme Page Description_ that will display just beneath the theme page title. This is handy if you want to give your users a little introduction or more information about your theme offering. 7\. The _Default Custom Link Label_ you enter here will be used for the custom links you add for each theme.

*   Note that this will only display if you have checked the corresponding box above. You can override this default label for each theme.

### Importing & Exporting Settings & Data

Yessiree! We've included the ability to export your settings and data so you can safely back them up in case of server snafus. You can also import previously saved settings and data back into your site, or even to a different site altogether. Let's look at that now. 

![Multisite Theme Manager Import Export](https://premium.wpmudev.org/wp-content/uploads/2014/04/multisite-theme-manager-1000-settings-import-export.png)

 1\. Export your settings & data.  
2\. Import previously exported settings & data.  
3\. Delete settings and/or data.

 1\. The _Export_ feature enables you to export all your settings and theme data to a handy config.xml file simply by clicking the _Download Export File_ button. 2\. You can _Import_ a previously exported config.xml file to set everything up with a single click. This can be an extremely handy time-saver if you are running multiple networks and want the same settings and data on all of them.

*   Note that if you have already uploaded a previously saved config.xml file, any categories included in that file will not be editable in the theme details editor.

3\. Finally, if you need to _Reset_ things, you can choose to either Delete all Custom Theme Data or Reset All Settings.

*   _Delete all Custom Theme Data_ will do just that; it will delete everything you have entered in the Edit Theme Details area of every theme (see below for more).
*   _Reset All Settings_ will not affect your theme data; it will only reset the plugin options to their defaults.

### Configuring Theme Details

Now that you've saved your network settings, let's head on over to _Themes > Installed Themes_ in your network admin. That's where you'll configure the details for each theme you make available to your users. You'll notice a new link in the actions available for each theme: "Edit Details". Click that link for any theme you have installed. 

![Multisite Theme Manager Edit Details](https://premium.wpmudev.org/wp-content/uploads/2014/04/multisite-theme-manager-1000-edit-details.png)

 In the theme details editor, you can customize any option to your heart's content! Start by entering the name you would like to be displayed in your users' wp-admin instead of the actual theme name. 

![Multisite Theme Manager Theme Details](https://premium.wpmudev.org/wp-content/uploads/2014/04/multisite-theme-manager-1000-theme-details.png)

*   If you have chosen to display a Custom Link (in Network Admin > Settings > Multisite Theme Manager), enter that link in the Custom URL field.
*   If you want to override the Default Custom Link Label you had set, enter that in the Custom URL Label field.
*   You can also set a custom image to be displayed for the theme instead of the one that comes with the theme. This feature is fully integrated with the media library so you can simply click the Choose Image button to upload your stuff. Recommended image size is 600x450px.

Now let's take a look at adding categories. 

![Multisite Theme Manager Category Add](https://premium.wpmudev.org/wp-content/uploads/2014/04/multisite-theme-manager-1000-category-add.png)

 Simply click the New Category link beneath the Categories metabox. Then enter your desired category name, and click the "Add" button. Voilà, your new category is added and automatically selected.

*   Note that when adding a new category, a small link will appear next to it that says "Undefined". This simply means that the plugin has not yet been fully updated with the new category information. Once you switch the Setup Mode to Enabled and save the settings again, that will update the plugin, and the link will then read "Edit".
*   To delete a category, you must uncheck it in each theme where you have it checked. It will then disappear.
*   Here's a cool idea: if you have our [Pro Sites](https://premium.wpmudev.org/project/pro-sites/ "WordPress Pro Sites Plugin - WPMU DEV") plugin installed, you can even categorize your themes according to the Pro Site levels you have set up to make it really easy for your users to see all the cool premium themes they can get when they upgrade their site.

The last thing you might want to do is enter a custom description for the theme to better brand it for your network. 

![Multisite Theme Manager Edit Description](https://premium.wpmudev.org/wp-content/uploads/2014/04/multisite-theme-manager-1000-edit-description.png)

 Once you're done editing all your custom information for the theme, click the Update button to save your settings. You'll now see all your custom stuff displayed right there on the main Themes screen. 

![Multisite Theme Manager Details](https://premium.wpmudev.org/wp-content/uploads/2014/04/multisite-theme-manager-1000-details.png)

*   If you have uploaded a custom image to replace the theme screenshot, you'll see a thumbnail of that too at the far right.

### Final Checks

Now head on over to the Themes page on your main site to see how everything looks. 

![Multisite Theme Manager Main Site](https://premium.wpmudev.org/wp-content/uploads/2014/04/multisite-theme-manager-1000-main-site1.png)

*   You'll see all your work pay off as your custom page title & description display at the top.
*   Your theme categories are nicely laid out in a row, and clicking any category will filter the display to show only those themes you have assigned to that category.
*   Ooh, and there are your custom screenshots too!

Go ahead and hover your mouse pointer over any theme you have customized, and click the Theme Details button. There, in the nice big detail display, is all your custom work, nicely branded for network. Once you're satisfied with how all your themes look on the main site, head on over to the Multisite Theme Manager Settings screen in your network admin and change the Setup Mode to Disabled. That will activate your custom Themes screen on every site in your network. We hope you have fun customizing your user's Themes experience with Multisite Theme Manager.
