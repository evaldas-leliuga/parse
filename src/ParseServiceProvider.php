<?php namespace Parse\Eloquent;

use Parse\ParseClient;
use Parse\Eloquent\Console\ModelMakeCommand;
use Parse\Eloquent\Auth\Providers\UserProvider;
use Parse\Eloquent\Auth\SessionGuard;
use Parse\Eloquent\Auth\Providers\AnyUserProvider;
use Parse\Eloquent\Auth\Providers\FacebookUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Foundation\Application as LaravelApplication;

class ParseServiceProvider extends ServiceProvider
{
	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->setupConfig();

		$this->registerCommands();

		$this->setupParse();
	}

	/**
	 * Setup the config.
	 *
	 * @return void
	 */
	protected function setupConfig()
	{
		$source = realpath(__DIR__ . '/../config/parse.php');

		if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
			$this->publishes([$source => config_path('parse.php')], 'parse');
		} else if ($this->app instanceof LumenApplication) {
			$this->app->configure('parse');
		}

		$this->mergeConfigFrom($source, 'parse');
	}

	protected function registerCommands()
	{
		$this->registerModelMakeCommand();

		$this->commands('command.parse.model.make');
	}

	protected function registerModelMakeCommand()
	{
		$this->app->singleton('command.parse.model.make', function ($app) {
			return new ModelMakeCommand($app['files']);
		});
	}

	/**
	 * Setup parse.
	 *
	 * @return void
	 */
	protected function setupParse()
	{
		$config = $this->app->config->get('parse');

		ParseClient::setStorage(new SessionStorage());
		ParseClient::initialize($config['app_id'], $config['rest_key'], $config['master_key']);
		ParseClient::setServerURL($config['server_url'], $config['mount_path']);

		// Register providers
		Auth::provider('parse', function ($app, array $config) {
			return new UserProvider($config['model']);
		});

		Auth::provider('parse-facebook', function ($app, array $config) {
			return new FacebookUserProvider($config['model']);
		});

		Auth::provider('parse-any', function ($app, array $config) {
			return new AnyUserProvider($config['model']);
		});

		// Register guard
		Auth::extend('session-parse', function ($app, $name, array $config) {
			$guard = new SessionGuard($name, Auth::createUserProvider($config['provider']), $app['session.store']);

			$guard->setCookieJar($this->app['cookie']);
			$guard->setDispatcher($this->app['events']);
			$guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));

			return $guard;
		});
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return string[]
	 */
	public function provides()
	{
		return [
			//
		];
	}
}
