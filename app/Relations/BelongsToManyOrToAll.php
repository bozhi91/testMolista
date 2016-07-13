<?php namespace App\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BelongsToManyOrToAll extends BelongsToMany {

    /**
     * The attribute that sets all included.
     *
     * @var string
     */
    protected $allAttribute;

    /**
     * The value for $allAttribute.
     *
     * @var string
     */
    protected $allValue;

    /**
     * Create a new belongs to many relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  string  $relationName
     * @return void
     */
    public function __construct(Builder $query, Model $parent, $table, $foreignKey, $otherKey, $allAttribute, $allValue, $relationName = null)
    {
        $this->table = $table;
        $this->otherKey = $otherKey;
        $this->foreignKey = $foreignKey;
        $this->relationName = $relationName;
        $this->allAttribute = $allAttribute;
        $this->allValue = $allValue;

        parent::__construct($query, $parent, $table, $foreignKey, $otherKey, $relationName);
    }

    /**
     * Set the join clause for the relation query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|null  $query
     * @return $this
     */
    protected function setJoin($query = null)
    {
        $query = $query ?: $this->query;

        // We need to join to the intermediate table on the related model's primary
        // key column with the intermediate table's foreign key for the related
        // model instance. Then we can set the "where" for the parent models.
        $baseTable = $this->related->getTable();

        $key = $baseTable.'.'.$this->related->getKeyName();

        $query->leftJoin($this->table, $key, '=', $this->getOtherKey());

        return $this;
    }

    /**
     * Set the where clause for the relation query.
     *
     * @return $this
     */
    protected function setWhere()
    {
        $foreign = $this->getForeignKey();
        $parent_key = $this->parent->getKey();
        $related = $this->related->getTable();
        $all_attribute = $this->allAttribute;
        $all_value = $this->allValue;

        $this->query->orWhere(function($query) use ($foreign, $parent_key, $related, $all_attribute, $all_value)
        {
            $query->where($foreign, '=', $parent_key);
            $query->orWhere($related.'.'.$all_attribute, '=', $all_value);
        });

        return $this;
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        $foreign = $this->getForeignKey();
        $parent_key = $this->parent->getKey();
        $related = $this->related->getTable();
        $all_attribute = $this->allAttribute;
        $all_value = $this->allValue;

        $this->query->orWhere(function($query) use ($foreign, $parent_key, $related, $all_attribute, $all_value, $models)
        {
            $this->query->whereIn($foreign, $this->getKeys($models));
            $query->orWhere($related.'.'.$all_attribute, '=', $all_value);
        });

        return $this;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        $dictionary = [];

        foreach ($results as $result) {
            if ($result->{$this->allAttribute} == $this->allValue) {
                foreach ($models as $model) {
                    $dictionary[$model->getKey()] []= $result;
                }
            } else {
                $dictionary[$result->pivot->{$this->foreignKey}][] = $result;
            }
        }

        // Once we have an array dictionary of child objects we can easily match the
        // children back to their parent using the dictionary and the keys on the
        // the parent models. Then we will return the hydrated models back out.
        foreach ($models as $model) {
            if (isset($dictionary[$key = $model->getKey()])) {
                $collection = $this->related->newCollection($dictionary[$key]);

                $model->setRelation($relation, $collection);
            }
        }

        return $models;
    }
}
