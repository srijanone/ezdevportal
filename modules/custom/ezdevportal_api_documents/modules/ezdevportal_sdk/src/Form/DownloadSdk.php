<?php

namespace Drupal\ezdevportal_sdk\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ezdevportal_sdk\GenerateSdk;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\CssCommand;

/**
 * Provides a config form of Download SDK.
 */
class DownloadSdk extends FormBase {

  /**
   * Services class object.
   *
   * @var \Drupal\ezdevportal_sdk\GenerateSdk
   */
  protected $sdk;

  /**
   * EzDevPortal config Service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Class constructor.
   */
  public function __construct(ConfigFactoryInterface $config_factory, GenerateSdk $generate_sdk) {
    $this->configFactory = $config_factory;
    $this->sdk = $generate_sdk;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('ezdevportal_sk.generateSdk_services')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ezdevportal_download_sdk';
  }

  /**
   * Build form.
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {

    $node = $node->id();
    $form['sdk_languages'] = [
      '#type' => 'select',
      '#title' => $this->t('Choose Language'),
      '#options' => $this->sdk->getSdkLanguages($node),
      '#prefix' => '<div id="sdk-languages-result" class="text-danger"></div>',
      '#ajax' => [
        'callback' => '::checkSdkLanguage',
        'effect' => 'fade',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => NULL,
        ],
      ],
    ];
    $form['sdk_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Download'),
      '#attributes' => ['class' => ['link-wrapper']],
      '#submit' => [
        'callback' => [$this, 'sdkApiRequestHandler'],
      ],
    ];
    $form['sdk_nid'] = [
      '#type' => 'hidden',
      '#default_value' => $node,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function checkSdkLanguage(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();
    if ($form_state->getValue('sdk_languages') == '-none') {
      $text = 'Choose a language to download a file';
      $ajax_response->addCommand(new CssCommand('.link-wrapper', ['pointer-events' => 'none']));
    }
    else {
      $text = '';
      $ajax_response->addCommand(new CssCommand('.link-wrapper', ['pointer-events' => 'auto']));
    }
    $ajax_response->addCommand(new HtmlCommand('#sdk-languages-result', $text));
    return $ajax_response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('sdk_languages') == '-none') {
      $form_state->setErrorByName('sdk_languages', $this->t('Choose a language to download a file!'));
    }
  }

  /**
   * The callback function to request to openapi-generator api.
   *
   * The file is downloaded on the ajax submit.
   */
  public function sdkApiRequestHandler(array $form, FormStateInterface $form_state) {
    $download_link = $this->sdk->sdkGenerateRequest(
      $form_state->getValue('sdk_languages'),
      $this->sdk->getApiSpecFromNode($form_state->getValue('sdk_nid')),
    );
    if (isset($download_link)) {
      $form_state->setResponse(new TrustedRedirectResponse($download_link));
    }
  }

}
