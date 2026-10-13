<?php

namespace MediaWiki\Extension\Theme;

class Compat {

    public static function init(): void {
        self::aliasCoreClasses();
    }

    private static function aliasCoreClasses(): void {

		if ( class_exists( \ResourceLoader::class ) && /* < 1.39 */
			!class_exists( 'MediaWiki\\ResourceLoader\\ResourceLoader', false ) ) {
			class_alias(
				\ResourceLoader::class,
				'MediaWiki\\ResourceLoader\\ResourceLoader'
			);
		}
		if ( class_exists( \Html::class ) && /* < 1.40 */
			!class_exists( 'MediaWiki\\Html\\Html', false ) ) {
			class_alias(
				\Html::class,
				'MediaWiki\\Html\\Html'
			);
		}
		if ( class_exists( \Linker::class ) && /* < 1.40 */
			!class_exists( 'MediaWiki\\Linker\\Linker', false ) ) {
			class_alias(
				\Linker::class,
				'MediaWiki\\Linker\\Linker'
			);
		}
		if ( class_exists( \Title::class ) && /* < 1.40 */
			!class_exists( 'MediaWiki\\Title\\Title', false ) ) {
			class_alias(
				\Title::class,
				'MediaWiki\\Title\\Title'
			);
		}
		if ( class_exists( \Config::class ) && /* < 1.41 */
			!class_exists( 'MediaWiki\\Config\\Config', false ) ) {
			class_alias(
				\Config::class,
				'MediaWiki\\Config\\Config'
			);
		}
		if ( class_exists( \GlobalVarConfig::class ) && /* < 1.41 */
			!class_exists( 'MediaWiki\\Config\\GlobalVarConfig', false ) ) {
			class_alias(
				\GlobalVarConfig::class,
				'MediaWiki\\Config\\GlobalVarConfig'
			);
		}
		if ( class_exists( \Sanitizer::class ) && /* < 1.41 */
			!class_exists( 'MediaWiki\\Parser\\Sanitizer', false ) ) {
			class_alias(
				\Sanitizer::class,
				'MediaWiki\\Parser\\Sanitizer'
			);
		}
		if ( class_exists( \RequestContext::class ) && /* < 1.42 */
			!class_exists( 'MediaWiki\\Context\\RequestContext', false ) ) {
			class_alias(
				\RequestContext::class,
				'MediaWiki\\Context\\RequestContext'
			);
		}
		if ( class_exists( \BeforePageDisplayHook::class ) && /* < 1.42 */
			!class_exists( 'MediaWiki\\Output\\Hook\\BeforePageDisplayHook', false ) ) {
			class_alias(
				\BeforePageDisplayHook::class,
				'MediaWiki\\Output\\Hook\\BeforePageDisplayHook'
			);
		}
		if ( class_exists( \OutputPageBodyAttributesHook::class ) && /* < 1.42 */
			!class_exists( 'MediaWiki\\Output\\Hook\\OutputPageBodyAttributesHook', false ) ) {
			class_alias(
				\OutputPageBodyAttributesHook::class,
				'MediaWiki\\Output\\Hook\\OutputPageBodyAttributesHook'
			);
		}
		if ( class_exists( \MediaWiki\Hook\BeforePageDisplayHook::class ) && /* < 1.42 */
			!class_exists( 'MediaWiki\\Output\\Hook\\BeforePageDisplayHook', false ) ) {
			class_alias(
				\MediaWiki\Hook\BeforePageDisplayHook::class,
				'MediaWiki\\Output\\Hook\\BeforePageDisplayHook'
			);
		}
		if ( class_exists( \MediaWiki\Hook\OutputPageBodyAttributesHook::class ) && /* < 1.42 */
			!class_exists( 'MediaWiki\\Output\\Hook\\OutputPageBodyAttributesHook', false ) ) {
			class_alias(
				\MediaWiki\Hook\OutputPageBodyAttributesHook::class,
				'MediaWiki\\Output\\Hook\\OutputPageBodyAttributesHook'
			);
		}
		if ( class_exists( \ApiBase::class ) && /* < 1.43 */
			!class_exists( 'MediaWiki\\Api\\ApiBase', false ) ) {
			class_alias(
				\ApiBase::class,
				'MediaWiki\\Api\\ApiBase'
			);
		}
		if ( class_exists( \ApiMain::class ) && /* < 1.43 */
			!class_exists( 'MediaWiki\\Api\\ApiMain', false ) ) {
			class_alias(
				\ApiMain::class,
				'MediaWiki\\Api\\ApiMain'
			);
		}
		if ( class_exists( \ApiResult::class ) && /* < 1.43 */
			!class_exists( 'MediaWiki\\Api\\ApiResult', false ) ) {
			class_alias(
				\ApiResult::class,
				'MediaWiki\\Api\\ApiResult'
			);
		}
		if ( class_exists( \ExtensionRegistry::class ) && /* < 1.43 */
			!class_exists( 'MediaWiki\\Registration\\ExtensionRegistry', false ) ) {
			class_alias(
				\ExtensionRegistry::class,
				'MediaWiki\\Registration\\ExtensionRegistry'
			);
		}
		if ( class_exists( \BaseTemplate::class ) && /* < 1.44 */
			!class_exists( 'MediaWiki\\Skin\\BaseTemplate', false ) ) {
			class_alias(
				\BaseTemplate::class,
				'MediaWiki\\Skin\\BaseTemplate'
			);
		}
		if ( class_exists( \Skin::class ) && /* < 1.44 */
			!class_exists( 'MediaWiki\\Skin\\Skin', false ) ) {
			class_alias(
				\Skin::class,
				'MediaWiki\\Skin\\Skin'
			);
		}
		if ( class_exists( \MediaWiki\User\UserOptionsLookup::class ) && // < 1.46
			!class_exists( 'MediaWiki\\User\\Options\\UserOptionsLookup', false ) ) {
			class_alias(
				\MediaWiki\User\UserOptionsLookup::class,
				'MediaWiki\\User\\Options\\UserOptionsLookup'
			);
		}
	}
}
