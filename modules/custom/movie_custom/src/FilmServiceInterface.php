<?php

namespace Drupal\movie_custom;

interface FilmServiceInterface {
    /**
     * @param string $id
     * 
     * @return mixed
     */
    public function createFilm(array $data);
}