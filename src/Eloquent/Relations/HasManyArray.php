<?php namespace Parse\Eloquent\Relations;

use Parse\Eloquent\Model;

class HasManyArray extends HasMany
{
	public function addConstraints()
	{
		$this->query->containedIn($this->foreignKey, $this->parentObject);
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
		$model->addUnique($this->foreignKey, [$this->parentObject->getParseObject()]);

		$model->save();

		return $model;
	}
}
