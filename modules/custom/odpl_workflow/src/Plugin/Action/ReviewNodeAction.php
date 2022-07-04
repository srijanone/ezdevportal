<?php

namespace Drupal\odpl_workflow\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Content moderation publish node.
 *
 * @Action(
 *   id = "odpl_workflows_review_node_action",
 *   label = @Translation("Review node (moderation_state)"),
 *   type = "node",
 *   confirm = TRUE
 * )
 */
class ReviewNodeAction extends ViewsBulkOperationsActionBase {

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

    switch ($state) {
      case 'draft':
        $entity->set('moderation_state', 'review');
        $entity->save();
        break;
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
