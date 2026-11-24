<?php

namespace MediaWiki\Extension\Theme;

use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Hook\OutputPageBodyAttributesHook;
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
	/** @var SkinFactory */
	private $skinFactory;

	/** @var UserOptionsLookup */
	private $userOptionsLookup;

	/**
	 * @param SkinFactory $skinFactory
	 * @param UserOptionsLookup $userOptionsLookup
	 */
	public function __construct(
		$skinFactory,
		$userOptionsLookup
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
		$prefix = $skin !== 'monaco' ? 'themeloader.' : '';
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
	 *
	 * @param OutputPage $out
	 */
	private function addJSonPreferences( $out ): void {
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

		$installedSkins =
			method_exists( $this->skinFactory, 'getInstalledSkins' )
				?	// @since 1.37
					$this->skinFactory->getInstalledSkins()
				:	// @phan-suppress-next-line PhanUndeclaredMethod
					$this->skinFactory->getSkinNames();
		$skinsWithThemes = Theme::getSkinsWithThemes( $installedSkins );
		$showIf = [ 'OR' ];
		foreach ( $skinsWithThemes as $skin_name ) {
			$showIf[] = [ '===', 'skin', $skin_name ];
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
	 *
	 * @param OutputPage $context
	 * @return string
	 */
	private function getTheme( $context ) {

		$skinName = $context->getSkin()->getSkinName();
		$defaultTheme = Theme::getDefaultTheme( $skinName, 'default' );

		// Check the following things in this order:
		// 1) per-page usetheme URL parameter
		// 2) user's personal preference/override
		// 3) value of $wgDefaultTheme (set in site configuration)
		$theme = $context->getRequest()->getRawVal( 'usetheme' )
			?? $this->userOptionsLookup->getOption( $context->getUser(), 'theme' )
			?? $defaultTheme;

		// Support any case of the theme name.
		$theme = strtolower( $theme );

		// Fall back to 'default' if the skin has no theme with this theme name.
		$resourceLoader = $context->getOutput()->getResourceLoader();
		if ( !Theme::skinHasTheme( $skinName, $theme, $resourceLoader ) ) {
			$theme = 'default';
		}

		return $theme;
	}
}
