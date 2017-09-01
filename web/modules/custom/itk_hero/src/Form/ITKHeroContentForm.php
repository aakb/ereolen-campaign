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

    // Add front page wrapper.
    $form['wrapper'] = [
      '#title' => t('ITK Hero'),
      '#type' => 'details',
      '#weight' => '1',
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

    // Set the rest of the configuration values.
    $this->getBaseConfig()->setMultiple([
      'itk_hero_lead' => $form_state->getValue('itk_hero_lead'),
      'itk_hero_sub' => $form_state->getValue('itk_hero_sub')['value'],
    ]);

    drupal_flush_all_caches();
  }
}
