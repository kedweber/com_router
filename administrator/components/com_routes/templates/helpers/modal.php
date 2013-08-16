<?php
/**
 * ComRoute
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Moyo Components
 * @subpackage  Routes
 */

class ComRoutesTemplateHelperModal extends KTemplateHelperAbstract
{
//    public function select($config = array())
//    {
//        $config = new KConfig($config);
//        $config->append(array(
//            'name' => '',
//            'visible' => true,
//            'link' => '',
//            'link_text' => $this->translate('Select'),
//            'link_selector' => 'modal'
//        ))->append(array(
//                'value' => $config->name
//            ));
//
//        $input = '<input name="%1$s" id="%1$s" value="%2$s" %3$s size="40" />';
//
//        $link = '<a class="%s"
//					rel="{\'ajaxOptions\': {\'method\': \'get\'}, \'handler\': \'iframe\', \'size\': {\'x\': 700}}"
//					href="%s">%s</a>';
//
//        $html  = sprintf($input, $config->name, $config->value, $config->visible ? 'type="text" readonly' : 'type="hidden"');
//        $html .= sprintf($link, $config->link_selector, $config->link, $config->link_text);
//
//        return $html;
//    }

    public function select($config = array())
    {
        //TODO:: Inject behavior modal.
        $config = new KConfig($config);
        $config->append(array(
            'name' => '',
            'attribs' => array(),
            'visible' => true,
            'link' => '',
            'link_text' => $this->translate('Select'),
            'link_selector' => 'modal',
            'callback' => 'Moyo.selectMenuItem'
        ))->append(array(
            'id' => $config->name,
            'value' => $config->name
        ));

        if ($config->callback) {
            $config->link .= '&callback='.$config->callback;
        }

        $attribs = KHelperArray::toString($config->attribs);

        $input = '<input name="%1$s" id="%2$s" value="%3$s" %4$s size="40" %5$s />';

        $link = '<a class="%s btn"
                    rel="{\'handler\': \'iframe\', \'size\': {\'x\': 768}}"
                    href="%s">%s</a>';

        $html = sprintf($input, $config->name, $config->id, $config->value, $config->visible ? 'type="text" readonly' : 'type="hidden"', $attribs);
        $html .= sprintf($link, $config->link_selector, $config->link, $config->link_text);

        $html .= "
        <script>
        jQuery(function($){
            if (typeof Moyo === 'undefined') Moyo = {};

            Moyo.selectMenuItem = function(selected) {
        	    $('#".$config->id."').val(selected.title);
        	    $('#".$config->target."').val(selected.id);

        	    SqueezeBox.close();
            };
        });
        </script>
        ";

        return $html;
    }
}
