<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Ticket
 *
 * @property int $id
 * @property int $order_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ticket available()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ticket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ticket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ticket query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ticket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ticket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ticket whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ticket whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ticket extends Model
{
    protected $guarded = [];

    public function scopeAvailable($query)
    {
        return $query->whereNull('order_id')->whereNull('reserved_at');
    }

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function getPriceAttribute()
    {
        return $this->concert->price;
    }

    public function release()
    {
        $this->update(['reserved_at' => null]);
    }

    public function reserve()
    {
        $this->update(['reserved_at' => Carbon::now()]);
    }
}
