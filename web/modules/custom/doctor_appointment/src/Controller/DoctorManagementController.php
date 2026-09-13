<?php

namespace Drupal\doctor_appointment\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

/**
 * Controller for Doctor Management table and roster operations.
 */
class DoctorManagementController extends ControllerBase {

  /**
   * Renders the Manage Doctors table page.
   *
   * @return array
   *   A render array.
   */
  public function listDoctors(): array {
    $node_storage = $this->entityTypeManager()->getStorage('node');
    $nids = $node_storage->getQuery()
      ->condition('type', 'doctor_profile')
      ->sort('created', 'DESC')
      ->accessCheck(TRUE)
      ->execute();

    $doctors = [];
    if (!empty($nids)) {
      /** @var \Drupal\node\NodeInterface[] $nodes */
      $nodes = $node_storage->loadMultiple($nids);
      foreach ($nodes as $node) {
        // Collect degrees (multiple cardinality).
        $degrees = [];
        if ($node->hasField('field_degree') && !$node->get('field_degree')->isEmpty()) {
          foreach ($node->get('field_degree')->getValue() as $item) {
            if (!empty($item['value'])) {
              $degrees[] = trim($item['value']);
            }
          }
        }

        // Collect chamber addresses (multiple cardinality).
        $chambers = [];
        if ($node->hasField('field_chamber_address') && !$node->get('field_chamber_address')->isEmpty()) {
          foreach ($node->get('field_chamber_address')->getValue() as $item) {
            if (!empty($item['value'])) {
              $chambers[] = trim($item['value']);
            }
          }
        }

        // Specialty.
        $specialty = '';
        if ($node->hasField('field_specialty') && !$node->get('field_specialty')->isEmpty()) {
          $specialty = $node->get('field_specialty')->value;
        }

        // Experience.
        $experience = NULL;
        if ($node->hasField('field_experience') && !$node->get('field_experience')->isEmpty()) {
          $experience = (int) $node->get('field_experience')->value;
        }

        // Consultation Fee.
        $fee = NULL;
        if ($node->hasField('field_consultation_fee') && !$node->get('field_consultation_fee')->isEmpty()) {
          $fee = (float) $node->get('field_consultation_fee')->value;
        }

        $title = $node->getTitle();
        // Generate initials.
        $clean_title = preg_replace('/^Dr\.?\s+/i', '', $title);
        $words = explode(' ', trim($clean_title));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
          $initials .= strtoupper(substr($w, 0, 1));
        }
        if (empty($initials)) {
          $initials = 'DR';
        }

        $doctors[] = [
          'id' => $node->id(),
          'title' => $title,
          'initials' => $initials,
          'specialty' => $specialty,
          'degrees' => $degrees,
          'experience' => $experience,
          'chambers' => $chambers,
          'fee' => $fee !== NULL ? number_format($fee, 2) : NULL,
          'status' => $node->isPublished(),
          'created' => \Drupal::service('date.formatter')->format($node->getCreatedTime(), 'custom', 'M d, Y'),
          'view_url' => $node->toUrl('canonical')->toString(),
          'edit_url' => $node->toUrl('edit-form')->toString(),
          'delete_url' => $node->toUrl('delete-form')->toString(),
        ];
      }
    }

    $create_url = Url::fromRoute('node.add', ['node_type' => 'doctor_profile'])->toString();

    return [
      '#theme' => 'manage_doctors',
      '#doctors' => $doctors,
      '#total_doctors' => count($doctors),
      '#create_url' => $create_url,
      '#attached' => [
        'library' => [
          'doctor_appointment/dashboard',
        ],
      ],
    ];
  }

}
