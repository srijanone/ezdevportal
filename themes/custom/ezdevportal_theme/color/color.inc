<?php

/**
 * @file
 * Lists available colors and color schemes for the Bootstrap Barrio theme.
 */

 // phpcs:ignore DrupalPractice.CodeAnalysis.VariableAnalysis.UnusedVariable
$info = [
  // Available colors and color labels used in theme.
  'fields' => [
    'top' => t('Header background'),
    'footer' => t('Footer background'),
    'footermenulinks' => t('Footer Menu links'),
    'menulink' => t('Menu top Link color'),
    'menulinkhover' => t('Menu top Link hover color'),
    'cardborder' => t('Card border'),
    'blockheadercolor' => t('Block Header color'),
    'bannerheadercolor' => t('Banner Header color'),
    'bannersecondarycolor' => t('Banner Secondary color'),
    'globallinks' => t('Global Links'),
    'button1' => t('Button 1'),
    'button2' => t('Button 2'),
  ],
  // Pre-defined color schemes.
  'schemes' => [
    'default' => [
      'title' => t('Standard (default)'),
      'colors' => [
        'top' => '#ffffff',
        'footer' => '#eeeeee',
        'footermenulinks' => '#352f2f',
        'menulink' => '#333333',
        'menulinkhover' => '#333333',
        'cardborder' => '#b7b7b7',
        'blockheadercolor' => '#333333',
        'bannerheadercolor' => '#352f2f',
        'bannersecondarycolor' => '#352f2f',
        'globallinks' => '#333333',
        'button1' => '#333333',
        'button2' => '#ffffff',
      ],
    ],
    'firehouse' => [
      'title' => t('Firehouse'),
      'colors' => [
        'top' => '#E03A3E',
        'footer' => '#E03A3E',
        'footermenulinks' => '#231F20',
        'menulink' => '#231F20',
        'menulinkhover' => '#231F20',
        'cardborder' => '#FDB925',
        'blockheadercolor' => '#231F20',
        'bannerheadercolor' => '#231F20',
        'bannersecondarycolor' => '#231F20',
        'globallinks' => '#231F20',
        'button1' => '#231F20',
        'button2' => '#FDB925',
      ],
    ],
    'ice' => [
      'title' => t('Ice'),
      'colors' => [
        'top' => '#3F7EB3',
        'footer' => '#3F7EB3',
        'footermenulinks' => '#6BA7CC',
        'menulink' => '#6BA7CC',
        'menulinkhover' => '#6BA7CC',
        'cardborder' => '#6BA7CC',
        'blockheadercolor' => '#3F7EB3',
        'bannerheadercolor' => '#3F7EB3',
        'bannersecondarycolor' => '#3F7EB3',
        'globallinks' => '#6BA7CC',
        'button1' => '#6BA7CC',
        'button2' => '#CBF1FA',
      ],
    ],
  ],

  // CSS files (excluding @import) to rewrite with new color scheme.
  'css' => [
    'css/colors.css',
  ],

  // Files to copy.
  'copy' => [
    'logo.svg',
  ],

  // Preview files.
  'preview_library' => 'ezdevportal_theme/color.preview',
  'preview_html' => 'color/preview.html',

  // Attachments.
  '#attached' => [
    'drupalSettings' => [
      'color' => [
        // Put the logo path into JavaScript for the live preview.
        'logo' => theme_get_setting('/profiles/custom/themes/custom/ezdevportal_theme/logo.svg', 'ezdevportal_theme'),
      ],
    ],
  ],
];
