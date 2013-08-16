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
<?= @helper('behavior.jquery'); ?>
<?= @helper('behavior.modal', array('selector' => 'a.modal-button')); ?>

<script>
    jQuery(function($){
        if (typeof Moyo === 'undefined') Moyo = {};

        Moyo.setComponent = function(selected) {

            request = selected.request;

            $('#query').val(selected.title);

            Moyo.setType(selected.request.option, selected.request.view);

            SqueezeBox.close();
        };

        Moyo.setType = function (option, view) {
            $('#fieldset').load('index.php?option=com_routes&view=fieldset&tmpl=component&componentname=' + option + '&componentview=' + view);
        }

        $("#test").submit(function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: 'index.php?option=com_routes&view=menu&tmpl=component',
                data: $.param({request: request}) + '&' + $(this).serialize() + '&action=link',
                success: function (data) {
                    window.parent.Moyo.setQuery(data.url);
                },
                cache: false
            });
        });
    });
</script>

<form action="" id="test" method="POST">
    <div class="row-fluid">
        <div class="span6">
            <div class="input-append">
                <?= @helper('modal.select', array(
                    'name'  => 'query',
                    'id' => 'query',
                    'value' => $route->query,
                    'link'  => @route('index.php?option=com_routes&view=menutypes&tmpl=component'),
                    'link_selector' => 'modal-button',
                    'callback' => 'setComponent',
                )); ?>
            </div>
        </div>
        <div class="span6">
            <div id="fieldset"></div>
        </div>
        <button class="btn">Save</button>
    </div>
</form>