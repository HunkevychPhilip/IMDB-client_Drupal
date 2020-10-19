<?php

namespace Drupal\movie_custom;

interface ImdbApiClientInterface {

    /**
     * @param string $id
     * 
     * @return mixed
     */
    public function getFilmInfo(string $id);
    
}