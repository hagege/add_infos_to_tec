# Changelog

## [Unreleased]

### Added

- Added support for each permalink-format for categories
- Added some hooks incl. documentation of them
- Now using new changelog hosted on GitHub

### Changed

- Cleaned up the readme.txt
- Set compatibility with WordPress 6.8
- Optimized GitHub action for any new release
- Optimized loading of our own CSS- and JS-scripts

### Removed

- Removed language files from release and repository
- Removed usage of category URL in settings, except "The Events Calendar" is not installed

## [1.5.2] 2025-02-20

### Fixed

- Fixed potential warning regarding thumbnails of events

## [1.5.1] 2024-12-08

### Fixed

- Fixed potential error during loading of settings

## [1.5.0] 2024-11-30

### Changed

- Compatible with WordPress Coding Standards
- Only compatible with PHP 7.4 or newer (incl. PHP 8.4)
- Updated translations

## [1.4.1] 2021-04-05

### Changed

- Enhancement: Improved and secure call for external links and improvement related to Web Vitals.

## [1.4] 2020-02-15

### Added

- Added feature: If using Classic Editor or Classic Block (Block Editor): After clicking on Icon "Add infos to the events calendar" it is possible to choose a category from the select box. Thanks to Adrian Lambertz from https://pixelbar.be for his great help implementing this option.
- Added feature: Settings: Now you can choose whether the categories should be sorted by frequency (default: frequently selected categories first) or by category name.
- Added: Even if The Events Calendar or any other event plugin is not installed, this plugin can be used without problems. Now only the options that are necessary in this case are shown.

### Changed

- Updated translations

## [1.3] 2019-05-30

### Fixed

- Fixed: Problem with the version number (1.2) so that no updates are performed automatically from Version 1.02 to 1.2.

## [1.2] 2019-05-28

### Added

- Added: Icon added to the editor tinycme (Classic Editor), so that now the entries for the internal and external link no longer only have to be entered manually directly as short code, but via an additional dialog. This option can be used in the Gutenberg Editor (Block Editor: Classic).

## [1.02] 2019-05-18

### Added

- Added: Automatically adds http:// to a URL before the link, if that is missing

### Fixed

- Fixed: If The Events Calendar is not installed, in some cases an error message appeared on the page with the short code

## [1.01] 2019-05-14

### Fixed

- Fixed: With the option "vl" the event list was not displayed correctly if the category was wrong or vl="" was selected.

## [1.0] 2019-05-14

### Added

- Added feature: The name of the buttons can now be defined via the settings. It is no longer necessary to have translation files that contain translations of the button names.
- Added: Display the copyright only if the field is not empty

### Changed

- Updated translations
- Updated design of the settings

## [0.66] 2019-05-10

### Added

- Add some infos and update language files

## [0.65] 2019-05-09

### Fixed

- Fixed a bug with the language files

## [0.62] 2019-05-07

### Added

- Initial release
