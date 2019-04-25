<?php

namespace app\modules\dispatch\assets;

use app\assets\AppAsset;

class AssignedAsset extends AppAsset
{
    public $css = [
        'css/assigned.css',
    ];
	public $js = [
        'js/assigned.js',
        'js/dispatch.js', //viewAssetRowClicked located in here may want to extract out
        'js/datePicker.js',
	];
}
