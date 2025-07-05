<?php

namespace Drupal\highcharts_intg\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Extending ControllerBase Class.
 */
class HighchartsController extends ControllerBase {

  /**
   * Retrieving chart data from Data Sheet.
   */
  public function chartdata($fid) {
    $config = $this->config('highcharts.config');
    $fileuri = $config->get("fileuri$fid");
    $chart_type = $config->get("chart_type$fid");
    $chart_data = [];
    if ($fileuri) {
      $file_open = fopen($fileuri, 'r');
      while (!feof($file_open)) {
        $chart_data[] = fgetcsv($file_open);
      }
      fclose($file_open);
    }
    $chart_data_formatted = $this->chartDataFormatting($chart_data);
    $chart_data_formatted['fid'] = $fid;
    $chart_data_formatted['chart_type'] = $chart_type;

    return new JsonResponse($chart_data_formatted);
  }

  /**
   * Formatting Chart Data.
   */
  public function chartDataFormatting($chart_data) {
    $chart_data_formatted = [];
    $chart_data_formatted['labels'] = $chart_data[0];
    unset($chart_data[0]);
    $i = 0;
    foreach ($chart_data_formatted['labels'] as $key => $label) {

      if (str_contains($label, 'Coord_')) {
        $chart_data_formatted['co_ord'][$i]['name'] = str_replace('Coord_', '', $label);
        $chart_data_formatted['co_ord'][$i]['data'] = array_map([$this, "converttonum"], array_column($chart_data, $key));
        $i++;
      }
      elseif (str_contains($label, '-axis_')) {
        $actual_label = preg_split('/(_)/', $label);

        $machine_label = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (strtolower(str_replace('-', '_', $actual_label[0]))));
        $chart_data_formatted[$machine_label]['name'] = $actual_label[1];
        $chart_data_formatted[$machine_label]['values'] = array_column($chart_data, $key);
      }
    }
    unset($chart_data_formatted['labels']);
    return $chart_data_formatted;
  }

  /**
   * Converting string to numeric.
   */
  public function converttonum($v) {
    return $v + 0;
  }

}
