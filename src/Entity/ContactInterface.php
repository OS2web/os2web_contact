<?php

namespace Drupal\os2web_contact\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining OS2Web Contact entities.
 *
 * @ingroup os2web_contact
 */
interface ContactInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the OS2Web Contact name.
   *
   * @return string
   *   Name of the OS2Web Contact.
   */
  public function getName();

  /**
   * Sets the OS2Web Contact name.
   *
   * @param string $name
   *   The OS2Web Contact name.
   *
   * @return \Drupal\os2web_contact\Entity\ContactInterface
   *   The called OS2Web Contact entity.
   */
  public function setName($name);

  /**
   * Gets the OS2Web Contact creation timestamp.
   *
   * @return int
   *   Creation timestamp of the OS2Web Contact.
   */
  public function getCreatedTime();

  /**
   * Sets the OS2Web Contact creation timestamp.
   *
   * @param int $timestamp
   *   The OS2Web Contact creation timestamp.
   *
   * @return \Drupal\os2web_contact\Entity\ContactInterface
   *   The called OS2Web Contact entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the OS2Web Contact revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the OS2Web Contact revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\os2web_contact\Entity\ContactInterface
   *   The called OS2Web Contact entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the OS2Web Contact revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the OS2Web Contact revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\os2web_contact\Entity\ContactInterface
   *   The called OS2Web Contact entity.
   */
  public function setRevisionUserId($uid);

}
