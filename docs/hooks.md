# Hooks

- [Actions](#actions)
- [Filters](#filters)

## Actions

*This project does not contain any WordPress actions.*

## Filters

### `ait_file_version`

*Filter the used file version (for JS- and CSS-files which get enqueued).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$plugin_version` | `string` | The plugin-version.
`$filepath` | `string` | The absolute path to the requested file.

**Changelog**

Version | Description
------- | -----------
`1.6.0` | Available since 1.6.0.

Source: [./classes/class-helper.php](classes/class-helper.php), [line 35](classes/class-helper.php#L35-L43)

### `ait_fs_beitrags_fuss`

*Filter the output in footer.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$fs_ausgabe` | `string` | The output string.
`$werte` | `array` | The shortcode attributes.

**Changelog**

Version | Description
------- | -----------
`1.6.0` | Available since 1.6.0.

Source: [./add-infos-to-the-events-calendar.php](add-infos-to-the-events-calendar.php), [line 160](add-infos-to-the-events-calendar.php#L160-L167)


<p align="center"><a href="https://github.com/pronamic/wp-documentor"><img src="https://cdn.jsdelivr.net/gh/pronamic/wp-documentor@main/logos/pronamic-wp-documentor.svgo-min.svg" alt="Pronamic WordPress Documentor" width="32" height="32"></a><br><em>Generated by <a href="https://github.com/pronamic/wp-documentor">Pronamic WordPress Documentor</a> <code>1.2.0</code></em><p>

