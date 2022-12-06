<?php

namespace Drupal\ezdevportal_user\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Ezdevportal User Change password Form.
 *
 * @Block(
 *   id = "ezdevportal_user_change_pwd",
 *   admin_label = @Translation("Ezdevportal User Password Change Block"),
 *   category = @Translation("Ezdevportal User"),
 * )
 */
class EzdevportalUserChangepwd extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The Form bulider.
   *
   * @var \Drupal\Core\Form\FormBuilderInterfac
   */
  protected $formBuilder;

  /**
   * Ezdevportal User Changepwd constructor.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Form\FormBuilderInterface $formBuilder
   *   The plugin implementation formBuilder.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FormBuilderInterface $formBuilder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->formBuilder = $formBuilder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return $this->formBuilder->getForm('Drupal\ezdevportal_user\Form\ChangePasswordForm');
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {

    return AccessResult::allowedIf(\Drupal::routeMatch()->getRouteName() == 'entity.user.canonical')
      ->cachePerUser();
  }

}
