<?php

namespace app\modules\dispatch\controllers;

use yii\web\Controller;

/**
 * Dispatch controller for the `dispatch` module
 */
class DispatchController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
