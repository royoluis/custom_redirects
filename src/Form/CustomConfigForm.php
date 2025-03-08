<?php

namespace Drupal\custom_redirects\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class CustomConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return "custom_redirects_settings_form";
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'custom_redirects.settings'
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('custom_redirects.settings');

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => t('URL to redirect to after logging out'),
      '#default_value' => $config->get('url'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();
    $config = $this->config('custom_redirects.settings');
    $config->set('url', $form_state->getValue('url'));
    $config->save();
    drupal_flush_all_caches();

    parent::submitForm($form, $form_state);
  }

}