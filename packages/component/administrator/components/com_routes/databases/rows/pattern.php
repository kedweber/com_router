<?php

defined('KOOWA') or die('Protected resource');

use Symfony\Component\Yaml\Dumper;

class ComRoutesDatabaseRowPattern extends KDatabaseRowDefault
{
	public function save()
	{
		parent::save();

		if($this->getModified()) {
			$patterns = $this->getService('com://site/routes.model.patterns')->getList();

			$array = array();

			foreach($patterns as $pattern) {
				$array[$pattern->slug] = array(
					'path'			=> '/{_locale}'.$pattern->path,
					'defaults'		=> array('option' => 'com_'.$pattern->package, 'view' => $pattern->name),
					'requirements'	=> $pattern->requirements ? json_decode($pattern->requirements, true) : null
				);

				error_log($pattern->requirements);
			}

			$dumper = new Dumper();

			$yaml = $dumper->dump($array, 3);

			file_put_contents(JPATH_ADMINISTRATOR.'/components/com_routes/config/routing.yml', $yaml);
		}
	}
}