<?php
/**
 * Admin Service Provider.
 *
 * @package CodeSoup\InstapageCache
 */

namespace CodeSoup\InstapageCache\Providers;

use CodeSoup\InstapageCache\Abstracts\AbstractServiceProvider;
use CodeSoup\InstapageCache\Admin\Init as AdminInit;

/**
 * The admin service provider.
 */
class AdminServiceProvider extends AbstractServiceProvider {

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		$this->container->singleton( 'admin', \CodeSoup\InstapageCache\Admin\Init::class );
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		if ( is_admin() ) {
			$this->container->get( 'admin' );
		}
	}
}
