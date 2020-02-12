<?php

namespace Drupal\os2web_contact\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\os2web_contact\Entity\ContactInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ContactController.
 *
 *  Returns responses for OS2Web Contact routes.
 */
class ContactController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a OS2Web Contact revision.
   *
   * @param int $os2web_contact_revision
   *   The OS2Web Contact revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($os2web_contact_revision) {
    $os2web_contact = $this->entityTypeManager()->getStorage('os2web_contact')
      ->loadRevision($os2web_contact_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('os2web_contact');

    return $view_builder->view($os2web_contact);
  }

  /**
   * Page title callback for a OS2Web Contact revision.
   *
   * @param int $os2web_contact_revision
   *   The OS2Web Contact revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($os2web_contact_revision) {
    $os2web_contact = $this->entityTypeManager()->getStorage('os2web_contact')
      ->loadRevision($os2web_contact_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $os2web_contact->label(),
      '%date' => $this->dateFormatter->format($os2web_contact->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a OS2Web Contact.
   *
   * @param \Drupal\os2web_contact\Entity\ContactInterface $os2web_contact
   *   A OS2Web Contact object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ContactInterface $os2web_contact) {
    $account = $this->currentUser();
    $os2web_contact_storage = $this->entityTypeManager()->getStorage('os2web_contact');

    $langcode = $os2web_contact->language()->getId();
    $langname = $os2web_contact->language()->getName();
    $languages = $os2web_contact->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $os2web_contact->label()]) : $this->t('Revisions for %title', ['%title' => $os2web_contact->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all os2web contact revisions") || $account->hasPermission('administer os2web contact entities')));
    $delete_permission = (($account->hasPermission("delete all os2web contact revisions") || $account->hasPermission('administer os2web contact entities')));

    $rows = [];

    $vids = $os2web_contact_storage->revisionIds($os2web_contact);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\os2web_contact\ContactInterface $revision */
      $revision = $os2web_contact_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $os2web_contact->getRevisionId()) {
          $link = $this->l($date, new Url('entity.os2web_contact.revision', [
            'os2web_contact' => $os2web_contact->id(),
            'os2web_contact_revision' => $vid,
          ]));
        }
        else {
          $link = $os2web_contact->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.os2web_contact.translation_revert', [
                'os2web_contact' => $os2web_contact->id(),
                'os2web_contact_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.os2web_contact.revision_revert', [
                'os2web_contact' => $os2web_contact->id(),
                'os2web_contact_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.os2web_contact.revision_delete', [
                'os2web_contact' => $os2web_contact->id(),
                'os2web_contact_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['os2web_contact_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
