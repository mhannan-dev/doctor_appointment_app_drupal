<?php

use Drupal\menu_link_content\Entity\MenuLinkContent;

// Remove any standalone 'Create Doctor Profile' link from menu_link_content
$links = \Drupal::entityTypeManager()->getStorage('menu_link_content')->loadByProperties([
  'title' => 'Create Doctor Profile',
]);
foreach ($links as $link) {
  echo "Deleting manual menu link: " . $link->getTitle() . " from menu " . $link->getMenuName() . "\n";
  $link->delete();
}
echo "Cleaned up standalone links.\n";
