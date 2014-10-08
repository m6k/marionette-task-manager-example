<?php

namespace Tm;

abstract class DataObject
{

	public function __construct($data)
	{
		foreach ($data as $key => $value) {
			if (!property_exists($this, $key)) {
				throw new \LogicException(
					get_class($this) ." does not have property $key"
				);
			}
			$this->$key = $value;
		}
	}


	public function toArray()
	{
		return get_object_vars($this);
	}

}
