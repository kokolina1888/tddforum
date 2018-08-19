<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;


class FavoritesTest extends TestCase
{
	use DatabaseMigrations;

	/** @test */

	public function an_authenticated_user_can_favourite_any_reply()
	{
		

		$this->signIn();

		$reply = create('App\Reply');

		$this->post('/replies/' . $reply->id . '/favorites');

		$this->assertCount(1, $reply->favorites);
	}

	/** @test */

	public function an_authenticated_user_can_unfavourite_any_reply()
	{
		
		$this->withoutExceptionHandling();
		$this->signIn();

		$reply = create('App\Reply');

		$this->post('/replies/' . $reply->id . '/favorites');
		$this->assertCount(1, $reply->favorites);

		$this->delete('/replies/' . $reply->id . '/favorites');

		$this->assertCount(0, $reply->fresh()->favorites);
	}


	/** @test */

	public function guest_cannot_favourite_anything()
	{

		$this->post('/replies/1/favorites')->assertRedirect('/login');
	}

	/** @test */

	public function an_authenticated_user_may_only_favourite_a_reply_once()
	{
		//$this->withoutExceptionHandling();

		$this->signIn();

		$reply = create('App\Reply');

		try{
			$this->post('/replies/' . $reply->id . '/favorites');
			$this->post('/replies/' . $reply->id . '/favorites');
		} catch(\Exception $e){
			$this->fail('Did not expect to insert same record twice.'); 
		}

		

		$this->assertCount(1, $reply->favorites);
	}


	
}