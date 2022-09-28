<?php

/**
 * @file
 * Provides additional install tasks.
 */

/**
 * Implements hook_install_tasks().
 */
function opdplite_install_tasks(&$install_state) {
  $tasks = [];

  $tasks['odplite_module_configure_form'] = [
    'display_name' => t('Select Optional Modules'),
    'type' => 'form',
    'function' => 'Drupal\odplite\Installer\Form\ModuleConfigureForm',
  ];
  $tasks['odplite_module_install'] = [
    'display_name' => t('Install Optional Modules'),
  ];

  return $tasks;
}

/**
 * Installs the demo modules in a batch.
 *
 * @param array $install_state
 *   The install state.
 */
function odplite_module_install(array &$install_state) {

  set_time_limit(0);
  $modules = $install_state['odplite_additional_modules'];
  try {
    \Drupal::service('module_installer')->install($modules);
  }
  catch (\Exception $e) {
    \Drupal::logger('opendevportal')->error($e->getMessage());
  }
}
