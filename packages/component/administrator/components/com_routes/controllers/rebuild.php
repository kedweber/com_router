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

class ComRoutesControllerRebuild extends ComDefaultControllerResource
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'request' => array('layout' => 'default'),
        ));

        parent::_initialize($config);
    }
}