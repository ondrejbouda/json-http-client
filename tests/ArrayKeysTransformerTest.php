<?php declare(strict_types = 1);

namespace Bouda\JsonHttpClientTests;

use Bouda\JsonHttpClient\ArrayKeysTransformer;
use PHPUnit\Framework\TestCase;

class ArrayKeysTransformerTest extends TestCase
{

	public function testTransformWithNoCallbacks(): void
	{
		$transformer = new ArrayKeysTransformer();

		$array = [
			'key' => 'value',
		];

		$this->assertSame($array, $transformer->transform($array));
	}



	public function testTransformWithNullCallback(): void
	{
		$transformer = new ArrayKeysTransformer([
			function ($key) {
				return $key;
			},
		]);

		$array = [
			'key' => 'value',
		];

		$this->assertSame($array, $transformer->transform($array));
	}



	public function testTransformWithConstantCallback(): void
	{
		$transformer = new ArrayKeysTransformer([
			function ($key) {
				return 'constant';
			},
		]);

		$array = [
			'key' => 'value',
		];

		$expectedArray = [
			'constant' => 'value',
		];

		$this->assertSame($expectedArray, $transformer->transform($array));
	}



	public function testTransformWithCallback(): void
	{
		$transformer = new ArrayKeysTransformer([
			function ($key) {
				return $key . '2';
			},
		]);

		$array = [
			'key' => 'value',
		];

		$expectedArray = [
			'key2' => 'value',
		];

		$this->assertSame($expectedArray, $transformer->transform($array));
	}



	public function testTransformWithMoreCallbacks(): void
	{
		$transformer = new ArrayKeysTransformer([
			function ($key) {
				return $key . '1';
			},
			function ($key) {
				return $key . '2';
			},
		]);

		$array = [
			'key' => 'value',
		];

		$expectedArray = [
			'key12' => 'value',
		];

		$this->assertSame($expectedArray, $transformer->transform($array));
	}



	public function testTransformMultidimensionalArray(): void
	{
		$transformer = new ArrayKeysTransformer([
			function ($key) {
				return mb_strtoupper($key);
			},
		]);

		$array = [
			'key1' => 'value1',
			'key2' => [
				'key3' => 'value3',
				'key4' => 'value4',
			],
		];

		$expectedArray = [
			'KEY1' => 'value1',
			'KEY2' => [
				'KEY3' => 'value3',
				'KEY4' => 'value4',
			],
		];

		$this->assertSame($expectedArray, $transformer->transform($array));
	}

}
