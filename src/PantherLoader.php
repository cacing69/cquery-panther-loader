<?php

namespace Cacing69\CqueryPantherLoader;

use Cacing69\Cquery\Loader;
use Cacing69\Cquery\Trait\HasGetWithDomCrawlerMethod;
use Symfony\Component\Panther\Client;

class PantherLoader extends Loader {
    use HasGetWithDomCrawlerMethod;
    protected $isRemote = true;

    public function __construct(string $content = null, $isRemote = true)
    {
        $this->isRemote = $isRemote;
        $this->uri = $content;
    }

    protected function fetchCrawler()
    {
        // i need to install chromedriver from app manager
        $this->client = Client::createChromeClient();
        $this->client->request('GET', $this->uri);
        $this->crawler = $this->client->getCrawler();

        if($this->callbackOnContentLoaded) {
            $_callbackOnContentLoaded = $this->callbackOnContentLoaded;
            $this->client = $_callbackOnContentLoaded($this->client, $this->crawler);
            $this->crawler = $this->client->getCrawler();
        }
    }
}
