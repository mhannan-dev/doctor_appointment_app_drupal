<?php

namespace Drupal\doctor_appointment\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for public doctor directory and detailed profile view.
 */
class DirectoryController extends ControllerBase
{

  public function listDoctors(Request $request)
  {
    $specialtyFilter = $request->query->get('specialty');
    $chamberFilter = $request->query->get('chamber');
    $searchQuery = trim($request->query->get('q', ''));

    $nodeStorage = $this->entityTypeManager()->getStorage('node');
    $query = $nodeStorage->getQuery()
      ->condition('type', 'doctor_profile')
      ->condition('status', 1)
      ->accessCheck(TRUE);

    if (!empty($specialtyFilter)) {
      $query->condition('field_doctor_specialty', $specialtyFilter);
    }
    if (!empty($chamberFilter)) {
      $query->condition('field_chamber_location', $chamberFilter);
    }
    if (!empty($searchQuery)) {
      $query->condition('title', '%' . $searchQuery . '%', 'LIKE');
    }

    $nids = $query->execute();
    $doctorNodes = !empty($nids) ? $nodeStorage->loadMultiple($nids) : [];

    $doctors = [];
    foreach ($doctorNodes as $node) {
      $specialtyName = 'General';
      if ($node->hasField('field_doctor_specialty') && !$node->get('field_doctor_specialty')->isEmpty()) {
        $term = $node->get('field_doctor_specialty')->entity;
        if ($term) {
          $specialtyName = $term->label();
        }
      }

      $chamberName = 'Main Clinic';
      if ($node->hasField('field_chamber_location') && !$node->get('field_chamber_location')->isEmpty()) {
        $term = $node->get('field_chamber_location')->entity;
        if ($term) {
          $chamberName = $term->label();
        }
      }

      $degrees = $node->hasField('field_doctor_degrees') ? $node->get('field_doctor_degrees')->value : '';
      $experience = $node->hasField('field_experience_years') ? $node->get('field_experience_years')->value : 5;
      $fee = $node->hasField('field_consultation_fee') ? $node->get('field_consultation_fee')->value : 800;
      $slotDuration = $node->hasField('field_slot_duration') ? $node->get('field_slot_duration')->value : 20;

      $workingDays = [];
      if ($node->hasField('field_working_days')) {
        foreach ($node->get('field_working_days') as $item) {
          $workingDays[] = ucfirst($item->value);
        }
      }

      $doctors[] = [
        'id' => $node->id(),
        'name' => $node->label(),
        'specialty' => $specialtyName,
        'chamber' => $chamberName,
        'degrees' => $degrees,
        'experience' => $experience,
        'fee' => number_format((float) $fee, 0),
        'slot_duration' => $slotDuration,
        'working_days' => implode(', ', $workingDays),
        'shift_start' => $node->hasField('field_shift_start') ? $node->get('field_shift_start')->value : '09:00',
        'shift_end' => $node->hasField('field_shift_end') ? $node->get('field_shift_end')->value : '17:00',
      ];
    }

    // Load specialties for filter dropdown
    $termStorage = $this->entityTypeManager()->getStorage('taxonomy_term');
    $specialtyTerms = $termStorage->loadTree('specialties', 0, NULL, TRUE);
    $specialties = [];
    foreach ($specialtyTerms as $t) {
      $specialties[] = ['id' => $t->id(), 'name' => $t->label()];
    }

    // Load chambers for filter dropdown
    $chamberTerms = $termStorage->loadTree('chamber_locations', 0, NULL, TRUE);
    $chambers = [];
    foreach ($chamberTerms as $t) {
      $chambers[] = ['id' => $t->id(), 'name' => $t->label()];
    }

    return [
      '#theme' => 'doctor_directory',
      '#doctors' => $doctors,
      '#specialties' => $specialties,
      '#chambers' => $chambers,
      '#selected_specialty' => $specialtyFilter,
      '#selected_chamber' => $chamberFilter,
      '#search_query' => $searchQuery,
      '#total_doctors' => count($doctors),
      '#attached' => [
        'library' => ['doctor_appointment/app_theme'],
      ],
    ];
  }

  public function viewDoctor(NodeInterface $node)
  {
    if ($node->bundle() !== 'doctor_profile') {
      throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    }

    $specialtyName = 'Specialist';
    if ($node->hasField('field_doctor_specialty') && !$node->get('field_doctor_specialty')->isEmpty()) {
      $term = $node->get('field_doctor_specialty')->entity;
      if ($term) {
        $specialtyName = $term->label();
      }
    }

    $chamberName = 'Main Clinic';
    if ($node->hasField('field_chamber_location') && !$node->get('field_chamber_location')->isEmpty()) {
      $term = $node->get('field_chamber_location')->entity;
      if ($term) {
        $chamberName = $term->label();
      }
    }

    $workingDays = [];
    if ($node->hasField('field_working_days')) {
      foreach ($node->get('field_working_days') as $item) {
        $workingDays[] = ucfirst($item->value);
      }
    }

    $doctor = [
      'id' => $node->id(),
      'name' => $node->label(),
      'specialty' => $specialtyName,
      'chamber' => $chamberName,
      'degrees' => $node->hasField('field_doctor_degrees') ? $node->get('field_doctor_degrees')->value : '',
      'experience' => $node->hasField('field_experience_years') ? $node->get('field_experience_years')->value : 5,
      'fee' => number_format((float) ($node->hasField('field_consultation_fee') ? $node->get('field_consultation_fee')->value : 800), 0),
      'slot_duration' => $node->hasField('field_slot_duration') ? $node->get('field_slot_duration')->value : 20,
      'working_days' => implode(', ', $workingDays),
      'shift_start' => $node->hasField('field_shift_start') ? $node->get('field_shift_start')->value : '09:00',
      'shift_end' => $node->hasField('field_shift_end') ? $node->get('field_shift_end')->value : '17:00',
    ];

    return [
      '#theme' => 'doctor_profile_detail',
      '#doctor' => $doctor,
      '#attached' => [
        'library' => ['doctor_appointment/app_theme'],
      ],
    ];
  }
}
