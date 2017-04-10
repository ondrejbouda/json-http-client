<?php declare(strict_types = 1);

namespace Bouda\JsonHttpClientTests;

use Bouda\JsonHttpClient\ArrayKeysTransformer;
use Bouda\JsonHttpClient\JsonHttpClient;
use GuzzleHttp\ClientInterface;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;

class JsonHttpClientTest extends TestCase
{

	public function testHappyPath(): void
	{
		/** @var ClientInterface|MockInterface $httpClient */
		$httpClient = Mockery::mock(ClientInterface::class);
		$resultArrayKeysTransformer = Mockery::mock(ArrayKeysTransformer::class);
		$logger = Mockery::mock(LoggerInterface::class);

		$jsonHttpClient = new JsonHttpClient($httpClient, $resultArrayKeysTransformer, $logger);

		$method = 'GET';
		$uri = 'http://some.uri';
		$options = [
			'body' => 'foo',
		];

		$responseStream = Mockery::mock(StreamInterface::class);
		$responseStream->shouldReceive('getContents')->andReturn('{"_element":"value"}');

		$httpResponse = Mockery::mock(ResponseInterface::class);
		$httpResponse->shouldReceive('getBody')->andReturn($responseStream);

		$expectedInterimResult = [
			'_element' => 'value',
		];

		$expectedFinalResult = [
			'element' => 'value',
		];

		// expectations
		$httpClient->shouldReceive('request')
			->once()
			->with($method, $uri, $options)
			->andReturn($httpResponse);
		$resultArrayKeysTransformer->shouldReceive('transform')
			->once()
			->with($expectedInterimResult)
			->andReturn($expectedFinalResult);
		$logger->shouldReceive('debug')
			->once()
			->with('json http client getting http://some.uri');

		$actualResult = $jsonHttpClient->request($method, $uri, $options);

		$this->assertSame($expectedFinalResult, $actualResult);
	}

}
