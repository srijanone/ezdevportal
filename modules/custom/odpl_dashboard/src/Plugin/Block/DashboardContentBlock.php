<?php

namespace Drupal\odpl_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Product Content block.
 *
 * @Block(
 *   id = "dashboard_content_block",
 *   admin_label = @Translation("Dashboard Content Block"),
 *   category = @Translation("Dashboard Content Block"),
 * )
 */
class DashboardContentBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;

  }

}
