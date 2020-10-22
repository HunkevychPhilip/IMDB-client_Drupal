<?php

namespace Drupal\movie_custom\Service;

use Drupal\movie_custom\FilmServiceInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

class FilmService implements FilmServiceInterface {

    protected $nodeStorage;

    protected $taxonomyStorage;
     
    public function __construct(EntityTypeManagerInterface $entityTypeManager)
    {
        $this->nodeStorage = $entityTypeManager->getStorage('node');
        $this->taxonomyStorage = $entityTypeManager->getStorage('taxonomy_term');
    }

    /** 
     * {@inheritdoc}
     */
    public function createFilm(array $data)
    {
        $film = [];
        
        if ($data['Response'] == FALSE) {
            return['error' =>$data['Error']];
        }
    }
}