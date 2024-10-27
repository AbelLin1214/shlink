<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Core\Action;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Shlinkio\Shlink\Core\Config\Options\RobotsOptions;
use Shlinkio\Shlink\Core\Crawling\CrawlingHelperInterface;

use function sprintf;

use const PHP_EOL;

readonly class RobotsAction implements RequestHandlerInterface, StatusCodeInterface
{
    public function __construct(private CrawlingHelperInterface $crawlingHelper, private RobotsOptions $robotsOptions)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // @phpstan-ignore-next-line The "Response" phpdoc is wrong
        return new Response(self::STATUS_OK, ['Content-type' => 'text/plain'], $this->buildRobots());
    }

    private function buildRobots(): iterable
    {
        yield <<<ROBOTS
        # For more information about the robots.txt standard, see:
        # https://www.robotstxt.org/orig.html


        ROBOTS;

        $userAgents = $this->robotsOptions->hasUserAgents() ? $this->robotsOptions->userAgents : ['*'];
        foreach ($userAgents as $userAgent) {
            yield sprintf('User-agent: %s%s', $userAgent, PHP_EOL);
        }

        if ($this->robotsOptions->allowAllShortUrls) {
            // Disallow rest URLs, but allow all short codes
            yield 'Disallow: /rest/';
            return;
        }

        $shortCodes = $this->crawlingHelper->listCrawlableShortCodes();
        foreach ($shortCodes as $shortCode) {
            yield sprintf('Allow: /%s%s', $shortCode, PHP_EOL);
        }

        yield 'Disallow: /';
    }
}
