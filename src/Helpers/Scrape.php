<?php

declare(strict_types=1);

/**
 * Helper file for scraping data from a url
 *
 * @package App
 * @subpackage App\Helper
 * @since 1.0.0
 * @version 1.0.0
 */

namespace App\Helpers;

use App\Models\Product;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use TextAnalysis\Classifiers\NaiveBayes;
use TextAnalysis\Tokenizers\TwitterTokenizer;
use function _\uniqBy as array_unique_by;

/**
 * Data Scraping helper class
 *
 * Contains all the nifty data extraction functions.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class Scrape
{
    /**
     * Fetch remote contents of given url
     *
     * @param string $url
     *
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return Crawler
     */
    public static function fetchDocument(string $url): Crawler
    {
        $client = new Client();

        $response = $client->get($url);

        return new Crawler($response->getBody()->getContents(), $url);
    }

    /**
     * Get all available page links
     *
     * @param Crawler $crawler
     * @param array $environment
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return array
     */
    public static function pageLinks(Crawler $crawler, array $environment):array
    {

        return $crawler->filter($environment["selector.pages"])->each(function (Crawler $node, $i) {
            $link = $node->attr("href");
            
            $query = parse_url($link, PHP_URL_QUERY);
            parse_str($query, $page);

            return $page["page"];
        });
    }
    
    /**
     * Scrape product raw data using nlp trainings. Rids having to target specific elements
     *
     * @param Crawler $crawler
     * @param array $environment
     * @param NaiveBayes $analysis
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return array
     */
    public static function scrapeProductsRaw(Crawler $crawler, array $environment, NaiveBayes $analysis):array
    {

        $products = $crawler->filter($environment["selector.product"]);

        return $products->each(function (Crawler $node, $i) use ($analysis, $environment) {

            $nodes =  $node->children($environment["selector.product.content"]);

            //Extract text
            $content = $nodes->each(function (Crawler $node, $i) {

                //normalize storage size text
                $text = Util::normalizeStorageText($node->text());

                return $text;
            });

            //Predictions
            $prediction = array_map(function ($text) use ($analysis) {

                $prediction = $analysis->predict(normalize_tokens(tokenize($text, TwitterTokenizer::class)));

                 $probability = max($prediction);
                $match = array_search($probability, $prediction);

                return compact("text", "match", "prediction");
            }, $content);

            //Extras. separated by | and tokens by =
            $selectors = explode("|", $environment["selector.product.extra"]);

            foreach ($selectors as $selector) {
                $match = strtok($selector, "=");
                $selector = strtok("=");

                $nodes = $node->children($selector);

                $nodes = $nodes->each(function (Crawler $node, $i) use ($selector) {

                    //Match [] in selector
                    preg_match("/.*\[(.*?)\]/", $selector, $attr);

                    return $node->attr($attr[1]);
                });

                $prediction[] = array(
                    "text" => $nodes ,
                    "match" => trim($match),
                    "prediction" => []
                );
            }

            // Normalize product data
            $keys = array_column($prediction, "match");
            $prediction = array_combine($keys, $prediction);
            
            return $prediction;
        });
    }

    /**
     * Process product list
     *
     * @param array $products
     * @param array $environment
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return array
     */
    public static function processProducts(array $products, array $environment):array
    {

        $data = array();

        foreach ($products as $product) {
            $title = sprintf("%s %s", $product["title"]["text"], Util::formatStorage($product["storage"]["text"]));
            $price = Util::formatPrice($product["price"]["text"]);
            $imageUrl = Util::formatImageUrl(current($product["image"]["text"]), $environment["app.baseurl"]);
            $capacityMB = Util::formatStorage($product["storage"]["text"], "MB");
            $availabilityText = Util::formatAvailability($product["availability"]["text"] ?? "");
            $isAvailable = Util::formatIsAvailable($product["availability"]["text"] ?? "");
            $shippingText = Util::formatShippingText($product["delivery"]["text"] ?? null);
            $shippingDate = Util::formatDeliveryDate($product["delivery"]["text"] ?? null);

            //Iterate over colors
            foreach ($product["color"]["text"] as $variant) {
                //phpcs:ignore Generic.Files.LineLength.TooLong
                $content = compact("title", "price", "imageUrl", "capacityMB", "availabilityText", "isAvailable", "shippingText", "shippingDate");

                $productItem = new Product($content);

                $productItem->color= Util::formatColor($variant);

                $data[] = $productItem;
            }
        }

        //Dedupe by title, price and color
        $data = array_unique_by($data, function ($product) {
            return json_encode($product->toArray(array("title", "price", "color")));
        });

        return $data;
    }
}
