<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\IceAndFireApi;

class BookController extends Controller
{
    public function getBooks(Request $request)
    {
        //Network Call
        $iceAndFireApi = new IceAndFireApi();
        $queryParams = $iceAndFireApi->getQueryParams($request);
        $response = $iceAndFireApi->getBooks($queryParams);

        //Error Handling
        if($response instanceof \Illuminate\Http\JsonResponse) return $response;

        //Data Mapping
        $data = $response;
		$books = [];
        foreach ($data as $book) {
            $books[] = $iceAndFireApi->bookResource($book);
        }

        //Pagination
        $page = $iceAndFireApi->page;
        $pageSize = $iceAndFireApi->pageSize;
        $prevPage = $page > 1 ? $page - 1 : null;
        $links = [
            'current' => url('/books?'.$queryParams),
            'next' => url('/books?page='.($page+1).'&pageSize='.$pageSize),
            'prev' => $prevPage ? url('/books?page='.$prevPage.'&pageSize='.$pageSize) : null,
        ];

        //Meta Data
        $meta_data = [
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => count($books),
        ];

        //Payload
        $data = [
            'status' => 'success',
            'message' => 'Books fetched successfully',
            'books' => $books,
            'links' => $links,
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

        //Payload
        $data = [
            'status' => 'success',
            'message' => 'Book fetched successfully',
            'book' => $book,
        ];

        //Response
        return response()->json($data, 200);
    }

    public function getBookCharactersList(Request $request, $book_id)
    {
        //Query Builder
        $sortOrder = $request->has('sortOrder') ? $request->sortOrder : null;

        //Network Call
        $iceAndFireApi = new IceAndFireApi();
        $queryParams = $iceAndFireApi->getQueryParams($request);
        $response = $iceAndFireApi->getBookById($id);

        //Error Handling
       if($response instanceof \Illuminate\Http\JsonResponse) return $response;
       $characters_list = $response['characters'];
       if(is_array($response)){
            $characters = $response;
            if($sort != null){
                $characters = $sort == 'name-desc' ? array_reverse($characters) : $characters;
            }
            $data = [
                'status' => 'success',
                'message' => 'Characters fetched successfully',
                'characters' => $characters,
                'meta_data' => [
                    'book_id' => $book_id,
                    'total_characters' => count($characters),
                    'page_size' => $pageSize,
                    'page' => $page,
                    'sort' => $sort != null ? $sort : null,
                    'filter' => $gender != null ? $gender . '-gender' : 'male & female'
                ],
            ];
            $response = response()->json($data, 200);
            return $response;
        }
        if($response->getStatusCode() !== 200) return $response;
    }
}
