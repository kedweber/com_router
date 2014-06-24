<?php

defined('KOOWA') or die('Protected resource');

use Symfony\Component\Yaml\Dumper;

class ComRoutesDatabaseRowPattern extends KDatabaseRowDefault
{
	public function save()
	{
		parent::save();

		$patterns = $this->getService('com://site/routes.model.patterns')->getList();

		$array = array();

		foreach($patterns as $pattern) {
			$array[$pattern->slug] = array(
				'path'			=> '/{_locale}'.$pattern->path,
				'defaults'		=> array_merge(array('option' => 'com_'.$pattern->package, 'view' => $pattern->name), $pattern->defaults ? json_decode($pattern->defaults, true) : array()),
				'requirements'	=> $pattern->requirements ? json_decode($pattern->requirements, true) : null
			);
		}

		$dumper = new Dumper();

		$yaml = $dumper->dump($array, 3);

		file_put_contents(JPATH_ADMINISTRATOR.'/config/com_routes/routing.yml', $yaml);
	}
}