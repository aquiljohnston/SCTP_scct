<?php

namespace app\assets;

class MapAsset extends AppAsset
{
    public $css = [
        'css/mapStylesheet.css',
        'css/tracker.css',
    ];
	public $js = [
        '//developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js',
        'js/geoxml3.js',
        'js/markerclusterer_compiled.js',
        'js/progressBar.js',
	];
}
