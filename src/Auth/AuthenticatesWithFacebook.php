<?php namespace Parse\Eloquent\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * This trait can be used along Laravel's default Auth controller to manage
 * Parse authentication. This trait assumes the implementing class is also
 * implementing \Illuminate\Foundation\Auth\AuthenticatesUsers.
 */
trait AuthenticatesWithFacebook
{
	protected $apiResponse = ['ok' => true];

	public function logInOrRegisterWithFacebookApi(Request $request)
	{
		$this->logInOrRegisterWithFacebook($request);

		return response()->json($this->apiResponse);
	}

	public function logInOrRegisterWithFacebookRedirect(Request $request)
	{
		$this->logInOrRegisterWithFacebook($request);

		return redirect($this->redirectPath());
	}

	/**
	 * Accepts both username|email/password and Facebook registration requests.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function registerAny(Request $request)
	{
		if ($request->id && $request->auth_token) {
			return $this->registerWithFacebook($request);
		} else {
			return $this->register($request);
		}
	}

	/**
	 * Registers a new user with Facebook, but the user isn't logged in to Laravel.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function registerWithFacebookRedirect(Request $request)
	{
		$this->logInWithFacebook($request);

		return redirect($this->redirectPath());
	}

	/**
	 * Registers a new user with Facebook, but the user isn't logged in to Laravel.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function registerWithFacebookApi(Request $request)
	{
		$this->logInWithFacebook($request);

		return response()->json($this->apiResponse);
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function logoutApi(Request $request)
	{
		$this->logout($request);

		return response()->json($this->apiResponse);
	}

	/**
	 * Registers a new user and/or logs the user in to Laravel.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return void
	 */
	protected function logInOrRegisterWithFacebook(Request $request)
	{
		$user = $this->logInWithFacebook($request);

		if (method_exists($this, 'getGuard')) {
			// Laravel 5.2
			Auth::guard($this->getGuard())->login($user, $request->has('remember'));
		} else {
			// Laravel 5.3+
			$this->guard()->login($user, $request->has('remember'));
		}
	}

	/**
	 * Registers a new user or log in into Parse if the user exists.
	 * Returns null if an error occured.
	 *
	 * @param Request $request
	 *
	 * @return \Parse\Eloquent\UserModel|null
	 */
	protected function logInWithFacebook(Request $request)
	{
		$class = config('auth.providers.users.model');

		return $class::logInWithFacebook($request->id, $request->access_token);
	}
}
