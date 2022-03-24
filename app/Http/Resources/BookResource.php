<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "title" => $this->resource['name'],
            "isbn" => $this->resource['isbn'],
            "authors" => $this->resource['authors'],
            "released_date" => $this->resource['released'],    
            "publisher" => $this->resource['publisher'],
            "number_of_pages" => $this->resource['numbersOfPages'],
            "country" => $this->resource['country'],
        ];
    }
}
