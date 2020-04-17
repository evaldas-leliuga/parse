<?php namespace Parse\Providers;

use Illuminate\Support\Str;
use Parse\ParseClient;
use Parse\Auth\Providers\UserProvider;
use Parse\Auth\SessionGuard;
use Parse\Auth\Providers\AnyUserProvider;
use Parse\Auth\Providers\FacebookUserProvider;
use Parse\Console\ModelMakeCommand;
use Parse\SessionStorage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

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
        $configFile = realpath(__DIR__ . '/../config/parse.php');

        if ($this->isLumen()) {
            $this->app->configure('parse');
        } else {
            $this->publishes([$configFile => config_path('parse.php')], 'parse');
        }

		$this->mergeConfigFrom($configFile, 'parse');
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
     * @return bool
     */
    private function isLumen(): bool
    {
        return Str::contains($this->app->version(), 'Lumen');
    }
}
