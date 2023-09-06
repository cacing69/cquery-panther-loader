<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use Cacing69\CqueryPantherLoader\PantherLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

define("WIKIPEDIA", "https://id.wikipedia.org/wiki/Halaman_Utama");

final class CquerypantherLoaderTest extends TestCase
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

    public function testLoaderPanther()
    {
        $data = new Cquery(WIKIPEDIA);

        $result = $data
            ->useLoader(PantherLoader::class)
            ->from("html")
            ->define(
                "title as title",
            )
            ->get();

        $this->assertSame("Kabupaten Sambas - Wikipedia bahasa Indonesia, ensiklopedia bebas", $result[0]["title"]);
    }
}
