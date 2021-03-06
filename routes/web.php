<?php

/*\Laravel\Lumen\Routing\Router $router */
use App\Http\Controllers\BookController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->get('/books', 'BookController@getBooks');
    $router->get('/books/{id}', 'BookController@getBookById');
    $router->get('/books/{book_id}/characters', 'BookController@getBookCharacters');
    $router->post('/books/{book_id}/comments', 'BookController@addBookComment');
});
