<?php

namespace Drupal\doctor_appointment\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Drupal\node\NodeInterface;

/**
 * Ensures Doctor Profile forms use the frontend MediCareHub Tailwind theme.
 */
class DoctorProfileThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match): bool {
    $route_name = $route_match->getRouteName();

    if ($route_name === 'node.add') {
      $node_type = $route_match->getParameter('node_type');
      $type_id = is_object($node_type) ? $node_type->id() : $node_type;
      return $type_id === 'doctor_profile';
    }

    if ($route_name === 'entity.node.edit_form' || $route_name === 'entity.node.canonical') {
      $node = $route_match->getParameter('node');
      return ($node instanceof NodeInterface && $node->bundle() === 'doctor_profile');
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match): ?string {
    return 'olivero';
  }

}
