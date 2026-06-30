<?php

/**
 * urlValidator validates and sanitizes a URL.
 *
 * @author Ajay Makwana <ajay@webplanex.net>
 */
class urlValidator extends sfValidatorUrl {

  protected function doClean($value) {
    $clean = (string) $value;

    // If the URL doesn't start with "http", add "http://".
    if (!preg_match('/https?:\/\/.+/', $clean)) {
      $clean = 'http://' . $clean;
    }

    // Add a trailing slash if the URL doesn't have one.
    if (!preg_match('/https?:\/\/.+\//', $clean)) {
      $clean .= '/';
    }

    // If the URL still isn't valid after that, it probably wasn't close enough to begin with.
    if (!preg_match($this->generateRegex(), $clean)) {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }

    return $clean;
  }

}