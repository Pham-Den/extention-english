<?php


namespace Plugins\ModelAction\Actions;


use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class GetListElasticAction extends EloquentAction
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
