<?php

namespace Drupal\os2web_contact\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

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
    $contact_entity = NULL;

    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node && $node->bundle() == 'os2web_page') {
      // Checking visibility.
      if (!$this->checkContactBlockVisible($node)) {
        return NULL;
      }

      // Getting this node contact block.
      $contact_entity = $this->getNodeContactBlock($node);

      // No contact block found, look up the menu for contact block.
      if (!$contact_entity) {
        /** @var \Drupal\Core\Menu\MenuLinkManagerInterface $menu_link_manager */
        $menu_link_manager = \Drupal::service('plugin.manager.menu.link');

        $links = $menu_link_manager->loadLinksByRoute('entity.node.canonical', ['node' => $node->id()]);

        // Getting current menu link.
        $activeLink = end($links);
        $parentLinkId = $activeLink->getParent();

        // Traversing through all menus link up the tree.
        while ($parentLinkId) {
          /** @var \Drupal\menu_link_content\Plugin\Menu\MenuLinkContent $parentLink */
          $parentLink = $menu_link_manager->createInstance($parentLinkId);
          $urlObject = $parentLink->getUrlObject();

          // Skipping external link menu items.
          if ($urlObject->isExternal()) {
            continue;
          }

          if ($urlObject->getRouteName() == 'entity.node.canonical') {
            $routeParams = $urlObject->getRouteParameters();
            $node = Node::load($routeParams['node']);

            if ($node && $node->bundle() == 'os2web_page') {
              // Checking visibility.
              if (!$this->checkContactBlockVisible($node)) {
                return NULL;
              }

              if ($contact_entity = $this->getNodeContactBlock($node)) {
                break;
              }
            }
          }

          $parentLinkId = $parentLink->getParent();
        }
      }
    }

    if ($contact_entity) {
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

  /**
   * Gets the contact block associated with the node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to find block in.
   *
   * @return mixed
   *   Referenced block entity or NULL.
   */
  protected function getNodeContactBlock(NodeInterface $node) {
    if ($node->hasField('field_os2web_page_contact') && !$node->field_os2web_page_contact->isEmpty()) {
      return $node->field_os2web_page_contact->referencedEntities()[0];
    }
  }

  /**
   * Checks if the block is set to be visible.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node to check against.
   *
   * @return bool
   *   TRUE is visible, FALSE otherwise.
   */
  protected function checkContactBlockVisible(NodeInterface $node) {
    if ($node->field_os2web_page_contact_hide->value) {
      return FALSE;
    }

    return TRUE;
  }

}
