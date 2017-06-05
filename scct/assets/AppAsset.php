<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/clients.css',
        'css/home.css',
        'css/projects.css',
        'css/user.css',
        'css/equipments.css',
        'css/timecards.css',
        'css/mileagecards.css',
        'css/projectlanding.css',
        'https://fonts.googleapis.com/css?family=Roboto:400,100,300',
        'css/customGridviewStyle.css',
        'css/customSortableInputStyle.css',
        'css/login.css',
        'css/dispatch.css',
        'css/assigned.css',
        'css/reports.css',
    ];
    public $cssOptions = [
        'type' => 'text/css',
    ];
    public $js = [
        'js/header.js',
        'js/footer.js',
        //'js/add_surveyor_modal.js',
        'js/logout_btn.js',
        'js/time_entry_modal.js',
        'js/mileage_entry_modal.js',
        'js/approve_multiple_timecard.js',
        'js/approve_multiple_mileagecard.js',
        'js/approve_equipment.js',
        'js/deactive_multiple_timeEntry.js',
        'js/deactive_multiple_mileageEntry.js',
        'js/project.js',
        'js/time_card.js',
        'js/mileage_card.js',
        'js/equipment.js',
        'js/user.js',
        //'js/unassign.js',
        'js/dispatch.js',
        'js/assigned.js',
        'js/reports.js',
        '//cdn.datatables.net/1.10.12/js/jquery.dataTables.js',
        '//code.jquery.com/ui/1.12.1/jquery-ui.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
