<?php

namespace Drupal\odpl_api_documents;

/**
 * File for  Api Document Helper.
 */
class ApiDocumentHelper {

  /**
   * Get node id from current path.
   */
  public function getNodeId() {

    $current_path = \Drupal::service('path.current')->getPath();

    $current_path_array = explode('/', $current_path);
    return end($current_path_array);

  }

}
