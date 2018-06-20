<?php

namespace App\Traits;

use App\Exceptions\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model as EloquentModel;

trait DbRepositoryTrait
{
  /**
   * Get all of the models from the database.
   *
   * @return \Illuminate\Support\Collection
   */
  public function all()
  {
    return $this->query()->get();
  }

  /**
   * Get the paginated models from the database.
   *
   * @param  int $perPage
   * @return \Illuminate\Pagination\LengthAwarePaginator
   */
  public function paginate($perPage = 15)
  {
    return $this->query()->latest()->paginate($perPage);
  }

  /**
   * Get a model by its primary key.
   *
   * @param int $id
   * @param array $related
   * @return \Illuminate\Database\Eloquent\Model
   *
   * @throws \App\Exceptions\ModelNotFoundException
   */
  public function get($id, array $related = null)
  {
    $model = $this->query()->find($id);

		if (! $model) throw new ModelNotFoundException(sprintf('No %s record found.', getClassName($this->model)));

		return $model;
  }

  /**
   * Get the model data by adding the given where query.
   *
   * @param  string     $column
   * @param  mixed      $value
   * @param  array|null $related
   * @return \Illuminate\Database\Eloquent\Collection
   *
   * @throws \App\Exceptions\ModelNotFoundException
   */
  public function getWhere($related = [])
  {
    $query = $this->query();

    foreach($related as $key => $value)
    {
      $query = $query->where($key, $value);
    }

    $models = $query->get();

		if ($models->isEmpty()) throw new ModelNotFoundException(sprintf('No %s record found.', getClassName($this->model)));

    return $models;
  }

  public function getPaginatedWhere($related = [])
  {
    $query = $this->query();

    foreach($related as $key => $value)
    {
      $query = $query->where($key, $value);
    }

    $models = $query->paginate();

    if ($models->isEmpty()) throw new ModelNotFoundException(sprintf('No %s record found.', getClassName($this->model)));

    return $models;
  }

  /**
   * Save a new model and return the instance.
   *
   * @param array $attributes
   * @return \Illuminate\Database\Eloquent\Model
   */
  public function create(array $attributes)
  {
    $model = $this->model;

    return $model::create($attributes);
  }

  /**
   * Get the first record matching the attributes or instantiate it.
   *
   * @param array $attributes
   * @return \Illuminate\Database\Eloquent\Model
   */
  public function firstOrNew(array $attributes)
  {
    $model = $this->model;

    return $model::firstOrNew($attributes);
  }

  /**
   * Get the first record matching the attributes or create it.
   *
   * @param array $attributes
   * @return \Illuminate\Database\Eloquent\Model
   */
  public function firstOrCreate(array $attributes)
  {
    $model = $this->model;

    return $model::firstOrCreate($attributes);
  }

  /**
   * Update the model by the given attributes.
   *
   * @return \Illuminate\Database\Eloquent\Model $model
   * @return bool|int
   */
  public function update($model, array $attributes)
  {
    $model->update($attributes);

    return $model;
  }

  /**
   * Delete the model from the database.
   *
   * @return \Illuminate\Database\Eloquent\Model $model
   * @return bool|null
   *
   * @throws \Exception
   */
  public function delete($model)
  {
    return ($model instanceof EloquentModel) ? $model->delete() : $this->get($model)->delete();
  }

  /**
   * @param int $perPage
   * @return mixed
   */
  public function  getRecentPaginated($perPage = 15)
  {
    $query = $this->query();

    $query = $query->orderBy('created_at', "DESC");

    $collection = $query->paginate($perPage);

    return $collection;
  }

  /**
	 * Begin querying the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function query()
	{
		return call_user_func("{$this->model}::query");
	}
}
