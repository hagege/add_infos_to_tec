# Add Infos to The Events Calendar

## About

This repository provides the features of WordPress plugin _Add Infos to The Events Calendar_. The repository is used as a basis for deploying the plugin to the WordPress repository. It is not intended to run as a plugin as it is, even if that is possible for development.

## Usage

### for users

Download the plugin [from the WordPress Repository](https://wordpress.org/plugins/add-infos-to-the-events-calendar/).
Or download the lastest release ZIP [from GitHub](https://github.com/hagege/add_infos_to_tec/releases).

### for developers

Checkout this repository in your development environment.

## Check for WordPress Coding Standards

### Initialize

`composer install`

### Run

`vendor/bin/phpcs --extensions=php --ignore=*/vendor/*,*/svn/*,add_shortcode_to_tec.php --standard=WordPress .`

### Repair

`vendor/bin/phpcbf --extensions=php --ignore=*/vendor/*,*/svn/*,add_shortcode_to_tec.php --standard=WordPress .`

## Check for WordPress VIP Coding Standards

Hint: this check runs against the VIP-GO-platform which is not our target for this plugin. Many warnings can be ignored.

### Run

`vendor/bin/phpcs --extensions=php --ignore=*/vendor/*,*/svn/* --standard=WordPress-VIP-Go .`
