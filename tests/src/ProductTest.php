<?php 

declare(strict_types=1);

/**
 * File for php unit test cases
 *
 * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @package App
 * @subpackage App\Tests
 * @since 1.0.0
 * @version 1.0.0
 */

namespace App\Tests;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

/**
 * Product Test Cases class
 *
 * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since 1.0.0
 * @version 1.0.0
 */
class ProductTest extends TestCase {

    /**
     * Test the product class magic functions
     *
     * @return void
     */
    public function testProduct(): void
    {

        $data = [
            'id' => 0,
        ];

        #Test product magic methods
        $product = new Product($data);

        #assert can be initialized from array
        self::assertSame(0, $product->id);

        $product->id = 7;
        $product->title = 'Samsung Galaxy Flip 128GB';
        $product->setAvailability('Availability: Out of Stock');

        #assert title getters
        self::assertSame('Samsung Galaxy Flip 128GB', $product->title);
        self::assertSame('Samsung Galaxy Flip 128GB', $product->getTitle());

        #assert availability getters
        self::assertSame('Availability: Out of Stock', $product->availability);
        self::assertSame('Availability: Out of Stock', $product->getAvailability());

        #assert data can be modified
        self::assertSame(7, $product->id);

        #assert can be reduced to specified fields
        self::assertCount(1, $product->toArray([ 'title' ]));
    }

}