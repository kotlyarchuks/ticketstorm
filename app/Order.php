<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Order
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $concert_id
 * @property string $email
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ticket[] $tickets
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereConcertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    protected $guarded = [];

    public static function fromReservation($email, $amount, $tickets){
        $order = Order::create([
            'email'  => $email,
            'amount' => $amount,
        ]);

        foreach ($tickets as $ticket)
        {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function toArray()
    {
        return [
            'email' => $this->email,
            'tickets' => $this->ticketCount(),
            'amount' => $this->amount
        ];
    }

    // FOR TESTING

    public function ticketCount()
    {
        return $this->tickets()->count();
    }
}
