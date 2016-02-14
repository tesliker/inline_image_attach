<?php

/**
* @file
* Contains \Drupal\inline_image_attach\Form\InlineImageAttachForm
*/

namespace Drupal\inline_image_attach\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class InlineImageAttachForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['inline_image_attach.settings'];
  }

  /**
  * {@inheridoc}
  */
  public function getFormId() {
    return 'inline_image_attach_form';
  }
  
  /**
  * {@inheridoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $nodes = entity_get_bundles('node');
    foreach ($nodes as $k => $node) {
      $type = str_replace(' ', '_', strtolower($node['label']));
      $fields = \Drupal::entityManager()->getFieldDefinitions('node', $type);
      $options = array('none' => 'None');
      foreach ($fields as $field => $value) {
        if (strpos($field, 'field_') !== FALSE || strpos($field, 'body') !== FALSE) {
          $options[$field] = $field;
        }
      }

      $config = $this->config('inline_image_attach.settings');

      if (count($options) > 1) {
        $form['inline_image_attach_' . $type] = array(
          '#type' => 'fieldset',
          '#title' => $type,
          '#description' => $this->t('Display controls in the bottom right corner'),
        );
        $form['inline_image_attach_' . $type]['iia_wysiwyg_' . $type] = array(
          '#type' => 'select',
          '#title' => 'WYSIWYG Field',
          '#description' => $this->t('Display controls in the bottom right corner'),
          '#default_value' => $config->get('iia_wysiwyg_' . $type),
          '#group' => 'inline_image_attach_' . $type,
          '#options' => $options,
        );
        $form['inline_image_attach_' . $type]['iia_image_' . $type] = array(
          '#type' => 'select',
          '#title' => 'Image Field',
          '#description' => $this->t('Display controls in the bottom right corner'),
          '#default_value' => $config->get('iia_image_' . $type),
          '#group' => 'inline_image_attach_' . $type,
          '#options' => $options,
        );
      }
    }

    return parent::buildForm($form, $form_state);
  }

  /**
  * {@inheritdoc}
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $values = $form_state->getValue();
    $config = \Drupal::service('config.factory')->getEditable('inline_image_attach.settings');
    foreach ($values as $key => $value) {
      if (strpos($key, 'iia_wysiwyg') !== FALSE || strpos($key, 'iia_image') !== FALSE) {
        $config->set($key, $value);
      }
    }
    $config->save();
  }
}