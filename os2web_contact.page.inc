<?php

/**
 * @file
 * Contains os2web_contact.page.inc.
 *
 * Page callback for OS2Web Contact entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for OS2Web Contact templates.
 *
 * Default template: os2web_contact.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_os2web_contact(array &$variables) {
  // Fetch Contact Entity Object.
  $os2web_contact = $variables['elements']['#os2web_contact'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
