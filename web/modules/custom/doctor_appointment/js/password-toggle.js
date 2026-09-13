(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.passwordToggle = {
    attach: function (context) {
      once('password-toggle', '[data-drupal-selector="password-toggle"]', context).forEach(function (button) {
        var $button = $(button);
        var $wrapper = $button.closest('.password-field-wrapper');
        var $input = $wrapper.find('input[type="password"]');

        if (!$input.length) {
          return;
        }

        $button.on('click', function (e) {
          e.preventDefault();

          var isPassword = $input.attr('type') === 'password';
          var newType = isPassword ? 'text' : 'password';

          $input.attr('type', newType);

          $button.attr('aria-label', isPassword ? Drupal.t('Hide password') : Drupal.t('Show password'));
          $button.attr('aria-pressed', isPassword);
        });
      });
    }
  };
})(jQuery, Drupal, once);