<?php

namespace Tests\Feature;

use App\Book;
use App\BookImporterInterface;
use App\Notifications\BookCreatedNotification;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_create_books()
    {
        $this->withoutExceptionHandling();

        // Preconditions
        $user = factory(User::class)->create();

        // Operations
        $this->actingAs($user)->postJson('/books', ['title' => 'The Lord of the Rings'])
            ->assertCreated();

        // Assertions
        $this->assertCount(1, $user->books);
    }

    /** @test */
    public function title_is_required()
    {
        // Preconditions
        $user = factory(User::class)->create();

        // Operations
        $this->actingAs($user)->postJson('/books', [])
            ->assertJsonValidationErrors(['title']);

        // Assertions
        $this->assertCount(0, $user->books);
    }

    /** @test */
    public function users_are_notified_on_book_creation()
    {
        Notification::fake();

        [$user1, $user2] = factory(User::class, 2)->create();

        $this->actingAs($user1)->postJson('/books', ['title' => 'The Lord of the Rings']);

        Notification::assertSentTo(
            $user2,
            BookCreatedNotification::class
        );
    }

    /** @test */
    public function book_creator_does_NOT_receive_a_notification()
    {
        Notification::fake();

        [$user1, $user2] = factory(User::class, 2)->create();

        $this->actingAs($user1)->postJson('/books', ['title' => 'The Lord of the Rings']);

        Notification::assertNotSentTo(
            $user1,
            BookCreatedNotification::class
        );
    }

    /** @test */
    public function users_can_import_books()
    {
        $mock = $this->mock(BookImporterInterface::class, function ($mock) {
            $mock->shouldReceive('import')
                ->once()
                ->with('1234567890')
                ->andReturn(factory(Book::class)->make());
        });

        $this->instance(BookImporterInterface::class, $mock);

        $user = factory(User::class)->create();

        $this->actingAs($user)->postJson('/books/import', ['isbn' => '1234567890'])
            ->assertCreated();

        $this->assertCount(1, $user->books);
    }
}
