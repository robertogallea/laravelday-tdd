<?php

namespace Tests\Unit;

use App\Book;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_add_books()
    {
        $user = factory(User::class)->create();
        $book = factory(Book::class)->make(['title' => 'The Lord of the Rings']);

        $user->addBook($book);

        $this->assertDatabaseHas('books', [
            'title' => 'The Lord of the Rings',
            'user_id' => $user->id
        ]);
    }
}
