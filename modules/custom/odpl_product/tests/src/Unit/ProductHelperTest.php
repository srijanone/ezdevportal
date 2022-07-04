<?php

namespace Drupal\Tests\odpl_product\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\odpl_product\ProductHelper;

/**
 * Simple test to ensure that asserts pass.
 *
 * @group odpl_product
 */
class ProductHelperTest extends UnitTestCase {

  /**
   * The product variable.
   *
   * @var Drupal\odpl_product\ProductHelper
   */

  protected $product;

  /**
   * Create new ProductCommonFunction object.
   */
  public function setUp() {
    $this->product = new ProductHelper();
  }

  /**
   * Tests Navigation Default Data in array.
   */
  public function testProductNavigationDefaultData() {
    $expected_value = [
    ['type' => 'Detail', 'text' => 'Details', 'path' => '?view=docs'],
    ['type' => 'Use-Case', 'text' => 'Use Cases', 'path' => '?view=usecases'],
    ['type' => 'Blog', 'text' => 'Blogs', 'path' => '?view=blogs'],
    ['type' => 'Faq', 'text' => 'FAQs', 'path' => '?view=faqs'],
    ];

    $actual_value = $this->product->productNavigationDefaultData();

    $this->assertSame($actual_value, $expected_value);

  }

  /**
   * Tests navigation class.
   */
  public function testgetProductNavigationClass() {

    $expected_value = 'product-dom-master-link';

    $actual_value = $this->product->getProductNavigationClass('Dom master');

    $this->assertSame($actual_value, $expected_value);

  }

  /**
   * Tests trim text.
   */
  public function testgetTrimText() {

    $expected_value = 'Lorem Ipsum is simply dummy te';

    $actual_value = $this->product->getTrimText("Lorem Ipsum is simply dummy text 
    of the printing and typesetting industry. Lorem Ipsum has been the 
    industry's standard dummy text ever since the 1500s, 
    when an unknown printer took a galley of type and scrambled 
    it to make a type specimen book");

    $this->assertSame($actual_value, $expected_value);

  }

}
