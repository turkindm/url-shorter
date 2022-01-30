<?php

namespace App\Services\Link;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class WebResource
{
    private Client $httpClient;

    private ?string $content = null;

    public function __construct(private string $url)
    {
        $this->httpClient = new Client();
    }

    public function isAvailable(): bool
    {
        try  {
            $this->tryRetrieveContent();
        } catch (UnreachableResourceException) {
            return false;
        }

        return true;
    }

    /**
     * @throws UnreachableResourceException
     */
    public function assertAvailable(): void
    {
        $this->tryRetrieveContent();
    }

    /**
     * @throws UnreachableResourceException
     */
    public function getTitle(): string
    {
        if ($this->content === null) {
            $this->tryRetrieveContent();
        }

        $matches = [];
        preg_match("/<title>(.*)<\/title>/is", $this->content, $matches);

        return $matches[1] ?? $this->defaultTitle();
    }

    /**
     * @throws UnreachableResourceException
     */
    private function tryRetrieveContent(): void
    {
        try {
            $response = $this->httpClient->get($this->url, ['timeout' => 1]);
        } catch (GuzzleException) {
            throw new UnreachableResourceException();
        }

        $this->content = $response->getBody();
    }

    private function defaultTitle(): string
    {
        return parse_url($this->url)['host'];
    }
}
