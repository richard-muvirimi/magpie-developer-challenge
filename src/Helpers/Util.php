<?php

declare(strict_types=1);

/**
 * Helper file for utility functions
 *
 * @package App
 * @subpackage App\Helper
 * @since 1.0.0
 * @version 1.0.0
 */

namespace App\Helpers;

use Exception;
use TextAnalysis\Classifiers\NaiveBayes;
use TextAnalysis\Tokenizers\TwitterTokenizer;
use Innmind\UrlResolver\UrlResolver;
use Innmind\Url\Url;

/**
 * Data Scraping utility class
 *
 * Contains all the other nifty functions.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class Util
{
    
    /**
     * Determine project root
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return void
     */
    public static function determineRoot():void
    {

        if (!defined("ROOT_PATH")) {
            $root = __DIR__;

            do {
                $root = dirname($root);

                // break if we don't find the file
                if (strrpos($root, DIRECTORY_SEPARATOR, intval(strpos($root, PATH_SEPARATOR))) < 3) {
                    break;
                }
            } while (! file_exists($root . '/index.php'));

        // Path to application root.
            define("ROOT_PATH", $root);
        }
    }

    /**
     * Load trainings
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return NaiveBayes
     */
    public static function loadTrainings():NaiveBayes
    {

        $analysis = naive_bayes();

        $trainings = self::getExtractionTrainings();

        foreach ($trainings as $name => $training) {
            $tokens = implode(",", $training);

            $analysis->train($name, normalize_tokens(tokenize($tokens)));
        }

        return $analysis;
    }

    /**
     * Load trainings from file
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return array
     */
    public static function getExtractionTrainings():array
    {

        $trainings = self::rootPath("training". DIRECTORY_SEPARATOR .  "extraction");

        $paths = scandir($trainings);

        $data = array();
        foreach ($paths as $file) {
            //load json files only
            if (str_ends_with($file, ".json")) {
                $path = $trainings . DIRECTORY_SEPARATOR . $file;

                $training = self::loadJson($path);

                $data[$training["name"]] = $training["training"];
            }
        }

        return $data;
    }

    /**
     * Load json file
     *
     * @param string $path
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return array
     */
    public static function loadJson(string $path):array
    {

        $contents = file_get_contents($path);
        return json_decode($contents, true);
    }

    /**
     * Load scraping environment
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return array
     */
    public static function loadEnvironment():array
    {

        $environment = self::rootPath(".environment");

        if (! file_exists($environment)) {
            throw new Exception("Environment file does not exist.", 2);
        }

        return parse_ini_file($environment) ?: [];
    }

    /**
     * Get path relative to root
     *
     * @param string $path
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return string
     */
    public static function rootPath(string $path):string
    {
        return rtrim(ROOT_PATH, "\\/") . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * Normalize storage text
     *
     * Separates size from unit i.e add space between
     *
     * @param string $text
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return string
     */
    public static function normalizeStorageText(string $text):string
    {
        return preg_replace("/^(\d+)([MGKT]?B)$/i", "$1 $2", $text);
    }

    /**
     * Persist data
     *
     * @param array $data The data to persist.
     * @param array $name Name of file to output.
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return void
     */
    public static function persistData(array $data, string $name = 'output.json'):void
    {

        $data = array_map(fn($product) => $product->toArray(), $data);

        $data = json_encode($data, JSON_PRETTY_PRINT);

        file_put_contents($name, $data);
    }

    /**
     * Format storage for output
     *
     * @param string $storage
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return string|float
     */
    public static function formatStorage(string $storage, string $unit = "")
    {
        if (empty($unit)) {
            $storage = preg_replace("/\s/", "", $storage);
        } else {
            $converter = new ByteConverter();

            preg_match("/(\d+\s?[GMKB])/i", $storage, $match);

            $storage = strtolower($match[1]);

            switch (strtoupper($unit)) {
                case "GB":
                    $storage = $converter->getGBytes($storage);
                    break;
                case "MB":
                    $storage =  $converter->getMBytes($storage);
                    break;
                case "KB":
                    $storage =  $converter->getKBytes($storage);
                    break;
                default:
                    $storage =  $converter->getBytes($storage);
                    break;
            }
        }

        return $storage;
    }

    /**
     * Format price for output
     *
     * @param string $price
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return float
     */
    public static function formatPrice(string $price):float
    {
        return (float) filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Resolve relative image url
     *
     * @param string $url
     * @param string $baseUrl
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return void
     */
    public static function formatImageUrl(string $url, string $baseUrl)
    {
        $resolve = new UrlResolver('https');

        return $resolve(
            Url::of($baseUrl . "/"),
            Url::of($url),
        )->toString();
    }

    /**
     * Format color for out put
     *
     * @param string $color
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return string
     */
    public static function formatColor(string $color):string
    {
        return strtolower($color);
    }

    /**
     * Format availability text
     *
     * @param string $availability
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return string
     */
    public static function formatAvailability(string $availability):string
    {
        return preg_replace("/.*:\s*(.*)/i", "$1", $availability);
    }

    /**
     * Load validation training
     *
     * @param string $name
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return array
     */
    public static function getValidationTraining(string $name):array
    {

        $path = "training". DIRECTORY_SEPARATOR . "validation" . DIRECTORY_SEPARATOR . $name;
        $path = self::rootPath($path);

        return self::loadJson($path);
    }

    /**
     * Determine if an item is in stock
     *
     * Uses a training model to determine if product is in stock
     *
     * @param string $availability
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return bool
     */
    public static function formatIsAvailable(string $availability):bool
    {

        $availability = self::formatAvailability($availability);

        $training = self::getValidationTraining("availability.json");

        $analysis = naive_bayes();

        foreach ($training["validation"] as $train) {
            $analysis->train($train["key"], normalize_tokens($train["training"]));
        }

        $prediction = $analysis->predict(normalize_tokens(tokenize($availability, TwitterTokenizer::class)));

        $probability = max($prediction);
        $validation = array_search($probability, $prediction);

        return $validation == "positive";
    }

    /**
     * Format shipping text
     *
     * @param string|null $text
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return string
     */
    public static function formatShippingText(?string $text):string
    {
        return $text ?? "";
    }

    /**
     * Parse delivery text for delivery date
     *
     * @param string|null $delivery
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return string
     */
    public static function formatDeliveryDate(?string $delivery):string
    {
        $delivery = $delivery ?? "";

        $phrases = rake(tokenize($delivery))->getPhrases();

        $deliveryDate ="";
        foreach ($phrases as $phrase) {
            $date = strtotime($phrase);

            if ($date !== false) {
                $deliveryDate = date("Y-m-d", $date);
                break;
            }
        }

        //Parse individual words
        if (empty($deliveryDate)) {
            foreach (tokenize($delivery) as $phrase) {
                $date = strtotime($phrase);
    
                if ($date !== false) {
                    $deliveryDate = date("Y-m-d", $date);
                    break;
                }
            }
        }

        return $deliveryDate;
    }
}
