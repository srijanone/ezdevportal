<?php

namespace Drupal\odpl_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\path_alias\AliasManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\odpl_dashboard\DashboardHelper;

/**
 * Provides a 'Dashboard Navigation' Block.
 *
 * @Block(
 *   id = "dashboard_navigation_block",
 *   admin_label = @Translation("Dashboard Navigation Block"),
 *   category = @Translation("Dashboard Navigation Block"),
 * )
 */
class DashboardNavigationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current path.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $currentPath;

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
   * @var Drupal\odpl_dashboard\GeneralFunctional
   */
  protected $navDashboardData;

  /**
   * DashboardNavigationBlock constructor.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The plugin request stack service.
   * @param \Drupal\Core\Path\AliasManager $alias_manager
   *   The path alias manager.
   * @param Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The Entity Manager.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    RequestStack $request_stack,
    AliasManager $alias_manager,
    EntityTypeManagerInterface $entity_type_manager,
    AccountInterface $account) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentPath = $request_stack;
    $this->aliasManager = $alias_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $account;
    $this->navDashboardData = new DashboardHelper();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack'),
      $container->get('path_alias.manager'),
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    // Get Navigation Default Data.
    $navigation = $this->navDashboardData->dashboardNavigationDefaultData();

    $form['dashboard_sidebar_navigation'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Navigation Item'),
        $this->t('Override Title'),
        $this->t('Path'),
        $this->t('Icon Path'),
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
      $form['dashboard_sidebar_navigation'][$key]['type'] = [
        '#size' => 30,
        '#type' => 'textfield',
        '#default_value' => $value['type'],
      ];
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
      $form['dashboard_sidebar_navigation'][$key]['add_icon_path'] = [
        '#size' => 30,
        '#type' => 'textfield',
        '#default_value' => !empty($config['dashboard_sidebar_navigation'][$key]['add_icon_path']) ?
        $config['dashboard_sidebar_navigation'][$key]['add_icon_path'] : $value['add_icon_path'],
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

      $parameters = \Drupal::request()->query->all();

      foreach ($navigation as $key => $value) {
        $navigation[$key]['class'] = $this->navDashboardData->getNavigationClass($value['type']);

        $navigation[$key]['text'] = !empty($value['text']) ? $value['text'] : $value['type'];

        $navigation[$key]['add_icon_path'] = $value['add_icon_path'];
        // Set active link based on current path.
        if (empty($parameters['view'])) {
          if (strpos($value['path'], 'app') !== FALSE) {
            $navigation[$key]['class'] .= ' active';
          }
        }
        elseif (strpos($value['path'], $parameters['view']) !== FALSE) {
          $navigation[$key]['class'] .= ' active';
        }
      }
    }

    return [
      '#theme' => 'dashboard_navigation',
      '#navigationData' => (!empty($navigation)) ? $navigation : [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
