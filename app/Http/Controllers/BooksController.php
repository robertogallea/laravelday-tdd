<?php

namespace App\Http\Controllers;

use App\Book;
use App\BookImporterInterface;
use App\Http\Requests\StoreBookRequest;
use App\Notifications\BookCreatedNotification;
use App\User;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    /**
     * @param StoreBookRequest $request
     * @return mixed
     */
    public function store(StoreBookRequest $request)
    {

        $book = $request->user()->addBook(new Book(['title' => $request->title]));

        User::where('id', '<>', $request->user()->id)
            ->get()->each->notify(new BookCreatedNotification($book));

        return $book;
    }

    /**
     * @param BookImporterInterface $bookImporter
     * @param Request $request
     * @return mixed
     */
    public function import(BookImporterInterface $bookImporter, Request $request)
    {
        $book = $bookImporter->import($request->isbn);

        $book = $request->user()->addBook($book);

        return $book;
    }
}
