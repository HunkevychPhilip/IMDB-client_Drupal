<?php

namespace Drupal\movie_custom\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\movie_custom\Client\ImdbApiClient;
use Drupal\movie_custom\Service\FilmService;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceForm extends FormBase
{

  /**
   * @var Drupal\movie_custom\Client\ImdbApiClient
   */
  protected $client;

  /**
   * @var Drupal\movie_custom\Service\FilmService
   */
  protected $filmService;

  function __construct (ImdbApiClient $client, FilmService $filmService, MessengerInterface $messenger)
  {
      $this->client = $client;
      $this->filmService = $filmService;
      $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId ()
  {
      return 'service-form';
  }

  public static function create (ContainerInterface $container)
  {
      return new static(
          $container->get('movie_custom.client'),
          $container->get('movie_custom.film_service'),
          $container->get('messenger')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm (array $form, FormStateInterface $form_state)
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
  public function submitForm (array &$form, FormStateInterface $form_state)
  {
    $name = $form_state->getValue('name');

    $data = $this->client->getFilmInfo($name);

    $entities = $this->filmService->createFilm($data);

    foreach ($entities as $entity) {
        $this->messenger->addMessage(t('<b>@title</b> @type was created', ['@type' => $entity->bundle(), '@title' => $entity->label()]));
    }
  }
}
