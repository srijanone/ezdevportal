<?php

namespace Drupal\odpl_user\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Odpl User Change password Form.
 *
 * @Block(
 *   id = "odpl_user_change_pwd",
 *   admin_label = @Translation("User Change password Form")
 * )
 */
class OdplUserChangepwd extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The Form bulider.
   *
   * @var \Drupal\Core\Form\FormBuilderInterfac
   */
  protected $formBuilder;

  /**
   * Odpl User Changepwd constructor.
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
    $form = $this->formBuilder->getForm('Drupal\odpl_user\Form\ChangePasswordForm');
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {

    return AccessResult::allowedIf(\Drupal::routeMatch()->getRouteName() == 'entity.user.canonical')
      ->cachePerUser();
  }

}
