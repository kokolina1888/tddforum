<?php

namespace Tests\Feature;

use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
       
    use DatabaseMigrations;
    protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = factory('App\Thread')->create();
    }
    
    /** @test */
    
    public function a_user_can_view_all_threads()
    {
       
       $this->get('/threads')
            ->assertSee($this->thread->title);
    }

    /** @test */
    
    public function a_user_can_view_a_single_threads()
    {
      
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
        
        // $response->assertStatus(200);
    }

   

    public function a_user_can_filter_threads_according_to_a_channel()
    {
            $channel = create('App\Channel');

            $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
            // dd($threadInChannel);
            $threadNotInChannel = create('App\Thread');
            // dd($threadNotInChannel);
            $this->get('/threads/' . $channel->slug)
                ->assertSee($threadInChannel->title)
                ->assertDontSee($threadNotInChannel->title);

    }

    /** @test */

    public function a_user_can_filter_threads_by_any_username()
    {
       
        $this->signIn(create('App\User', ['name' => 'JohnDoe']));
        
        $threadByJohn = create('App\Thread', ['user_id' =>auth()->id()]);
         
        $threadNotByJohn = create('App\Thread');
           
        $this->get('/threads?by=JohnDoe/')
                ->assertSee($threadByJohn->title)
                ->assertDontSee($threadNotByJohn->title);

    }

    /** @test */
    function a_user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);
        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithThreeReplies->id], 3);
        $threadWithNoReplies = $this->thread;
        $response = $this->getJson('threads?popular=1')->json();
        $this->assertEquals([3, 2, 0], array_column($response['data'], 'replies_count'));
    }

    // /** @test */
    // function a_user_can_filter_threads_by_those_that_are_unanswered()
    // {
    //     // $this->withoutExceptionHandling();
    //     $thread = create('App\Thread');

    //     create('App\Reply', ['thread_id' => $thread->id]);
    //     // dd($thread->id);
    //     // $threads = Thread::all()->count();
    //     $response = $this->getJson('threads?unanswered=1')->json();
        
    //     $this->assertCount(1, $response);

    // }

    // /** @test */
    // function a_user_can_request_all_replies_for_given_thread()
    // {
    //     $thread = create('App\Thread');
    //     create('App\Reply', ['thread_id' => $thread->id], 2);

    //     $response = $this->getJson($thread->path() . '/replies')->json();

    //     $this->assertCount(1, $response['data']);
    //     $this->assertEquals(2, $response['total']);

    // }

   
}
