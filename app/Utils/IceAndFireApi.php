<?php

namespace app\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Request;

class IceAndFireApi 
{
	private $base_uri = "https://anapioficeandfire.com/api/";
	public $page = 1;
	public $pageSize = 10;
	public $queryParams = [];
	public $gender = null;
	public $sortOrder = null;

	public function getBooks($query_params)
	{
		$uri = $this->base_uri . 'books?' . $query_params;
		$http_verb = 'GET';
		return self::httpConnector($uri, $http_verb);
	}

	public function getBookById($book_id)
	{
		$uri = $this->base_uri . 'books/' . $book_id;
		$http_verb = 'GET';
		return self::httpConnector($uri, $http_verb);
	}

	public function getCharacters($query_params) 	
	{
		$uri = $this->base_uri . 'characters?' . $query_params;
		$http_verb = 'GET';
		return self::httpConnector($uri, $http_verb);
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
		return response()->json(['error' => $e->getMessage()], $e->getCode());
	} catch (ConnectException $e) {
		return response()->json(['error' => $e->getMessage()], $e->getCode());
	}
  }

	public function buildQueryParams($request)
	{
		$this->page = $request->has('page') ? $request->page : $this->page;
		$this->pageSize = $request->has('pageSize') ? $request->pageSize : $this->pageSize;
		$this->gender = $request->has('gender') ? $request->gender : $this->gender;
		$this->queryParams = $this->gender != null 
		? ['page' => $this->page, 'pageSize' => $this->pageSize, 'gender' => $this->gender] 
		: ['page' => $this->page, 'pageSize' => $this->pageSize];
		return http_build_query($this->queryParams);
	}

	public function paginate($request)
	{
		$this->buildQueryParams($request);
		$page = $this->page;
		$pageSize = $this->pageSize;
		$prevPage = $page > 1 ? $page - 1 : null;
		return [
			'current' => url('/books?'.http_build_query($this->queryParams)),
			'next' => url('/books?page='.($page+1).'&pageSize='.$pageSize),
			'prev' => $prevPage ? url('/books?page='.$prevPage.'&pageSize='.$pageSize) : null,
		];
	}

	public function sortCharacters($items, $order)
	{
		$this->sortOrder = $order;
		return $this->sortOrder == 'name-desc' ? array_reverse($items) : $items;
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

}