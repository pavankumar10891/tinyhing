<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);

        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       // \URL::forceScheme('https');
        Validator::extend("custom_password", function($attribute, $value, $parameters) {
			if (preg_match("#[0-9]#", $value) && preg_match("#[a-zA-Z]#", $value)) {
				return true;
			} else {
				return false;
			}
		});
    }
}
