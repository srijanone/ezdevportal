<?php

namespace Drupal\ezdevportal_base\Plugin\Block;

use Drupal\Core\Url;
use Drupal\Core\Block\BlockBase;
use Drupal\path_alias\AliasManager;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Banner Content block.
 *
 * @Block(
 *   id = "banner_content_block",
 *   admin_label = @Translation("Banner Block"),
 *   category = @Translation("Ezdevportal Global"),
 * )
 */
class BannerContentBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
   * File storage for files.
   *
   * @var \Drupal\file\FileStorageInterface
   */
  protected $fileStorage;

  /**
   * The File Url Generator.
   *
   * @var Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * Bannercontent constructor.
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
   * @param Drupal\Core\File\FileUrlGeneratorInterface $file_url_generator
   *   The file url generator.
   */
  public function __construct(
      array $configuration,
      $plugin_id,
      $plugin_definition,
      RequestStack $request_stack,
      AliasManager $alias_manager,
      EntityTypeManagerInterface $entity_type_manager,
      FileUrlGeneratorInterface $file_url_generator) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentPath = $request_stack;
    $this->aliasManager = $alias_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->fileStorage = $entity_type_manager->getStorage('file');
    $this->fileUrlGenerator = $file_url_generator;
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
      $container->get('file_url_generator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $build = [];
    $banner_image = [];

    $current_path = \Drupal::service('path.current')->getPath();
    $array_node = explode('/', $current_path);
    $nid = end($array_node);
    $node = $this->entityTypeManager->getStorage('node')->load($nid);

    if (!empty($config['banner_title'])) {
      $name = $config['banner_title'];
    }
    else {
      if ($node) {
        $name = $node->get('title')->value;
      }
      else {
        $name = '';
      }

    }
    if (!empty($config['banner_text'])) {
      $bannerText = $config['banner_text'];
    }
    else {
      $bannerText = '';
    }

    $build['title'] = [
      '#prefix' => '',
      '#markup' => $name,
      '#suffix' => '',
    ];

    $build['text'] = [
      '#prefix' => '',
      '#markup' => $bannerText,
      '#suffix' => '',
    ];
    $build['banner_height'] = [
      '#prefix' => '',
      '#markup' => !empty($config['banner_height']) ? $config['banner_height'] : '307px',
      '#suffix' => '',
    ];

    if (!empty($banner_image[0])) {
      if ($file = $this->fileStorage->load($banner_image[0])) {
        $uri = Url::fromUri($this->fileUrlGenerator->generateAbsoluteString($file->getFileUri()))->toString();
        $build['bannerimg'] = [
          '#markup' => $uri,
        ];
      }
    }
    else {
      $build['bannerimg'] = [
        '#markup' => '/profiles/contrib/ezdevportal/themes/custom/ezdevportal_theme/images/banner_image.png',
      ];
    }

    return [
      '#theme' => 'banner_content',
      '#bannerdata' => $build,
      '#cache' => [
        'contexts' => ['url'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['banner_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $config['banner_title'] ?? '',
    ];

    $form['banner_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#format' => 'full_html',
      '#default_value' => $config['banner_text'] ?? '',
    ];

    $form['banner_height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Banner height'),
      '#format' => 'full_html',
      '#default_value' => $config['banner_height'] ?? '307px',
    ];

    $form['banner_image'] = [
      '#type' => 'managed_file',
      '#title' => t('Banner Image'),
      '#upload_validators' => [
        'file_validate_extensions' => ['gif png jpg jpeg'],
        'file_validate_size' => [25600000],
      ],
      '#theme' => 'image_widget',
      '#preview_imgage_style' => 'default',
      '#upload_location' => 'public://',
      '#progress_message' => 'One moment while we save your file...',
      '#default_value' => $this->configuration['banner_image'] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

    $banner_image = $form_state->getValue('banner_image');
    if ($banner_image != $this->configuration['banner_image'] && !empty($banner_image[0])) {
      $file = $this->fileStorage->load($banner_image[0]);
      $file->setPermanent()->save();
    }

    $this->configuration['banner_title'] = $form_state->getValue('banner_title');
    $this->configuration['banner_text'] = $form_state->getValue('banner_text');
    $this->configuration['banner_height'] = $form_state->getValue('banner_height');
    $this->configuration['banner_image'] = $form_state->getValue('banner_image');
  }

}
