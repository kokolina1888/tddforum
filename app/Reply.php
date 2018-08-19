<?php

namespace App;

use App\Favoritable;
use App\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use Favoritable, RecordsActivity;

	protected $guarded = [];

	protected $with = ['owner', 'favorites'];

    protected $appends = ['favoritesCount', 'isFavorited'];

    public static function boot()
    {
        parent::boot();

        static::created(function($reply){
            $reply->thread->increment('replies_count');
        });

        static::deleted(function($reply){
            $reply->thread->decrement('replies_count');
        });
    }
	
    public function owner()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }

    public function favorites()
    {
    	return $this->morphMany('App\Favorite', 'favorited');
    }

    public function isFavorited()
    {
    	return $this->favorites->where('user_id', auth()->id())->count();
    }

    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    public function getFavoritesCountAttribute()
    {
    	return $this->favorites->count();
    }

    public function thread()
    {
        return $this->belongsTo('App\Thread');
    }

     /**
     * Favorite the current reply.
     *
     * @return Model
     */
    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];
        if (! $this->favorites()->where($attributes)->exists()) {
            return $this->favorites()->create($attributes);
        }
    }

    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];

        // $this->favorites()->where($attributes)->delete();

         $this->favorites()->where($attributes)->get()->each->delete();
    
        
    }

    public function path()
    {
        return $this->thread->path() . "#{$this->id}";
    }

}
