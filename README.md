# TopUp Mama Backend Assessment - Book Listing API

## Introduction
Thsi API can be used for listing the names of books along with their authors and comment count, adding and listing anonymous comments for a book, and getting the character list for a book

## Base URL
http://143.110.151.70/api/v1

## Endpoints
### GET /books
Book names in the book list endpoint are sorted by release date from earliest to newest and each book are listed along with authors and count of comments.

### GET /books/{book_id}
The book detail endpoint returns the book name, author, release date, and count of comments & comments for the book with the given book_id.

### POST /books/{book_id}/comments
The book comment endpoint allows anonymous users to add comments for the book with the given book_id.

### GET /books/{book_id}/characters
The book character endpoint returns the character list for the book with the given book_id.

### Setup/Installation
## Prerequisites
- PHP 7.2 and above
- Composer

**Note:** The API is soley dependent on AnAPIofIceandFire.

- run composer install
- run php artisan key:generate
- run php artisan migrate
- run php artisan db:seed

## Testing
- run vendor/bin/phpunit


## Documentation
- The API doc is published on https://documenter.getpostman.com/view/5744463/UVyoVcxQ