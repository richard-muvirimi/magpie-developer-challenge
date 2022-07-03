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

use App\Helpers\Util;
use PHPUnit\Framework\TestCase;

/**
 * Utility Test Cases class
 *
 * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since 1.0.0
 * @version 1.0.0
 */
class UtilityTest extends TestCase {


	/**
	 * Test environment and validity of trainings
	 *
	 * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since 1.0.0
	 * @version 1.0.0
	 * 
	 * @return void
	 */
	public function testEnvironment():void {

		$this->assertFileEqualsCanonicalizing(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "index.php", ROOT_PATH . DIRECTORY_SEPARATOR . "index.php");

		// Trainings
		$trainings = Util::getExtractionTrainings();

		// Trainings loaded
		$this->assertArrayHasKey("availability", $trainings);
		$this->assertArrayHasKey("delivery", $trainings);
		$this->assertArrayHasKey("price", $trainings);
		$this->assertArrayHasKey("storage", $trainings);
		$this->assertArrayHasKey("title", $trainings);

		// Trainings not empty
		$this->assertIsArray($trainings["availability"]);
		$this->assertIsArray($trainings["delivery"]);
		$this->assertIsArray($trainings["price"]);
		$this->assertIsArray($trainings["storage"]);
		$this->assertIsArray($trainings["title"]);

		// Validation training
		$trainings = Util::getValidationTraining("availability.json");

		$this->assertArrayHasKey("validation", $trainings);

		$this->assertIsArray($trainings["validation"]);
	}

	/**
	 * Test storage utility functions
	 *
	 * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since 1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function testStorageFunctions():void{

		$size = "10GB";

		$this->assertEquals("10 GB", Util::normalizeStorageText($size));

		// Size
		$this->assertEquals(10000000, Util::formatStorage($size, "KB"));
		$this->assertEquals(10000, Util::formatStorage($size, "MB"));
		$this->assertEquals(10, Util::formatStorage($size, "GB"));

	}

	/**
	 * Test price utility functions
	 *
	 * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since 1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function testPriceFunctions():void{

		// Price
		$this->assertEquals(10, Util::formatPrice("$10"));
		$this->assertEquals(10.99, Util::formatPrice("$10.99"));
	}

	/**
	 * Test resolving relative urls
	 *
	 * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since 1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function testRelativeImageUrl():void{

		$baseUrl = "https://www.magpiehq.com/developer-challenge/smartphones";
		$imageUrl = "../images/lg-k42.png";

		$expected = "https://www.magpiehq.com/developer-challenge/images/lg-k42.png";

		$this->assertEquals($expected, Util::formatImageUrl($imageUrl, $baseUrl));
	}

	/**
	 * Test formatting color
	 *
	 * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since 1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function testformatColor():void{
		$color = "Red";

		$this->assertEquals("red", Util::formatColor($color));
	}

	/**
	 * Test extracting product availability
	 *
	 * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since 1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function testResolveAvailability():void{
		$availability = "Availability: In Stock";

		$this->assertEquals("In Stock", Util::formatAvailability($availability));
		$this->assertTrue(Util::formatIsAvailable($availability));

		$availability = "Availability: Out of Stock";

		$this->assertEquals("Out of Stock", Util::formatAvailability($availability));
		$this->assertFalse(Util::formatIsAvailable($availability));
	}

	/**
	 * Resolve shipping and delivery date
	 *
	 * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since 1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function testResolveShipping():void{

		$shipping = "Order within 6 hours and have it Tuesday 5th Jul 2022";

		$this->assertEquals($shipping, Util::formatShippingText($shipping));
		$this->assertEquals("2022-07-05", Util::formatDeliveryDate($shipping));

		$shipping = "Delivers 2022-07-03";

		$this->assertEquals($shipping, Util::formatShippingText($shipping));
		$this->assertEquals("2022-07-03", Util::formatDeliveryDate($shipping));

	}

}
