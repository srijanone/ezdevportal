<?php

namespace Drupal\ezdevportal_user\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\social_api\Plugin\NetworkManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Social Auth Block for Login.
 *
 * @Block(
 *   id = "ezdevportal_social_auth_login",
 *   admin_label = @Translation("Ezdevportal Social Auth Login Block"),
 *   category = @Translation("Ezdevportal User"),
 * )
 */
class EzdevportalSocialAuthLoginBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The network manager.
   *
   * @var \Drupal\social_api\Plugin\NetworkManager
   */
  private $networkManager;

  /**
   * SocialAuthLoginBlock constructor.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\social_api\Plugin\NetworkManager $network_manager
   *   The Social API network manager.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    $plugin_definition,
    NetworkManager $network_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->networkManager = $network_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.network.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $networks = [];
    foreach ($this->networkManager->getDefinitions() as $definition) {
      $networks[] = $this->networkManager->createInstance($definition['id']);
    }
    return [
      '#theme' => 'ezdevportal_login_with',
      '#social_networks' => $networks,
    ];
  }
}
