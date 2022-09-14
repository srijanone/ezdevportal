<?php

namespace Drupal\odpl_dashboard;

use Drupal\Core\Url;

/**
 * File for general function usage.
 */
class DashboardHelper {

  /**
   * Function for checking multiarray .
   */
  public function isMultiarray($array) {

    $multiarray = array_filter($array, 'is_array');

    if (count($multiarray) > 0) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Function for set navigation default data.
   */
  public function developerNavigationDefaultData() {

    $array = [
      ['Go to your Apps', '/dashboard?view=app', 'developer'],
      ['Get Started', '/quick-guide', 'developer'],
      ['FAQs', '/faqs', 'developer'],
      ['ACCOUNT SETTINGS', '', 'siteadmin'],
      ['VIEW SITE', '/home', 'siteadmin'],
    ];

    if ($this->isMultiarray($array)) {
      foreach ($array as $key => $value) {
        $nav[$key] = [
          'text' => $value[0],
          'path' => $value[1],
          'visibility' => $value[2],
        ];
      }
      return $nav;
    }

  }

  /**
   * Function for set dashboard navigation default data.
   */
  public function dashboardNavigationDefaultData() {
    $array = [
        ['Applications', 'My Application', '/dashboard', '/user/apps'],
        ['Support', 'Support', '?view=support', '/node/add/issue'],
        ['Forum', 'Forum', '?view=forum', '/node/add/forum'],
    ];

    if ($this->isMultiarray($array)) {
      foreach ($array as $key => $value) {
        $nav[$key] = [
          'type' => $value[0],
          'text' => $value[1],
          'path' => $value[2],
          'add_icon_path' => $value[3],
        ];
      }
      return $nav;
    }

  }

  /**
   * Function for set navigation class.
   */
  public function getNavigationClass($classdata) {
    if (!empty($classdata)) {
      return 'dashboard-' . str_replace(" ", "-", strtolower($classdata)) . '-link';
    }
  }

  /**
   * Function for set icon path.
   */
  public function getIconPath($data) {

    if (!empty($data)) {

      if ($data == '/user/apps') {
        $current_user = \Drupal::currentUser();
        $id = $current_user->id();
        $moduleHandler = \Drupal::service('module_handler');
        if ($moduleHandler->moduleExists('odpl_apigee_connector')) {
          return Url::fromRoute('entity.developer_app.add_form_for_developer', ['user' => $id])->toString();
        }
      }
      else {
        return $data;
      }
    }

  }

}
