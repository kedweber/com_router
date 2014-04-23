<?php

defined('KOOWA') or die('Protected resource');

class ComRoutesModelRoutes extends ComDefaultModelDefault
{
	/**
	 * @param KConfig $config
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_state
			->insert('package'		, 'string')
			->insert('name'			, 'string')
			->insert('custom'		, 'int')
		;
	}

	/**
	 * @param KDatabaseQuery $query
	 */
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		$state = $this->_state;

		parent::_buildQueryWhere($query);

		if($state->package) {
			$query->where('tbl.package', '=', $state->package);
		}

		if($state->name) {
			$query->where('tbl.name', '=', $state->name);
		}

		if(is_numeric($state->custom)) {
			$query->where('tbl.custom', '=', $state->custom);
		}
	}
}