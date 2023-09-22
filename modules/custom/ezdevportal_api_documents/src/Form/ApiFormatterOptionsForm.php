<?php

namespace Drupal\ezdevportal_api_documents\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\path_alias\AliasManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Form to provide option for the UI formatter for API.
 */
class ApiFormatterOptionsForm extends FormBase {

  /**
   * Current path variable.
   *
   * @var mixed
   */
  protected $currentPath;

  /**
   * Path alias service variable.
   *
   * @var mixed
   */
  protected $aliasManager;

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'api_formatter_option_form';
  }

  /**
   * ProductBannerBlock constructor.
   *
   * @param Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The plugin request stack service.
   * @param Drupal\path_alias\AliasManager $path_alias
   *   The plugin path alias service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(RequestStack $request_stack, AliasManager $path_alias, RouteMatchInterface $routeMatch, EntityTypeManagerInterface $entity_type_manager) {
    $this->currentPath = $request_stack;
    $this->aliasManager = $path_alias;
    $this->routeMatch = $routeMatch;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('path_alias.manager'),
      $container->get('current_route_match'),
      $container->get('entity_type.manager')

    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $current_path = $this->currentPath->getCurrentRequest();
    $this->parent = $this->currentPath->getCurrentRequest()->get('parent');
    $url_alias = $this->aliasManager->getPathByAlias($current_path->getPathInfo());
    $this->path_info = explode('/', $url_alias);
    $formatter_option = $current_path->get('view');
    $options = [];

    // Get node based on whether revision feature is enabled.
    if (empty($this->path_info[4])) {
      $node = $this->routeMatch->getParameter('node');
    }
    else {
      $node = $this->entityTypeManager->getStorage('node')->loadRevision($this->path_info[4]);
    }

    if ($node instanceof NodeInterface &&
      $node->getType() == "api_document") {
      $options = $node->get('field_format')->getValue();
      $allowed_values = $node->getFieldDefinition('field_format')->getFieldStorageDefinition()->getSetting('allowed_values');
      $options = $options ? array_column($options, 'value') : [];
      $options = array_combine($options, $options) ?: [];
      $options = array_intersect_key($allowed_values, $options);
    }

    $form['formatter_options'] = [
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => (!empty($formatter_option) ? $formatter_option : 'swagger_ui_file'),
      '#ajax' => [
        'callback' => [$this, 'apiFormatSwitcherHandler'],
        'event' => 'change',
      ],
    ];

    return $form;
  }

  /**
   * The callback function for when the `formatter_options` element is changed.
   *
   * The display formatter for the API is changed as per selection.
   */
  public function apiFormatSwitcherHandler(array $form, FormStateInterface $form_state) {
    // Get the value of formatter selected for API display.
    $formatter_option = $form_state->getValue('formatter_options');
    // @todo Add try catch for any exception.
    $response = new AjaxResponse();
    // Refresh the page to display the api with selected formatter.
    $url = Url::fromRoute('<current>', [
      'view' => $formatter_option,
      'parent' => $this->parent,
    ]);
    $command = new RedirectCommand($url->toString());
    $response->addCommand($command);

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // ...
  }

}
