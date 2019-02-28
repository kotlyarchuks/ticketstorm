<?php

namespace App;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Concert
 *
 * @property-read mixed $formatted_date
 * @property-read mixed $formatted_price
 * @property-read mixed $formatted_time
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert published()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert query()
 * @mixin \Eloquent
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title
 * @property string $subtitle
 * @property \Illuminate\Support\Carbon $date
 * @property int $price
 * @property string $location
 * @property string $street
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $additional_info
 * @property string|null $published_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order[] $orders
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ticket[] $tickets
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert whereAdditionalInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert whereSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert whereZip($value)
 */
class Concert extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }

        return $this;
    }

    public function remainingTickets()
    {
        return $this->tickets()->available()->count();
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    public function getFormattedTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price/100, 2);
    }

    public function buyTickets($email, $ticketQuantity)
    {
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();

        if($tickets->count() < $ticketQuantity){
            throw new NotEnoughTicketsException;
        }

        $order = $this->orders()->create([
            'email'   => $email,
        ]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    // FOR TESTING
    public function ordersFor($email)
    {
        return $this->orders()->where('email', $email)->get();
    }
    
    public function hasOrderFor($email)
    {
        return $this->ordersFor($email)->count() > 0;
    }
}
