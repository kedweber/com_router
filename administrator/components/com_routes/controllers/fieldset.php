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

class ComRoutesControllerFieldset extends ComDefaultControllerDefault
{
//    public function getRequest()
//    {
//        $this->_request->append(array(
//            'option' => 'com_content',
//            'view'  => 'article',
//            'fieldset' => 'request',
//        ));
//
//        return $this->_request;
//    }

    protected function _actionRead(KCommandContext $context)
    {
        $data = $this->getModel()->getItem();

        return $data;
    }
}