<?php namespace Parse\Eloquent\Relations;

class HasOne extends HasOneOrMany
{
	public function getResults()
	{
		return $this->query->first();
	}
}
