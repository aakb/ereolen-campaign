<?php
/**
 * @file
 * Contains Drupal\itk_hero\Form\ITKHeroContentForm.
 */

namespace Drupal\itk_hero\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Class ITKHeroForm
 *
 * @package Drupal\itk_hero\Form
 */
class ITKHeroContentForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itk_hero_settings';
  }

  /**
   * Get key/value storage for base config.
   *
   * @return object
   */
  private function getBaseConfig() {
    return \Drupal::getContainer()->get('itk_hero.config');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getBaseConfig();

    // Header wrapper
    $form['wrapper_header'] = [
      '#title' => t('ITK Header'),
      '#type' => 'details',
      '#weight' => '1',
      '#open' => TRUE,
    ];

    $fids = [];
    if (!empty($input)) {
      if (!empty($input['itk_header_image'])) {
        $fids[0] = $form_state->getValue('itk_header_image');
      }
    }
    else {
      $fids[0] = $config->get('itk_header_image', '');
    }

    $form['wrapper_header']['itk_header_image'] = [
      '#title' => t('Header image'),
      '#type' => 'managed_file',
      '#default_value' => ($fids[0]) ? $fids : '',
      '#upload_location' => 'public://',
      '#required' => true,
      '#weight' => '3',
      '#open' => TRUE,
      '#description' => t('The image used for the header.'),
    ];

    // Add front page wrapper.
    $form['wrapper'] = [
      '#title' => t('ITK Hero'),
      '#type' => 'details',
      '#weight' => '2',
      '#open' => TRUE,
    ];

    $form['wrapper']['itk_hero_lead'] = [
      '#title' => t('Lead text'),
      '#type' => 'textfield',
      '#default_value' => $config->get('itk_hero_lead'),
      '#weight' => '2',
    ];

    $form['wrapper']['itk_hero_sub'] = [
      '#title' => t('Sub text'),
      '#type' => 'text_format',
      '#default_value' => $config->get('itk_hero_sub'),
      '#weight' => '3',
      '#format' => 'plain_text',
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#attributes' => ['class' => ['button--primary']],
      '#value' => t('Save content'),
      '#weight' => '6',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message('Settings saved');

    // Fetch the file id previously saved.
    $config = $this->getBaseConfig();
    $old_fid = $config->get('itk_header_image', '');

    // Load the file set in the form.
    $value = $form_state->getValue('itk_header_image');
    $form_fid = count($value) > 0 ? $value[0] : 0;
    $file = ($form_fid) ? File::load($form_fid) : FALSE;

    // If a file is set.
    if ($file) {
      $fid = $file->id();
      // Check if the file has changed.
      if ($fid != $old_fid) {

        // Remove old file.
        if ($old_fid) {
          $this->removeFile($old_fid);
        }

        // Add file to file_usage table.
        \Drupal::service('file.usage')->add($file, 'itk_header', 'user', '1');
      }
    }
    else {
      // If old file exists but no file set in form, remove old file.
      if ($old_fid) {
        $this->removeFile($old_fid);
      }
    }

    // Set the rest of the configuration values.
    $this->getBaseConfig()->setMultiple([
      'itk_header_image' => $file ? $file->id() : NULL,
      'itk_hero_lead' => $form_state->getValue('itk_hero_lead'),
      'itk_hero_sub' => $form_state->getValue('itk_hero_sub')['value'],
    ]);

    drupal_flush_all_caches();
  }


  /**
   * Deletes a a file from file usage table.
   *
   * @param int $fid
   *   The file id of the file to delete.
   */
  private function removeFile($fid) {
    // Load and delete old file.
    $file = File::load($fid);
    \Drupal::service('file.usage')->delete($file, 'itk_header', 'user', '1', '1');
  }
}
