<?php

namespace App;

use App\Events\ThreadReceivedNewReply;
use App\Filters\ThreadFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    // added b/c mass assignment exception (array)
    use RecordsActivity;
    
    protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];

    protected static function boot()
    {
        parent::boot();

        //static::addGlobalScope('replyCount', function($builder)
        //{
        //    $builder->withCount('replies');
        //});

        //static::addGlobalScope('creator', function($builder)
        //{
        //    $builder->withCount('creator');
        //});
        static::deleting(function ($thread) {
            //$thread->replies()->delete();
            $replies = $thread->replies;
            foreach ($replies as $reply) {
                $reply->delete();
            }

            //$thread->replies->each(function($reply){
            //    $reply->delete();
            //});
            //$thread->replies->each->delete();
        });
        // you know what, this is a kind of unecessary steps and you can do it some other uncomplex way
        // jeffrey way..... showing off... 

    }

    public function getReplyCountAttribute()
    {
        return $this->replies()->count();
    }

    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
        //return '/threads/' . $this->channel->slug . '/' . $this->id;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class)
            //->withCount('favorites')
            // does this register as favorites_count? yes it does -- this is eager loading I believe,
            // we load the thread with it's replies and whether it has been favorited and the amount of times... why?
            // i dunno yet
            ->with('owner');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
        // thread links to channel and belongs to channel
    }

    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceivedNewReply($reply));
        
        //$this->notifySubscribers($reply);

        //event(new ThreadHasNewReply($this, $reply));

        //$this->subscriptions
        //    ->where('user_id', '!=', $reply->user_id)
        //    //->filter(function ($sub) use ($reply) {
        //    //    return $sub->user_id != $reply->user_id;
        //    //})
        //    ->each
        //    ->notify($reply);
        //    //->each(function($sub) use ($reply) {
        //    //    $sub->user->notify(new ThreadWasUpdated($this, $reply));
        //    //});
        ////foreach ($this->subscriptions as $subscription) {
        ////    if($subscription->user_id != $reply->user_id) {
        ////    $subscription->user->notify(new ThreadWasUpdated($this, $reply));
        ////    }
        ////}

        return $reply;
        //$this->increment('replies_count');
        //this method is good, but we can also use
        //Model Events - 
        // This is kind of blowing my mind
        //1. We call a Thread
        //2. We create a function that defines a 
        //   relationship in which we are able to retrieve records
        //   associated with that Thread by referring to it's class
        //3. But then we take this mere declaration 
        //   in which a relationship is defined, to create a reply with it
        //How would I prefer it work?
        //use App/Reply;
        //$reply = Reply::class 
    }

    public function notifySubscribers($reply){
        $this->subscriptions
        ->where('user_id', '!=', $reply->user_id)
        ->each
        ->notify($reply);
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
        // return filter->by(builder(where('', '')))
    }
    // looking at this function closely
    // we use a filter, accepts a filter/query... but we really just pass the $filter...
    // the $filter then applies (a) $query
    // but the apply method is subjugated to Filters.php file where we apply our $query as a $builder

    // but where does the $query come from?

    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function unsubscribe($userId = null)
    {
        return $this->subscriptions()
            ->where('user_id', $userId?: auth()->id())
            ->delete();
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }
}
