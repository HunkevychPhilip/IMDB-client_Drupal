<?php

namespace Drupal\movie_custom\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\movie_custom\Client\ImdbApiClient;
use Drupal\movie_custom\Service\FilmService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceForm extends FormBase {

    /**
     * @var Drupal\movie_custom\Client\ImdbApiClient
     */
    protected $client;

    /**
     * @var Drupal\movie_custom\Service\FilmService
     */
    protected $filmService;

    function __construct(ImdbApiClient $client, FilmService $filmService)
    {
        $this->client = $client;
        $this->filmService = $filmService;

    }

    /**
     * {@inheritdoc}
     */
  public function getFormId()
    {
        return 'service_form';
    }

public static function create(ContainerInterface $container) {
    return new static(
        $container->get('movie_custom.client'),
        $container->get('movie_custom.film_service')
    );
}

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $form['name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Film ID'),

        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
       $name = $form_state ->getValue('name');
       $data = $this->client->getFilmInfo($name);

       if (isset($data['Title'])) {
        $this->messenger->addError($data['Title']);
       }

    $this->filmService->createFilm($data);
    

    }
}
