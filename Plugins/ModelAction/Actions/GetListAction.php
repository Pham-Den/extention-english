<?php


namespace Plugins\ModelAction\Actions;


use App\Services\VersionManager;
use App\Traits\HasVersions;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class GetListAction extends EloquentAction
{
	public function handle(Request $request)
	{
		$config = $this->config;


		$result = QueryBuilder::for($this->getQuery($request))
			->defaultSort($config['defaultSort'] ?? '-id')
			->allowedIncludes($config['allowedIncludes'] ?? [])
			->allowedFilters($config['allowedFilters'] ?? [])
			->allowedSorts($config['allowedSorts'] ?? 'id')
			->paginate($request->input('perPage', 15))
			->appends($request->query())->toArray();

		return $this->getResponse($result, $request);
	}
}
