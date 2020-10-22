<?php

namespace Drupal\movie_custom\Client;

use Drupal\movie_custom\Form\ImdbApiSettings;
use Drupal\movie_custom\ImdbApiClientInterface;
use GuzzleHttp\Exception\RequestException;

class ImdbApiClient implements ImdbApiClientInterface {

    protected $imdbClient;

    const API_URL = 'http://www.omdbapi.com/';

    protected $apiKey;

    public function __construct($client, $configFactory) {
        $this->imdbClient = $client;
        $this->apiKey = $configFactory->get(ImdbApiSettings::SETTINGS)->get(
            'api_key'
        );
    }

   protected function apiQuery(array $query) {

       try {
           if (empty($query['apikey'])) {
               $query['apikey'] = $this ->apiKey;
           }
           $response = $this->imdbClient->get(self::API_URL, ['query'=> $query]);

           return json_decode($response->getBody(), TRUE);
       } catch (RequestException $e){

        return ['errorCode' => $e->getCode(), 'message' => $e->getMessage()];
       }
   }
   public function getFilmInfo(string $id){
    return $this->apiQuery(['i' => $id]);
   }

}
