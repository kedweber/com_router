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

class ComRoutesDispatcher extends ComDefaultDispatcher
{
    /**
     * @param KConfig $config
     */
    protected function _initialize(KConfig $config)
	{
		$config->append(array(
            'controller' => 'patterns',
		));

		parent::_initialize($config);
	}
}