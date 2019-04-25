<?php

namespace app\modules\dispatch\assets;

use app\assets\AppAsset;

class InspectionAsset extends AppAsset
{
    public $css = [
        'css/inspection.css',
    ];
	public $js = [
        'js/inspection.js',
        'js/dispatch.js', //viewAssetRowClicked located in here may want to extract out
	];
}
