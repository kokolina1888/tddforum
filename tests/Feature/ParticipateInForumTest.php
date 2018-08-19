<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class ParticipateInForumTest extends TestCase
{
	use DatabaseMigrations;

	protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = factory('App\Thread')->create();
    }

    /** @test */

    public function an_authenticated_user_may_participate_in_forum_threads()
    {

    	$this->signIn($user = factory('App\User')->create());

    	$reply = factory('App\Reply')->make(['thread_id' =>$this->thread->id]);


    	$this->post($this->thread->path() . '/replies', $reply->toArray());
// dd($reply->body);
    	$this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertEquals(1, $this->thread->fresh()->replies_count);
    }

    /** @test */

    public function an_unauthenticated_user_may_not_add_replies()
    {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');        
        $this->post($this->thread->path() . '/replies', []);

   
    }

    /** @test */

    public function a_reply_requires_a_body()
    {
       $this->signIn();
       $thread = create('App\Thread');
       $reply = make('App\Reply', ['body' => null]);
       $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertSessionHasErrors('body');

    }

    /** @test */

    public function unauthorized_users_cannot_delete_replies()
    {
        $reply = create('App\Reply');

        $this->delete('/replies/' . $reply->id)
                ->assertRedirect('/login');

        $this->signIn()->delete('/replies/' . $reply->id)
            ->assertStatus(403);



    }

     /** @test */

    public function authorized_users_can_delete_replies()
    {
        $this->signIn();
        $reply = create('App\Reply', ['user_id'=>auth()->id()]);
        

        $this->delete('/replies/' . $reply->id)->assertStatus(302);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0, $reply->thread->fresh()->replies_count); 

    }

    /** @test */

    public function authorized_users_can_update_replies()
    {
        $this->withoutExceptionHandling();
        $this->signIn();
        $reply = create('App\Reply', ['user_id'=>auth()->id()]);
        

        $this->post('/replies/' . $reply->id, ['body' =>'Have been changed']);
        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => 'Have been changed'] );

    }

    /** @test */

    public function unauthorized_users_cannot_update_replies()
    {
        $reply = create('App\Reply');

        $this->post('/replies/' . $reply->id)
                ->assertRedirect('/login');

        $this->signIn()->post('/replies/' . $reply->id)
            ->assertStatus(403);



    }


    /** @test */

    public function replies_that_contain_spam_may_not_be_created()
    {
       $this->withoutExceptionHandling();
        
        $this->signIn();
        $reply = create('App\Thread');
        $reply = make('App\Reply', [
            'body' => 'Yahoo Customer Support'
            ]);
        // $this->expectException(\Exception::class);

        $this->post($thread->path() . '/replies/', $reply->toArray())
                ->assertStatus(422);



    }

    /** @test */

    function user_may_only_reply_once_in_two_minutes()
    {
         $this->signIn();

         $thread = create('App\Thread');

            $reply = make('App\Reply', [
            'body' => 'My reply'
            ]);

            $this->post($thread->path() . '/replies/', $reply->toArray())
                ->assertStatus(200);
    }

}
