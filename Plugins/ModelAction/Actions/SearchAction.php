<?php


namespace Plugins\ModelAction\Actions;


use App\Services\VersionManager;
use App\Traits\HasVersions;
use Illuminate\Http\Request;
use Plugins\CTMS\Models\Contract;
use Spatie\QueryBuilder\QueryBuilder;

class SearchAction extends EloquentAction
{
    public function handle(Request $request)
    {
        $config = $this->config;
        $class = $config['class'];
        $filter = $request->input('filter');
        try {
            $searchResult = $this->getQuery()
                ->paginate($request->input('perPage', 15))
                ->onlyModels()
                ->appends($request->query())->toArray();;
            return $searchResult;
        } catch (\Throwable $e) {
            \Log::error($e);
            return response($e->getMessage(), 400);
        }
    }
    protected function getQuery($request)
    {
        $query = $config['class']::search();
//        if (!empty($config['scopes']) && is_array($config['scopes'])) {
//            foreach ($config['scopes'] as $scope) {
//                $query = app()->call($scope . '@query', [
//                    'query' => $query
//                ]);
//            }
//        }
        return $query;
    }
}
