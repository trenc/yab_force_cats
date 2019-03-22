<?php

// This is a PLUGIN TEMPLATE for Textpattern CMS.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Plugin names should start with a three letter prefix which is
// unique and reserved for each plugin author ("abc" is just an example).
// Uncomment and edit this line to override:
$plugin['name'] = 'yab_image_cats';

// Allow raw HTML help, as opposed to Textile.
// 0 = Plugin help is in Textile format, no raw HTML allowed (default).
// 1 = Plugin help is in raw HTML.  Not recommended.
# $plugin['allow_html_help'] = 1;

$plugin['version'] = '0.1.0';
$plugin['author'] = 'Tommy Schmucker';
$plugin['author_uri'] = 'http://www.yablo.de/';
$plugin['description'] = 'Forces you to use image categories.';

// Plugin load order:
// The default value of 5 would fit most plugins, while for instance comment
// spam evaluators or URL redirectors would probably want to run earlier
// (1...4) to prepare the environment for everything else that follows.
// Values 6...9 should be considered for plugins which would work late.
// This order is user-overrideable.
$plugin['order'] = '5';

// Plugin 'type' defines where the plugin is loaded
// 0 = public              : only on the public side of the website (default)
// 1 = public+admin        : on both the public and admin side
// 2 = library             : only when include_plugin() or require_plugin() is called
// 3 = admin               : only on the admin side (no AJAX)
// 4 = admin+ajax          : only on the admin side (AJAX supported)
// 5 = public+admin+ajax   : on both the public and admin side (AJAX supported)
$plugin['type'] = '3';

// Plugin "flags" signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

$plugin['flags'] = '';

// Plugin 'textpack' is optional. It provides i18n strings to be used in conjunction with gTxt().
// Syntax:
// ## arbitrary comment
// #@event
// #@language ISO-LANGUAGE-CODE
// abc_string_name => Localized String

/** Uncomment me, if you need a textpack
$plugin['textpack'] = <<< EOT
#@admin
#@language en-gb
abc_sample_string => Sample String
abc_one_more => One more
#@language de-de
abc_sample_string => Beispieltext
abc_one_more => Noch einer
EOT;
**/
// End of textpack

if (!defined('txpinterface'))
        @include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---
/**
 * yab_image_cats
 *
 * A Textpattern CMS plugin.
 * Forces you to use image categories.
 *
 * @author Tommy Schmucker
 * @link   http://www.yablo.de/
 * @link   http://tommyschmucker.de/
 * @date   2019-03-22
 *
 * This plugin is released under the GNU General Public License Version 2 and above
 * Version 2: http://www.gnu.org/licenses/gpl-2.0.html
 * Version 3: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * config var
 *
 * @param string $what
 * @return string configuration value
 */
function yab_ic_config($what)
{

	$config = array(

		'preSelectedCategory' => '' // name of the pre selected image category

	);

	return $config[$what];

}


if (@txpinterface === 'admin')
{

	register_callback(

		'yab_image_cats',
		'admin_side',
		'body_end'

	);

}

/**
 * Prefill the image category for uploads
 *
 * @return void Echos the JavaScript injection
 */
function yab_image_cats()
{

	global $event, $step;

	$url = hu.'textpattern/index.php?event=image&search_method=category&crit=';

	if ($event !== 'image' or $step === 'image_edit')
	{

		return;

	}

	extract(gpsa(array(

 	 	'crit',
 	 	'search_method'

	)));

	$preCat = yab_ic_config('preSelectedCategory');

	if ($search_method === 'category')
	{

		if ($crit)
		{

			$preCat = $crit;

		}

	}


	$js = <<<EOF

(function() {

	const preCat = '$preCat';

	const catDropdown = document.getElementById('image_category');


	catDropdown.querySelector('option[value="' + preCat +'"]').selected = true;


	const selectedUploadCat = catDropdown.options[catDropdown.selectedIndex].value;

	const uploadButton = document.querySelectorAll('.inline-file-uploader-actions > input[type=submit]')[0];

	setDisabledByValue(uploadButton, selectedUploadCat);


	catDropdown.onchange = function() {

		const changedUploadCat = this.options[this.selectedIndex].value;

		setDisabledByValue(uploadButton, changedUploadCat);

		// change uri
		// we can do better next by ajax loading the results
		if (changedUploadCat) {

			window.location = '$url' + changedUploadCat;

		}

	}


	function setDisabledByValue(element, value) {

		if (value === '') {

			element.setAttribute('disabled', true);

		} else {

			element.removeAttribute('disabled');

		}

	}

})();

EOF;

	echo '<script>'.$js.'</script>';

}
# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN CSS ---

# --- END PLUGIN CSS ---
-->
<!--
# --- BEGIN PLUGIN HELP ---
h1. yab_image_cats

p. A TXP plugin that forces you to use image categories.

p. *Version:* 0.1.0

h2. Table of contents

# "Plugin requirements":#help-section02
# "Configuration":#help-config03
# "Changelog":#help-section10
# "License":#help-section11
# "Author contact":#help-section12

h2(#help-section02). Plugin requirements

p. yab_image_cats's  minimum requirements:

* Textpattern 4.7.3

h2(#help-config03). Configuration

p. Install the plugin and activate it. In the plugin code got to the first function named @yab_ic_configi()@.<br />
Here you can change the @preSelectedCategory@ to a category which you would be pre selected.

h2(#help-section10). Changelog

* v0.1.0: 2019-03-22
** initial release

h2(#help-section11). Licence

This plugin is released under the GNU General Public License Version 2 and above
* Version 2: "http://www.gnu.org/licenses/gpl-2.0.html":http://www.gnu.org/licenses/gpl-2.0.html
* Version 3: "http://www.gnu.org/licenses/gpl-3.0.html":http://www.gnu.org/licenses/gpl-3.0.html

h2(#help-section12). Author contact

* "Author's site":https://www.yablo.de/
* "Author's site":https://tommyschmucker.de/
* "Plugin on GitHub":https://github.com/trenc/yab_image_cats
# --- END PLUGIN HELP ---
-->
<?php
}
?>
