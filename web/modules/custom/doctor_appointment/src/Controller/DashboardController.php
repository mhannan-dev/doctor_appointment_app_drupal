<?php

namespace Drupal\doctor_appointment\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for Doctor Appointment role dashboards.
 */
class DashboardController extends ControllerBase {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a DashboardController object.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user account.
   */
  public function __construct(AccountInterface $current_user) {
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user')
    );
  }

  /**
   * Renders the public marketing home page.
   *
   * @return array
   *   A render array.
   */
  public function homepage(): array {
    $dashboard_url = NULL;
    if ($this->currentUser->isAuthenticated()) {
      $roles = $this->currentUser->getRoles();
      if (in_array('doctor', $roles, TRUE)) {
        $dashboard_url = Url::fromRoute('doctor_appointment.doctor_dashboard')->toString();
      }
      elseif (in_array('clinic_admin', $roles, TRUE)) {
        $dashboard_url = Url::fromRoute('doctor_appointment.admin_dashboard')->toString();
      }
      else {
        $dashboard_url = Url::fromRoute('doctor_appointment.patient_dashboard')->toString();
      }
    }

    return [
      '#theme' => 'doctor_appointment_home',
      '#dashboard_url' => $dashboard_url,
      '#attached' => [
        'library' => [
          'doctor_appointment/dashboard',
        ],
      ],
    ];
  }

  /**
   * Renders the patient dashboard.
   *
   * @return array
   *   A render array.
   */
  public function patientDashboard(): array {
    return [
      '#theme' => 'patient_dashboard',
      '#user_name' => $this->currentUser->getDisplayName(),
      '#upcoming_count' => 0,
      '#completed_count' => 0,
      '#pending_count' => 0,
      '#attached' => [
        'library' => [
          'doctor_appointment/dashboard',
        ],
      ],
    ];
  }

  /**
   * Renders the doctor dashboard.
   *
   * @return array
   *   A render array.
   */
  public function doctorDashboard(): array {
    return [
      '#theme' => 'doctor_dashboard',
      '#doctor_name' => $this->currentUser->getDisplayName(),
      '#today_count' => 0,
      '#pending_count' => 0,
      '#attached' => [
        'library' => [
          'doctor_appointment/dashboard',
        ],
      ],
    ];
  }

  /**
   * Renders the clinic admin dashboard.
   *
   * @return array
   *   A render array.
   */
  public function adminDashboard(): array {
    return [
      '#theme' => 'clinic_admin_dashboard',
      '#attached' => [
        'library' => [
          'doctor_appointment/dashboard',
        ],
      ],
    ];
  }

}
