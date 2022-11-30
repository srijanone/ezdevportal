<?php

namespace Drupal\odpl_notification\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a block with list of notification items.
 *
 * @Block(
 *   id = "odpl_notification_widget_block",
 *   admin_label = @Translation("Ezdevportal Notification Block"),
 *   category = @Translation("Ezdevportal Notifications")
 * )
 */
class OdplNotificationBlock extends BlockBase implements ContainerFactoryPluginInterface, BlockPluginInterface {

  /**
   * Drupal\Core\Session\AccountInterface definition.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The Database Connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration,
    $plugin_id,
    $plugin_definition,
    AccountInterface $current_user,
    Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
    $this->database    = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Retrieve existing configuration for this block.
    $config = $this->getConfiguration();

    // Add a form field to the existing block configuration form.
    $form['block_notification_type'] = [
      '#type'    => 'select',
      '#title'   => $this->t('Notification Content'),
      '#options' => ['As Admin', 'As Logged-In user'],
      '#default_value' => $config['block_notification_type'] ?? '',
    ];

    $form['block_notification_logs_display'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Skip Display to own activities'),
      '#default_value' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Save our custom settings when the form is submitted.
    $this->setConfigurationValue('block_notification_type', $form_state->getValue('block_notification_type'));
    $this->setConfigurationValue('block_notification_logs_display', $form_state->getValue('block_notification_logs_display'));
  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIf($account->isAuthenticated());
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $connection = $this->database;
    $config     = $this->getConfiguration();

    // Get logged user session.
    $currentUser = $this->currentUser;

    $uid = $currentUser->id();
    $roles = $currentUser->getRoles();
    $notificationType = 0;
    $totalCount = 0;
    $unreadCount = 0;
    $notificationList = [];

    $clerallQuery = $connection->select('notifications_clear_all', 'nca');
    $clerallQuery->fields('nca');
    $clerallQuery->condition('nca.uid', $uid);
    $clerallQuery->orderBy('nca.id', 'DESC');
    $ncaRes = $clerallQuery->execute()->fetchObject();

    $startingNotiId = isset($ncaRes->notification_id) ? $ncaRes->notification_id : 0;

    $query = $connection->select('notifications', 'n');
    $query->fields('n', [
      'id',
      'message',
      'status',
      'created',
    ]);

    $query->condition('n.id', $startingNotiId, '>');

    if (isset($config['block_notification_type'])
     && $config['block_notification_type'] == 1
     && $config['block_notification_logs_display'] == 1) {
      $query->condition('n.entity_uid', $uid);
      $query->condition('n.uid', $uid, '<>');
      $notificationType = 1;
    }
    elseif (isset($config['block_notification_type'])
     && $config['block_notification_type'] == 1
     && $config['block_notification_logs_display'] == 0) {
      $query->condition('n.entity_uid', $uid);
      $notificationType = 1;
    }
    elseif (isset($config['block_notification_type'])
    && $config['block_notification_type'] == 0
    && $config['block_notification_logs_display'] == 1) {
      $query->condition('n.uid', $uid, '<>');
    }

    if (in_array('site_admin', $roles)) {
      $query->condition('n.bundle', 'user');
    }
    elseif (in_array('developer', $roles)) {
      $query->condition('n.bundle', 'user', '<>');
    }

    $query->orderBy('n.created', 'DESC');
    $res = $query->execute();

    while ($notification = $res->fetchObject()) {
      if (!empty($notification->message)) {

        $nasQuery = $connection->select('notifications_actions', 'nas');
        $nasQuery->fields('nas');
        $nasQuery->condition('nas.uid', $uid);
        $nasQuery->condition('nas.notification_id', $notification->id);
        $nasRes = $nasQuery->execute()->fetchObject();

        $nasId  = isset($nasRes->id) ? $nasRes->id : '';
        $status = isset($nasRes->status) ? $nasRes->status : 0;

        if ($status == 2) {
          continue;
        }

        $createdDate = date('D d M y', $notification->created);
        $time = time() - $notification->created;
        $units = [
          31536000 => 'year',
          2592000 => 'month',
          604800 => 'week',
          86400 => 'day',
          3600 => 'hour',
          60 => 'min',
          1 => 'sec',
        ];
        foreach ($units as $unit => $val) {
          if ($time > $unit) {
            $numberOfUnits = floor($time / $unit);
            $time_ago = $numberOfUnits . ' ' . $val;
            break;
          }
        }
        $notificationList[$createdDate][] = [
          'id'      => $notification->id,
          'message' => $notification->message,
          'status'  => $status,
          'nas_id'  => $nasId,
          'created' => $notification->created,
          'timeAgo' => $time_ago,
        ];

        if ($notification->status == 0) {
          $unreadCount++;
        }

        $totalCount++;
      }
    }

    return [
      '#theme' => 'odpl_notification',
      '#uid' => $uid,
      '#notification_type' => $notificationType,
      '#total' => $totalCount,
      '#unread' => $unreadCount,
      '#notification_list' => $notificationList,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
