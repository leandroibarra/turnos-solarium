<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @param UrlGenerator $url
	 * @return void
	 */
	public function boot(UrlGenerator $url)
	{
		if (\App::environment() == 'production')
			$url->forceScheme('https');
	}
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
