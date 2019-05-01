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
        'https://fonts.googleapis.com/css?family=Roboto:400,100,300',
        '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
        'css/site.css',
        'css/customGridviewStyle.css',
        'css/customSortableInputStyle.css',
		//'css/indexStyleSheet.css', appears to be unnecessary
		//'css/projectlanding.css', TODO appears unused
    ];
    public $cssOptions = [
        'type' => 'text/css',
    ];
    public $js = [
        '//code.jquery.com/ui/1.12.1/jquery-ui.js',
		'js/confirm_modal.js',
        'js/header.js',
        'js/footer.js',
        'js/logout_btn.js',
        'js/FileSaver.min.js',//TODO determine usage
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
