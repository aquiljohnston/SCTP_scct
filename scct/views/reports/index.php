<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\Reports;
use kartik\widgets\DepDrop;
use kartik\form\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AocSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reports';
$this->params['breadcrumbs'][] = $this->title;
?>
<body id="reports-page">
<div class="dispatchAid" id="reportsContainer">
	<div class="containerByDropdown">
		<div class="reportsDropdown" >
				Report:
			<select id="reportsDropdown" class="reportsDropdown" style="width: 16%">
				<option>Please make a selection</option>
			</select>
			<input type="button" id="go" name="go" value="Go" style="display: none;"/>
			<input type="button" id="export" name="export" value="Export" style="display: none;"/>
		</div>
		<div id="noSelectionError" style="display:inline-block;color: red; display: none;">* No report selected.</div>
		<div id="noDateError" style="display:inline-block;color: red; display: none;">* You must enter a date.</div>
		<div id="selectDateFirstError" style="display:inline-block;color: red; display: none;">* Date(s) must be
			selected before selecting Inspector.
		</div>
		<div id="selectDate" class="reportsDropdown" style="width: 300px;height: 20px;display: block;padding-top: 5px; display: none;">Select a
			Date: <input type="text" id="datePickerSelectDate" style="float: right;"></div>
		<div id="beginDate" class="reportsDropdown" style="width: 300px;height: 20px;display: block;display: none;">Begin Date: 
			<input type="text" id="datePickerBeginDate" style="float: right;"></div>
		<div id="endDate" style="width: 300px;height:20px;display: block;padding-top: 5px; display: none; ">End Date:
			<input type="text" class="reportsDropdown"  id="datePickerEndDate" style="float: right;"></div>
		<div id="dropDownListView" style="width: 430px; height: 20px;padding-top: 20px;display: block;">
			<label id="mapGridListHeader" style="display: none;">MapGrid List: </label>
			<select id="parmDropdown" class="reportsDropdown" style="float: right; display: none; margin-right: 41%; width: 20%;">
			</select>
			<label id="inspectorListHeader" style="display: none;">Inspector List: </label>
			<select id="inspectorsDropdown" class="reportsDropdown"  style="display: none; float: right; margin-right: 13%;">
			</select>
		</div>

		<table id="reportTable" style="width:100%;">
			<thead></thead>
			<tbody></tbody>
		</table>
	</div>
</div>
</body>