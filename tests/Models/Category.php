<?php namespace Parse\Eloquent\Test\Models;

use Parse\Eloquent\Model;

class Category extends Model
{
	public function posts()
	{
		return $this->hasManyArray(Post::class);
	}
}
