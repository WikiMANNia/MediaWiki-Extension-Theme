<?php

namespace MediaWiki\Extension\Theme;

use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Hook\OutputPageBodyAttributesHook;

/**
 * Compatibility note:
 * The hook interfaces were moved to MediaWiki\Output\Hook in MW 1.42.
 * The old MediaWiki\Hook names remain available as deprecated aliases.
 * Tested with MW 1.35, 1.39, 1.43, 1.45 and 1.47.0-alpha.
 * Keep the old imports until the aliases are removed from MediaWiki core.
 *
 * - use MediaWiki\Output\Hook\BeforePageDisplayHook;
 * - use MediaWiki\Output\Hook\OutputPageBodyAttributesHook;
*/

use MediaWiki\Preferences\Hook\GetPreferencesHook;
use MediaWiki\ResourceLoader\Hook\ResourceLoaderGetConfigVarsHook;

use MediaWiki\Context\RequestContext;
use MediaWiki\MediaWikiServices;
use MediaWiki\Output\OutputPage;
use MediaWiki\Parser\Sanitizer;
use MediaWiki\Skin\Skin;
use MediaWiki\User\Options\UserOptionsLookup;
use MediaWiki\User\User;
use SkinFactory;

class Hooks implements
	BeforePageDisplayHook,
	GetPreferencesHook,
	OutputPageBodyAttributesHook,
	ResourceLoaderGetConfigVarsHook
{
	private SkinFactory $skinFactory;
	private UserOptionsLookup $userOptionsLookup;

	public function __construct(
		SkinFactory $skinFactory,
		UserOptionsLookup $userOptionsLookup
	) {
		$this->skinFactory = $skinFactory;
		$this->userOptionsLookup = $userOptionsLookup;
	}

    public static function onRegistration() {
        Compat::init();
    }

	/**
	 * Add the JS needed to preview themes in real time onto the output
	 * on Special:Preferences and its global version, Special:GlobalPreferences.
	 *
	 * @param OutputPage $out
	 * @param Skin $sk
	 * @return void This hook must not abort, it must return no value
	 */
	public function onBeforePageDisplay( $out, $sk ): void {
		$this->addJSonPreferences( $out );

		$theme = $this->getTheme( $out );

		if ( $theme === 'default' ) {
			return;
		}

		$skin = strtolower( $sk->getSkinName() );
		$prefix = 'themeloader.';
		$moduleName = $prefix . "skins.$skin.$theme";
		$script = $out->getResourceLoader()->getLoadScript( 'local' );

		if (
			$out->getTitle()->isSpecial( 'Preferences' ) ||
			$out->getTitle()->isSpecial( 'GlobalPreferences' )
		) {
			// Add the CSS file with a <link> element.
			// This allows to remove the theme style on live preview.
			$out->addLink( [
				'id' => 'mw-themeloader-module',
				'data-theme' => $theme,
				'rel' => 'stylesheet',
				'href' => wfAppendQuery( $script, [
					'modules' => $moduleName,
					'only' => 'styles',
					'skin' => $skin
				] )
			] );
		} else {
			// Add the CSS file via ResourceLoader.
			$out->addModuleStyles( $moduleName );
		}
	}

	/**
	 * Add the JS needed to preview themes in real time onto the output
	 * on Special:Preferences and its global version, Special:GlobalPreferences.
	 */
	private function addJSonPreferences( OutputPage $out ): void {
		if (
			$out->getTitle()->isSpecial( 'Preferences' ) ||
			$out->getTitle()->isSpecial( 'GlobalPreferences' )
		) {
			// Only load this JS on Special:Preferences/Special:GlobalPreferences
			$out->addModules( 'ext.theme.livepreview' );
		}
	}

	/**
	 * Add the theme selector to user preferences.
	 *
	 * @param User $user
	 * @param array &$defaultPreferences
	 * @return bool|void True or no return value to continue or false to abort
	 */
	public function onGetPreferences( $user, &$defaultPreferences ) {
		$ctx = RequestContext::getMain();
		$services = MediaWikiServices::getInstance();
		$config = $services->getMainConfig();
		$skinName = $ctx->getSkin()->getSkinName();

		$themes = Theme::getAvailableThemes( $skinName );
		// Braindead code needed to make the theme *names* show up
		// Without this they show up as "0", "1", etc. in the UI
		$themeArray = [];
		foreach ( $themes as $themeKey ) {
			$msg_key = ( $themeKey === 'default' )
				?	"theme-name-default"
				:	"theme-name-{$skinName}-{$themeKey}";
			$themeDisplayNameMsg = $ctx->msg( $msg_key );
			if ( $themeDisplayNameMsg->isDisabled() ) {
				// No i18n available for this -> use the key as-is
				$themeDisplayName = ucfirst( $themeKey );
			} else {
				// Use i18n; it's much nicer to display formatted theme names if and when
				// a theme name contains spaces, uppercase characters, etc.
				$themeDisplayName = $themeDisplayNameMsg->text();
			}
			$themeArray[$themeDisplayName] = $themeKey;
		}

		$installedSkins = $this->skinFactory->getInstalledSkins(); // @since 1.37
		$skinsWithThemes = Theme::getSkinsWithThemes( $installedSkins );
		$showIf = [ 'OR' ];
		foreach ( $skinsWithThemes as $skinName ) {
			$showIf[] = [ '===', 'skin', $skinName ];
		}

		$defaultTheme = Theme::getDefaultTheme( $skinName, 'default' );
		$defaultTheme = $this->userOptionsLookup->getOption( $user, 'theme', $defaultTheme );
		$defaultPreferences['theme'] = [
			'type' => 'select',
			'options' => $themeArray,
			'default' => $defaultTheme,
			'label-message' => 'theme-prefs-label',
			'section' => 'rendering/skin',
			'hide-if' => [ 'NOT', $showIf ],
		];

		// If a skin has no themes (besides "default"),
		// show only an informative message instead
		$defaultPreferences['notheme'] = [
			'type' => 'info',
			'label-message' => 'theme-prefs-label',
			'default' => $ctx->msg( 'theme-unsupported-skin' )->text(),
			'section' => 'rendering/skin',
			'hide-if' => $showIf,
		];
	}

	/**
	 * Add theme-<theme name> class to the <body> element to allow per-theme
	 * styling on on-wiki CSS pages, such as MediaWiki:Vector.css.
	 * The class is added only for non-default themes.
	 *
	 * @param OutputPage $out
	 * @param Skin $sk
	 * @param array &$bodyAttrs Existing attributes of the <body> tag as an array
	 * @return void This hook must not abort, it must return no value
	 */
	public function onOutputPageBodyAttributes( $out, $sk, &$bodyAttrs ): void {
		$theme = $this->getTheme( $out );

		if ( $theme !== 'default' ) {
			$bodyAttrs['class'] .= ' theme-' . Sanitizer::escapeClass( $theme );
		}
	}

	/**
	 * Expose the value of $wgDefaultTheme as a JavaScript globals so that site/user
	 * JS can use mw.config.get( 'wgDefaultTheme' ) to read its value.
	 *
	 * @param array &$vars Pre-existing JavaScript global variables
	 * @param string $skin
	 * @param Config $config
	 * @return void This hook must not abort, it must return no value
	 */
	public function onResourceLoaderGetConfigVars( &$vars, $skin, $config ): void {
		$vars['wgDefaultTheme'] = $config->get( 'DefaultTheme' );
	}

	/**
	 * Detect the theme and return its name
	 */
	private function getTheme( OutputPage $context ): string {

		$skinName = $context->getSkin()->getSkinName();
		$fallbackTheme = 'default';
		$defaultTheme = Theme::getDefaultTheme( $skinName, $fallbackTheme );
		$theme = $defaultTheme;

		$disableSelectThemes = Theme::isDisabledSelectThemes( $skinName );
		if ( !$disableSelectThemes ) {
			$availableThemes = Theme::getAvailableThemes( $skinName );
			$themeFromRequest = $context->getRequest()->getRawVal( 'usetheme' );
			$themeFromLookup = $this->userOptionsLookup->getOption( $context->getUser(), 'theme' );

			// Check the following things in this order:
			// 1) per-page usetheme URL parameter
			// 2) user's personal preference/override
			// 3) value of $wgDefaultTheme (set in site configuration)
			if ( in_array( $themeFromRequest, $availableThemes, true ) ) {
				$theme = $themeFromRequest;
			} elseif ( in_array( $themeFromLookup, $availableThemes, true ) ) {
				$theme = $themeFromLookup;
			} elseif ( in_array( $defaultTheme, $availableThemes, true ) ) {
				$theme = $defaultTheme;
			} else {
				$theme = $fallbackTheme;
			}
		}

		// Fall back to 'default' if the skin has no theme with this theme name.
#		$resourceLoader = $context->getOutput()->getResourceLoader();
#		if ( !Theme::skinHasTheme( $skinName, $theme, $resourceLoader ) ) {
#			$theme = 'default';
#		}
		// There is confusion regarding 'default' because the term has two meanings.
		// #1 - It refers to the skin in its "original state" without any additional CSS resources from a theme.
		// #2 - It refers to the theme preselected in LocalSettings.php via $wgDefaultTheme.
		// The situation became more complex with the introduction of the 'DisableSelectThemes' function.
		// The skinHasTheme function now returns false, A) if the skin actually has no themes.
		// B) if theme selection is completely disabled or all themes are on the skip list.
		// If 'DisableSelectThemes' is enabled, the preselected $wgDefaultTheme should be used, not 'default'.

		return $theme;
	}
}
