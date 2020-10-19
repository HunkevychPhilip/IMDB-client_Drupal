<?php

namespace Drupal\movie_custom\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements Form for API settings.
 */
class ImdbApiSettings extends ConfigFormBase
{

    /** 
     * Config settings.
     *
     * @var string
     */
    const SETTINGS = 'movie_custom.imdb_settings';

    /** 
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'movie_custom_admin_settings';
    }

    /** 
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            static::SETTINGS,
        ];
    }

    /** 
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config(static::SETTINGS);

        $form['api_key'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Set API key'),
            '#default_value' => $config->get('api_key'),
        ];

        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('SET'),
        ];

        return $form;
    }

    /** 
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Retrieve the configuration.
        $this->config(static::SETTINGS)
            // Set the submitted configuration setting.
            ->set('api_key', $form_state->getValue('api_key'))
            ->save();

        $this->messenger()->addStatus($this->t('API key has been set'));
    }
}
