<?php
/**
 * ComRouter
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Moyo Components
 * @subpackage  Router
 */
 
defined('KOOWA') or die('Protected resource');

class ComRouterDatabaseTableMenu_items extends KDatabaseTableDefault
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'name' => 'menu'
        ));

        parent::_initialize($config);
    }
}