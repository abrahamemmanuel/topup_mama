<?php

namespace app\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use App\Http\Resources\BookResource;
use App\Http\Resources\BooksCollection;

class IceAndFireApi 
{
	private $client;
	private $base_uri = "https://anapioficeandfire.com/api/";
	
	public function getBooks()
	{
		$uri = $this->base_uri . 'books';
		$http_verb = 'GET';
		return self::httpConnector($uri, $http_verb);
	}

	protected static function httpConnector($uri, $http_verb)
    {
        try {
            $client = new Client();
            $response = $client->request($http_verb, $uri, [
                'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                ], 
            ]);
			$data = self::responseTransformer($response);
			return $data;
		} catch (BadResponseException $e) {
			return $e->getMessage();
		} catch (ConnectException $e) {
			return $e->getMessage();
		}
    }

	protected static function responseTransformer($response)
	{
		$data = json_decode($response->getBody()->getContents(), true);
		$data = array_map(function($item) {
			unset($item['url']);
			unset($item['povCharacters']);
			unset($item['characters']);
			return $item;
		}, $data);
		return $data;
	}
}