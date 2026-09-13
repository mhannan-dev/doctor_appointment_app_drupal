<?php

use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;

echo "Starting Doctor Profile content type creation...\n";

// 1. Create Content Type
$bundle = 'doctor_profile';
$type = NodeType::load($bundle);
if (!$type) {
  $type = NodeType::create([
    'type' => $bundle,
    'name' => 'Doctor Profile',
    'description' => 'Profile information for registered doctors including specialty, qualifications, experience, chamber location, and consultation fee.',
    'display_submitted' => FALSE,
    'new_revision' => TRUE,
    'preview_mode' => 1,
  ]);
  $type->save();
  echo "✓ Created node type 'doctor_profile'.\n";
} else {
  echo "• Node type 'doctor_profile' already exists.\n";
}

// 2. Define Fields
$fields = [
  'field_specialty' => [
    'type' => 'string',
    'storage_settings' => ['max_length' => 255, 'is_ascii' => FALSE, 'case_sensitive' => FALSE],
    'field_settings' => [],
    'label' => 'Specialty',
    'description' => 'e.g., Cardiologist, Neurologist, Orthopedic Surgeon',
    'required' => TRUE,
    'widget' => ['type' => 'string_textfield', 'weight' => 1],
    'display' => ['type' => 'string', 'label' => 'inline', 'weight' => 1],
  ],
  'field_degree' => [
    'type' => 'string',
    'storage_settings' => ['max_length' => 255, 'is_ascii' => FALSE, 'case_sensitive' => FALSE],
    'field_settings' => [],
    'cardinality' => -1,
    'label' => 'Degree / Qualifications',
    'description' => 'e.g., MBBS, FCPS (Medicine), MD (Cardiology). Click "Add another item" to add multiple degrees.',
    'required' => TRUE,
    'widget' => ['type' => 'string_textfield', 'weight' => 2],
    'display' => ['type' => 'string', 'label' => 'inline', 'weight' => 2],
  ],
  'field_experience' => [
    'type' => 'integer',
    'storage_settings' => ['unsigned' => TRUE, 'size' => 'normal'],
    'field_settings' => ['min' => 0, 'max' => 100, 'prefix' => '', 'suffix' => ' Years'],
    'cardinality' => 1,
    'label' => 'Experience (Years)',
    'description' => 'Total years of medical experience (e.g., 10)',
    'required' => FALSE,
    'widget' => ['type' => 'number', 'weight' => 3],
    'display' => ['type' => 'number_integer', 'label' => 'inline', 'weight' => 3, 'settings' => ['thousand_separator' => '', 'prefix_suffix' => TRUE]],
  ],
  'field_chamber_address' => [
    'type' => 'string_long',
    'storage_settings' => ['case_sensitive' => FALSE],
    'field_settings' => [],
    'cardinality' => -1,
    'label' => 'Chamber Address',
    'description' => 'Full chamber address or hospital room details. Click "Add another item" to add multiple chambers.',
    'required' => TRUE,
    'widget' => ['type' => 'string_textarea', 'weight' => 4, 'settings' => ['rows' => 3]],
    'display' => ['type' => 'basic_string', 'label' => 'above', 'weight' => 4],
  ],
  'field_consultation_fee' => [
    'type' => 'decimal',
    'storage_settings' => ['precision' => 10, 'scale' => 2],
    'field_settings' => ['min' => 0, 'prefix' => '৳ ', 'suffix' => ' BDT'],
    'cardinality' => 1,
    'label' => 'Consultation Fee',
    'description' => 'Consultation fee amount (e.g., 1000.00)',
    'required' => TRUE,
    'widget' => ['type' => 'number', 'weight' => 5],
    'display' => ['type' => 'number_decimal', 'label' => 'inline', 'weight' => 5, 'settings' => ['thousand_separator' => ',', 'decimal_separator' => '.', 'scale' => 2, 'prefix_suffix' => TRUE]],
  ],
];

// 3. Create Field Storages and Fields
foreach ($fields as $field_name => $info) {
  $cardinality = $info['cardinality'] ?? 1;
  // Storage
  $field_storage = FieldStorageConfig::loadByName('node', $field_name);
  if (!$field_storage) {
    $field_storage = FieldStorageConfig::create([
      'field_name' => $field_name,
      'entity_type' => 'node',
      'type' => $info['type'],
      'settings' => $info['storage_settings'],
      'cardinality' => $cardinality,
    ]);
    $field_storage->save();
    echo "✓ Created field storage: $field_name (cardinality: $cardinality)\n";
  } else {
    if ($field_storage->getCardinality() !== $cardinality) {
      $field_storage->setCardinality($cardinality);
      $field_storage->save();
      echo "✓ Updated field storage cardinality: $field_name -> " . ($cardinality === -1 ? 'Unlimited' : $cardinality) . "\n";
    } else {
      echo "• Field storage $field_name already exists.\n";
    }
  }

  // Field instance
  $field = FieldConfig::loadByName('node', $bundle, $field_name);
  if (!$field) {
    $field = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $bundle,
      'label' => $info['label'],
      'description' => $info['description'],
      'required' => $info['required'],
      'settings' => $info['field_settings'],
    ]);
    $field->save();
    echo "✓ Created field: $field_name on bundle $bundle\n";
  } else {
    echo "• Field $field_name already exists on bundle $bundle.\n";
  }
}

// 4. Configure Form Display
$form_display = EntityFormDisplay::load("node.{$bundle}.default");
if (!$form_display) {
  $form_display = EntityFormDisplay::create([
    'targetEntityType' => 'node',
    'bundle' => $bundle,
    'mode' => 'default',
    'status' => TRUE,
  ]);
}

$form_display->setComponent('title', [
  'type' => 'string_textfield',
  'weight' => 0,
]);

foreach ($fields as $field_name => $info) {
  $form_display->setComponent($field_name, [
    'type' => $info['widget']['type'],
    'weight' => $info['widget']['weight'],
    'settings' => $info['widget']['settings'] ?? [],
  ]);
}
$form_display->save();
echo "✓ Configured EntityFormDisplay for $bundle\n";

// 5. Configure View Display
$view_display = EntityViewDisplay::load("node.{$bundle}.default");
if (!$view_display) {
  $view_display = EntityViewDisplay::create([
    'targetEntityType' => 'node',
    'bundle' => $bundle,
    'mode' => 'default',
    'status' => TRUE,
  ]);
}

foreach ($fields as $field_name => $info) {
  $view_display->setComponent($field_name, [
    'type' => $info['display']['type'],
    'label' => $info['display']['label'],
    'weight' => $info['display']['weight'],
    'settings' => $info['display']['settings'] ?? [],
  ]);
}
$view_display->save();
echo "✓ Configured EntityViewDisplay for $bundle\n";

echo "All done! Doctor Profile content type is fully configured.\n";
