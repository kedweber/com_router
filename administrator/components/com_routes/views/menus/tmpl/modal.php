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

defined('KOOWA') or die('Protected resource'); ?>

<?= @helper('behavior.bootstrap'); ?>
<?= @helper('behavior.mootools'); ?>

<script src="media://lib_koowa/js/koowa.js" />

<div class="com_routes">
    <form action="<?= @route('&layout=modal'); ?>" method="get" class="-koowa-grid" data-toolbar=".toolbar-list">
        <table class="table table-striped">
            <colgroup>
                <col style="width: 50%;">
                <col style="width: 50%;">
            </colgroup>
            <thead>
                <tr>
                    <th>
                        <?= @helper('grid.sort', array('column' => 'title')) ?>
                    </th>
                    <th>
                        <?= @helper('grid.sort', array('column' => 'menutype')) ?>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <?= @helper('paginator.pagination', array('total' => $total)) ?>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                <tr>
                    <td>
                        <div class="input-append">
                            <input type="text" id="search" name="search" value="<?= $state->search; ?>" placeholder="Search..." >
                            <button class="btn"><i class="icon-search"></i>&nbsp;</button>
                            <button type="button" class="btn" onclick="document.getElementById('search').value='';this.form.submit();"><i class="icon-remove"></i>&nbsp;</button>
                        </div>
                    </td>
                    <td>
                        <?= @helper('listbox.types'); ?>
                    </td>
                </tr>
            <? foreach($menus as $menu) : ?>
                <tr>
                    <td>
                        <? if ($state->callback) : ?>
                            <a onclick="window.parent.<?= $state->callback; ?>(<?= @escape(json_encode($menu->toArray())); ?>);">
                               <?= $menu->title; ?>
                            </a>
                        <? else : ?>
                            <?= $menu->title; ?>
                        <? endif; ?>
                    </td>
                    <td>
                        <?= $menu->menutype_title; ?>
                    </td>
                </tr>
            <? endforeach ?>
            </tbody>
        </table>
      </form>
</div>