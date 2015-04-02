<?php namespace KodiCMS\CMS\Support;

class ModuleLoader {

	/**
	 * @var array
	 */
	protected $_registeredModules = [];

	/**
	 * @param array $modulesList
	 */
	public function __construct(array $modulesList)
	{
		foreach ($modulesList as $moduleName => $modulePath) {
			if(is_numeric($moduleName)) {
				$moduleName = $modulePath;
				$modulePath = NULL;
			}
			$this->addModule($moduleName, $modulePath);
		}
	}

	/**
	 * @return array
	 */
	public function getRegisteredModules()
	{
		return $this->_registeredModules;
	}

	/**
	 * @param string $moduleName
	 * @param string $modulePath
	 * @return $this
	 */
	public function addModule($moduleName, $modulePath)
	{
		$this->_registeredModules[] = new Module($moduleName, $modulePath);

		return $this;
	}

	/**
	 * @return $this
	 */
	public function bootModules()
	{
		foreach ($this->_registeredModules as $module) {
			$module->boot();
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function registerModules()
	{
		foreach ($this->_registeredModules as $module) {
			$module->register();
		}

		return $this;
	}
}