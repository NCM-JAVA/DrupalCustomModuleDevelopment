<?php

namespace Drupal\highcharts_intg\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Highcharts Plugin for displaying the chart.
 *
 * @Block(
 *      id = "highcharts_plugin",
 *      admin_label = "Highcharts Plugin",
 *      category = "Custom"
 * ) *
 */
class HighchartsPluginBlock extends BlockBase {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['fid' => NULL];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $fid = $config['fid'];
    $highchart_libraries[] = 'highcharts_intg/global';
    foreach ($config['libraries'] as $library) {
      switch ($library) {
        case 'export':
          $highchart_libraries[] = 'highcharts_intg/highcharts_lib.export';
          $highchart_libraries[] = 'highcharts_intg/highcharts_lib.series_label';
          break;

        case 'data_export':
          $highchart_libraries[] = 'highcharts_intg/highcharts_lib.export_data';
          break;

        case 'accessibility':
          $highchart_libraries[] = 'highcharts_intg/highcharts_lib.accessibility';
          break;
      }
    }
    return [
      '#theme' => 'highcharts_block',
      '#fid' => $fid,
      '#attached' => [
        'library' => $highchart_libraries,
      ],
    ];
  }

  /**
   * Inherited.
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    if (!is_dir('public://upload')) {
      mkdir('public://upload');
    }
    $chart_types = ['line' => 'Line', 'bar' => 'Bar'];
    $form['file_upload'] = [
      '#type' => 'file',
      '#title' => 'Data upload',
      '#size' => 40,
      '#description' => $this->t('Upload only csv file'),
    ];
    $form['fid'] = ['#type' => 'hidden'];
    $form['uploaded_filename'] = ['#type' => 'hidden'];
    $form['libraries'] = [
      '#type' => 'select',
      '#title' => 'Library',
      '#options' => [
        'export' => $this->t('Export'),
        'accessibility' => $this->t('Accessibility'),
        'data_export' => $this->t('Data Export'),
      ],
      '#multiple' => TRUE,
      '#default_value' => $this->configuration['libraries'],
    ];
    $form['chart_type'] = [
      '#type' => 'select',
      '#title' => 'Chart Type',
      '#options' => $chart_types,
      '#required' => TRUE,
      '#default_value' => $this->configuration['chart_type'],
    ];
    return $form;
  }

  /**
   * Inherited.
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    $file_name = $form_state->getValue('file_upload');
    $fid = $this->configuration['fid'];
    next($file_name);
    $file_mimetype = next($file_name);
    if ($file_name) {
      $file = file_save_upload('settings', ['file_validate_extensions' => ['csv']], 'public://upload');
      if (!empty($file[0])) {
        if ($file[0]->getMimeType() != 'text/csv') {
          $form_state->setErrorByName('file_upload', $this->t('Please upload CSV format file'));
        }
        else {
          $form_state->setValue('uploaded_filename', $file[0]->getFileUri());
          $form_state->setValue('fid', $file[0]->id());
        }
      }
      elseif ($file_mimetype != 'text/csv') {
        $form_state->setErrorByName('file_upload', $this->t('Please upload CSV format file'));
      }
    }
    elseif (!$fid) {
      $form_state->setErrorByName('file_upload', $this->t('Please upload the CSV file'));
    }
  }

  /**
   * Inherited.
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $libraries = $form_state->getValue('libraries');
    $chart_type = $form_state->getValue('chart_type');
    $fid = $form_state->getValue('fid');
    $config = $this->configFactory->getEditable('highcharts.config');
    if (!empty($fid)) {
      $uploaded_filename = $form_state->getValue('uploaded_filename');
      $this->configuration['fid'] = $fid;
      $config->set("fileuri$fid", $uploaded_filename);
    }
    $fid = $this->configuration['fid'];
    $config->set("chart_type$fid", $chart_type)->save();
    $this->configuration['libraries'] = $libraries;
    $this->configuration['chart_type'] = $chart_type;
  }

}
