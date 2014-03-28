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
        $package    = KRequest::get('get.option', 'string');
        $view       = KRequest::get('get.view', 'string');

        $pattern    = $this->getService('com://admin/routes.model.patterns')->component($package)->view(KInflector::singularize($view))->getItem();
        $parts      = array_filter(explode("/", $pattern->pattern));

        $sections   = array();

        foreach($parts as $part) {
            foreach(explode("|", $part) as $value) {
                if(substr($value, 0, 1) == ':') {
                    $identity_column = str_replace(':', '', $value);
                } else {
                    $column = $value;
                }
            }

            if(!$identity_column && $column) {
                $sections[] = $column;
            } else {
                if(count($identifier = explode("_", $identity_column)) == 3) {
                    $url = 'index.php?option=com_'.$identifier[0].'&view='.$identifier[1].'&id='.$context->data->{$identity_column};
                } else {
                    $url = 'index.php?option='.$package.'&view='.$view.'&'. ($identity_column ? $identity_column : $column) .'='. ($context->data->{$identity_column} ? $context->data->{$identity_column} : $context->data->{$column});
                }

                $item = $this->_getMenuItem($url);

                $itemtitle = $item->title ? $item->title : $itemtitle;
                $itemid = $item->id ? $item->id : $itemid;

                if(!$itemid && KInflector::isSingular($view)) {

                    $params = array();
                    foreach($this->_filters as $filter) {
                        $params[$filter] = $context->data->{$filter};
                    }

                    $url = 'index.php?option='.$package.'&view='.KInflector::pluralize($view);
//                    if($params) {
                        $url.= '&'.http_build_query($params);
//                    }

                    $item = $this->_getMenuItem($url);

                    if($item->id) {
                        $itemid = $item->id;
                    }

                    unset($item);
                }

                if(count($identifier = explode("_", $identity_column)) == 3) {
                    if($item) {
                        $sections[] = $item->alias;
                    } else {
                        $sections[] = $this->getService('com://admin/'.$identifier[0].'.model.'.KInflector::pluralize($identifier[1]))->id($context->data->{$identity_column})->getItem()->{$column};
                    }
                } else {
                    if($item) {
                        $sections[] = $item->alias;
                    } else {
                        if(in_array($identifier[0], $this->_ancestors->toArray())) {
                            $taxonomy = $this->getService('com://admin/taxonomy.model.taxonomies')->id($context->data->{$identifier[0]})->getItem();

                            if($taxonomy->id) {
                                $parts = explode("_", $taxonomy->table, 2);

                                $identifier = clone $this->getIdentifier();

                                $identifier->application = 'site';
                                $identifier->package = $parts[0];
                                $identifier->path = 'model';
                                $identifier->name = $parts[1];

                                $row = $this->getService($identifier)->id($taxonomy->row)->getItem();

                                if($row->{$column}) {
                                    $sections[] = $row->{$column};
                                } else {
                                    continue;
                                }
                            }
                        } elseif($identity_column == 'id' && $column) {
                            $sections[] = $context->data->{$column};
                        } else {
                            $sections[] = $context->data->{$identity_column} ? $context->data->{$identity_column} : $context->data->{$column};
                        }
                    }
                }
            }

            unset($identity_column);
            unset($column);
        }

        $sections = array_map('strtolower', $sections);
		$sections = array_map(array($this , 'sanitize'), $sections);

        $path   = implode('/', $sections);
        $query  = 'option='.$package.'&view='.KInflector::singularize($view).'&id='.$context->data->id;

        if($path && $query) {
            $iso_code = substr(JFactory::getLanguage()->getTag(), 0, 2);

            $row = $this->getService('com://admin/routes.model.routes')->query($query)->lang($iso_code)->getItem();
            $row->setData(array(
                'path'      => $path,
                'query'     => $query,
                'menu_title'=> $itemtitle,
                'itemId'    => $itemid,
                'enabled'   => 1,
                'lang'      => $iso_code
            ));
            $row->save();
        }
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