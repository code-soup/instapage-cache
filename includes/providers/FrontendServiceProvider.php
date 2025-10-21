<?php
/**
 * Frontend Service Provider.
 *
 * @package CodeSoup\InstapageCache
 */

namespace CodeSoup\InstapageCache\Providers;

use CodeSoup\InstapageCache\Abstracts\AbstractServiceProvider;

/** If this file is called directly, abort. */
defined( 'ABSPATH' ) || die;

/**
 * The FrontendServiceProvider class.
 */
class FrontendServiceProvider extends AbstractServiceProvider {

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		$this->container->singleton( 'frontend', \CodeSoup\InstapageCache\Frontend\Init::class );
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		if ( ! is_admin() ) {
			$this->container->get( 'frontend' );
		}
	}
}
