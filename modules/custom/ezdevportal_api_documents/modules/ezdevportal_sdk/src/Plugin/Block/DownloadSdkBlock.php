<?php

namespace Drupal\ezdevportal_sdk\Plugin\Block;

use Drupal\Core\Url;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Component\Serialization\Json;
use Drupal\ezdevportal_api_documents\ApiDocumentHelper;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Download SDK' Block.
 *
 * @Block(
 *   id = "download_sdk",
 *   admin_label = @Translation("Download SDK Block"),
 *   category = @Translation("Ezdevportal API Document"),
 * )
 */
class DownloadSdkBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * Current path instance.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPath;

  /**
   * Api Document Helper variable.
   *
   * @var Drupal\ezdevportal_api_documents\ApiDocumentHelper
   */
  protected $apiDocumentHelper;

  /**
   * Constructs a new DownloadSdkBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *   The logger service.
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *   The current path service.
   */
  public function __construct(array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    CurrentPathStack $current_path) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->currentPath = $current_path;
    $this->apiDocumentHelper = new ApiDocumentHelper();

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('path.current')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $nid = $this->apiDocumentHelper->getNodeId();
    $build = [];
    if (!empty($nid)) {
      $build['download_sdk'] = [
        '#type' => 'link',
        '#title' => $this->t('Download SDK'),
        '#url' => Url::fromRoute('ezdevportal_sdk.form', ['node' => $nid]),
        '#attributes' => [
          'class' => [
            'use-ajax',
            'button',
          ],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => Json::encode([
            'width' => 700,
          ]),
        ],
      ];
    }

    // Attach the library for pop-up dialogs/modals.
    $bulid['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $bulid['#attached']['library'][] = 'core/drupal.ajax';
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), ['node:' . $this->apiDocumentHelper->getNodeId()]);
  }

}
