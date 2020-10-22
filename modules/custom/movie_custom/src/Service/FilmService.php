<?php

namespace Drupal\movie_custom\Service;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\movie_custom\FilmServiceInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

class FilmService implements FilmServiceInterface
{
  const NODE = 'node';
  const TAXONOMY_TERM = 'taxonomy_term';
  const FILE = 'file';

  protected $nodeStorage;
  protected $taxonomyStorage;
  protected $fileStorage;
  protected $messenger;
  protected $entities = [];

  public function __construct (EntityTypeManagerInterface $entityTypeManager, MessengerInterface $messenger)
  {
    $this->nodeStorage = $entityTypeManager->getStorage(self::NODE);
    $this->taxonomyStorage = $entityTypeManager->getStorage(self::TAXONOMY_TERM);
    $this->fileStorage = $entityTypeManager->getStorage(self::FILE);
    $this->messenger = $messenger;
  }

  /**
  * {@inheritdoc}
  */
  public function createFilm (array $data)
  {
    $actors = explode(', ', $data['Actors']);
    $director = explode(', ', $data['Director']);
    $countries = explode(', ', $data['Country']);
    $genres = explode(', ',  $data['Genre']);
    $productions = explode(', ', $data['Production']);
    $releaseDate = date('Y-m-d', strtotime($data['Released']));
    $ratingImdb = $data['imdbRating'];
    $runtime = (int) $data['Runtime'];
    $imdbID = $data['imdbID'];

    $referenceActors = $this->referenceEntitiesMultiple($actors, 'person');
    $referenceDirector = $this->referenceEntitiesMultiple($director, 'person');
    $referenceCountry = $this->referenceEntitiesMultiple($countries, 'contries');
    $referenceGenre = $this->referenceEntitiesMultiple($genres, 'tags');
    $referenceProduction = $this->referenceEntitiesMultiple($productions, 'production');
    $referenceFile= $this->referenceFile($data['Poster']);

    $film = $this->checkFilm($imdbID);

    if (empty($film)) {
      $values = [
        'title' => $data['Title'],
        'type' => 'film',
        'field_poster' => $referenceFile,
        'field_actors' => $referenceActors,
        'field_director' => $referenceDirector,
        'field_film_country' => $referenceCountry,
        'field_film_genre' => $referenceGenre,
        'field_film_production' => $referenceProduction,
        'field_year' => $releaseDate,
        'field_rank_imdb' => $ratingImdb,
        'field_runtime' => $runtime,
        'field_imdbid' => $imdbID,
      ];

      $film = $this->nodeStorage->create($values);
      $film->save();
      $this->entities[] = $film;
    } else {
      $this->messenger->addMessage(t('This film <b>@title</b> already exists', ['@title' => $data['Title']]), MessengerInterface::TYPE_WARNING);
    }

    return $this->entities;
  }

  protected function referenceEntitiesMultiple (array $titles, $type)
  {
    $ids = [];
    foreach ($titles as $title) {
      $ids[] = $type == 'person' ? $this->referenceNode($title, $type) : $this->referenceTaxonomy($title, $type);
    }

    return $ids;
  }

  protected function referenceNode ($title, $type)
  {
    $values = [
      'type' => $type,
      'title' => $title
    ];

    $nodes = $this->nodeStorage->loadByProperties($values);

    if ($nodes) {
      $node = reset($nodes);
    } else {
      $node = $this->nodeStorage->create($values);
      $node->save();
      $this->entities[] = $node;
    }

    return $node->id();
  }

  protected function referenceTaxonomy ($title, $type)
  {
    $values = [
      'vid' => $type,
      'name' => $title
    ];

    $terms = $this->taxonomyStorage->loadByProperties($values);
    if ($terms) {
      $term = reset($terms);
    } else {
      $term = $this->taxonomyStorage->create($values);
      $term->save();
      $this->entities[] = $term;
    }

    return $term->id();
  }

  protected function checkFilm ($imdbID)
  {
    $query = $this->nodeStorage->getQuery();
    $query->condition('type', 'film');
    $query->condition('field_imdbid', $imdbID);

    return $query->execute();
  }

  protected function referenceFile ($url)
  {
    $url = str_replace('._V1_SX300', '', $url);
    $data = file_get_contents($url);
    $fileName = pathinfo($url, PATHINFO_BASENAME);
    $file = file_save_data($data, 'public://posters/' . $fileName, FileSystemInterface::EXISTS_RENAME);

    return $file->id();
  }
}
