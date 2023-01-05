<?php

namespace Drupal\ezdevportal_product\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\path_alias\AliasManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ezdevportal_product\ProductHelper;

/**
 * Provides a 'Product Navigation' Block.
 *
 * @Block(
 *   id = "product_navigation_block",
 *   admin_label = @Translation("Product Navigation Block"),
 *   category = @Translation("Ezdevportal API Product"),
 * )
 */
class ProductNavigationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The product object.
   *
   * @var mixed
   */
  protected $product;

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
   * The navigation default data.
   *
   * @var Drupal\ezdevportal_product\ProductCommonFunction
   */
  protected $navdData;

  /**
   * ProductNavigationBlock constructor.
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
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    RequestStack $request_stack,
    AliasManager $alias_manager,
    EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentPath = $request_stack;
    $this->aliasManager = $alias_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->navData = new ProductHelper();
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
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $navigation = $this->navData->productNavigationDefaultData();

    $form['product_sidebar_navigation'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Navigation Item'),
        $this->t('Override Title'),
        $this->t('Path'),
        $this->t('SVG Icon'),
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

    $navigation = !empty($config['product_sidebar_navigation']) ? $config['product_sidebar_navigation'] : $navigation;

    foreach ($navigation as $key => $value) {
      $form['product_sidebar_navigation'][$key]['#attributes']['class'][] = 'draggable';
      $form['product_sidebar_navigation'][$key]['type'] = [
        '#size' => 30,
        '#type' => 'textfield',
        '#default_value' => $value['type'],
        '#disabled' => TRUE,
      ];
      $form['product_sidebar_navigation'][$key]['text'] = [
        '#size' => 30,
        '#type' => 'textfield',
        '#default_value' => !empty($config['product_sidebar_navigation'][$key]['text']) ?
        $config['product_sidebar_navigation'][$key]['text'] : $value['text'],
      ];
      $form['product_sidebar_navigation'][$key]['path'] = [
        '#size' => 30,
        '#type' => 'textfield',
        '#default_value' => !empty($config['product_sidebar_navigation'][$key]['path']) ?
        $config['product_sidebar_navigation'][$key]['path'] : $value['path'],
      ];
      $form['product_sidebar_navigation'][$key]['svg_icon'] = [
        '#size' => 30,
        '#type' => 'textarea',
        '#default_value' => !empty($config['product_sidebar_navigation'][$key]['svg_icon']) ?
        $config['product_sidebar_navigation'][$key]['svg_icon'] : '',
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
    $this->configuration['product_sidebar_navigation'] = $values['product_sidebar_navigation'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    if (!empty($this->configuration['product_sidebar_navigation'])) {
      $navigation = $this->configuration['product_sidebar_navigation'];
      $parameters = \Drupal::request()->query->all();
      foreach ($navigation as $key => $value) {

        // Set class-name of navigation link.
        $navigation[$key]['class'] = $this->navData->getProductNavigationClass($value['type']);

        $navigation[$key]['text'] = !empty($value['text']) ? $value['text'] : $value['type'];
        // Set active link based on current path.
        if (empty($parameters['view'])) {
          if (strpos($value['path'], 'docs') !== FALSE) {
            $navigation[$key]['class'] .= ' active';
          }
        }
        elseif (strpos($value['path'], $parameters['view']) !== FALSE) {
          $navigation[$key]['class'] .= ' active';
        }
      }

    }
    return [
      '#theme' => 'product_navigation',
      '#navigationData' => (!empty($navigation)) ? $navigation : [],
    ];
  }

}
