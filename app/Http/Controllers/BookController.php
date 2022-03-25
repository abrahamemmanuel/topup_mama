<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\IceAndFireApi;

class BookController extends Controller
{
    public function getBooks(Request $request)
    {
        $page = $request->has('page') ? $request->page : 1;
        $pageSize = $request->has('pageSize') ? $request->pageSize : 10;
        $query_params = http_build_query(['page' => $page, 'pageSize' => $pageSize]);
        $iceAndFireApi = new IceAndFireApi();
        $books = $iceAndFireApi->getBooks($query_params);
        $data = [
            'status' => 'success',
            'message' => 'Books fetched successfully',
            'books' => $books,
        ];
        $response = response()->json($data, 200);
        return $response;
    }

    public function getBookById($id)
    {
        $iceAndFireApi = new IceAndFireApi();
        $book = $iceAndFireApi->getBookById($id);
        $data = [
            'status' => 'success',
            'message' => 'Book fetched successfully',
            'books' => $book,
        ];
        $response = response()->json($data, 200);
        return $response;
    }

    public function getBookCharactersList(Request $request, $book_id)
    {
        $sort = $request->has('sort') ? $request->sort : null;
        $page = $request->has('page') ? $request->page : 1;
        $pageSize = $request->has('pageSize') ? $request->pageSize : 10;
        $gender = $request->has('gender') ? $request->gender : null;
        $query_params = $gender != null 
        ? ['page' => $page, 'pageSize' => $pageSize, 'gender' => $gender] 
        : ['page' => $page, 'pageSize' => $pageSize];
        $iceAndFireApi = new IceAndFireApi();
        $characters = $iceAndFireApi->getSingleBookCharacters(http_build_query($query_params), $book_id);
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
}
