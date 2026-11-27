# Theme

Die Pflege dieses Forks der MediaWiki-Erweiterung [Theme](https://wikimannia.org/mediawiki.org/Extension-Theme_(Fork).html) wird von WikiMANNia verwaltet.

The maintenance of this fork of the MediaWiki extension [Theme](https://wikimannia.org/mediawiki.org/Extension-Theme_(Fork).html) is managed by WikiMANNia.

El mantenimiento de esta bifurcación de la extensión de MediaWiki [Theme](https://wikimannia.org/mediawiki.org/Extension-Theme_(Fork).html) está gestionado por WikiMANNia.

## Description

Die Erweiterung unterstützt Themen, sie wird von themen-unterstützenden Skins verwendet.

- Das Skin ´cologneblue´ stellt das Thema ´partybook´ bereit.
- Das Skin ´monobook´ stellt die Themen ´dark´, ´pink´, ´stellarbook´ und ´wipi´ bereit.
- Das Skin ´vector´ stellt die Themen ´dark´, ´deepsea´ und ´dark-grey´ bereit.

The extension supports themes and is used by theme-supporting skins.

- The skin ´cologneblue´ will be provided with the theme ´partybook´.
- The skin ´monobook´ will be provided with the themes ´dark´, ´pink´, ´stellarbook´, and ´wipi´.
- The skin ´vector´ will be provided with the themes ´dark´, ´deepsea´ and ´dark-grey´.

La extensión admite temas y es utilizada por las apariencias que los admiten.

- La apariencia ´cologneblue´ incluirá el tema ´partybook´.
- La apariencia ´monobook´ incluirá los temas ´dark´, ´pink´, ´stellarbook´ y ´wipi´.
- La apariencia ´vector´ incluirá los temas ´dark´, ´deepsea´ y ´dark-grey´.

## Configuration

Definiere das voreingestellte Thema:

Define the default theme:

Definir el tema predeterminado:

: ´$wgDefaultTheme = 'default';´	// old style for backward compatibility

: ´$wgDefaultThemes['skinname1'] = 'themename1';´
: ´$wgDefaultThemes['skinname2'] = 'themename2';´


Einzelne Themen von der Auswahl in den Benutzereinstellungen ausnehmen:

Exclude individual themes from the theme selection in user settings:

Excluir temas individuales de la selección de temas en la configuración de usuario:

´$wgSkipThemes['monobook'] = [
		'pink' => true,
		'stellarbook' => true
	];´
´$wgSkipThemes['vector'] = [
		'dark' => true
	];´

´SkipThemes´ kann nun aber - unbeabsichtigt - dazu führen, dass das ´DefaultTheme´ gar nicht mehr zu den zugelassenen Themen gehört.
Es kann - optional - eine Ausweichalternative definiert werden:

However, using ´SkipThemes´ can unintentionally cause the ´DefaultTheme´ to no longer be among the allowed themes.
Optionally, an alternative theme can be defined:

Sin embargo, usar ´SkipThemes´ puede provocar involuntariamente que ´DefaultTheme´ deje de estar entre los temas permitidos.
Opcionalmente, se puede definir un tema alternativo:

: ´$wgFallbackTheme = 'default';´


Die Auswahl der Themen in den Benutzereinstellungen deaktivieren:

Disable theme selection in user settings:

Deshabilitar la selección de temas en la configuración de usuario:

- $wgDisableSelectThemes['monaco'] = true;
- $wgDisableSelectThemes['monobook'] = true;

## Compatibility

This extension has been tested with MediaWiki versions `1.35.14`, `1.39.17`, `1.43.9`, `1.45.4`, and `1.47.0-alpha`.

## Version history

Version 2.3 - Jan 29, 2023
- Remove DeepSea Vector theme
- It's broken in MW 1.39(+) and needs significantly more love than the other themes.
- Should someone be willing to volunteer to fix and maintain this theme, patches are more than welcome and restoring it will be easy.
- But for now, it unfortunately has to go.

Version 2.4 - May 9, 2024
- Use namespaced PHP class ResourceLoader
- This change requires MediaWiki 1.39+.

Version 2.5 - Jul 5, 2024
- Add $wgSkipThemes to allow sysadmins to disable certain skins' certain themes
- Bug: T220121
- Does not currently work with ShoutWiki's forks of the old Wikia Quartz and Monaco skins.
- $wgSkipThemes is an array that looks like this: ´
$wgSkipThemes = [
	'monobook' => [
		'stellarbook' => true
	],
	'vector' => [
		'dark' => true
	]
];´
- If you wanted to disable the ´StellarBook´ MonoBook and ´dark´ Vector themes, for example.

Version 2.5.1 - Oct 29, 2024
- Future-proofing
- Bug: T377521

Version (2.5.2) - Apr 24, 2025
- Use namespaced PHP classes

Version 3.0 - Oct 8, 2026
- Add namespace
- Add file ´Compat.php´
- Add backward compatibility to REL1_35

Version 3.1 - Oct 13, 2026
- Add theme ´wipi´

Version 3.2 - Oct 15, 2026
- Add theme ´deepsea´

Version 3.3 - Oct 29, 2026
- Add theme ´partybook´

Version 3.3.1 - Oct 30, 2026
- Upgrade ´extension.json´ to manifest version 2

Version 3.3.2 - Nov 15, 2026
- Throwing an InvalidArgumentException is to much at this point. A warning will be good enough.
- Add function ´getDefaultTheme()´ to handle global variables ´DefaultTheme´ and ´FallbackTheme´.

Version 3.4 - Nov 21, 2026
- Add global variable ´DisableSelectThemes´.

Version 3.5 - Nov 21, 2026
- Changes regarding skin ´Monaco´.

Version 3.6 - Nov 22, 2026
- The code
´$theme = $context->getRequest()->getRawVal( 'usetheme' )
	?? $this->userOptionsLookup->getOption( $context->getUser(), 'theme' )
	?? $defaultTheme;´
had to be revised because the return value always needs to be checked if it is in the list of allowed themes.

- The code
´// Fall back to 'default' if the skin has no theme with this theme name.
		$resourceLoader = $context->getOutput()->getResourceLoader();
		if ( !Theme::skinHasTheme( $skinName, $theme, $resourceLoader ) ) {
			$theme = 'default';
		}´
had to be disabled due of the ´DisableSelectThemes´ function.
The ´skinHasTheme´ function now returns ´false´ not only when the skin actually has no themes, but also when theme selection is completely disabled or all available themes are in the skip list.
In this case, ´$theme´ should be set to ´$wgDefaultTheme´ and not to 'default'.

Version 3.7 - Nov 23, 2026
- Add global variable ´$wgDefaultThemes´ for 'per-skin' default themes
