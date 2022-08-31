<?php

namespace Drupal\odpl_admin;

/**
 * Provides an interface for odpl_admin constants.
 */
interface OdplAdminInterface {
  /**
   * The Form api access index.
   */
  const FORM_ACCESS = '#access';

  /**
   * Constant for banner block preprocess.
   */
  const BANNER_DATA = '#bannerdata';

  /**
   * The Form api markup index.
   */
  const MARKUP_INDEX = '#markup';

  /**
   * Constant for Siteadmin api list page path.
   */
  const API_LIST_PATH = '/api-list';

}
