<?php
/**
 * ComRoutes
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Moyo Components
 * @subpackage  Routes
 */
 
defined('KOOWA') or die('Protected resource');

class ComRoutesDatabaseTableRoutes extends KDatabaseTableDefault
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'name' => 'routes'
        ));

        parent::_initialize($config);
    }
}