<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use Cacing69\CqueryPantherLoader\PantherLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\Panther\Client;

define("WIKIPEDIA", "https://id.wikipedia.org/wiki/Halaman_Utama");
define("QUOTE_TO_SCRAPE_JS", "http://quotes.toscrape.com/js/");
define("OSU_EDU_SEARH_JOHN_MILLER", "https://www.osu.edu/search/?query=John%20Miller&view=people");

final class PantherLoaderTest extends TestCase
{
    public function testFormSearchOnWikipedia()
    {
        $data = new Cquery(WIKIPEDIA);

        $result = $data
            ->onContentLoaded(function (HttpBrowser $browser, Crawler $crawler) {
                $form = new Form($crawler->filter("#searchform")->getNode(0), WIKIPEDIA);

                $browser->submit($form, [
                    "search" => "sambas",
                ]);
                return $browser;
            })
            ->from("html")
            ->define(
                "title as title",
            )
            ->get();

        $this->assertSame("Kabupaten Sambas - Wikipedia bahasa Indonesia, ensiklopedia bebas", $result[0]["title"]);
    }

    public function testLoaderPantherScrapeQuotesLoadedByJs()
    {
        $data = new Cquery(QUOTE_TO_SCRAPE_JS, PantherLoader::class);

        $result = $data
            ->from(".container")
            ->define(
                ".quote > .text",
            )
            ->get();

        $this->assertCount(10, $result);
    }

    public function testLoaderWithWaitForVisibilityPanther()
    {
        $data = new Cquery(OSU_EDU_SEARH_JOHN_MILLER, PantherLoader::class);

        $result = $data
            ->onContentLoaded(function (Client $client) {
                $client->waitForVisibility(".omc-results-tab-nav_-D5XY");

                return $client;
            })
            ->from(".bux-grid__cell.bux-grid__cell--12 .bux-accordion")
            ->define(
                "h3 > div > div:nth-child(1) as name",
            )
            ->get();

        $this->assertCount(5, $result);
    }
}
