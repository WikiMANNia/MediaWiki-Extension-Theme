# Theme

Die Pflege dieses Forks der MediaWiki-Erweiterung [Theme](https://www.mediawiki.org/wiki/Extension:Theme/de) wird von WikiMANNia verwaltet.

The maintenance of this fork of the MediaWiki extension [Theme](https://www.mediawiki.org/wiki/Extension:Theme) is managed by WikiMANNia.

El mantenimiento de esta bifurcación de la extensión de MediaWiki [Theme](https://www.mediawiki.org/wiki/Extension:Theme/es) está gestionado por WikiMANNia.

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

- El tema visual ´cologneblue´ incluirá el tema ´partybook´.
- El tema visual ´monobook´ incluirá los temas ´dark´, ´pink´, ´stellarbook´ y ´wipi´.
- El tema visual ´vector´ incluirá los temas ´dark´, ´deepsea´ y ´dark-grey´.

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
- If you wanted to disable the StellarBook MonoBook and dark Vector themes, for example.

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
