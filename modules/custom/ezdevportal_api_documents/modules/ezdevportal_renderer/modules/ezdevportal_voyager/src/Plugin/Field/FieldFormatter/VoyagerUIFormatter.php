<?php

namespace Drupal\ezdevportal_voyager\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\file\Plugin\Field\FieldFormatter\FileFormatterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'Voyager' UI formatter.
 *
 * @FieldFormatter(
 *   id = "voyager_ui",
 *   label = @Translation("Voyager UI"),
 *   description = @Translation("Formats file fields with GraphQL Voyager YAML or JSON
 *   files with a rendered Voyager UI"), field_types = {
 *     "file"
 *   }
 * )
 */
class VoyagerUIFormatter extends FileFormatterBase implements ContainerFactoryPluginInterface {

  /**
   * File URL generator.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  private FileUrlGeneratorInterface $fileUrlGenerator;

  /**
   * Constructs a VoyagerUIFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $file_url_generator
   *   File URL generator.
   */
  public function __construct(string $plugin_id, mixed $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, string $label, string $view_mode, array $third_party_settings, FileUrlGeneratorInterface $file_url_generator) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->fileUrlGenerator = $file_url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('file_url_generator')
    );
  }

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
    $elements['#attached']['library'][] = 'ezdevportal_voyager/graphQL_voyager';
    foreach ($this->getEntitiesToView($items, $langcode) as $file) {
      $voyager_file = $this->fileUrlGenerator->generateAbsoluteString($file->getFileUri());
      $elements['#attached']['drupalSettings']['ezdevportal_voyager']['document'] = $voyager_file;
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element[] = [
      '#theme' => 'voyager_ui_field_item',
    ];

    return $element;
  }

}
