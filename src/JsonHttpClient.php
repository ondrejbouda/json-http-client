<?php declare(strict_types = 1);

namespace Bouda\JsonHttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class JsonHttpClient
{

	/**
	 * @var ClientInterface
	 */
	private $httpClient;

	/**
	 * @var ArrayKeysTransformer
	 */
	private $resultArrayKeysTransformer;

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	public function __construct(
		?ClientInterface $httpClient = null,
		?ArrayKeysTransformer $resultArrayKeysTransformer = null,
		?LoggerInterface $logger = null
	)
	{
		$this->httpClient = $httpClient ?? new Client();
		$this->resultArrayKeysTransformer = $resultArrayKeysTransformer ?? new ArrayKeysTransformer();
		$this->logger = $logger ?? new NullLogger();
	}



	/**
	 * @param string $method
	 * @param string $uri
	 * @param mixed[] $options
	 * @return mixed[]
	 * @throws GuzzleException
	 */
	public function request(string $method, string $uri, ?array $options = []): array
	{
		$this->logger->debug(sprintf('json http client getting %s', $uri));

		$response = $this->httpClient->request($method, $uri, $options);

		$result = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);

		$result = $this->resultArrayKeysTransformer->transform($result);

		return $result;
	}

}
