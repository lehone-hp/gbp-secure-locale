<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountRequest extends Model
{
    const TYPE_SUSPEND = 'suspend_subscription';

    const TYPE_CANCEL = 'cancel_subscription';

    const TYPE_CLOSE = 'close_account';

    const TYPES = [self::TYPE_SUSPEND, self::TYPE_CANCEL, self::TYPE_CLOSE];

    const STATUS_PENDING = 'pending';

    const STATUS_IN_PROGRESS = 'in_progress';

    const STATUS_DONE = 'done';

    const STATUS_REJECTED = 'rejected';

    const STATUSES = [self::STATUS_PENDING, self::STATUS_IN_PROGRESS, self::STATUS_DONE, self::STATUS_REJECTED];

    protected $fillable = ['user_id', 'order_id', 'type', 'reason', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
