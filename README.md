# Wordpress PDF Light Viewer Plugin #
**Contributors:** antongorodezkiy, teamleadpower
  
**Tags:** pdf, pdfs, embed, pdf embed, publish pdf, import pdf, flipbook
  
**Donate link:** 
  
**Requires at least:** 3.5
  
**Tested up to:** 4.7

**Stable tag:** 1.3.17
  
**License:** GPLv2
  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

## Description ##

Plugin allows you to embed normal, big and very big pdf documents to the wordpress site as flipbooks with thumbnail navigation and zoom.

[Website](http://pdf-light-viewer.wp.teamlead.pw/) | [Demo](http://pdf-light-viewer.wp.teamlead.pw/demo/) | [Docs](http://pdf-light-viewer.wp.teamlead.pw/wp-content/plugins/pdf-light-viewer/documentation/index.html) | [PRO Addon Features](http://codecanyon.net/item/pdf-light-viewer-pro-addon/14089505) | [Support](http://support.wp.teamlead.pw/)

### Features ###
* Unlimited PDF files.
* Unlimited PDF file size without site performance issues.
* Turn.js flipbook integrated.
* Dashboard flipbook preview.
* Slider thumbnails navigation integrated.
* Lazy loading pages integrated.
* Pages zoom.
* Pages navigation.
* Fulscreen feature (for modern browsers).
* s2member compatible.
* Developer-friendly.
* [Well-documented](http://pdf-light-viewer.wp.teamlead.pw/wp-content/plugins/pdf-light-viewer/documentation/index.html).
* Fully translatable to any language using .po files.
* Supports wordpress multisites
* Responsive design
* Clear default look
* CLI integration
* Bulk PDF Import
* PDF Downloading
* Per-page downloading in JPEG or PDF
* Imagick or Gmagick support

### [PRO Addon](http://codecanyon.net/item/pdf-light-viewer-pro-addon/14089505) Features ###
* Single separate PDF page.
* Regular Wordpress PDF Archive page with 2 templates.
* One-page PDF Archive page with 2 templates.
* PDF Archive page by shortcode on custom page or frontpage.
* PDF Categories.
* Simple PDF page search.
* Document printing.
* Per-page printing.
* SEO friendly mode.

## Installation ##
1. Get the package
1. Upload plugin package with WordPress
1. Activate WordPress PDF Light Viewer Plugin on the WordPress Plugins page
1. That\'s all! You can start from [Quick start guide](http://pdf-light-viewer.wp.teamlead.pw/wp-content/plugins/pdf-light-viewer/documentation/index.html#quick)
1. After plugin activation please go to plugin\'s settings on the page http://yoursite.com/wp-admin/options-general.php?page=pdf-light-viewer

## Screenshots ##

###1. Settings
###
[missing image]

###2. PDF List
###
[missing image]

###3. PDF Import Page
###
[missing image]

## Other Notes ##

## Changelog ##

## 1.3.17 - 2017-03-29
* Private PDFs fix

## 1.3.16 - 2017-02-10
* Fix for ghostscript usage during the import (for wrong resolution issue)
* Setting to force two-pages layout for some screen sizes
* Enable/disable Zoom interactively
* Zoom magnify option

## 1.3.15 - 2017-01-19
* Stop import possibility
* Assets enqueue improvement
* Added French translation (R. Valentin)

## 1.3.14 - 2016-11-25
* Add setting to turn on/off url flipbook navigation
* JS function to recalculate sizes

## 1.3.13 - 2016-09-18
* fix PHP 5.3 compatibility

## 1.3.12 - 2016-11-16
* Added shortcode to enqueue assets in widgets

## 1.3.11 - 2016-11-11
* Added assets limit conditions and filter
* GraphicsMagick: Low quality for resulting images fix
* Improve page images preloading

## 1.3.10 - 2016-10-03
* Option to adjust result images size
* Fixed bug with resulting image quality
* Mobile styles improvements
* Fixed php error when server has no Imagick/Gmagick
* Fixed javascript bug in IE
* Changed file upload field to prevent url pasting

## 1.3.9 - 2016-09-11
* fix PHP 5.3 compatibility
* preload page images

## 1.3.8 - 2016-09-02
* Improved and fixed Gmagick support
* Added control for imagemagick/graphicsmagick switching
* Improved single page PDF download
* Added maximum viewer height setting
* Added go to page functionality

## 1.3.7 - 2016-08-25
* Post including logic improved
* Settings page help small improvements
* Smallfix for function type
* Documentation update

## 1.3.6 - 2016-08-19
* Make password protection work
* Single page PDF download
* Fixing minor php errors

## 1.3.5 - 2016-07-31 ##
* Adjustable viewer height

## 1.3.4 - 2016-07-08 ##
* CLI import fix

## 1.3.3 - 2016-06-24 ##
* CMB2 compatibility pull request merged
* toolbar fix
* pre-page download fix
* fullscreen fix

## 1.3.2 - 2016-06-22 ##
* per page download in toolbar
* page numbers bugfixing

## 1.3.1 - 2016-06-21 ##
* GraphicsMagick alternative support
* Page numbers in toolbar
* Control arrows in toolbar

## 1.3.0 - 2016-06-07 ##
* CMB to CMB2 dependency upgrade
* Imagetragick notification added
* Hide button for welcome notification
* Templates structure improvements
* Added import of only specific pages
* per-page downloading

## 1.2.7 - 2016-04-27 ##
* added maximum book width setting
* fix PHP 5.3 compatibility
* improved fullscreen mode

### 1.2.6 - 2016-04-22 ###
* Simple Line Icons conflict fix
* fix for compatibility with Magazon and other themes
* Smart Manager compatibility fix
* Mailpoet compatibility fix

### 1.2.5 - 2016-04-14 ###
* improve colors and/or colorspaces of resulting images

### 1.2.4 - 2016-03-21 ###
* option to display with a single page layout only
* hide top toolbar when there are no icons
* categories for PDFs
* additional template to display thumbnail carousel at the top instead of the bottom
* enhance full screen function in Chrome and IE

### 1.2.3 - 2016-01-20 ###
* integration with WP CLI
* added CLI command bulk-import
* fixed missing font-awesome css
* icons compatibility fix

### 1.2.2 - 2015-12-25 ###
* fixing issue with 404 links in pagination

### 1.2.1 - 2015-12-18 ###
* bugfixing pro addon compatibility
* design improvements

### 1.2.0 - 2015-12-14 ###
* PHP 5.3 is now required instead of PHP 5.2
* Design improvements
* Changed icon set from FontAwesome to Simple Line Icons
* Fixed admin notifications handling
* Refactoring, improved code logic
* Added compatibility with PRO Addon

### 1.1.7 - 2015-12-05 ###
* Fixed bug when 2nd page in a 2-page pdf not displayed in thumbnails
* Pages limit bug
* Fixed typos

### 1.1.6 - 2015-11-22 ###
* Improvements for lazy loading pages
* Enabled zoom for fullscreen mode
* Improvements to detect GhosScript installation on Unix-like systems
* Improvements for settings page

### 1.1.5 - 2015-09-03 ###
* Fixes for compatibility with themes/plugins which are using humanmade/Custom-Meta-Boxes

### 1.1.4 - 2015-09-03 ###
* Fixed bug for metaboxes on Import page (when Import page shows only title)

### 1.1.3 - 2015-08-04 ###
* Added responsiveness for tablet and phones
* Improved fullscreen mode
* Hiding fullscreen button for browsers without Fullscreen API support
* Fixing lazyload issues - thanks to Amit for reporting the issue
* Some interface improvements - thanks to zipzit
* BX Slider wrong path #3 - fixed
* pdf-light-viewer template is not functional from user's theme. #5 - fixed

### 1.1.1-1.1.2 - 2015-07-15 ###
* Added information about GhostScript to the documentation - thanks to Alexander
* Improved requirements section
					
### 1.1.0 - 2015-06-02 ###
* Improved CMYK PDF import
* Improved admin import status messages
* Added landscape PDF support
* Fixed some bugs with images sizing
* Increased default image sizes
* Added page zoom for flipbooks
* Added compatibility for 2 or more instances of plugin on the same page
* Added options to disable zoom and fullscreen
* Updated client side libraries

### 1.0.1 - 2015-01-14 ###
* Fixed PHP errors
* Added docs explanation for Visual Composer How-To and "Imagick is not supported" requirement error

### 1.0 - 2014-11-26 ###
The plugin released
