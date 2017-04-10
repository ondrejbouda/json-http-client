<?php declare(strict_types = 1);

namespace Bouda\JsonHttpClient;

class ArrayKeysTransformer
{

	/**
	 * @var \Closure[]
	 */
	private $transformationCallbacks = [];

	/**
	 * @param \Closure[] $transformationCallbacks
	 */
	public function __construct(array $transformationCallbacks = [])
	{
		$this->transformationCallbacks = $transformationCallbacks;
	}

	/**
	 * @param mixed[] $array
	 * @return mixed[]
	 */
	public function transform(array $array): array
	{
		if (empty($this->transformationCallbacks)) {
			return $array;
		}

		$result = [];

		foreach ($array as $key => $value) {

			foreach ($this->transformationCallbacks as $callback) {
				$key = $callback($key);
			}

			$result[$key] = is_array($value)
				? $this->transform($value)
				: $value;
		}

		return $result;
	}

}
