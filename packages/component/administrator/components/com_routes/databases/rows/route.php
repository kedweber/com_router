<?php
/**
 * Com
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Socialhub
 * @subpackage  ...
 * @uses        Com_
 */

defined('KOOWA') or die('Protected resource');

class ComRoutesDatabaseRowRoute extends KDatabaseRowDefault
{
    public $jemoeder;

    public function save()
    {
        if(!$this->itemId) {
            $this->itemId = JApplication::getInstance('site')->getMenu()->getItems('link', 'index.php?'. $this->query, true)->id;
        }

        parent::save();
    }

    public function build($config = array())
    {
        $config = new KConfig(($config));
        $config->append(array(
            'package'	=> null,
            'name'      => null,
            'pattern'   => null,
            'relations' => null,
            'filters'   => null,
            'row'       => null,
        ));

		$config->relations = new KConfig($config->relations);

        $this->load();

        $parts      = array_reverse(array_filter(explode("/", $config->pattern)));

        $sections   = array();

        $test = array();

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
//                if(count($identifier = explode("_", $identity_column)) == 3) {
//                    $url = 'index.php?option=com_'.$identifier[0].'&view='.$identifier[1].'&id='.$this->{$identity_column};
//                } else {
//                    $url = 'index.php?option='.$config->component.'&view='.$config->view.'&'. ($identity_column ? $identity_column : $column) .'='. ($config->row->{$identity_column} ? $config->row->{$identity_column} : $config->row->{$column});
//                }
//
//                $url = 'index.php?option='.$config->component.'&view='.$config->view.'&id='.$config->row->id;
//
//                $item = $this->_getMenuItem($url);
//
//                $this->itemId = $item->id ? $item->id : null;

                $item = '';

                if(KInflector::isSingular($config->name)) {
                    $params = array();
                    foreach($config->filters as $param) {
                        $params[$param] = $config->row->{$param};
                    }

                    $url = 'index.php?option=com_'.$config->package.'&view='.KInflector::singularize($config->name).'&id='.$config->row->id;
                    if($params) {
                        $url.= '&'.http_build_query($params);
                    }

					$item = $this->_getMenuItem($url);

                    if($item->id) {
                        $this->itemId = $item->id;
                    }

                    unset($item);
                }

                if(count($identifier = explode("_", $identity_column)) == 3) {
                    if($item) {
                        $sections[] = $item->alias;
                    } else {
                        $sections[] = $this->getService('com://admin/'.$identifier[0].'.model.'.KInflector::pluralize($identifier[1]))->id($this->{$identity_column})->getItem()->{$column};
                    }
                } else {
                    if($item) {
                        $sections[] = $item->alias;
                    } else {
                        if(in_array($identifier[0], $config->relations->toArray())) {
                            if($this->{$identifier[0]}) {
                                $taxonomy = $this->getService('com://admin/taxonomy.model.taxonomies')->id($this->{$identifier[0]})->getItem();
                            } else {
                                $taxonomy = $config->row->getTaxonomy()->getAncestors(array('filter' => array('type' => $identifier[0])))->top();
                            }

                            if($taxonomy->id) {

                                $taxonomy = $taxonomy->getTaxonomy();
                                $parts = explode("_", $taxonomy->table, 2);

                                $identifier = clone $this->getIdentifier();

                                $identifier->application = 'site';
                                $identifier->package = $parts[0];
                                $identifier->path = 'model';
                                $identifier->name = $parts[1];

                                $row = $this->getService($identifier)->id($taxonomy->row)->getItem();

                                $test[] = $row->toArray();

                                $this->jemoeder = $row;

                                if($row->{$column}) {
                                    $sections[] = $row->{$column};
                                } else {
                                    continue;
                                }
                            }
                        } elseif($identity_column == 'id' && $column) {
                            $sections[] = $config->row->{$column};
                        } else {
                            if(is_object($this->jemoeder) && $this->jemoeder->isRelationable()) {
                                $current  = $this->jemoeder->getTaxonomy()->getAncestors(array('filter' => array('type' => $identity_column)))->top();
                            }

                            if($current->id)
                            {
                                $sections[] = $current->{$column};
                            } else {
                                $sections[] = $config->row->{$identity_column} ? $config->row->{$identity_column} : $config->row->{$column};
                            }
                        }
                    }
                }
            }

            unset($identity_column);
            unset($column);
        }

        if($this->isNew()) {
            $this->enabled = 1;
        }

		$sections = array_map('strtolower', $sections);
		$sections = array_map(array($this , 'sanitize'), $sections);
		$sections = array_map(array($this , '__explode'), $sections);

		$path = array();

		foreach($sections as $section) {
			$path = array_merge($path, $section);
		}

		$path = array_filter($path);

		$this->package		= $config->package;
		$this->name			= $config->name;
        $this->path			= implode('/', array_reverse($path));

		if($this->path) {
			parent::save();
		}
    }

    protected function _getMenuItem($url)
    {
        return JApplication::getInstance('site')->getMenu()->getItems('link', $url, true);
    }

	private function __explode($item)
	{
		return array_reverse(explode('/', $item));
	}

	public function sanitize($string)
	{
		$filter = $this->getService('koowa:filter.slug');

		return $filter->sanitize($string);
	}
}