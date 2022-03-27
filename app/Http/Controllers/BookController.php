<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\IceAndFireApi;
use App\Models\Comment;

class BookController extends Controller
{
    public function getBooks(Request $request)
    {
        //Network Call
        $iceAndFireApi = new IceAndFireApi();
        $queryParams = $iceAndFireApi->buildQueryParams($request);
        $response = $iceAndFireApi->getBooks($queryParams);

        //Error Handling
        if($response instanceof \Illuminate\Http\JsonResponse) return $response;

        //Data Mapping
        $data = $response;
		$books = [];
        foreach ($data as $book) {
            $books[] = $iceAndFireApi->bookResource($book);
        }

		//Set Comments Count For Each Book
		foreach ($books as $book) {
			$book['comments_count'] = 0;
			$comments = Comment::where('book_id', $book['book_id'])->get();
			$book['comments_count'] = count($comments) > 0 ? count($comments) : 0;
		}

        //Meta Data
        $meta_data = [
            'page' => $iceAndFireApi->page,
            'pageSize' => $iceAndFireApi->pageSize,
            'total_books' => count($books),
        ];

        //Payload
        $data = [
            'status' => 'success',
            'message' => 'Books fetched successfully',
            'books' => $books,
            'links' => $iceAndFireApi->paginate($request, '/books?'),
            'meta_data' => $meta_data
        ];

        //Response
        return response()->json($data, 200);
    }

    public function getBookById($id)
    {
        //Network Call
        $iceAndFireApi = new IceAndFireApi();
        $response = $iceAndFireApi->getBookById($id);

        //Error Handling
        if($response instanceof \Illuminate\Http\JsonResponse) return $response;

        //Data Mapping
        $book = $iceAndFireApi->bookResource($response);

		//Retrieve Comments From DB By Book Id In a Reverse Chronological Order
		$comments = Comment::where('book_id', $id)->orderBy('created_at', 'desc')->get();
		$book['comments'] = $comments;

        //Payload
        $data = [
            'status' => 'success',
            'message' => 'Book fetched successfully',
            'book' => $book,
        ];

        //Response
        return response()->json($data, 200);
    }

    public function getBookCharacters(Request $request, $book_id)
    {
		//Set Sort Order
		$sortOrder = $request->has('sortOrder') ? $request->sortOrder : null;

		//First Network Call: Fetch Book By Id
		$iceAndFireApi = new IceAndFireApi();
		$queryParams = $iceAndFireApi->buildQueryParams($request);
		$bookResponse = $iceAndFireApi->getBookById($book_id);

		//Error Handling
		if($bookResponse instanceof \Illuminate\Http\JsonResponse) return $bookResponse;
		
		//Retrieve Characters List from book response
		$characters_list = $bookResponse['characters'];

		//Second Network Call: Fetch All Characters
		$charactersResponse = $iceAndFireApi->getCharacters($queryParams);

		//Error Handling
		if($charactersResponse instanceof \Illuminate\Http\JsonResponse) return $charactersResponse;

		//Data Mapping:
		$characters = [];
		foreach ($charactersResponse as $character) {
			if(in_array($character['url'], $characters_list)) {
				$characters[] = $iceAndFireApi->characterResource($character);
			}
		}

		//Apply Sort
		$characters = $iceAndFireApi->sortCharacters($characters, $sortOrder);

		//Meta Data
		$meta_data = [
			'book_id' => $book_id,
			'page' => $iceAndFireApi->page,
			'page_size' => $iceAndFireApi->pageSize,
			'total_characters_matched_on_current_page' => count($characters),
			'sort_order' =>  $sortOrder != null ? $sortOrder : null,
			'filter' => $iceAndFireApi->gender != null ? $iceAndFireApi->gender . '-gender' : 'male & female'
		];

		//Payload
		$data = [
			'status' => 'success',
			'message' => 'Characters fetched successfully',
			'characters' => $characters,
			'links' => $iceAndFireApi->paginate($request, 'books/'.$book_id.'/characters?'),
			'meta_data' => $meta_data
		];
		
		//Response
		return response()->json($data, 200);
    }

	public function addBookComment(Request $request, $book_id)
	{	
		//Validate Request
		$validator = $this->validate($request, [
			'body' => 'required|string|max:255',
		]);

		//Network Call
		$iceAndFireApi = new IceAndFireApi();
		$response = $iceAndFireApi->getBookById($book_id);

		//Error Handling
		if($response instanceof \Illuminate\Http\JsonResponse) return $response;

		//Data Mapping
		$book = $iceAndFireApi->bookResource($response);

		//Create Comment
		$comment = new Comment();
		$comment->book_id = $book_id;
		$comment->body = $request->body;
		$comment->client_ip = $request->client_ip();
		$comment->user_name = 'Anonymous';
		$comment->save();

		//Payload
		$data = [
			'status' => 'success',
			'message' => 'Comment added successfully',
			'comment' => $comment,
			'book_id' => $book_id
		];

		//Response
		return response()->json($data, 200);
	}
}
