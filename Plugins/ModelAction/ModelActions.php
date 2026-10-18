<?php


namespace Plugins\ModelAction;


use Plugins\ModelAction\Actions\BulkSaveAction;
use Plugins\ModelAction\Actions\DeleteAction;
use Plugins\ModelAction\Actions\FindAction;
use Plugins\ModelAction\Actions\FindByAction;
use Plugins\ModelAction\Actions\GetAllAction;
use Plugins\ModelAction\Actions\GetListAction;
use Plugins\ModelAction\Actions\GetListElasticAction;
use Plugins\ModelAction\Actions\GetOptionsAction;
use Plugins\ModelAction\Actions\SaveAction;
use Plugins\ModelAction\Actions\SearchAction;

class ModelActions
{
    public static function createActions($modelName, $config)
    {
        $actions=[];
        // dd(1);
        if (!empty($config['class'])) {
            $actions = [
                'list-search' => new SearchAction($modelName, $config),
                'list' => new GetListAction($modelName, $config),
                'all' => new GetAllAction($modelName, $config),
                'delete' => new DeleteAction($modelName, $config),
                'paginate' => new GetListAction($modelName, $config),
                'find' => new FindAction($modelName, $config),
                'findBy' => new FindByAction($modelName, $config),
                'save' => new SaveAction($modelName, $config),
                'bulk-save' => new BulkSaveAction($modelName, $config),
                'update' => new SaveAction($modelName, $config),
                'create' => new SaveAction($modelName, $config),
                'options' => new GetOptionsAction($modelName, $config),
                'paginate-elastic' => new GetListElasticAction($modelName, $config),
            ];
            $onlyActions = $config['onlyActions'] ?? [];
            $actions = array_filter(
                $actions,
                fn($key) => in_array($key, $onlyActions),
                ARRAY_FILTER_USE_KEY
            );
        }
        return array_merge($actions, $config['actions'] ?? []);
    }
}
