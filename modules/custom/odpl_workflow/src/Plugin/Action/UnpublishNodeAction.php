<?php

namespace Drupal\odpl_workflow\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Content moderation unpublish node.
 *
 * @Action(
 *   id = "odpl_workflows_unpublish_node_action",
 *   label = @Translation("Unpublish Node"),
 *   type = "node",
 *   confirm = TRUE
 * )
 */
class UnpublishNodeAction extends ViewsBulkOperationsActionBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function execute(ContentEntityInterface $entity = NULL) {
    if (!$state = $entity->get('moderation_state')->getString()) {
      return $this->t(":title  - can't change state",
        [
          ':title' => $entity->getTitle(),
        ]
      );
    }

    if ($state == 'published') {
      $entity->set('moderation_state', 'unpublished');
      $entity->save();
    }

    return $this->t(':title state changed to :state',
      [
        ':title' => $entity->getTitle(),
        ':state' => $entity->get('moderation_state')->getString(),
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($account->hasPermission('use content transition unpublish')) {
      return $return_as_object ? AccessResult::allowed() : TRUE;
    }
    return $return_as_object ? AccessResult::forbidden() : FALSE;
  }

}
