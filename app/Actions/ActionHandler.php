<?php


namespace App\Actions;

class ActionHandler
{
	protected $actions = [];
	protected $middlewares = [];
	protected $permissions = [];
	protected $configs = [];

	public function __construct(protected $beforeHandle = null)
	{
	}

	public function middlewares($middlewares = [])
	{
		$this->middlewares = $middlewares;
		return $this;
	}

	public function permissions($permissions)
	{
		$this->permissions = $permissions;
		return $this;
	}

	public function bindRoute($path)
	{
		\Route::any($path, function (\Illuminate\Http\Request $request) {
			$prefix = $request->route('prefix');
			$permissions = $this->permissions[$prefix] ?? [];
			return app()->call([$this, 'handle'], [...$request->route()->parameters(), 'permissions' => $permissions, 'request' => $request]);
		})->middleware(['web', ...$this->middlewares]);
	}

	public function register($prefix, $actions, $permissions = [], $config = [])
	{
		if (!isset($this->actions[$prefix]) || !is_array($this->actions[$prefix])) {
			$this->actions[$prefix] = [];
		}
		$this->actions[$prefix] = array_merge($this->actions[$prefix], $actions);
		$this->permissions[$prefix] = $permissions;
		$this->configs[$prefix] = $config;
	}

	public function handle($prefix, $action, $permissions, $request)
	{
		ob_start();
		$checkAccess = true;
		$config = $this->configs[$prefix] ?? [];

		//re assign custom request from config
		if (!empty($config['requests']) && !empty($config['requests'][$action])) {
			$request = app($config['requests'][$action]);
		}

		if (!empty($this->actions[$prefix][$action])) {
			if (is_string($this->actions[$prefix][$action])) {
				$result = app()->call($this->actions[$prefix][$action] . '@handle', ['request' => $request]);
				return $result;
			}
			$result = app()->call([$this->actions[$prefix][$action], 'handle'], ['request' => $request]);
			return $result;
		}
		throw new \Exception('Action not found!');
	}
}

