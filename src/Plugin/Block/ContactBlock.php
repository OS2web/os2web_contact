<?php

namespace Drupal\os2web_contact\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;

/**
 * Provides a 'OS2Web Contact' block.
 *
 * @Block(
 *   id = "os2web_contact",
 *   admin_label = @Translation("OS2Web Contact block")
 * )
 */
class ContactBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $block = NULL;
    $hide_contact = FALSE;
    $ids = array_filter(\Drupal::service('menu.active_trail')->getActiveTrailIds(NULL));
    $contact_entity = NULL;
    while ($id = array_shift($ids)) {
      /** @var \Drupal\menu_link_content\Plugin\Menu\MenuLinkContent $menuLink */
      $menuLink = \Drupal::service('plugin.manager.menu.link')->createInstance($id);
      $urlObject = $menuLink->getUrlObject();
      // Skipping external link menu items.
      if ($urlObject->isExternal()) {
        continue;
      }
      $routeName = $urlObject->getRouteName();
      $routeParams = $urlObject->getRouteParameters();
      if ($routeName == 'entity.node.canonical') {
        $node = Node::load($routeParams['node']);
        if ($node && $node->bundle() == 'os2web_page') {
          if ($node->field_os2web_page_contact_hide->value) {
            $hide_contact = TRUE;
          }
          if ($node->hasField('field_os2web_page_contact') && !$node->field_os2web_page_contact->isEmpty()) {
            $contact_entity = $node->field_os2web_page_contact->referencedEntities()[0];
            break;
          }
        }
      }
    }
    if ($contact_entity && !$hide_contact) {
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder('os2web_contact');
      $block = $view_builder->view($contact_entity);
    }
    return $block;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
