<?php

namespace Drupal\os2web_contact;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\os2web_contact\Entity\ContactInterface;

/**
 * Defines the storage handler class for OS2Web Contact entities.
 *
 * This extends the base storage class, adding required special handling for
 * OS2Web Contact entities.
 *
 * @ingroup os2web_contact
 */
interface ContactStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of OS2Web Contact revision IDs for a specific OS2Web Contact.
   *
   * @param \Drupal\os2web_contact\Entity\ContactInterface $entity
   *   The OS2Web Contact entity.
   *
   * @return int[]
   *   OS2Web Contact revision IDs (in ascending order).
   */
  public function revisionIds(ContactInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as OS2Web Contact author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   OS2Web Contact revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\os2web_contact\Entity\ContactInterface $entity
   *   The OS2Web Contact entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ContactInterface $entity);

  /**
   * Unsets the language for all OS2Web Contact with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
