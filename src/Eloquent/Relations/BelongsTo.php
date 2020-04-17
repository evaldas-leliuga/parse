<?php namespace Parse\Eloquent\Relations;

use Parse\Eloquent\Model;

class BelongsTo extends Relation
{
	protected $embeddedClass;

	protected $keyName;

	protected $childObject;

	public function __construct($embeddedClass, $keyName, Model $childObject)
	{
		$this->embeddedClass = $embeddedClass;
		$this->childObject = $childObject;
		$this->keyName = $keyName;
	}

	public function getResults()
	{
		$class = $this->embeddedClass;

		$parent = $this->childObject->getParseObject()->get($this->keyName);

		if ($parent) {
			return (new $class($parent))->fetch();
		} else {
			return null;
		}
	}
}
