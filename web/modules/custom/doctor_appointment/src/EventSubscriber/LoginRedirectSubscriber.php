<?php

namespace Drupal\doctor_appointment\EventSubscriber;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirects users to their corresponding dashboard upon logging in.
 */
class LoginRedirectSubscriber implements EventSubscriberInterface {

  protected AccountProxyInterface $currentUser;

  public function __construct(AccountProxyInterface $currentUser) {
    $this->currentUser = $currentUser;
  }

  public function checkForRedirect(RequestEvent $event) {
    $request = $event->getRequest();

    // Check if the current route is user.login and request has a post action or user is just logged in
    $route_name = $request->attributes->get('_route');

    if ($this->currentUser->isAuthenticated()) {
      $roles = $this->currentUser->getRoles();

      // If user is accessing /user or /user/login, redirect to role portal
      if (in_array($route_name, ['user.login', 'user.page', 'entity.user.canonical'], TRUE)) {
        if (in_array('doctor', $roles, TRUE)) {
          $event->setResponse(new RedirectResponse(Url::fromRoute('doctor_appointment.doctor_dashboard')->toString()));
        }
        elseif (in_array('clinic_admin', $roles, TRUE)) {
          $event->setResponse(new RedirectResponse(Url::fromRoute('doctor_appointment.clinic_admin_dashboard')->toString()));
        }
        elseif (in_array('patient', $roles, TRUE)) {
          $event->setResponse(new RedirectResponse(Url::fromRoute('doctor_appointment.patient_dashboard')->toString()));
        }
      }
    }
  }

  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => [['checkForRedirect', 10]],
    ];
  }

}
