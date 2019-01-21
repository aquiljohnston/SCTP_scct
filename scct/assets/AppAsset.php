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
        'css/tracker.css',
        'css/inspection.css',
        'css/notification.css',
        '//cdn.datatables.net/1.10.12/css/jquery.dataTables.css',
        "//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css",
        'css/indexStyleSheet.css',
        'css/mapStylesheet.css',
        'https://vjs.zencdn.net/5.4.6/video-js.css',
        'css/cge.css',
        'css/task.css',
        'css/notify.css',
    ];
    public $cssOptions = [
        'type' => 'text/css',
    ];
    public $js = [
		'js/confirm_modal.js',
        'js/header.js',
        'js/footer.js',
        //'js/add_surveyor_modal.js',
        'js/logout_btn.js',
        'js/time_entry_modal.js',
        'js/mileage_entry_modal.js',
        'js/approve_multiple_timecard.js',
        'js/approve_multiple_mileagecard.js',
        'js/project.js',
        'js/time_card.js',
        'js/mileage_card.js',
        'js/mileage_entries.js',
        'js/equipment.js',
        'js/user.js',
        'js/dispatch.js',
        'js/assigned.js',
        'js/reports.js',
        'js/notification.js',
        '//cdn.datatables.net/1.10.12/js/jquery.dataTables.js',
        '//code.jquery.com/ui/1.12.1/jquery-ui.js',
        'js/geoxml3.js',
        //'js/geoxmlfull_v3.js',
        //'js/markerclusterer.js',
        'js/markerclusterer_compiled.js',
        'js/progressBar.js',
        '//developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js',
        'js/inspection.js',
        'js/FileSaver.min.js',
        'https://vjs.zencdn.net/ie8/1.1.1/videojs-ie8.min.js',
        'https://vjs.zencdn.net/5.4.6/video.js',
        'js/cge.js',
        'js/clients.js',
        'js/task.js',
        'js/notify.js',		
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
