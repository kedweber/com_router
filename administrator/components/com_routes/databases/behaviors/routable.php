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

class ComRoutesDatabaseBehaviorRoutable extends KDatabaseBehaviorAbstract
{
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
     * @param KCommandContext $context
     */
    protected function _afterTableInsert(KCommandContext $context)
    {
        //TODO: Add multilingual
        $package    = KRequest::get('get.option', 'string');
        $view       = KRequest::get('get.view', 'string');

        $pattern    = $this->getService('com://admin/routes.model.patterns')->component($package)->view($view)->getItem();
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
                        if($identity_column == 'id' && $column) {
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

        $path   = implode('/', $sections);
        $query  = 'option='.$package.'&view='.KInflector::singularize($view).'&id='.$context->data->id;

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
}