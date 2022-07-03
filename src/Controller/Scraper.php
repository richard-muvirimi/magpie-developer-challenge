<?php

declare(strict_types=1);

/**
 * Scraper controlling file.
 *
 * @package App
 * @subpackage App\Controller
 * @since 1.0.0
 * @version 1.0.0
 */

namespace App\Controller;

use App\Helpers\Scrape as HelpersScrape;
use App\Helpers\Util;
use TextAnalysis\Classifiers\NaiveBayes;

/**
 * Scraper controller class
 *
 * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since 1.0.0
 * @version 1.0.0
 */
class Scraper
{
    /**
     * Products list
     *
     * @var array
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     */
    private array $products = [];

    /**
     * Environment map, parsed from .environment
     *
     * @var array
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     */
    private array $environment = [];

    /**
     * Text analysis
     *
     * @var NaiveBayes
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     */
    private NaiveBayes $analysis;

    /**
     * Run scraper
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return void
     */
    public function run(): void
    {

        Util::determineRoot();

        $this->environment = Util::loadEnvironment();
        $this->analysis = Util::loadTrainings();
        
        $this->init();
    }

    /**
     * Initialize scraper, scrape and persist
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return void
     */
    private function init():void
    {
        $document = HelpersScrape::fetchDocument($this->environment["app.baseurl"]);

        $pages = HelpersScrape::pageLinks($document, $this->environment);

        foreach ($pages as $pageId) {
            $data = array(
                "page" => $pageId
            );

            $url = $this->environment["app.baseurl"] . "?" . http_build_query($data);

            $page = HelpersScrape::fetchDocument($url);

            $productItem = HelpersScrape::scrapeProductsRaw($page, $this->environment, $this->analysis);

            array_push($this->products, ...$productItem);
        }

        $this->products = HelpersScrape::processProducts($this->products, $this->environment);

        Util::persistData($this->products);
    }
}
