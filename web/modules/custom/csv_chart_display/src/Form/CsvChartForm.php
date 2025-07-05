<?php

namespace Drupal\csv_chart_display\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CsvChartForm extends FormBase {

  public function getFormId() {
    return 'csv_chart_display_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['csv_file'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload a CSV file'),
      '#description' => $this->t('Upload a CSV file with two columns: label and numeric value.'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Generate Chart'),
    ];

    // Always attach the library and render canvas
    $form['chart'] = [
      '#markup' => '<canvas id="csvChart" width="600" height="400"></canvas>',
      '#attached' => [
        'library' => ['csv_chart_display/chart'],
      ],
    ];

    // Attach chart data to drupalSettings if available
    if ($form_state->get('chart_data')) {
      $form['#attached']['drupalSettings']['csvChartData'] = $form_state->get('chart_data');
    }

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $validators = ['file_validate_extensions' => ['csv']];
    $file = file_save_upload('csv_file', $validators, FALSE, 0);

    if ($file) {
      $real_path = \Drupal::service('file_system')->realpath($file->getFileUri());
      $rows = array_map('str_getcsv', file($real_path));

      if (count($rows[0]) < 2) {
        $this->messenger()->addError($this->t('CSV must have at least two columns.'));
        return;
      }

      $labels = [];
      $values = [];

      foreach ($rows as $index => $row) {
        if ($index === 0) {
          // Optionally verify header columns here
          continue; // Skip header row
        }
        $labels[] = $row[0];
        $values[] = is_numeric($row[1]) ? (float) $row[1] : 0;
      }

      $form_state->set('chart_data', [
        'labels' => $labels,
        'values' => $values,
      ]);
      $form_state->setRebuild(TRUE);
    }
    else {
      $this->messenger()->addError($this->t('No file uploaded or invalid file.'));
    }
  }
}