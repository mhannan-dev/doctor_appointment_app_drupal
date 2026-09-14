<?php

namespace Drupal\doctor_appointment\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Service for generating dynamic doctor appointment slots and checking availability.
 */
class SlotGeneratorService {

  protected EntityTypeManagerInterface $entityTypeManager;
  protected Connection $database;

  public function __construct(EntityTypeManagerInterface $entityTypeManager, Connection $database) {
    $this->entityTypeManager = $entityTypeManager;
    $this->database = $database;
  }

  /**
   * Generates next N available dates for a doctor based on their working days.
   */
  public function getAvailableDates(NodeInterface $doctorNode, int $daysAhead = 14): array {
    $workingDays = [];
    if ($doctorNode->hasField('field_working_days') && !$doctorNode->get('field_working_days')->isEmpty()) {
      foreach ($doctorNode->get('field_working_days') as $item) {
        $workingDays[] = strtolower(trim($item->value));
      }
    }

    // Default to Sunday-Thursday if not configured
    if (empty($workingDays)) {
      $workingDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'saturday'];
    }

    $dates = [];
    $today = new \DateTime('today', new \DateTimeZone('Asia/Dhaka'));

    for ($i = 0; $i < $daysAhead; $i++) {
      $checkDate = clone $today;
      $checkDate->modify("+{$i} day");
      $dayName = strtolower($checkDate->format('l'));

      if (in_array($dayName, $workingDays, TRUE)) {
        $dates[] = [
          'date' => $checkDate->format('Y-m-d'),
          'formatted' => $checkDate->format('D, d M Y'),
          'day_name' => $checkDate->format('l'),
          'is_today' => ($i === 0),
        ];
      }
    }

    return $dates;
  }

  /**
   * Generates time slots for a given doctor on a given date.
   */
  public function getSlotsForDate(NodeInterface $doctorNode, string $dateStr): array {
    $startTimeStr = '09:00';
    $endTimeStr = '17:00';
    $duration = 20;

    if ($doctorNode->hasField('field_shift_start') && !$doctorNode->get('field_shift_start')->isEmpty()) {
      $startTimeStr = trim($doctorNode->get('field_shift_start')->value);
    }
    if ($doctorNode->hasField('field_shift_end') && !$doctorNode->get('field_shift_end')->isEmpty()) {
      $endTimeStr = trim($doctorNode->get('field_shift_end')->value);
    }
    if ($doctorNode->hasField('field_slot_duration') && !$doctorNode->get('field_slot_duration')->isEmpty()) {
      $duration = (int) $doctorNode->get('field_slot_duration')->value;
      if ($duration <= 0) {
        $duration = 20;
      }
    }

    // Parse start and end time
    $start = \DateTime::createFromFormat('H:i', $startTimeStr);
    $end = \DateTime::createFromFormat('H:i', $endTimeStr);

    if (!$start || !$end || $start >= $end) {
      $start = \DateTime::createFromFormat('H:i', '09:00');
      $end = \DateTime::createFromFormat('H:i', '17:00');
    }

    // Query booked slots for this doctor on this date
    $bookedSlots = $this->getBookedSlots($doctorNode->id(), $dateStr);

    $slots = [];
    $current = clone $start;

    while ($current < $end) {
      $slotStart = clone $current;
      $slotEnd = clone $current;
      $slotEnd->modify("+{$duration} minutes");

      if ($slotEnd > $end) {
        break;
      }

      $slotLabel = $slotStart->format('h:i A') . ' - ' . $slotEnd->format('h:i A');
      $isBooked = in_array($slotLabel, $bookedSlots, TRUE);

      $slots[] = [
        'time' => $slotLabel,
        'start_raw' => $slotStart->format('H:i'),
        'booked' => $isBooked,
        'available' => !$isBooked,
      ];

      $current->modify("+{$duration} minutes");
    }

    return $slots;
  }

  /**
   * Retrieves booked time slots from appointments.
   */
  public function getBookedSlots(int $doctorId, string $dateStr): array {
    $storage = $this->entityTypeManager->getStorage('node');
    $query = $storage->getQuery()
      ->condition('type', 'appointment')
      ->condition('field_appointment_doctor', $doctorId)
      ->condition('field_appointment_date', $dateStr)
      ->condition('field_appointment_status', 'cancelled', '<>')
      ->accessCheck(FALSE);

    $nids = $query->execute();
    if (empty($nids)) {
      return [];
    }

    $appointments = $storage->loadMultiple($nids);
    $booked = [];
    foreach ($appointments as $apt) {
      if ($apt->hasField('field_appointment_time_slot') && !$apt->get('field_appointment_time_slot')->isEmpty()) {
        $booked[] = trim($apt->get('field_appointment_time_slot')->value);
      }
    }

    return $booked;
  }

  /**
   * Check if a specific slot is available.
   */
  public function isSlotAvailable(int $doctorId, string $dateStr, string $slotLabel): bool {
    $booked = $this->getBookedSlots($doctorId, $dateStr);
    return !in_array($slotLabel, $booked, TRUE);
  }

}
