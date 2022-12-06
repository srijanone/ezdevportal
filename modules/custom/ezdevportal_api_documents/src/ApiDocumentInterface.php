<?php

namespace Drupal\ezdevportal_api_documents;

/**
 * Interface defining apidocument helper.
 */
interface ApiDocumentInterface {

  /**
   * Form Ids.
   */
  const FORM_IDS = ['node_api_document_form', 'node_api_document_edit_form'];

  /**
   * API type.
   */
  const API_TYPE = ['rest', 'graphql', 'async'];

}
