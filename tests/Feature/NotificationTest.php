<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class NotificationTest extends TestCase
{

	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();
		$this->signIn();
	}
	

	/** @test */
	function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_the_current_user()
	{
		$thread = create('App\Thread')->subscribe();
		$this->assertCount(0, auth()->user()->notifications);
		$thread->addReply([
			'user_id' => auth()->id(),
			'body' => 'Some reply here'
			]);
		$this->assertCount(0, auth()->user()->fresh()->notifications);
		$thread->addReply([
			'user_id' => create('App\User')->id,
			'body' => 'Some reply here'
			]);
		$this->assertCount(1, auth()->user()->fresh()->notifications);
	}


	/** @test */

	function a_user_can_clear_notification()
	{
		// $this->signIn();
		$this->withoutExceptionHandling();

		$thread = create('App\Thread')->subscribe();
		$thread->addReply([
			'user_id' => create('App\User')->id,
			'body' => 'Some reply here'
			]);
		$this->assertCount(1, auth()->user()->unreadNotifications);

		$notificationId = auth()->user()->unreadNotifications->first()->id;

		$this->delete("/profiles/" . auth()->user()->name . "/notifications/{$notificationId}");

		$this->assertCount(0, auth()->user()->fresh()->unreadNotifications);


	}

	/** @test */
  //   function a_user_can_fetch_their_unread_notifications()
  //   {
  //       create(DatabaseNotificationFactor::class);
  // //       $this->withoutExceptionHandling();

		// // $thread = create('App\Thread')->subscribe();
		// // $thread->addReply([
		// // 	'user_id' => create('App\User')->id,
		// // 	'body' => 'Some reply here'
		// // 	]);


  //       $this->assertCount(
  //           1,
  //           $this->getJson("/profiles/" . auth()->user()->name . "/notifications")->json()
  //       );
  //   }
}
