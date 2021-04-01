<?php

namespace App\Traits;

trait AppendQueryParameters
{
    public static function appendFilterCriteria($query)
    {
        $filterable = static::$filterable;
        if (isset($filterable)) {
            $filterFromRequest = request()->get('filters');
            if (is_array($filterFromRequest)) {
                $filterFromRequestCollection = collect($filterFromRequest,);
                $filterFromRequestCollection->each(function ($requestFilterValue, $requestFilterKey) use (&$query, $filterable) {
                    if (in_array($requestFilterKey, array_keys($filterable))) {
                        $query->where($filterable[$requestFilterKey], $requestFilterValue);
                    }
                });
            }
        }

        return $query;
    }

    public static function appendPagingCriteria($query)
    {
        $page = request()->get('page') ?? static::DEFAULT_PAGE;
        $perPage = request()->get('per_page') ?? static::DEFAULT_PER_PAGE;
        if ($page > 1) {
            $query->skip(($page - 1) * $perPage)->take($perPage);
        }
        return $query;
    }

    public static function appendSearchCriteria($query)
    {
        $search = request()->get('search');
        if ($search) {
            $query->where(function ($query) use ($search) {
                $searchableCollection = collect(static::$searchable);
                $searchableCollection->each(function ($searchKey) use (&$query, $search) {
                    $query->OrWhere($searchKey, 'LIKE', '%' . $search . '%');
                });
            });
        }
        return $query;
    }

    public static function appendQueryOptionsToRequest($query)
    {
        $query = static::appendFilterCriteria($query);
        $query = static::appendPagingCriteria($query);
        $query = static::appendSearchCriteria($query);
        return $query;
    }
}
