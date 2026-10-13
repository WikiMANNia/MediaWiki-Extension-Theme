# Theme

Die Pflege dieses Forks der MediaWiki-Erweiterung [Theme](https://www.mediawiki.org/wiki/Extension:Theme/de) wird von WikiMANNia verwaltet.

The maintenance of this fork of the MediaWiki extension [Theme](https://www.mediawiki.org/wiki/Extension:Theme) is managed by WikiMANNia.

El mantenimiento de esta bifurcación de la extensión de MediaWiki [Theme](https://www.mediawiki.org/wiki/Extension:Theme/es) está gestionado por WikiMANNia.

## Description

Die Erweiterung unterstützt Themen, sie wird von themen-unterstützenden Skins verwendet.

- Dem Skin ´monobook´ werden die Themen ´dark´, ´pink´ und ´stellarbook´ bereitgestellt.
- Dem Skin ´vector´ werden die Themen ´dark´ und ´dark-grey´ bereitgestellt.

The extension supports themes and is used by theme-supporting skins.

- The skin ´monobook´ will be provided with the themes ´dark´, ´pink´, and ´stellarbook´.
- The skin ´vector´ will be provided with the themes ´dark´ and ´dark-grey´.

La extensión admite temas y es utilizada por las apariencias que los admiten.

- El tema visual ´monobook´ incluirá los temas ´dark´, ´pink´ y ´stellarbook´.
- El tema visual ´vector´ incluirá los temas ´dark´ y ´dark-grey´.

## Configuration

Definiere das voreingestellte Thema:

Define the default theme:

Definir el tema predeterminado:

: "DefaultTheme": "default",

Schließe folgende Themen aus:

Exclude the following themes:

Excluir los siguientes temas:

: "SkipThemes": []

## Compatibility

This extension has been tested with MediaWiki versions `1.39.17`, `1.43.9`, `1.45.4`, and `1.47.0-alpha`.

The extension is expected to work with MediaWiki 1.36–1.38, but these versions are not currently tested.

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
- If you wanted to disable the StellarBook MonoBook and dark Vector themes, for example.

Version 2.3 - Jan 29, 2023
- Remove DeepSea Vector theme
- It's broken in MW 1.39(+) and needs significantly more love than the other themes.

Version 2.4 - May 9, 2024
- Use namespaced PHP class ResourceLoader
- This change requires MediaWiki 1.39+.

Version 2.5 - Jul 5, 2024
- Add $wgSkipThemes to allow sysadmins to disable certain skins' certain themes
- Lightly tested with a few skins and themes and seems to work on any and all remotely sane, FOSS skins.
- Does not currently work with ShoutWiki's forks of the old Wikia Quartz and Monaco skins.
- Quartz is a trickier case, but for Monaco it *seems* (again, based on very little testing) that theme-related code needs to be removed from SkinMonaco#initPage and that's it.
´$wgSkipThemes is an array that looks like this:
$wgSkipThemes = [
	'monobook' => [
		'stellarbook' => true
	],
	'vector' => [
		'dark' => true
	]
];´
- If you wanted to disable the ´StellarBook MonoBook´ and ´dark Vector´ themes, for example.

Version 2.5.1 - Oct 29, 2024
- Future-proofing
- Bug: T377521

Version (2.5.2) - Apr 24, 2025
- Use namespaced PHP classes
