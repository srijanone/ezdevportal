<?php

namespace Drupal\ezdevportal_rapidoc_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\file\Plugin\Field\FieldFormatter\FileFormatterBase;

/**
 * Plugin implementation of the 'rapidoc' UI formatter.
 *
 * @FieldFormatter(
 *   id = "rapidoc_ui",
 *   label = @Translation("RapiDoc UI"),
 *   description = @Translation("Formats file fields with RapiDoc YAML or JSON
 *   files with a rendered RapiDoc UI"), field_types = {
 *     "file"
 *   }
 * )
 */
class RapiDocUIFormatter extends FileFormatterBase {

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
    return parent::view($items, $langcode);
    // Library cannot be attached here. Need to be added inline in twig.
    // $elements['#attached']['library'][] = 'ezdevportal_rapidoc_formatter/rapidoc';.
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $file) {
      $rapidoc_file = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
      $element[$delta] = [
        '#theme' => 'rapidoc_ui_field_item',
        '#field_name' => $this->fieldDefinition->getName(),
        '#delta' => $delta,
        '#file_url' => $rapidoc_file,
      ];
    }
    return $element;
  }

}
