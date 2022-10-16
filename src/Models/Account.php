<?php

namespace Spork\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Kregel\LaravelAbstract\AbstractModelTrait;
use Spork\Core\Models\AbstractModel;
use Spork\Core\Models\FeatureList;

class Account extends AbstractModel
{
    use HasFactory, AbstractModelTrait;

    protected $fillable = [
        'account_id',
        'mask',
        'name',
        'official_name',
        'balance',
        'available',
        'subtype',
        'type',
        'feature_list_id',
    ];

    protected static function booted()
    {
        parent::booted();

        static::creating(function ($item) {
            if (empty($item->account_id)) {
                $item->account_id = md5($item->name.$item->type);
            }
        });
    }

    public function accessToken()
    {
        return $this->belongsTo(FeatureList::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getValidationCreateRules(): array
    {
        return [
            'feature_list_id' => 'exists:feature_lists,id',
            'account_id' => 'unique:accounts,id',
            'mask' => 'string',
            'name' => 'string|required',
            'official_name' => 'string',
            'balance' => 'required',
            'available' => '',
            'subtype' => 'string',
            'type' => 'string|required',
        ];
    }

    public function getAbstractAllowedFilters(): array
    {
        return ['feature_list_id'];
    }
}
