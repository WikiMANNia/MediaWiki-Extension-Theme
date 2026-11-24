<?php

namespace MediaWiki\Extension\Theme;

use MediaWiki\Api\ApiBase;
use MediaWiki\Api\ApiMain;
use MediaWiki\Api\ApiResult;
use SkinFactory;

/**
 * API module for listing skins' themes.
 * Lazy shim until https://gerrit.wikimedia.org/r/465451/ is merged into core,
 * at which point this file can go away and the JS can go back to using
 * api.php?action=query&meta=siteinfo&siprop=themes
 *
 * @file
 * @ingroup API
 */
class ApiThemeList extends ApiBase {

	/** @var SkinFactory */
	private $skinFactory;

	/**
	 * @param ApiMain $main
	 * @param string $action
	 * @param SkinFactory $skinFactory
	 */
	public function __construct(
		$main,
		$action,
		$skinFactory
	) {
		parent::__construct( $main, $action );
		$this->skinFactory = $skinFactory;
	}

	/**
	 * Main entry point.
	 */
	public function execute() {
		$this->appendThemes();
	}

	/**
	 * @param string $property
	 * @return bool
	 */
	private function appendThemes( $property = 'themes' ) {
		$data = [];
		$config = $this->getConfig();
		$defaultSkin = $config->get( 'DefaultSkin' );
		$skinNames =
			method_exists( $this->skinFactory, 'getInstalledSkins' )
				?	// @since 1.37
					$this->skinFactory->getInstalledSkins()
				:	// @phan-suppress-next-line PhanUndeclaredMethod
					$this->skinFactory->getSkinNames();

		foreach ( $skinNames as $skinName => $displayName ) {
			$themes = Theme::getAvailableThemes( $skinName );
			$defaultTheme = Theme::getDefaultTheme( $skinName, 'default' );
			// When this stuff is in core, use:
			// $themes = Skin::getAvailableThemes( $skinName );
			foreach ( $themes as $idx => $themeKey ) {
				$msg_key = ( $themeKey === 'default' )
					?	"theme-name-default"
					:	"theme-name-{$skinName}-{$themeKey}";
				$themeDisplayNameMsg = $this->msg( $msg_key );
				$themeDisplayNameMsg->inLanguage( $this->getLanguage() );
				$themeDisplayName = $themeDisplayNameMsg->isDisabled()
					?	ucfirst( $themeKey )
					:	$themeDisplayNameMsg->text();
				$theme = [ 'code' => $themeKey ];
				ApiResult::setContentValue( $theme, 'name', $themeDisplayName );
				if ( ( $themeKey === $defaultTheme ) && ( $skinName === $defaultSkin ) ) {
					$theme['default'] = true;
				}
				$data[$skinName][] = $theme;
			}
		}
		ApiResult::setIndexedTagName( $data, 'theme' );

		return $this->getResult()->addValue( 'query', $property, $data );
	}

	/**
	 * @see ApiBase#getAllowedParams()
	 * @return array
	 */
	public function getAllowedParams() {
		return [];
	}

	/**
	 * @see ApiBase#getExamplesMessages()
	 * @return array
	 */
	protected function getExamplesMessages() {
		return [
			'action=themelist' => 'apihelp-themelist-example-1'
		];
	}
}
