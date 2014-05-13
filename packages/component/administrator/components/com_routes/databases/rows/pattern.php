<?php

defined('KOOWA') or die('Protected resource');

use Symfony\Component\Yaml\Dumper;

class ComRoutesDatabaseRowPattern extends KDatabaseRowDefault
{
	public function save()
	{
		if($this->getModified()) {
			$patterns = $this->getService('com://site/routes.model.patterns')->getList();

			$array = array();

			foreach($patterns as $pattern) {
				$array[$pattern->slug] = array(
					'path'			=> '/{_locale}'.$pattern->path,
					'defaults'		=> $pattern->path,
					'defaults'		=> array('option' => 'com_'.$pattern->package, 'view' => $pattern->name),
					'requirements'	=> $pattern->requirements
				);
			}

			$dumper = new Dumper();

			$yaml = $dumper->dump($array, 2);

			file_put_contents(JPATH_ADMINISTRATOR.'/components/com_routes/config/routing.yml', $yaml);
		}

		parent::save();
	}
}