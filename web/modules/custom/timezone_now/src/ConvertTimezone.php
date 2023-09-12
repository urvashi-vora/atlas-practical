<?php

namespace Drupal\timezone_now;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Component\Datetime\TimeInterface;

/**
 * Provides a service to convert and format the current time based on timezone.
 */
class ConvertTimezone {

  /**
   * Config factory interface.
   *
   * @var Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Time Interface.
   *
   * @var Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * Constructs a new ConvertTimezone object.
   *
   * @param Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config factory interface.
   * @param Drupal\Component\Datetime\TimeInterface $time
   *   Time Interface.
   */
  public function __construct(ConfigFactoryInterface $configFactory, TimeInterface $time) {
    $this->configFactory = $configFactory;
    $this->time = $time;
  }

  /**
   * Gets the current time and formats it based on the specified timezone.
   *
   * @param string $timezone
   *   The timezone to convert and format the current time for.
   *
   * @return array
   *   An array containing country, city, and formatted current time.
   */
  public function getCurrentTime($timezone) {
    $config = $this->configFactory->get('timezone_now.settings');
    $country = $config->get('country');
    $city = $config->get('city');

    $current_time = $this->time->getCurrentTime();

    $timezone_obj = new \DateTimeZone($timezone);
    $datetime = new \DateTime();
    $datetime->setTimestamp($current_time);
    $datetime->setTimezone($timezone_obj);
    $formatted_time = $datetime->format('jS M Y - h:i A');

    return [
      'country' => $country,
      'city' => $city,
      'current_time' => $formatted_time,
    ];
  }

}
