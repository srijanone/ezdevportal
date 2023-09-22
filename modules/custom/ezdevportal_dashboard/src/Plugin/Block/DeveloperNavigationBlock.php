<?php

namespace Drupal\ezdevportal_dashboard\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\ezdevportal_dashboard\DashboardHelper;
use Drupal\path_alias\AliasManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Developer Navigation' Block.
 *
 * @Block(
 *   id = "developer_navigation_block",
 *   admin_label = @Translation("Developer Navigation Block"),
 *   category = @Translation("Ezdevportal User"),
 * )
 */
class DeveloperNavigationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The path alias manager.
   *
   * @var \Drupal\path_alias\AliasManager
   */
  protected $aliasManager;

  /**
   * Object EntityTypeManager.
   *
   * @var Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The navigation default data.
   *
   * @var Drupal\ezdevportal_dashboard\GeneralFunctional
   */
  protected $navData;

  /**
   * ProductNavigationBlock constructor.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Path\AliasManager $alias_manager
   *   The path alias manager.
   * @param Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The Entity Manager.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    AliasManager $alias_manager,
    EntityTypeManagerInterface $entity_type_manager,
  AccountInterface $account,
  RouteMatchInterface $routeMatch
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->aliasManager = $alias_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $account;
    $this->routeMatch = $routeMatch;
    $this->navData = new DashboardHelper();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('path_alias.manager'),
      $container->get('entity_type.manager'),
      $container->get('current_user'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    // Get  Navigation Default Data.
    $navigation = $this->navData->developerNavigationDefaultData();

    $form['dashboard_sidebar_navigation'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Title'),
        $this->t('Path'),
        $this->t('Visibility'),
      ],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'mytable-order-weight',
        ],
      ],
      '#prefix' => '<div id="main-container">',
      '#suffix' => '</div>',
    ];

    $navigation = !empty($config['dashboard_sidebar_navigation']) ? $config['dashboard_sidebar_navigation'] : $navigation;

    foreach ($navigation as $key => $value) {
      $form['dashboard_sidebar_navigation'][$key]['#attributes']['class'][] = 'draggable';

      $form['dashboard_sidebar_navigation'][$key]['text'] = [
        '#size' => 30,
        '#type' => 'textfield',
        '#default_value' => !empty($config['dashboard_sidebar_navigation'][$key]['text']) ?
        $config['dashboard_sidebar_navigation'][$key]['text'] : $value['text'],
      ];
      $form['dashboard_sidebar_navigation'][$key]['path'] = [
        '#size' => 30,
        '#type' => 'textfield',
        '#default_value' => !empty($config['dashboard_sidebar_navigation'][$key]['path']) ?
        $config['dashboard_sidebar_navigation'][$key]['path'] : $value['path'],
      ];
      $visibility = !empty($config['dashboard_sidebar_navigation'][$key]['visibility']) ?
      reset($config['dashboard_sidebar_navigation'][$key]['visibility']) : $value['visibility'];

      $form['dashboard_sidebar_navigation'][$key]['visibility'] = [
        '#type' => 'checkboxes',
        '#options' => [
          'developer' => $this->t('Developer'),
          'siteadmin' => $this->t('Site-admin'),
        ],
        '#default_value' => [$visibility],

      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['dashboard_sidebar_navigation'] = $values['dashboard_sidebar_navigation'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    if (!empty($this->configuration['dashboard_sidebar_navigation'])) {
      $navigation = $this->configuration['dashboard_sidebar_navigation'];

      $role = $this->currentUser->getRoles();
      foreach ($navigation as $key => $value) {
        // Set class-name of navigation link.
        $navigation[$key]['class'] = $this->navData->getNavigationClass($value['text']);

        if (($value['visibility']['developer'] == '0' && in_array('developer', $role)) || ($value['visibility']['siteadmin'] == '0' && in_array('site_admin', $role))) {
          $navigation[$key]['text'] = !empty($value['text']) ? $value['text'] : '';
          unset($navigation[$key]);
        }
      }
    }
    return [
      '#theme' => 'developer_navigation',
      '#navigationData' => (!empty($navigation)) ? $navigation : [],
      '#cache' => [
        'contexts' => ['url'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {

    $route_name = ['user.login', 'user.pass', 'user.register'];

    if (in_array($this->routeMatch->getRouteName(), $route_name)) {
      return AccessResult::forbidden();
    }
    else {
      return AccessResult::allowed();
    }
  }

}
