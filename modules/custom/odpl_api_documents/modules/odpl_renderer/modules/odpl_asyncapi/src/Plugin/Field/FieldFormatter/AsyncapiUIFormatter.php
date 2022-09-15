<?php

namespace Drupal\odpl_asyncapi\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Plugin\Field\FieldFormatter\FileFormatterBase;

/**
 * Plugin implementation of the 'Async API' UI formatter.
 *
 * @FieldFormatter(
 *   id = "async",
 *   label = @Translation("Async API UI"),
 *   description = @Translation("Formats file fields with Async API YAML
 *   files with a rendered Async API UI"), field_types = {
 *     "file"
 *   }
 * )
 */
class AsyncapiUIFormatter extends FileFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function view(FieldItemListInterface $items, $langcode = NULL) {
    $elements = parent::view($items, $langcode);
    $elements['#attached']['library'][] = 'odpl_asyncapi/react_app';
    foreach ($this->getEntitiesToView($items, $langcode) as $file) {
      $asyncapi_file = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
      $elements['#attached']['drupalSettings']['odpl_asyncapi']['document'] = $asyncapi_file;
    }
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $element[] = [
      '#theme' => 'asyncapi_ui_field_item',
    ];
    return $element;
  }

}
