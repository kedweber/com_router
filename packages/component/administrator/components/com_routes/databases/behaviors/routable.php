<?php
/**
 * ComRoutes
 *
 * @author      Dave Li <info@kubica.nl>
 * @category    Nooku
 * @package     Components
 * @subpackage  Routable
 */
 
defined('KOOWA') or die('Protected resource');

//TODO:: Merge ancestors and descendants into relations!
class ComRoutesDatabaseBehaviorRoutable extends KDatabaseBehaviorAbstract
{
    /**
     * @var mixed
     */
    protected $_ancestors;

    /**
     * @var mixed
     */
    protected $_descendants;

    /**
     * @var mixed
     */
    protected $_filters;

    /**
     * @param KConfig $config
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority' => KCommand::PRIORITY_LOWEST,
        ));

        parent::_initialize($config);
    }

    /**
     * @param KConfig $config
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_ancestors   = $config->ancestors;
        $this->_descendants = $config->descendants;
        $this->_filters = $config->filters;
    }

    /**
     * @param KCommandContext $context
     */
    protected function _afterTableInsert(KCommandContext $context)
    {
		//TODO: Add multilingual
		$package    = str_replace('com_', null, KRequest::get('get.option', 'string'));
		$view       = KRequest::get('get.view', 'string');

		$config = array(
			'component' => $package,
			'view'      => $view,
			'relations' => new KConfig(),
			'pattern'   => $this->getService('com://admin/routes.model.patterns')->component($package)->view(KInflector::singularize($view))->getItem()->pattern,
			'row'       => $context->data,
		);

		$route          = $this->getService('com://admin/routes.database.row.route');
		$route->query   = 'option=com_'.$package.'&view='.$view.'&id='.$context->data->id;
		$route->lang    = substr(JFactory::getLanguage()->getTag(), 0, 2);
		$route->build($config);


		$cache = JFactory::getCache('router', '');
		$cache->setCaching(true);

		$query = array(
			'lang'		=> $route->lang,
			'option'	=> 'com_'.$package,
			'view'		=> $view,
			'format'	=> 'html',
			'id'		=> $context->data->id,
		);

		$cacheId = http_build_query($query);

		$cache->store(array($route->path, $query), 'build: '.$cacheId);

		$route->save();
    }

    /**
     * @param KCommandContext $context
     */
    protected function _afterTableUpdate(KCommandContext $context)
    {
        $this->_afterTableInsert($context);
    }

    protected function _getMenuItem($url)
    {
        return JApplication::getInstance('site')->getMenu()->getItems('link', $url, true);
    }

    /**
     * @param $column
     * @return bool
     */
    protected function isIdentityColumn($column)
    {
        $result = false;

        if($column === 'id' || $column === $this->getMixer()->getIdentityColumn()) {
            $result = true;
        }

        return $result;
    }

    public function getRelations()
    {
        return $this->_ancestors;
    }

    public function getFilters()
    {
        return $this->_filters;
    }

	public function sanitize($string)
	{
		$filter = $this->getService('koowa:filter.slug');

		return $filter->sanitize($string);
	}
}