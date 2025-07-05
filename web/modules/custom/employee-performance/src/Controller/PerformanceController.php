<?php

namespace Drupal\employee_performance\Controller;

use Drupal\Core\Controller\ControllerBase;

class PerformanceController extends ControllerBase {

  public function report() {
    $employees = ['Alice', 'Bob', 'Carol', 'Dan'];
    $scores = [85, 78, 92, 88];

    return [
      '#type' => 'html_tag',
      '#tag' => 'canvas',
      '#attributes' => [
        'id' => 'employeeChart',
        'width' => '600',
        'height' => '400',
      ],
      '#attached' => [
        'library' => [
          'employee_performance/chart',
        ],
        'drupalSettings' => [
          'employeePerformance' => [
            'labels' => $employees,
            'data' => $scores,
          ],
        ],
      ],
    ];
  }

}