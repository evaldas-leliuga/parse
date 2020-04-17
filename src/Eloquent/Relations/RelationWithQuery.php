<?php namespace Parse\Eloquent\Relations;

use Parse\Eloquent\Query;
use Parse\Eloquent\Model;

abstract class RelationWithQuery extends Relation
{
	protected $query;

	/**
	 * @param Model
	 */
	protected $parentObject;

	abstract protected function addConstraints();

	public function __construct(Query $query, Model $parentObject)
	{
		$this->query = $query;
		$this->parentObject = $parentObject;

		$this->addConstraints();
	}

	public function __call($method, $parameters)
	{
		$result = call_user_func_array([$this->query, $method], $parameters);

		if ($result === $this->query) {
			return $this;
		}

		return $result;
	}
}
