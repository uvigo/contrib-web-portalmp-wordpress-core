=== Plugin Name ===
Contributors: uvigo-atic, fernandogonzalez
Tags: wordpress, plugin, uvigo
Requires at least: 4.9
Tested up to: 4.9
Requires PHP: 7.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin UVigo with basic and common elements.

== Description ==

Plugin UVigo with basic and common elements.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/wpcoreuvigo` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

== Development ==

This plugin uses Webpack to prepare and compile frontend resources (CSS, Javascript, images, fonts, etc.)

The steps to compile are:

1. Install dependencies with yarn: `yarn`
2. To compile and build:
`yarn build`
`yarn build:production`
`yarn build:admin`
`yarn build:admin:production`

3. To develop with browsersync:
`yarn start`
`yarn start:admin`

= How to deploy =

To deploy this plugin you can use a script based in node that copy and compress all necessary files and folders. Then you can use that zip to distribute and install in WordPress.

Execute: `yarn deploy`

This command, compilem, build and zip files and folders.


== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.


== Changelog ==

Please refer to CHANGELOG.md
