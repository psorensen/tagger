=== Tagger ===
Contributors: psorensen
Tags: tags, wp-cli
Requires at least: 4.0
Tested up to: 4.7.3
Stable tag: 0.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

![Build Status](https://travis-ci.org/psorensen/tagger.svg?branch=master)

This plugin provides a WP-CLI command to scan posts in a Wordpress installation and suggest terms based on a user-provided CSV file.

![Gif of Plugin at work](http://g.recordit.co/T4oDW6TEB8.gif)


== Installation ==

1. Upload the `tagger` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress or via wp-cli: `wp plugin activate tagger`
3

== Usage ==
Create a CSV file matching the following format:

| term                | tags                                   |
|---------------------|----------------------------------------|
| term name or slug 1 | list, of, applicable, terms or phrases |
| term name or slug 2 | list, of, applicable, terms or phrases |

Using WP-CLI (in the same directory as the CSV file,) enter the following command:

`wp tagger tag_posts your_filename.csv`

The plugin will scan your posts and output a CSV file of post IDs with applicable terms at `/wp-content/uploads/tagger_results/`.

The following arguments are accepted:

- `--post_type`: comma-seperated list of post_types to scan. Defaults to post.
- `--threshold`: minimum number of collective occurences of the provided terms per tag. Defaults to 2.


== Changelog ==

= 0.1.0 =
* Initial Release

== Roadmap ==

This project currently exists as a tool to merely analyze posts and return a report. The following features are planned to be provided:

- Admin interface for submitting CSV files and downloading report
- Ability to confirm report and tag posts accordingly
- Content fields weighting (e.g. title*3)


