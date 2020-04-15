<?php namespace Parse\Eloquent\Test\Models;

use Parse\Eloquent\Model;

class Post extends Model
{
	public function categories()
	{
		return $this->belongsToMany(Category::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
