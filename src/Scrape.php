<?php

declare(strict_types=1);

/**
 * Scrape Tool init
 *
 * @package App
 * @since 1.0.0
 * @version 1.0.0
 */

namespace App;

use App\Controller\Scraper;

/**
 * And off we go
 */
$scrape = new Scraper();
$scrape->run();
