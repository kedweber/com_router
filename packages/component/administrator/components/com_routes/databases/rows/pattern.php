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

class ComRoutesDatabaseRowPattern extends KDatabaseRowDefault
{
    public function save()
    {
        if($this->rebuild == 1) {
            $identifier             = clone $this->getIdentifier();
            $identifier->package    = str_replace('com_', '', $this->component);
            $identifier->path       = array('controller');
            $identifier->name       = KInflector::singularize($this->view);

            $rows = $this->getService($identifier)->limit(0)->browse();

            //TODO:: Support for composite keys!
            foreach($rows as $row) {
                $relations  = array();
                $table      = $row->getTable();

//                echo "<pre>";
//                print_r( $table->getBehavior('routable')->getRelations());
//                echo "</pre>";
//                exit;

                if($table->hasBehavior('routable')) {
                    $relations  = $table->getBehavior('routable')->getRelations();
                    $filters    = $table->getBehavior('routable')->getFilters();
                }

                $config = array(
                    'component' => $this->component,
                    'view'      => $this->view,
                    'pattern'   => $this->pattern,
                    'relations' => $relations,
                    'filters'   => $filters,
                    'row'       => $row,
                );

                $route          = $this->getService('com://admin/routes.database.row.route');
                $route->query   = 'option=com_'.$identifier->package.'&view='.$identifier->name.'&id='.$row->id;
                $route->lang    = substr(JFactory::getLanguage()->getTag(), 0, 2);
                $route->build($config);
            }

            return false;
        }

		parent::save();
    }
}