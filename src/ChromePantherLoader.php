<?php

namespace Cacing69\CqueryPantherLoader;

use Cacing69\Cquery\Loader;
use Cacing69\Cquery\Trait\HasDomCrawlerGetter;
use Symfony\Component\Panther\Client;

class ChromePantherLoader extends Loader {
    use HasDomCrawlerGetter;
    protected $isRemote = true;

    public function __construct(string $content = null, $isRemote = true)
    {
        $this->isRemote = $isRemote;
        $this->uri = $content;
    }

    protected function fetchCrawler()
    {
        // i need to install chromedriver from app manager
        // kill $(lsof -i:9515)
        $this->client = Client::createChromeClient();
        $this->client->request('GET', $this->uri);
        $this->crawler = $this->client->getCrawler();

        if($this->callbackOnContentLoaded) {
            $_callbackOnContentLoaded = $this->callbackOnContentLoaded;
            $this->client = $_callbackOnContentLoaded($this->client, $this->crawler);
            $this->crawler = $this->client->getCrawler();
        }

        $this->callbackClientOnEnd = function ($client) {
           $client->close();
           $client->quit();

            return $client;
        };
    }
}
