<?php

namespace Drupal\ezdevportal_workflow\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;

/**
 * Content moderation draft node.
 *
 * @Action(
 *   id = "ezdevportal_workflows_draft_node_action",
 *   label = @Translation("Draft Node"),
 *   type = "node",
 *   confirm = TRUE
 * )
 */
class DraftNodeAction extends ViewsBulkOperationsActionBase {

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

    if ($state == 'unpublished' || $state == 'review') {
      $entity->set('moderation_state', 'draft');
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
    if ($account->hasPermission('use content transition create_new_draft')) {
      return $return_as_object ? AccessResult::allowed() : TRUE;
    }
    return $return_as_object ? AccessResult::forbidden() : FALSE;
  }

}
