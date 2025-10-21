<?php
/**
 * Caching Service Provider.
 *
 * @package CodeSoup\InstapageCache
 */

declare( strict_types=1 );

namespace CodeSoup\InstapageCache\Providers;

use CodeSoup\InstapageCache\Abstracts\AbstractServiceProvider;
use CodeSoup\InstapageCache\Services\CachingService;
use CodeSoup\InstapageCache\Admin\SettingsPage;

/**
 * The caching service provider.
 */
class CachingServiceProvider extends AbstractServiceProvider {

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		$this->container->singleton(
			'caching',
			function () {
				return new CachingService(
					new SettingsPage()
				);
			}
		);
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		$caching = $this->container->get( 'caching' );
		$caching->init();
	}
}

