<?php

namespace Drupal\os2web_contact\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a OS2Web Contact revision.
 *
 * @ingroup os2web_contact
 */
class ContactRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The OS2Web Contact revision.
   *
   * @var \Drupal\os2web_contact\Entity\ContactInterface
   */
  protected $revision;

  /**
   * The OS2Web Contact storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $contactStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->contactStorage = $container->get('entity_type.manager')->getStorage('os2web_contact');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'os2web_contact_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => \Drupal::service('date.formatter')->format($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.os2web_contact.version_history', ['os2web_contact' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $os2web_contact_revision = NULL) {
    $this->revision = $this->ContactStorage->loadRevision($os2web_contact_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->ContactStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('OS2Web Contact: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of OS2Web Contact %title has been deleted.', ['%revision-date' => \Drupal::service('date.formatter')->format($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.os2web_contact.canonical',
       ['os2web_contact' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {os2web_contact_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.os2web_contact.version_history',
         ['os2web_contact' => $this->revision->id()]
      );
    }
  }

}
