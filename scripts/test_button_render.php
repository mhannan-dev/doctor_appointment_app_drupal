<?php

$menu_tree = \Drupal::service('navigation.menu_tree');
$parameters = new \Drupal\Core\Menu\MenuTreeParameters();
$parameters->onlyEnabledLinks();
$tree = $menu_tree->load('content', $parameters);
$manipulators = [
  ['callable' => 'menu.default_tree_manipulators:checkAccess'],
  ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
];
$tree = $menu_tree->transform($tree, $manipulators);
$build = $menu_tree->build($tree);

$item = $build['#items']['doctor_appointment.manage_doctors'] ?? null;
echo "Item exists: " . ($item ? 'Yes' : 'No') . "\n";
if ($item) {
  echo "Icon:\n";
  print_r($item['icon']);

  // Test rendering toolbar-button
  $element = [
    '#type' => 'component',
    '#component' => 'navigation:toolbar-button',
    '#props' => [
      'text' => $item['title'],
      'icon' => $item['icon'],
      'html_tag' => 'a',
      'url' => $item['url']->toString(),
    ],
  ];
  $html = \Drupal::service('renderer')->renderRoot($element);
  echo "Rendered toolbar button HTML:\n" . $html . "\n";
}
