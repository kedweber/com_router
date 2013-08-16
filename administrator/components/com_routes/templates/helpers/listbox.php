<?php
/**
 * ComRoute
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Moyo Components
 * @subpackage  Routes
 */

class ComRoutesTemplateHelperListbox extends ComDefaultTemplateHelperListbox
{
    public function types($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'model'    => 'menu_types',
            'value'    => 'menutype',
            'name'     => 'menutype',
            'text'     => 'title',
            'attribs'  => array('onchange' => 'this.form.submit();')
        ));

        return parent::_listbox($config);
    }
}