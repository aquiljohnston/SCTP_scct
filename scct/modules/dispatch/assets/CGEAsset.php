<?php

namespace app\modules\dispatch\assets;

use app\assets\AppAsset;

class CGEAsset extends AppAsset
{
    public $css = [
        'css/cge.css',
        'css/addSurveyor.css',
    ];
	public $js = [
        'js/cge.js',
        'js/dispatch.js', //viewAssetRowClicked located in here may want to extract out
	];
}
