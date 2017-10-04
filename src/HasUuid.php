<?php
namespace Rap2hpoutre\Models;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ramsey\Uuid\Uuid;

/**
 * Trait HasUuid
 *
 * Why choose between seq id and uuid? Use both!
 * Highly inspired by: https://gist.github.com/danb-humaan/b385ef92ed2336fd5d12
 *
 * @package Rap2hpoutre\Models
 * @property mixed uuid
 * @method static uuid(string $uuid)
 */
trait HasUuid
{
    /**
     * Binds creating/saving events to create UUIDs (and also prevent them from being overwritten).
     *
     * @return void
     */
    public static function bootUuidModel()
    {
        static::creating(function ($model) {
            // Don't let people provide their own UUIDs, we will generate a proper one.
            $model->uuid = Uuid::uuid4()->toString();
        });
        static::saving(function ($model) {
            // What's that, trying to change the UUID huh?  Nope, not gonna happen.
            $original_uuid = $model->getOriginal('uuid');
            if ($original_uuid !== $model->uuid) {
                $model->uuid = $original_uuid;
            }
        });
    }

    /**
     * Scope a query to only include models matching the supplied UUID.
     * Returns the model by default, or supply a second flag `false` to
     * get the Query Builder instance.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @param  \Illuminate\Database\Schema\Builder $query The Query Builder instance.
     * @param  string $uuid  The UUID of the model.
     * @param  bool|true $first Returns the model by default, or set to `false` to chain for query builder.
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeUuid($query, $uuid, $first = true)
    {
        if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $uuid) !== 1)) {
            throw (new ModelNotFoundException)->setModel(get_class($this));
        }
        $search = $query->where('uuid', $uuid);
        return $first ? $search->firstOrFail() : $search;
    }

    /**
     * Save the pivot.
     *
     * @param BelongsToMany $relation
     * @param $item
     * @param array $attributes
     * @return $this
     */
    public function savePivot(BelongsToMany $relation, $item, $attributes = [])
    {
        $relation->withTimestamps()->save($item, array_merge(['uuid' => Uuid::uuid4()->toString()], $attributes));
        return $this;
    }
}