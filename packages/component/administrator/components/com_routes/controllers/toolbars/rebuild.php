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

class ComRoutesControllerToolbarRebuild extends ComDefaultControllerToolbarDefault
{
    public function getCommands()
    {
        $title = JText::_('Rebuild');

        $this->reset()
            ->setTitle($title)
            ->addRebuild()
        ;

        $app = JFactory::getApplication();
        $app->JComponentTitle = $title;

        return parent::getCommands();
    }

    protected function _commandRebuild(KControllerToolbarCommand $command)
    {
        $command->label = 'Rebuild';
        $command->append(array(
                'attribs' => array(
                    'data-action' => 'edit',
                    'data-data'   => '{rebuild:1}'
                ))
        );
        $command->icon = 'refresh';
    }
}