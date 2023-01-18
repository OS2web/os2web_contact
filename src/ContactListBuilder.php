<?php

namespace Drupal\os2web_contact;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of OS2Web Contact entities.
 *
 * @ingroup os2web_contact
 */
class ContactListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Contact ID');
    $header['name'] = $this->t('Name');
    $header['status'] = $this->t('Published');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\os2web_contact\Entity\Contact $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.os2web_contact.edit_form',
      ['os2web_contact' => $entity->id()]
    );
    $row['status'] = ($entity->status[0]->value ? $this->t('yes'):$this->t('no'));

    return $row + parent::buildRow($entity);
  }

}
