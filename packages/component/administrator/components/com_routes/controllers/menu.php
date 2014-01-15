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

class ComRoutesControllerMenu extends ComDefaultControllerDefault
{
    protected function _actionLink(KCommandContext $context)
    {
        $parts = array();

        if($context->data->request) {
            foreach($context->data->request as $key => $value) {
                $parts[] = $key.'='.$value;
            }
        }

        $url = implode('&', $parts);

        //TODO: Improve!
        header('Content-Type: application/json');
        echo json_encode(array('url' => $url));
        exit;
    }
}