<?php

namespace Drupal\odpl_product\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Product Content block.
 *
 * @Block(
 *   id = "product_content_block",
 *   admin_label = @Translation("Product Content Block"),
 *   category = @Translation("Product Content Block"),
 * )
 */
class ProductContentBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => '',
    ];
  }

}
