<?php

namespace MediaWiki\Extension\Theme;

use MediaWiki\MediaWikiServices;
use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\ResourceLoader\ResourceLoader;

/**
 * Utility/helper class.
 *
 * @file
 * @date 16 June 2017
 */
class Theme {

	/**
	 * Get all the enabled themes the given skin has.
	 *
	 * @param string $ourSkin Skin name, e.g. aurora, monobook, vector, etc.
	 * @return array Array containing theme names for the skin or 'default' if
	 *               the skin has no custom themes available
	 */
	public static function getAvailableThemes( string $ourSkin ): array {
		// Paranoia
		$ourSkin = strtolower( $ourSkin );

		$themes = ExtensionRegistry::getInstance()->getAttribute( 'ThemeModules' );

		if ( !isset( $themes[$ourSkin] ) ) {
			$themes[$ourSkin] = [];
		}

		// Ensure that 'default' is always the 1st array item
		array_unshift( $themes[$ourSkin], 'default' );

		// Filter out the themes defined in $wgSkipThemes for a skin, if any
		$skipThemes = self::getSkipThemes();

		if ( !is_array( $skipThemes ) ) {
			wfLogWarning(
				"Theme extension has an invalid configuration: 'SkipThemes' must be an array. Ignoring configuration."
			);
			$skipThemes = [];
		}

		if ( !isset( $skipThemes[$ourSkin] ) || !is_array( $skipThemes[$ourSkin] ) ) {
			$skipThemes[$ourSkin] = [];
		}

		foreach ( $skipThemes[$ourSkin] as $skippable => $value ) {

			$key = array_search( $skippable, $themes[$ourSkin], true );

			if ( ( $key !== false ) && ( $value === true ) ) {
				unset( $themes[$ourSkin][$key] );
			}
		}

		// Fallback to 'default' to prevent an empty theme selection
		return !empty( $themes[$ourSkin] ) ? $themes[$ourSkin] : [ 'default' => 'default' ];
	}

	/**
	 * Gets the default theme.
	 */
	public static function getDefaultTheme( string $ourSkin, string $hardcodedFallback ): string {

		$config = MediaWikiServices::getInstance()->getMainConfig();

		$availableThemes = self::getAvailableThemes( $ourSkin );

		$fallbackTheme = $config->has( 'FallbackTheme' )
				? $config->get( 'FallbackTheme' )
				: $hardcodedFallback;
		$defaultTheme = $config->has( 'DefaultTheme' )
				? $config->get( 'DefaultTheme' )
				: $fallbackTheme;

		// Validate default theme
		if (
			!is_string( $defaultTheme ) ||
			!in_array( $defaultTheme, $availableThemes, true )
		) {
			$defaultTheme = $fallbackTheme;
		}

		// Validate fallback theme
		if (
			!is_string( $defaultTheme ) ||
			!in_array( $defaultTheme, $availableThemes, true )
		) {
  			$defaultTheme = $hardcodedFallback;
		}

		// Last resort: use the first available theme
		if (
  			!is_string( $defaultTheme ) ||
  			!in_array( $defaultTheme, $availableThemes, true )
		) {
  			$defaultTheme = reset( $availableThemes );
		}

		return $defaultTheme;
	}

	/**
	 * Gets the skiped themes.
	 */
	private static function getSkipThemes(): array {

		$config = MediaWikiServices::getInstance()->getMainConfig();

		return $config->has( 'SkipThemes' ) ? $config->get( 'SkipThemes' ) : [];
	}

	/**
	 * @return bool
	 */
	public static function isDisabledSelectThemes( string $skin ): bool {
		$services = MediaWikiServices::getInstance();
		$config = $services->getMainConfig();
		if ( $config->has( 'DisableSelectThemes' ) ) {
			$disableSelectThemes = $config->get( 'DisableSelectThemes' );
			if ( isset( $disableSelectThemes[$skin] ) ) {
				return $disableSelectThemes[$skin];
			}
		}
		return false;
	}

	/**
	 * Check if the given skin has a theme named $theme and that it is usable
	 * (i.e. NOT in the $wgSkipThemes configuration variable array).
	 *
	 * @param string $skin Skin name, e.g. aurora, monobook, vector, etc.
	 * @param string $theme Theme name, e.g. dark, pink, etc.
	 * @param ResourceLoader $resourceLoader Reference to ResourceLoader
	 * @return bool
	 */
	public static function skinHasTheme( string $skin, string $theme, ResourceLoader $resourceLoader ): bool {

		$config = MediaWikiServices::getInstance()->getMainConfig();

		$skin = strtolower( $skin );
		$theme = strtolower( $theme );

		// Special case, all skins have a default theme since default means
		// "the skin without any custom styling"
		if ( $theme === 'default' ) {
			return true;
		}

		// The skin may provides themes but the selection of themes is disabled
		if ( self::isDisabledSelectThemes( $skin ) ) {
			return false;
		}

		$skipThemes = self::getSkipThemes();
		if ( isset( $skipThemes[$skin] ) &&
			isset( $skipThemes[$skin][$theme] ) ) {
			// This theme for this skin is explicitly disabled in the site's configuration.
			return $skipThemes[$skin][$theme];
		}

		$moduleName = 'themeloader.skins.' . $skin . '.' . $theme;

		if ( $resourceLoader->isModuleRegistered( $moduleName ) && $resourceLoader->getModule( $moduleName ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get skins with themes.
	 *
	 * @param array $skins Array with all skin names (skin key -> i18ned/human-friendly name)
	 * @return array Array containing skin names which supports themes
	 */
	public static function getSkinsWithThemes( array $skins ): array {
		// We only want the internal values, not the potentially i18n'ed (or CamelCased or
		// whatever), human-friendly names
		$skins = array_keys( $skins );

		$skinsWithThemes = [];
		$themes = ExtensionRegistry::getInstance()->getAttribute( 'ThemeModules' );

		foreach ( $skins as $skin ) {
			if ( isset( $themes[$skin] ) && !self::isDisabledSelectThemes( $skin ) ) {
				$skinsWithThemes[] = $skin;
			}
		}

		return $skinsWithThemes;
	}
}
