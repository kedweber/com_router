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

class ComRoutesDatabaseTableMenus extends KDatabaseTableDefault
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'name' => 'menu'
        ));

        parent::_initialize($config);
    }
}