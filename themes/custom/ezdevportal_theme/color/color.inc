<?php

/**
 * @file
 * Lists available colors and color schemes for the Bootstrap Barrio theme.
 */

 // phpcs:ignore DrupalPractice.CodeAnalysis.VariableAnalysis.UnusedVariable
$info = [
  // Available colors and color labels used in theme.
  'fields' => [
    'sliderarrow' => t('Slider Arrow'),
    'explorebtnbg' => t('Explore Button Background'),
    'explorebtntext' => t('Explore Button Text'),
    'trendicon' => t('Trend Icon'),
    'contentlisttitle' => t('Content List Title'),
    'contentcardcenteredtitle' => t('Content Card Centered Title'),
    'contentcardcenteredtitlecardtitle' => t('Content Card Centered Title Card Title'),
    'menulinkhover' => t('Menu Link Hover'),
    'button' => t('Button'),
    'subscribebutton' => t('Subscribe Button Text'),
    'checkboxes' => t('Checkboxes'),
    'activelinksinproductpage' => t('Active Links In Product Page'),
    'discovermoretagscolor' =>  t('Discover More Tags Text Color'),
    'discovermoretagsborder' =>  t('Discover More Tags Background Color'),
    'discovermoretagsbg' =>  t('Discover More Tags Background'),
    'discovermoretagshover' =>  t('Discover More Tags Hover'),
    'discovermoretagsbghover' =>  t('Discover More Tags Background Hover'),
    'tabs' => t('Tabs'),
    'forumtabs' => t('Forum Tabs'),
    'forumlikebtnborder' => t('Forum Like Button Border'),
    'blogtag' => t('Blog Tag'),
    'contentcardscenteredbg' => t('Content Cards Centered Background'),
    'contentcardscenteredtitle' => t('Content Cards Centered Title'),
    'likebottomtext' => t('Like Bottom Text')
  ],
  // Pre-defined color schemes.
  'schemes' => [
    'default' => [
      'title' => t('Standard (default)'),
      'colors' => [
        'sliderarrow' => '#333333',
        'explorebtnbg' => '#ffffff',
        'explorebtntext' => '#333333',
        'trendicon' => '#333333',
        'contentcardcenteredtitle' => '#352f2f',
        'contentcardcenteredtitlecardtitle' => '#333333',
        'menulinkhover' => '#333333',
        'button' => '#333333',
        'subscribebutton' => '#ffffff',
        'checkboxes' => '#333333',
        'activelinksinproductpage' => '#333333',
        'discovermoretagscolor' => '#6c757d',
        'discovermoretagsborder' => '#6c757d',
        'discovermoretagsbg' => '#ffffff',
        'discovermoretagshover' => '#ffffff',
        'discovermoretagsbghover' => '#6c757d',
        'tabs' => '#ffffff',
        'forumtabs' => '#ffffff',
        'forumlikebtnborder' => '#ffffff',
        'blogtag' => '#6c757d',
        'contentcardscenteredbg' => '#eaf1ff',
        'contentcardscenteredtitle' => '#333333',
        'likebottomtext' => '#6a6a6a'
        ],
    ],
      'blue' => [
      'title' => t('Blue'),
      'colors' => [
        'sliderarrow' => '#416AD9',
        'explorebtnbg' => '#416AD9',
        'explorebtntext' => '#ffffff',
        'trendicon' => '#416AD9',
        'contentcardcenteredtitle' => '#416AD9',
        'contentcardcenteredtitlecardtitle' => '#416AD9',
        'menulinkhover' => '#416AD9',
        'button' => '#416AD9',
        'subscribebutton' => '#416AD9',
        'checkboxes' => '#416AD9',
        'activelinksinproductpage' => '#416AD9',
        'discovermoretagscolor' => '#416AD9',
        'discovermoretagsborder' => '#416AD9',
        'discovermoretagsbg' => '#ffffff',
        'discovermoretagshover' => '#ffffff',
        'discovermoretagsbghover' => '#416AD9',
        'tabs' => '#416AD9',
        'forumtabs' => '#416AD9',
        'forumlikebtnborder' => '#416AD9',
        'blogtag' => '#416AD9',
        'contentcardscenteredbg' => '#F5F5F5',
        'contentcardscenteredtitle' => '#ffffff',
        'likebottomtext' => '#416AD9'
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
