<?php

namespace Drupal\ezdevportal_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\path_alias\AliasManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a Action Content block.
 *
 * @Block(
 *   id = "action_content_block",
 *   admin_label = @Translation("Action Block"),
 *   category = @Translation("Ezdevportal Global"),
 * )
 */
class ActionContentBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The path validator service.
   *
   * @var \Drupal\Core\Path\PathValidatorInterface
   */
  protected $path;

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
   * @param \Drupal\Core\Path\PathValidatorInterface $path
   *   The path validator service.
   */
  public function __construct(
      array $configuration,
      $plugin_id,
      $plugin_definition,
      RequestStack $request_stack,
      AliasManager $alias_manager,
      EntityTypeManagerInterface $entity_type_manager,
      FileUrlGeneratorInterface $file_url_generator,
      PathValidatorInterface $path) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentPath = $request_stack;
    $this->aliasManager = $alias_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->fileStorage = $entity_type_manager->getStorage('file');
    $this->fileUrlGenerator = $file_url_generator;
    $this->path = $path;
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
      $container->get('file_url_generator'),
      $container->get('path.validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = $this->getConfiguration();
    $build = [];
    $num_actions = $this->configuration['num_actions'];
    for ($i = 0; $i < $num_actions; $i++) {
      if (!empty($config[$i]['action_title'])) {
        $name = $config[$i]['action_title'];
      }
      else {

        $name = '';
      }

      if (!empty($config[$i]['action_text'])) {
        $actiontext = $config[$i]['action_text'];
      }
      else {
        $actiontext = '';
      }

      $build[$i]['title'] = [
        '#prefix' => '',
        '#markup' => $name,
        '#suffix' => '',
      ];

      $build[$i]['text'] = [
        '#prefix' => '',
        '#markup' => $actiontext,
        '#suffix' => '',
      ];

      $action_image = $this->configuration[$i]['action_image'];
      if (!empty($action_image[0]) && $file = $this->fileStorage->load($action_image[0])) {
        $uri = Url::fromUri($this->fileUrlGenerator->generateAbsoluteString($file->getFileUri()))->toString();
        $build[$i]['actionimg'] = [
          '#markup' => $uri,
        ];
      }

      if (!empty($config[$i]['action_image_path'])) {
        $action_image_path = $config[$i]['action_image_path'];
      }
      else {

        $action_image_path = '';
      }
      $build[$i]['action_image_path'] = [
        '#prefix' => '',
        '#markup' => $action_image_path,
        '#suffix' => '',
      ];

      if ($this->configuration[$i]['action_link']) {
        $default_uri = $this->path->getUrlIfValid($this->configuration[$i]['action_link']);
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
      $cus_class = '';
      if (isset($this->configuration[$i]['custom_class'])) {
        $cus_class = $this->configuration[$i]['custom_class'];
      }
      $build[$i]['link'] = [
        '#type' => 'link',
        '#title' => $this->configuration[$i]['action_url_text'],
        "#weight" => 1,
        '#url' => $path,
        '#attributes' => [
          'target' => '_blank',
          'class' => 'btn-subscribe ' . $cus_class,
        ],
      ];

      $webform = $this->configuration[$i]['webform'];
      if ($webform == '_none') {
        $build[$i]['webform'] = [];
      }
      else {
        $build[$i]['webform'] = [
          '#type' => 'webform',
          '#webform' => $webform,
          '#prefix' => '<div class="' . $cus_class . '">',
          '#suffix' => '</div>',
        ];
      }
    }
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

    // Gather the number of names in the form already.
    $num_actions = $form_state->get('num_actions') ? $form_state->get('num_actions') : $this->configuration['num_actions'];
    $form_state->set('num_actions', $num_actions);
    // We have to ensure that there is at least one num_actions field.
    if ($num_actions === NULL) {
      $num_actions = 1;
    }

    $form['#tree'] = TRUE;
    $form['actions_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Action Content Block'),
      '#prefix' => '<div id="names-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    for ($i = 0; $i < $num_actions; $i++) {
      $form['actions_fieldset'][$i]['action_title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#default_value' => $this->configuration[$i]['action_title'] ?? '',
      ];
      $form['actions_fieldset'][$i]['action_text'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Description'),
        '#format' => 'full_html',
        '#default_value' => $this->configuration[$i]['action_text'] ?? '',
      ];
      $form['actions_fieldset'][$i]['action_image_path'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Path to custom icon'),
        '#default_value' => $this->configuration[$i]['action_image_path'] ?? '',
      ];
      $form['actions_fieldset'][$i]['action_image'] = [
        '#type' => 'managed_file',
        '#title' => $this->t('Icon'),
        '#upload_validators' => [
          'file_validate_extensions' => ['gif png jpg jpeg'],
          'file_validate_size' => [25600000],
        ],
        '#theme' => 'image_widget',
        '#preview_imgage_style' => 'default',
        '#upload_location' => 'public://',
        '#progress_message' => 'One moment while we save your file...',
        '#default_value' => $this->configuration[$i]['action_image'] ?? '',
      ];
      $form['actions_fieldset'][$i]['action_url_text'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Link Text'),
        '#default_value' => $this->configuration[$i]['action_url_text'] ?? '',
      ];
      $form['actions_fieldset'][$i]['action_link'] = [
        '#title' => $this->t('Link'),
        '#type' => 'textfield',
        '#description' => $this->t('Internal links only .Do not use External URL'),
        '#default_value' => $this->configuration[$i]['action_link'] ?? '',
      ];
      $form['actions_fieldset'][$i]['webform'] = [
        '#title' => $this->t('Form'),
        '#type' => 'select',
        '#options' => $webformtitle,
        '#default_value' => $this->configuration[$i]['webform'] ?? '_none',
      ];
      $form['actions_fieldset'][$i]['custom_class'] = [
        '#title' => $this->t('Custom Class'),
        '#type' => 'textfield',
        '#description' => $this->t('Add any custom class to this block.'),
        '#default_value' => $this->configuration[$i]['custom_class'] ?? '',
      ];
    }

    $form['actions_fieldset']['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions_fieldset']['actions']['add_name'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add item'),
      '#submit' => [[$this, 'addOne']],
      '#ajax' => [
        'callback' => [$this, 'addmoreCallback'],
        'wrapper' => 'names-fieldset-wrapper',
      ],
    ];
    // If there is more than one name, add the remove button.
    if ($num_actions > 1) {
      $form['actions_fieldset']['actions']['remove_name'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove item'),
        '#submit' => [[$this, 'removeCallback']],
        '#ajax' => [
          'callback' => [$this, 'addmoreCallback'],
          'wrapper' => 'names-fieldset-wrapper',
        ],
      ];
    }

    return $form;
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['settings']['actions_fieldset'];
  }

  /**
   * Submit handler for the "add-one" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addOne(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_actions');
    $add_button = $name_field + 1;
    $form_state->set('num_actions', $add_button);
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "remove" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeCallback(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_actions');
    if ($name_field > 1) {
      $remove_button = $name_field - 1;
      $form_state->set('num_actions', $remove_button);
    }
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration = [];
    $num_actions = $form_state->get('num_actions');
    $this->configuration['num_actions'] = $num_actions;
    for ($i = 0; $i < $num_actions; $i++) {
      $value = $form_state->getValue('actions_fieldset') ? $form_state->getValue('actions_fieldset') : '';
      $this->configuration[$i]['action_title'] = $value[$i]['action_title'];
      $this->configuration[$i]['action_text'] = $value[$i]['action_text'];
      $this->configuration[$i]['action_image_path'] = $value[$i]['action_image_path'];
      $this->configuration[$i]['action_image'] = $value[$i]['action_image'];
      $this->configuration[$i]['action_link'] = $value[$i]['action_link'];
      $this->configuration[$i]['action_url_text'] = $value[$i]['action_url_text'];
      $this->configuration[$i]['webform'] = $value[$i]['webform'];
      $this->configuration[$i]['custom_class'] = $value[$i]['custom_class'];
    }
  }

}
