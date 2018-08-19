<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadTest extends TestCase
{
	use DatabaseMigrations;


	protected $thread;

	 public function setUp()
    {
        parent::setUp();

        $this->thread = factory('App\Thread')->create();


    }

     /** @test */
    public function a_thread_notifies_subscribers_when_a_reply_is_added() 
    {
    Notification::fake();
       $this->signin();

       $this->thread
            ->subscribe()
            ->addReply([
            'body' => 'foobar',
            'user_id' => 1,
            ]);
       Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);

    }
     /** @test */
    public function a_thread_has_a_creator() 
    {
        $this->assertInstanceOf('App\User', $this->thread->creator);
    }

      /** @test */
    public function a_thread_has_replies() 
    {
        $this->assertInstanceOf(
        	'Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'foobar',
            'user_id' => 1,
            ]);
        $this->assertCount(1, $this->thread->replies);
    }

    /** @test */
    public function a_thread_belongs_to_a_channel()
    {
        $thread = create('App\Thread');

        $this->assertInstanceOf('App\Channel', $thread->channel);
    }

     /** @test */
    public function a_thread_can_make_a_string_path()
    {
        $thread = create('App\Thread');

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->id}", $thread->path());
    }

    /** @test */
    public function a_thread_cane_be_subscribed_to()
    {
        $thread = create('App\Thread');
        $this->signIn();
        $thread->subscribe();
        $this->assertEquals(1, $thread->subscriptions()->where('user_id', auth()->id())->count());
    }


    /** @test */

    public function a_thread_can_be_unsubscribed_from()
    {
        $thread = create('App\Thread');
        $thread->subscribe($userId = 1);

        $thread->unsubscribed($userId);

        $this->assertCount(0, $thread->subscriptions);

    }

    /** @test*/ 
    function it_knows_if_auth_user_is_subscribed_to_it()
    {
        $thread = create('App\Thread');
        $this->signIn();
        $this->assertFalse($thread->isSubscribedTo);
        $thread->subscribe();
        $this->assertTrue($thread->isSubscribedTo);

    }

    /** @test */

    function a_thread_can_check_if_auth_user_has_read_all_replies()
    {
        $this->signIn();

        $thread = create('App\Thread');

        $this->assertTrue($thread->hasUpdatesFor(auth()->user()));

        // $key = sprintf("users.%s.visits.%s", auth()->id(), $thread->id);

        cache()->forever(auth()->user()->visitedThreadCacheKey($thread), Carbon::now());

        $this->assertFalse($thread->hasUpdatesFor(auth()->user()));

    }
}
