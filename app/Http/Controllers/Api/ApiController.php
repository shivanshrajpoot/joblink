<?php

namespace App\Http\Controllers\Api;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use App\Http\Controllers\Controller;
use League\Fractal\Resource\Collection;
use App\Traits\ApiResponseHandlerTrait;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Transformers\Serializer\CustomDataArraySerializer;

class ApiController extends Controller
{

  use ApiResponseHandlerTrait;

  function __construct()
  {
    $this->fractal = new Manager;

    $this->fractal->setSerializer(new CustomDataArraySerializer);

    // parse Includes
    if (request()->get('include')) $this->fractal->parseIncludes(request()->get('include'));

    // set per page limit
    if( request()->get('limit')) $this->perPage = request()->get('limit');
  }

  /**
   * respond with a single item
   *
   * @param $item
   * @param $callback Transformer function
   * @return \Illuminate\Http\JsonResponse
   */
  protected function respondWithItem($item, $callback)
  {
    $resource = new Item($item, $callback);

    $rootScope = $this->fractal->createData($resource);

    return response()->success($rootScope->toArray());
  }

  /**
   * respond with a collection and meta ( pagination meta )
   *
   * @param $collection
   * @param $callback
   * @return \Illuminate\Http\JsonResponse
   */
  protected function respondWithPagination($collection, $inputs, $callback, $meta = [])
  {
    $paginator = $collection;

    $paginator->appends($inputs);

    $collection = $paginator->getCollection();

    $resource = new Collection($collection, $callback);

    if(!empty($meta)) $resource->setMeta($meta);

    $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

    $rootScope = $this->fractal->createData($resource);

    return response()->success($rootScope->toArray());
  }

  /**
   * @param $collection
   * @param $inputs
   * @param $callback
   * @return \Illuminate\Http\JsonResponse
   */
  public function respondWithCustomPagination($collection, $inputs, $callback)
  {
    $paginator = $collection;

    $paginator->appends($inputs);

    $collection = $paginator->getCollection();

    $resource = new Collection($collection->slice($this->perPage*($paginator->currentPage() - 1), $this->perPage), $callback);

    $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

    $rootScope = $this->fractal->createData($resource);

    return response()->success($rootScope->toArray());
  }

  /**
   * respond with collection of items
   *
   * @param $collection
   * @param $callback
   * @return \Illuminate\Http\JsonResponse
   */
  protected function respondWithCollection($collection, $callback)
  {
    $resource = new Collection($collection, $callback);

    $rootScope = $this->fractal->createData($resource);

    return response()->success($rootScope->toArray());
  }


  /**
   * add version info to api
   *
   * @param $data
   * @return mixed
   */
  public function addVersionInfo($data)
  {
    $data['minimumVersion'] = env('API_MINIMUM_VERSION');
    $data['currentVersion'] = env('API_CURRENT_VERSION');

    return $data;
  }

}