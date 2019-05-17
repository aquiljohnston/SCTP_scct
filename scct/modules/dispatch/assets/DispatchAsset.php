<?php

namespace app\modules\dispatch\assets;

use app\assets\AppAsset;

class DispatchAsset extends AppAsset
{
    public $css = [
        'css/dispatch.css',
        'css/addSurveyor.css',
        'css/viewAssets.css',
    ];
	public $js = [
        'js/dispatch.js',
        'js/datePicker.js',
	];
}
