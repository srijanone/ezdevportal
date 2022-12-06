<?php

namespace Drupal\ezdevportal_sdk;

use Drupal\node\NodeInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use GuzzleHttp\ClientInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;

/**
 * Provides a Services for Download SDK.
 */
class GenerateSdk {
  use StringTranslationTrait;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * An http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Drupal\Core\Messenger\MessengerInterface definition.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Drupal\Core\Logger\LoggerChannelFactoryInterface definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The file URL generator.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * Pass the dependency to the object constructor.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    ClientInterface $http_client,
    MessengerInterface $messenger,
    LoggerChannelFactoryInterface $factory,
    FileUrlGeneratorInterface $file_url_generator) {
    $this->entityTypeManager = $entity_type_manager;
    $this->httpClient = $http_client;
    $this->messenger = $messenger;
    $this->loggerFactory = $factory;
    $this->fileUrlGenerator = $file_url_generator;
  }

  /**
   * Get all the languages supported for SDK Generation.
   *
   * @return array
   *   The drop down select list options.
   */
  public function getSdkLanguages($nid) {
    $result = [
      '-none' => 'Select',
    ];

    try {
      $node = $this->entityTypeManager->getStorage('node')->load($nid);
      if ($node instanceof NodeInterface) {
        $taxonomy_term = $node->get('field_sdk_language')->getValue();
        foreach ($taxonomy_term as $value) {
          $termId = $value['target_id'];
          $term_name = strtolower($this->entityTypeManager->getStorage('taxonomy_term')->load($termId)->getName());
          $result[$term_name] = $this->entityTypeManager->getStorage('taxonomy_term')->load($termId)->getName();
        }
      }
    }
    catch (\Exception $e) {
      $this->loggerFactory->get('ezdevportal_sdk')->error($e->getMessage());
    }

    return $result;
  }

  /**
   * POST request to the openapi-generator API.
   *
   * @param string $lang
   *   The Language of the openapi-generator API.
   * @param string $spec_path
   *   The path of the API spec.
   *
   * @return string
   *   The path of the downloadable link.
   */
  public function sdkGenerateRequest($lang, $spec_path) {
    try {
      $response = $this->httpClient->post('http://api.openapi-generator.tech/api/gen/clients/' . $lang, [
        'body' => json_encode(["openAPIUrl" => $spec_path]),
        'headers' => [
          'Content-Type' => 'application/json',
          'Accept' => '*/*',
        ],
      ]);
      $json_string = (string) $response->getBody();
      $json_obj = json_decode($json_string);
      return $json_obj->link;
    }
    catch (RequestException $e) {
      $this->loggerFactory->get('ezdevportal_sdk')->error($e->getMessage());
      $this->messenger->addError(
        $this->t('Unable to download, Please try again later or contact administrator.')
      );
    }
  }

  /**
   * Get the API spec path from the current open node.
   *
   * @param int $nid
   *   The node id.
   *
   * @return string
   *   The url of the API spec file.
   */
  public function getApiSpecFromNode($nid) {
    $node = $this->entityTypeManager->getStorage('node')->load($nid);
    if ($node instanceof NodeInterface) {
      $file_id = $node->get('field_document')->getValue()[0]['target_id'];
      if (!empty($file_id)) {
        $file = $this->entityTypeManager->getStorage('file')->load($file_id);
        return $this->fileUrlGenerator->generateAbsoluteString($file->getFileUri());
      }
    }
  }

}
