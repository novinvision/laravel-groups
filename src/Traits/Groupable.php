<?php

namespace NovinVision\laravelGroup\Traits;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use NovinVision\laravelGroup\Models\Group;
use NovinVision\laravelGroup\Models\GroupItem;

trait Groupable
{

    protected array $queuedGroups = [];

    public function groups()
    {
        return $this->morphToMany(Group::class, 'item', GroupItem::class);
    }

    public static function bootGroupable()
    {
        static::created(function (Model $model) {
            if (count($model->queuedGroups) === 0) {
                return;
            }

            $model->attachGroups($model->queuedGroups);
            $model->queuedGroups = [];
        });

        static::deleted(function (Model $deletedModel) {
            $groups = $deletedModel->groups()->get();

            $deletedModel->detachGroups($groups);
        });
    }

    public function syncGroups(string|array|Arrayable $groups, string $field = 'id'): static
    {
        $groups = \App\Models\Group::query()->whereIn($field, $groups)->get();
        $this->groups()->sync($groups->pluck('id')->toArray());
        return $this;
    }

    public function setGroupsAttribute(string|array|ArrayAccess|Group $groups)
    {
        if (!$this->exists) {
            $this->queuedGroups = $groups;
            return;
        }

        $this->syncGroups($groups);
    }

    protected static function convertToGroups($values, $by = 'id'): \Illuminate\Support\Collection
    {
        if ($values instanceof Group) {
            $values = [$values];
        }

        return collect($values)->map(function ($value) use ($by) {
            if ($value instanceof Group) {
                return $value;
            }

            return Group::query()->where($by, $value)->first();
        });
    }

    public function detachGroups(array|Arrayable|string $groups, string|null $by = 'id'): static
    {
        if (is_string($groups)) {
            $groups = Arr::wrap($groups);
        }

        if (!$groups instanceof Arrayable) {
            $groups = collect($groups);
        }

        $groups = static::convertToGroups($groups, $by);
        collect($groups)->filter()->each(fn(Group $group) => $this->groups()->detach($group));

        return $this;
    }

    public function attachGroups(array|ArrayAccess|Group $groups, string $field = 'id'): static
    {
        $groups = Group::query()->whereIn($field, $groups)->get();
        $this->groups()->syncWithoutDetaching($groups->pluck('id')->toArray());

        return $this;
    }

}
