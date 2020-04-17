<?php namespace Parse\Auth;

use Illuminate\Auth\Authenticatable;
use Parse\Eloquent\User as BaseUserModel;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends BaseUserModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
	use Authenticatable, CanResetPassword;

	public function getKeyName()
	{
		return 'id';
	}

	public function getKey()
	{
		return $this->id();
	}

	public function __construct($data = null, $useMasterKey = null)
	{
		parent::__construct($data, $useMasterKey);

		$this->rememberTokenName = 'rememberToken';
	}

	/**
	 * Determine if the entity has the given abilities.
	 *
	 * @param iterable|string $abilities
	 * @param array|mixed     $arguments
	 *
	 * @return bool
	 */
	public function can($abilities, $arguments = [])
	{
		return app(GateContract::class)->forUser($this)->check($abilities, $arguments);
	}

	/**
	 * Determine if the entity does not have the given abilities.
	 *
	 * @param iterable|string $abilities
	 * @param array|mixed     $arguments
	 *
	 * @return bool
	 */
	public function cant($abilities, $arguments = [])
	{
		return !$this->can($abilities, $arguments);
	}

	/**
	 * Determine if the entity does not have the given abilities.
	 *
	 * @param iterable|string $abilities
	 * @param array|mixed     $arguments
	 *
	 * @return bool
	 */
	public function cannot($abilities, $arguments = [])
	{
		return $this->cant($abilities, $arguments);
	}
}
