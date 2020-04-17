<?php namespace Parse\Eloquent\Relations;

class HasMany extends HasOneOrMany
{
	public function getResults()
	{
		return $this->query->get();
	}
}
