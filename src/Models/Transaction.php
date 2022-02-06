<?php

namespace Spork\Finance\Models;

use App\Models\AbstractModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kregel\LaravelAbstract\AbstractModelTrait;
use Spatie\QueryBuilder\AllowedFilter;

class Transaction extends AbstractModel
{
    use HasFactory, AbstractModelTrait;

    protected $fillable = [
        'id',
        'name',
        'amount',
        'account_id',
        'date',
        'pending',
        'category_id',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'float',
        'pending' => 'boolean',
        'date' => 'date',
    ];

    public function scopeForAccounts(Builder $query, $value)
    {
        $query->with('account');
        return $query->whereIn('account_id', explode(',', $value));
    }

    public function getAbstractAllowedFilters(): array
    {
        return [
            AllowedFilter::scope('for_accounts')
        ];
    }

    public function getAbstractAllowedSorts(): array
    {
        return [
            'date'
        ];
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }
}
