<?php

namespace Drupal\odpl_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\path_alias\AliasManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;

/**
 * Provides a Action Content block.
 *
 * @Block(
 *   id = "action_content_block",
 *   admin_label = @Translation("Action Content Block"),
 *   category = @Translation("Action Content Block"),
 * )
 */
class ActionContentBlock extends BlockBase implements ContainerFactoryPluginInterface {

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

    if (!empty($config['action_title'])) {
      $name = $config['action_title'];
    }
    else {

      $name = '';
    }

    if (!empty($config['action_text'])) {
      $actiontext = $config['action_text'];
    }
    else {
      $actiontext = '';
    }

    $build['title'] = [
      '#prefix' => '',
      '#markup' => $name,
      '#suffix' => '',
    ];

    $build['text'] = [
      '#prefix' => '',
      '#markup' => $actiontext,
      '#suffix' => '',
    ];

    $action_image = $this->configuration['action_image'];
    if (!empty($action_image[0])) {
      if ($file = $this->fileStorage->load($action_image[0])) {
        $uri = Url::fromUri($this->fileUrlGenerator->generateAbsoluteString($file->getFileUri()))->toString();
        $build['actionimg'] = [
          '#markup' => $uri,
        ];
      }
    }

    if ($this->configuration['action_link']) {
      $default_uri = \Drupal::service('path.validator')->getUrlIfValid($this->configuration['action_link']);
      $url_object = $default_uri ? $default_uri : Url::fromRoute('<front>');
      $route_name = $url_object->getRouteName();

      $route_parameters = $url_object->getrouteParameters();

      if ($route_parameters) {
        $path = Url::fromRoute($route_name, $route_parameters);
      }
      else {
        $path = Url::fromRoute($route_name);
      }

    }
    else {
      $path = Url::fromRoute('<front>');
    }

    $build['link'] = [
      '#type' => 'link',
      '#title' => $this->configuration['action_url_text'],
      "#weight" => 1,
      '#url' => $path,
      '#attributes' => ['target' => '_blank', 'class' => 'btn-subscribe'],
    ];

    $webform = $this->configuration['webform'];
    $build['webform'] = [
      '#type' => 'webform',
      '#webform' => $webform,
    ];

    return $build;

  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $webformtitle = [];
    $webformtitle['_none'] = 'Select';
    $webform = $this->entityTypeManager->getStorage('webform')->loadMultiple(NULL);
    foreach ($webform as $webforms) {
      $webformtitle[$webforms->get('id')] = $webforms->get('title');
    }

    $form['action_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->configuration['action_title'] ?? '',
    ];

    $form['action_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#format' => 'full_html',
      '#default_value' => $this->configuration['action_text'] ?? '',
    ];

    $form['action_image'] = [
      '#type' => 'managed_file',
      '#title' => t('Icon'),
      '#upload_validators' => [
        'file_validate_extensions' => ['gif png jpg jpeg'],
        'file_validate_size' => [25600000],
      ],
      '#theme' => 'image_widget',
      '#preview_imgage_style' => 'default',
      '#upload_location' => 'public://',
      '#progress_message' => 'One moment while we save your file...',
      '#default_value' => $this->configuration['action_image'] ?? '',
    ];

    $form['action_url_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link Text'),
      '#default_value' => $this->configuration['action_url_text'] ?? '',

    ];

    $form['action_link'] = [
      '#title' => t('Link'),
      '#type' => 'textfield',
      'description' => 'Internal links only .Do not use External URL',
      '#default_value' => $this->configuration['action_link'] ?? '',

    ];

    $form['webform'] = [
      '#title' => t('Form'),
      '#type' => 'select',
      '#options' => $webformtitle,
      '#default_value' => $this->configuration['webform'] ?? '_none',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

    $action_image = $form_state->getValue('action_image');

    if ($action_image != $this->configuration['action_image']) {

      if (!empty($action_image[0])) {
        $file = $this->fileStorage->load($action_image[0]);
        $file->setPermanent()->save();
      }

    }

    $this->configuration['action_title'] = $form_state->getValue('action_title');
    $this->configuration['action_text'] = $form_state->getValue('action_text');
    $this->configuration['action_image'] = $form_state->getValue('action_image');
    $this->configuration['action_link'] = $form_state->getValue('action_link');
    $this->configuration['action_url_text'] = $form_state->getValue('action_url_text');
    $this->configuration['webform'] = $form_state->getValue('webform');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;

  }

}
