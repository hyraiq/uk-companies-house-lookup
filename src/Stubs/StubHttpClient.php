<?php

declare(strict_types=1);

namespace Hyra\UkCompaniesHouseLookup\Stubs;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class StubHttpClient implements HttpClientInterface
{
    /** @var null|mixed[] */
    private ?array $defaultOptions = null;

    private ?MockResponse $stubResponse = null;

    /** @var null|mixed[] */
    private ?array $companyDetailsOptions = null;

    /**
     * @psalm-suppress RedundantIdentityWithTrue
     *
     * @param mixed[] $options
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        if ('GET' !== $method) {
            throw new \LogicException('Not implemented: UK Companies House API client should only use GET requests');
        }

        return match (true) {
            (bool) \preg_match('/^\/company\/(?:\d{8}|[A-Za-z]{2}\d{6})+$/i', $url) => $this->handleCompanyRequest($url, $options),
            default                                                                 => throw new \LogicException(
                'Not implemented: UK Companies House API client only responds to "/company/{companyNumber}"'
            )
        };
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        throw new \LogicException('Not implemented: UK Companies House API client should only be using the request method');
    }

    /**
     * @param mixed[] $options
     */
    public function withOptions(array $options): static
    {
        $this->defaultOptions = $options;

        return $this;
    }

    /**
     * @param mixed[] $data
     */
    public function setStubResponse(array $data, int $statusCode = 200): void
    {
        try {
            $responseContent = \json_encode($data, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \LogicException(\sprintf('Unable to json_encode $data: %s', $e->getMessage()), 0, $e);
        }

        $this->stubResponse = new MockResponse(
            $responseContent,
            ['http_code' => $statusCode]
        );
    }

    /**
     * @param mixed[] $expectedOptions
     */
    public function assertDefaultOptions(array $expectedOptions): void
    {
        TestCase::assertSame($expectedOptions, $this->defaultOptions);
    }

    /**
     * @param mixed[] $expectedOptions
     */
    public function assertCompanyEndpointCalled(array $expectedOptions): void
    {
        TestCase::assertSame($expectedOptions, $this->companyDetailsOptions);
    }

    public function assertCompanyEndpointNotCalled(): void
    {
        if (null !== $this->companyDetailsOptions) {
            TestCase::fail('Company endpoint should not have been called');
        }
    }

    /**
     * @param mixed[] $options
     */
    private function handleCompanyRequest(string $url, array $options): ResponseInterface
    {
        if (null !== $this->companyDetailsOptions) {
            throw new \LogicException('Not implemented: the ApiClient should only be called once');
        }

        $this->companyDetailsOptions = $options;

        // @phpstan-ignore-next-line - no, a mock http client will not throw TransportException
        return $this->getMockHttpClient()->request('GET', $url, $options);
    }

    private function getMockHttpClient(): MockHttpClient
    {
        if (null === $this->stubResponse) {
            throw new \LogicException('You must set the stub response before calling the ApiClient');
        }

        return new MockHttpClient($this->stubResponse, 'https://api.company-information.service.gov.uk/');
    }
}
