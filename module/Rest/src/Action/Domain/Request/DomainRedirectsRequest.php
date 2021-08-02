<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Rest\Action\Domain\Request;

use Shlinkio\Shlink\Core\Config\NotFoundRedirectConfigInterface;
use Shlinkio\Shlink\Core\Config\NotFoundRedirects;

use function array_key_exists;

class DomainRedirectsRequest
{
    private string $authority;
    private ?string $baseUrlRedirect = null;
    private bool $baseUrlRedirectWasProvided = false;
    private ?string $regular404Redirect = null;
    private bool $regular404RedirectWasProvided = false;
    private ?string $invalidShortUrlRedirect = null;
    private bool $invalidShortUrlRedirectWasProvided = false;

    private function __construct()
    {
    }

    public static function fromRawData(array $payload): self
    {
        $instance = new self();
        $instance->validateAndInit($payload);
        return $instance;
    }

    private function validateAndInit(array $payload): void
    {
        // TODO Validate data
        $this->baseUrlRedirectWasProvided = array_key_exists('baseUrlRedirect', $payload);
        $this->regular404RedirectWasProvided = array_key_exists('regular404Redirect', $payload);
        $this->invalidShortUrlRedirectWasProvided = array_key_exists('invalidShortUrlRedirect', $payload);

        $this->authority = $payload['domain'];
        $this->baseUrlRedirect = $payload['baseUrlRedirect'] ?? null;
        $this->regular404Redirect = $payload['regular404Redirect'] ?? null;
        $this->invalidShortUrlRedirect = $payload['invalidShortUrlRedirect'] ?? null;
    }

    public function authority(): string
    {
        return $this->authority;
    }

    public function toNotFoundRedirects(?NotFoundRedirectConfigInterface $defaults = null): NotFoundRedirects
    {
        return new NotFoundRedirects(
            $this->baseUrlRedirectWasProvided ? $this->baseUrlRedirect : $defaults?->baseUrlRedirect(),
            $this->regular404RedirectWasProvided ? $this->regular404Redirect : $defaults?->regular404Redirect(),
            $this->invalidShortUrlRedirectWasProvided
                ? $this->invalidShortUrlRedirect
                : $defaults?->invalidShortUrlRedirect(),
        );
    }
}
