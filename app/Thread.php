<?php

namespace App;

use App\RecordsActivity;
use App\ThreadSubscription;
use App\Filters\ThreadFilters;
use App\Events\ThreadHasNewReply;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
   
   use RecordsActivity;
	protected $guarded = [];

    protected $with = ['creator', 'channel']; 

    protected $append = ['isSubscribedTo'];

    public static function boot()
    {
        parent::boot();
        // static::addGlobalScope('replyCount', function($builder){
        //     $builder->withCount('replies');
        // });

            static::deleting(function ($thread) {
                // var_dump($thread->replies);
            $thread->replies->each->delete();
        });
        // static::addGlobalScope('creator', function($builder){
        //     $builder->with('creator');
        // });

        // static::created(function($thread){
        //     $thread->recordActivity('created');
        // });
       
    }

    // protected function recordActivity($event)
    // {
    //     Activity::create([
    //             'user_id' => auth()->id(),
    //             'type' =>  $this->getActivityType($event),
    //             'subject_id' => $this->id,
    //             'subject_type' => get_class($this),
    //              ]);
    // }

    // protected function getActivityType($event)
    // {
    //    return $event . '_' . strtolower((new \ReflectionClass($this))->getShortName()); 
    // }

    public function path()
    {
        if(isset($this->channel->slug)){
    	return "/threads/{$this->channel->slug}/{$this->id}";
    } else {
        return 'no channel slug';
    }
    }

    public function replies()
    {
    	return $this->hasMany('App\Reply');
                
    }

    public function creator()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }

    public function addReply($reply)
    {	
    	$reply = $this->replies()->create($reply);

        event(new ThreadHasNewReply($this, $reply));

       
        return $reply;
    }

    public function channel()
    {
        return $this->belongsTo('App\Channel');
    }

     /**
     * Apply all relevant thread filters.
     *
     * @param  Builder       $query
     * @param  ThreadFilters $filters
     * @return Builder
     */
    public function scopeFilter($query, ThreadFilters $filters)
    {
        // dd($query);

        return $filters->apply($query);
    }

    public function subscribe($userId = null)
    {
        $this->subscriptions()->create(
            ['user_id' => $userId ?: auth()->id()]);

        return $this;
    }

     public function unsubscribed($userId = null)
    {
        $this->subscriptions()->where(
            'user_id', $userId ?: auth()->id())->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
                ->where('user_id', auth()->id())
                ->exists();
    }

    public function hasUpdatesFor()
    {
        $key = sprintf("users.%s.visits.%s", auth()->id(), $this->id);
        
        return $this->updated_at > cache($key);
    }

}
