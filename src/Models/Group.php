<?php

namespace NovinVision\laravelGroup\Models;

class Group extends \Illuminate\Database\Eloquent\Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'parent_id',
        'status',
    ];

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Group::class, 'parent_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->hasMany(Group::class, 'parent_id', 'id');
    }
}
