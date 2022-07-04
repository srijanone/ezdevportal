<?php

namespace Drupal\odpl_product;

/**
 * File for product function usage.
 */
class ProductHelper {

  /**
   * Function for checking multiarray.
   */
  public function isMultiarray($array) {

    $multiarray = array_filter($array, 'is_array');

    if (count($multiarray) > 0) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Function for set navigation default data.
   */
  public function productNavigationDefaultData() {
    $array = [
      ['Detail', 'Details', '?view=docs'],
      ['Use-Case', 'Use Cases', '?view=usecases'],
      ['Blog', 'Blogs', '?view=blogs'],
      ['Faq', 'FAQs', '?view=faqs'],
    ];

    if ($this->isMultiarray($array)) {
      foreach ($array as $key => $value) {
        $nav[$key] = [
          'type' => $value[0],
          'text' => $value[1],
          'path' => $value[2],
        ];
      }
      return $nav;
    }

  }

  /**
   * Function for set navigation class.
   */
  public function getProductNavigationClass($classdata) {
    if (!empty($classdata)) {
      return 'product-' . str_replace(" ", "-", strtolower($classdata)) . '-link';
    }

  }

  /**
   * Function for set trim text length of 30 char.
   */
  public function getTrimText($text) {
    if (!is_null($text)) {
      $remove_tag = strip_tags(trim($text));
      $string = preg_replace("/\s|&nbsp;/", ' ', $remove_tag);
      return substr($string, 0, 30);
    }

  }

}
