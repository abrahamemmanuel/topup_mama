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
        $iceAndFireApi = new IceAndFireApi();
        $books = $iceAndFireApi->getBooks();
        $this->assertIsArray($books);
    }
}
