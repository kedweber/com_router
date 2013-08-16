<?php
/**
 * ComRoutes
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Moyo Components
 * @subpackage  Routes
 */

defined('KOOWA') or die('Protected resource'); ?>

<h2 class="modal-title"><?= @text('COM_MENUS_TYPE_CHOOSE'); ?></h2>
<ul class="menu_types">
    <? foreach ($types as $name => $list) : ?>
        <li><dl class="menu_type">
                <dt><?= @text($name) ;?></dt>
                <dd>
                    <ul>
                        <? foreach ($list as $item) :?>
                            <li><a class="choose_type" href="#" title="<?= @text($item->description); ?>"
                                   onclick="window.parent.Moyo.<?= KRequest::get('get.callback', 'string'); ?>(<?= @escape(json_encode(array('title' => @text($item->title), 'request' => $item->request))); ?>)">
                                    <?= @text($item->title);?>
                                </a>
                            </li>
                        <? endforeach; ?>
                    </ul>
                </dd>
            </dl>
        </li>
    <? endforeach; ?>
</ul>