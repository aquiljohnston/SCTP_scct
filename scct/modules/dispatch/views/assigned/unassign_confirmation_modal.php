<?php
	use yii\helpers\Html;
?>

<div id="unassignConfirmationBody">
	<div id="unassignConfirmationListHeaders">
			<div class='unassignConfirmationColumn'>
				Maps
			</div>
			<div class='unassignConfirmationColumn'>
				Surveyors
			</div>
	</div>
    <div id="unassignConfirmationList">
		<?php
			$modalHTML = '';
			forEach($unassignConfirmationData as $element){
				yii::trace('ELEMENT ' . json_encode($element));
				//build map data
				$mapGridStr = array_key_exists('MapGrid', $element) ? "Map Grid: " . $element['MapGrid'] . "<br>" : "";
				$sectionStr = array_key_exists('SectionNumber', $element) ? "Section Number: " . $element['SectionNumber'] . "<br>" : "";
				$inspectionTypeStr = array_key_exists('InspectionType', $element) ? "Inspection Type: " . $element['InspectionType'] . "<br>" : "";
				$billingCodeStr = array_key_exists('BillingCode', $element) ? "Billing Code: " . $element['BillingCode'] . "<br>" : "";
				$officeNameStr = array_key_exists('OfficeName', $element) ? "Office Name: " . $element['OfficeName'] . "<br>" : "";
				$unassignMapStr = $mapGridStr . $sectionStr . $inspectionTypeStr . $billingCodeStr . $officeNameStr;
				
				//build user data
				$unassignUserStr = '';
				$userData = $element['Users'];
				unset($element['Users']);
				forEach($userData as $user){
					$element['AssignedUserID'] = $user['UserID'];
					$unassignUserStr .= "<span><input type='checkbox' value='" . json_encode($element) ."' checked='checked'> " .  $user['UserFullName'] . "</span><br>";
				}
				$modalHTML .= "<div class='unassignConfirmationRow'><div class='unassignConfirmationColumn'>" . $unassignMapStr . "</div><div class='unassignConfirmationColumn'>" . $unassignUserStr . "</div></div>";
			}
			echo $modalHTML; 
		?>
    </div>
</div>
<div class="unassignConfirmationFooter clearfix" >
	<div id="unassignConfirmButton" class="unassignbtn">
		<?php echo Html::button('Confirm', ['class' => 'btn btn-primary', 'id' => 'unassignConfirmBtn']); ?>
	</div>
	<div id="unassignCancelButton" class="unassignbtn">
		<?php echo Html::button('Cancel', ['class' => 'btn btn-primary', 'id' => 'unassignCancelBtn']); ?>
	</div>
</div>	

<script type="text/javascript">
	$(document).ready(function () {
        //initialize unassign confirmation modal
		initUnassignConfirmationModal();
    });

	function initUnassignConfirmationModal(){
		// When the user clicks buttons, close the modal
		$(document).off('click', '#unassignCancelBtn').on('click', '#unassignCancelBtn', function () {
			$('#unassignConfirmationModal').modal('hide');
		});
		$(document).off('click', '#unassignConfirmBtn').on('click', '#unassignConfirmBtn', function () {
			$('#unassignConfirmationModal').modal('hide');
			unassignButtonListener();
		});
	}
	
	//bound to modal confirm for map/section level unassign
	function unassignButtonListener() {
		confirmedUnassignData = getUnassignConfirmedDataArray();
		unassignMapData = confirmedUnassignData['unassignMapData'];
		unassignSectionData = confirmedUnassignData['unassignSectionData'];
		var form = $("#AssignForm");
		var sort = getAssignedIndexSortParams();
		//append sort to form values
		var dataParams = form.serialize() + "&sort=" + sort;
		$('#loading').show();
		$.ajax({
			url: '/dispatch/assigned/unassign',
			data: {unassignMap: unassignMapData, unassignSection: unassignSectionData},
			type: 'POST',
			beforeSend: function () {
				$('#loading').show();
			}
		}).done(function () {
			$.pjax.reload({
				container: '#assignedGridview',
				timeout: 99999,
				type: 'GET',
				url: form.attr("action"),
				data: dataParams
			});
			$('#assignedGridview').on('pjax:success', function () {
				$('#loading').hide();
				resetValue();
				unassignCheckboxListener();
			});
			$('#assignedGridview').on('pjax:error', function (e) {
				resetValue();
				e.preventDefault();
			});
		});

		// disable remove surveyor button again
		$("#removeSurveyorButton").prop('disabled', true);
	}
	
	//get confirmed unassign user data
	function getUnassignConfirmedDataArray() {
		confirmedDataArray = {};
		confirmedDataArray['unassignMapData'] = [];
		confirmedDataArray['unassignSectionData'] = [];
		$('#unassignConfirmationList input:checked').each(function(){
			dataArray = JSON.parse($(this).val());
			if('SectionNumber' in dataArray){
				confirmedDataArray['unassignSectionData'].push(dataArray);
			} else {
				confirmedDataArray['unassignMapData'].push(dataArray);
			}
		});
		return confirmedDataArray;
	}

	
</script>