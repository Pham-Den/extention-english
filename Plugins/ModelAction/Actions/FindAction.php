<?php


namespace Plugins\ModelAction\Actions;


use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class FindAction extends EloquentAction
{
	public function handle(Request $request)
	{
		$config = $this->config;
		$class = $config['class'];
		$model = new $class;
		$query = $class::query();
		$query->where($model->getKeyName(), $request->input('id', 0));
		$result = $query->with($config['allowedIncludes'] ?? [])->first();

		$appends = $request->input('appends', []);
		if (empty($appends)) {
			$appends = $config['appends']['find'] ?? [];
		}
		if (!empty($appends)) {
			$result->append($appends);
		}

		$result = $this->getResponse($result->toArray(), $request);
		return $result;
	}
}
