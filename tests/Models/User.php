<?php namespace Parse\Eloquent\Test\Models;

use Parse\Eloquent\Model;

class User extends Model
{
	public function posts()
	{
		return $this->hasMany(Post::class);
	}
}
