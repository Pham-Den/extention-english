<?php


namespace Plugins\ModelAction\Actions;


use App\Services\VersionManager;
use App\Traits\HasVersions;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class GetOptionsAction extends EloquentAction
{
    public function handle(Request $request)
    {
        $data = $request->all();
        $config = $this->config;
        $class = $config['class'];
        $model = new $class;

        $labelKey = $data['labelKey'];
        $valueKey = $data['valueKey'];

        $select = [$model->getKeyName(),"$valueKey as value","$labelKey as label","$valueKey","$labelKey"];
        if ($request->input('select')) {
            $select = array_merge($select, explode(',', $request->input('select')));
        }
        $result = QueryBuilder::for($this->getQuery($request))
            ->defaultSort($config['defaultSortOptions'] ?? $config['defaultSort'] ?? '-id')
            ->allowedIncludes($config['allowedIncludes'] ?? [])
            ->allowedFilters($config['allowedFilters'] ?? [])
            ->allowedSorts($config['allowedSorts'] ?? 'id')
            ->groupBy(\DB::raw($config['groupBy'] ?? 'id'))
            ->limit($request->input('limit', $config['limit'] ?? 500))
            ->select($select)
            ->get();

        return $this->getResponse($result, $request);
    }
}
