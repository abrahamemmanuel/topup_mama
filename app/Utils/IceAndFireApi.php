<?php

namespace app\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

class IceAndFireApi 
{
	private $base_uri = "https://anapioficeandfire.com/api/";

	public function getBooks($query_params)
	{
		$uri = $this->base_uri . 'books?' . $query_params;
		$http_verb = 'GET';
		$data = self::httpConnector($uri, $http_verb);
		$books = [];
			foreach ($data as $book) {
				$books[] = $this->bookResource($book);
			}
		return $books;
	}

	public function getBookById($book_id)
	{
		return $this->bookResource(self::bookLoader($book_id));
	}

	public function getSingleBookCharactersList($book_id)
	{
		$book = self::bookLoader($book_id);
		$characters_list = $book['characters'];
		return $characters_list;
	}

	public function getSingleBookCharacters($query_params, $book_id)
	{
		$uri = $this->base_uri . 'characters?' . $query_params;
		$http_verb = 'GET';
		$data = self::httpConnector($uri, $http_verb);
		$characters_list = $this->getSingleBookCharactersList($book_id);
		$characters = [];
		foreach ($data as $character) {
			if(in_array($character['url'], $characters_list)) {
				$characters[] = $this->characterResource($character);
			}
		}
		return $characters;
	}

	public function bookResource($book)
	{
		return [
			'book_id' => substr(strrchr($book['url'], '/'), 1),
			'book_title' => $book['name'],
			'authors' => $book['authors'],
			'isbn' => $book['isbn'],
			'publisher' => $book['publisher'],
			'number_of_pages' => $book['numberOfPages'],
			'country' => $book['country'],
			'release_date' => $book['released'] != '' ? date('jS F Y', strtotime($book['released'])) : '',
			'media_type' => $book['mediaType'],
		];
	}

	public function characterResource($character)
	{
		return [
			'character_id' => substr(strrchr($character['url'], '/'), 1),
			'character_name' => $character['name'],
			'gender' => $character['gender'],
			'culture' => $character['culture'] == '' ? 'not provided' : $character['culture'],
			'date_of_birth' => $character['born'] == '' ? 'not provided' : $character['born'],
			'date_of_death' => $character['died'] == '' ? 'not provided' : $character['died'],
			'titles' => count($character['titles']) == 0 ? 'not provided' : $character['titles'],
		];
	}

	protected function bookLoader($id)
	{
		$uri = $this->base_uri . 'books/' . $id;
		$http_verb = 'GET';
		$books = self::httpConnector($uri, $http_verb);
		return $books;
	}

	protected static function httpConnector($uri, $http_verb)
  {
    try{
						$client = new Client();
            $response = $client->request($http_verb, $uri, [
                'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                ], 
            ]);
			return json_decode($response->getBody()->getContents(), true);
		} catch (BadResponseException $e) {
			return $e->getMessage();
		} catch (ConnectException $e) {
			return $e->getMessage();
		}
  }
}