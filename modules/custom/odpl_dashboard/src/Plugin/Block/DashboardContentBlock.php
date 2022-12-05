<?php

namespace Drupal\odpl_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Product Content block.
 *
 * @Block(
 *   id = "dashboard_content_block",
 *   admin_label = @Translation("Developer Dashboard Content Block"),
 *   category = @Translation("Ezdevportal User"),
 * )
 */
class DashboardContentBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => '',
      '#cache' => [
        'contexts' => ['url'],
      ],
    ];
  }

}
