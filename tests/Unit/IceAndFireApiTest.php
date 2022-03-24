<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Utils\IceAndFireApi;

class IceAndFireApiTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function should_fetch_list_of_books()
    {
        $pageSize = 12;
        $page = 1;
        $query_params = http_build_query(['pageSize' => $pageSize, 'page' => $page]);
        $iceAndFireApi = new IceAndFireApi();
        $books = $iceAndFireApi->getBooks($query_params);
        $this->assertIsArray($books);
        $this->assertEquals(count($books), $pageSize * $page);
        $this->assertCount(9, $books[0]);
    }

    /**
     * @test
     * @return void
     */
    public function should_get_book_by_id()
    {
        $book_id = 12;
        $iceAndFireApi = new IceAndFireApi();
        $book = $iceAndFireApi->getBookById($book_id);
        $this->assertIsArray($book);
        $this->assertCount(9, $book);
    }

    /**
     * @test
     * @return void
     */
    public function should_get_characters_list_by_book_id()
    {
        $book_id = 1;
        $iceAndFireApi = new IceAndFireApi();
        $characters_list = $iceAndFireApi->getSingleBookCharactersList($book_id);
        $this->assertIsArray($characters_list);
    }

    /**
     * @test
     * @return void
     */
    public function should_fetch_single_book_characters()
    {
        $pageSize = 10;
        $page = 2;
        $book_id = 1;
        $query_params = http_build_query(['pageSize' => $pageSize, 'page' => $page]);
        $iceAndFireApi = new IceAndFireApi();
        $characters = $iceAndFireApi->getSingleBookCharacters($query_params, $book_id);
        $this->assertIsArray($characters);
    }
}
