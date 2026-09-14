<?php

namespace Drupal\doctor_appointment\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Url;

/**
 * Controller for the multi-step Doctor Appointment booking wizard.
 */
class BookingController extends ControllerBase
{

  public function bookingForm(NodeInterface $node, Request $request)
  {
    if ($node->bundle() !== 'doctor_profile') {
      throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    }

    /** @var \Drupal\doctor_appointment\Service\SlotGeneratorService $slotService */
    $slotService = \Drupal::service('doctor_appointment.slot_generator');

    $availableDates = $slotService->getAvailableDates($node, 14);

    $selectedDate = $request->query->get('date');
    if (empty($selectedDate) && !empty($availableDates)) {
      $selectedDate = $availableDates[0]['date'];
    }

    $slots = [];
    if (!empty($selectedDate)) {
      $slots = $slotService->getSlotsForDate($node, $selectedDate);
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

    $doctor = [
      'id' => $node->id(),
      'name' => $node->label(),
      'specialty' => $specialtyName,
      'chamber' => $chamberName,
      'degrees' => $node->hasField('field_doctor_degrees') ? $node->get('field_doctor_degrees')->value : '',
      'fee' => number_format((float) ($node->hasField('field_consultation_fee') ? $node->get('field_consultation_fee')->value : 800), 0),
      'slot_duration' => $node->hasField('field_slot_duration') ? $node->get('field_slot_duration')->value : 20,
    ];

    $currentUser = $this->currentUser();
    $currentUserData = [
      'is_authenticated' => $currentUser->isAuthenticated(),
      'name' => $currentUser->isAuthenticated() ? $currentUser->getDisplayName() : '',
      'email' => $currentUser->isAuthenticated() ? $currentUser->getEmail() : '',
    ];

    return [
      '#theme' => 'booking_wizard',
      '#doctor' => $doctor,
      '#available_dates' => $availableDates,
      '#selected_date' => $selectedDate,
      '#slots' => $slots,
      '#current_user_data' => $currentUserData,
      '#attached' => [
        'library' => ['doctor_appointment/app_theme'],
      ],
    ];
  }

  public function submitBooking(NodeInterface $node, Request $request)
  {
    if ($node->bundle() !== 'doctor_profile') {
      throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    }

    $dateStr = trim($request->request->get('appointment_date', ''));
    $timeSlot = trim($request->request->get('time_slot', ''));
    $patientName = trim($request->request->get('patient_name', ''));
    $patientPhone = trim($request->request->get('patient_phone', ''));
    $patientAge = (int) $request->request->get('patient_age', 0);
    $patientGender = trim($request->request->get('patient_gender', 'Male'));
    $complaint = trim($request->request->get('patient_complaint', ''));

    if (empty($dateStr) || empty($timeSlot) || empty($patientName) || empty($patientPhone)) {
      $this->messenger()->addError($this->t('Please fill in all required fields (date, slot, name, and phone).'));
      return new RedirectResponse(Url::fromRoute('doctor_appointment.booking', ['node' => $node->id()])->toString());
    }

    /** @var \Drupal\doctor_appointment\Service\SlotGeneratorService $slotService */
    $slotService = \Drupal::service('doctor_appointment.slot_generator');

    // Concurrency check: verify slot is still available
    if (!$slotService->isSlotAvailable($node->id(), $dateStr, $timeSlot)) {
      $this->messenger()->addError($this->t('Sorry, slot @slot on @date has just been booked. Please pick another slot.', [
        '@slot' => $timeSlot,
        '@date' => $dateStr,
      ]));
      return new RedirectResponse(Url::fromRoute('doctor_appointment.booking', ['node' => $node->id(), 'date' => $dateStr])->toString());
    }

    // Generate unique reference number
    $refNumber = 'APT-' . date('Ym') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

    $currentUser = $this->currentUser();
    $patientUid = $currentUser->isAuthenticated() ? $currentUser->id() : 1;

    $appointment = Node::create([
      'type' => 'appointment',
      'title' => $refNumber . ' - ' . $patientName . ' with ' . $node->label(),
      'status' => 1,
      'uid' => $patientUid,
      'field_appointment_ref' => $refNumber,
      'field_appointment_doctor' => $node->id(),
      'field_appointment_patient' => $patientUid,
      'field_appointment_date' => $dateStr,
      'field_appointment_time_slot' => $timeSlot,
      'field_patient_name' => $patientName,
      'field_patient_phone' => $patientPhone,
      'field_patient_age' => $patientAge > 0 ? $patientAge : 30,
      'field_patient_gender' => $patientGender,
      'field_patient_complaint' => $complaint,
      'field_appointment_status' => 'confirmed',
    ]);
    $appointment->save();

    $this->messenger()->addStatus($this->t('Appointment booked successfully! Your Reference ID is: @ref', ['@ref' => $refNumber]));

    // If request wants JSON (e.g. AJAX)
    if ($request->isXmlHttpRequest()) {
      return new JsonResponse([
        'status' => 'success',
        'ref' => $refNumber,
        'redirect' => Url::fromRoute('doctor_appointment.track', [], ['query' => ['ref' => $refNumber]])->toString(),
      ]);
    }

    return new RedirectResponse(Url::fromRoute('doctor_appointment.track', [], ['query' => ['ref' => $refNumber]])->toString());
  }
}
