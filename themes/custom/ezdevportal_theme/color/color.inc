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
      'title' => t('Blue Lagoon (default)'),
      'colors' => [
        'top' => '#055a8e',
        'footer' => '#cccccc',
        'footermenulinks' => '#aeaeae',
        'menulink' => '#ff0000',
        'menulinkhover' => '#cccccc',
        'cardborder' => '#fff000',
        'blockheadercolor' => '#dddddd',
        'bannerheadercolor' => '#eeeeee',
        'bannersecondarycolor' => '#aaaaaa',  
        'globallinks' => '#bbbbbb',
        'button1' => '#adadad',
        'button2' => '#acacac',
      ],
    ],
    'firehouse' => [
      'title' => t('Firehouse'),
      'colors' => [
        'top' => '#055a8e',
        'footer' => '#cccccc',
        'footermenulinks' => '#aeaeae',
        'menulink' => '#ff0000',
        'menulinkhover' => '#cccccc',
        'cardborder' => '#fff000',
        'blockheadercolor' => '#dddddd',
        'bannerheadercolor' => '#eeeeee',
        'bannersecondarycolor' => '#aaaaaa',  
        'globallinks' => '#bbbbbb',
        'button1' => '#adadad',
        'button2' => '#acacac',  
      ],
    ],
    'ice' => [
      'title' => t('Ice'),
      'colors' => [
        'top' => '#055a8e',
        'footer' => '#cccccc',
        'footermenulinks' => '#aeaeae',
        'menulink' => '#ff0000',
        'menulinkhover' => '#cccccc',
        'cardborder' => '#fff000',
        'blockheadercolor' => '#dddddd',
        'bannerheadercolor' => '#eeeeee',
        'bannersecondarycolor' => '#aaaaaa',  
        'globallinks' => '#bbbbbb',
        'button1' => '#adadad',
        'button2' => '#acacac',  
      ],
    ],
    'plum' => [
      'title' => t('Plum'),
      'colors' => [
        'top' => '#055a8e',
        'footer' => '#cccccc',
        'footermenulinks' => '#aeaeae',
        'menulink' => '#ff0000',
        'menulinkhover' => '#cccccc',
        'cardborder' => '#fff000',
        'blockheadercolor' => '#dddddd',
        'bannerheadercolor' => '#eeeeee',
        'bannersecondarycolor' => '#aaaaaa',  
        'globallinks' => '#bbbbbb',
        'button1' => '#adadad',
        'button2' => '#acacac', 
      ],
    ],
    'slate' => [
      'title' => t('Slate'),
      'colors' => [
        'top' => '#055a8e',
        'footer' => '#cccccc',
        'footermenulinks' => '#aeaeae',
        'menulink' => '#ff0000',
        'menulinkhover' => '#cccccc',
        'cardborder' => '#fff000',
        'blockheadercolor' => '#dddddd',
        'bannerheadercolor' => '#eeeeee',
        'bannersecondarycolor' => '#aaaaaa',  
        'globallinks' => '#bbbbbb',
        'button1' => '#adadad',
        'button2' => '#acacac',
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
