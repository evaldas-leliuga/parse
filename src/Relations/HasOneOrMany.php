<?php namespace Parse\Eloquent\Relations;

use Parse\Eloquent\Query;
use Parse\Eloquent\Model;

abstract class HasOneOrMany extends RelationWithQuery
{
	protected $foreignKey;

	public function __construct(Query $query, Model $parentObject, $foreignKey)
	{
		$this->foreignKey = $foreignKey;

		parent::__construct($query, $parentObject);
	}

	public function addConstraints()
	{
		$this->query->where($this->foreignKey, $this->parentObject);
	}

	/**
	 * Create a new child object, and relate it to this.
	 *
	 * @param array $data
	 *
	 * @return Model
	 */
	public function create(array $data)
	{
		$class = $this->query->getFullClassName();

		$model = new $class($data);

		return $this->save($model);
	}

	/**
	 * Relate other object to this object.
	 *
	 * @param Model $model The child object
	 *
	 * @return Model
	 */
	public function save(Model $model)
	{
		$model->{$this->foreignKey} = $this->parentObject;

		$model->save();

		return $model;
	}
}
