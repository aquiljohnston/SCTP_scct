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
		'http://fonts.googleapis.com/css?family=Roboto:400,100,300',
    ];
	public $cssOptions = [
		'type' => 'text/css',
	];
    public $js = [
		'js/header.js',
		'js/footer.js',
		'js/logout_btn.js',
		'js/time_entry_modal.js',
		'js/mileage_entry_modal.js',
		'js/approve_multiple_timecard.js',
		'js/approve_multiple_mileagecard.js',
		'js/approve_equipment.js',
		'js/deactive_multiple_timeEntry.js',
		'js/deactive_multiple_mileageEntry.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
		'yii\bootstrap\BootstrapPluginAsset',
    ];
}
