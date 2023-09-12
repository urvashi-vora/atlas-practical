<?php

namespace Drupal\timezone_now\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\timezone_now\ConvertTimezone;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Timezone' block.
 *
 * @Block(
 *   id = "timezone_now_block",
 *   admin_label = @Translation("Timezone Now Block"),
 * )
 */
class TimezoneNowBlock extends BlockBase implements BlockPluginInterface, ContainerFactoryPluginInterface {

  /**
   * Converts the Timezone.
   *
   * @var \Drupal\timezone_now\ConvertTimezone
   */
  protected $convertTimezone;

  /**
   * The configuration factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a TimezoneNowBlock object.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConvertTimezone $convertTimezone, ConfigFactoryInterface $configFactory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->convertTimezone = $convertTimezone;
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('timezone_now.convert_timezone'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->configFactory->get('timezone_now.config');

    $country = $config->get('country');
    $city = $config->get('city');
    $timezone = $config->get('timezone');

    $current_time = $this->convertTimezone->getCurrentTime($timezone);

    $formatted_time = $current_time['current_time'];

    $build = [
      '#theme' => 'timezone_now_block',
      '#country' => $country,
      '#city' => $city,
      '#formatted_time' => $formatted_time,
      '#attached' => [
        'library' => [
          'timezone_now/custom',
        ],
      ],
    ];
    
    $build['#cache']['contexts'] = ['timezone'];
    $build['#cache']['max-age'] = 0;
    
    return $build;
  }

}
