<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
	// Technician - None
	// Engineer - CRUD for Equipment
	// Supervisor - CRUD
	// Project Manager - CRUD
	// Admin - Project + Client + CRUD

    public function actionInit()
    {
        $auth = Yii::$app->authManager;
		
		//reset all
		$auth->removeAll();
		
		
		//user CRUD request/////////////////////////////////////////////////////////////////
        // add "viewUserIndex" permission
        $viewUserIndex = $auth->createPermission('viewUserIndex');
        $viewUserIndex->description = 'View the user index';
        $auth->add($viewUserIndex);
		
		// add "viewUser" permission
        $viewUser = $auth->createPermission('viewUser');
        $viewUser->description = 'View a user';
        $auth->add($viewUser);
		
		// add "createUser" permission
        $createUser = $auth->createPermission('createUser');
        $createUser->description = 'Create a user';
        $auth->add($createUser);

        // add "updateUser" permission
        $updateUser = $auth->createPermission('updateUser');
        $updateUser->description = 'Update user';
        $auth->add($updateUser);
		
		// add "deleteUser" permission
        $deleteUser = $auth->createPermission('deleteUser');
        $deleteUser->description = 'Delete user';
        $auth->add($deleteUser);
		
		
		//equipment CRUD request/////////////////////////////////////////////////////////////////
        // add "viewEquipmentIndex" permission
        $viewEquipmentIndex = $auth->createPermission('viewEquipmentIndex');
        $viewEquipmentIndex->description = 'View the Equipment Index';
        $auth->add($viewEquipmentIndex);
		
		// add "viewEquipment" permission
        $viewEquipment = $auth->createPermission('viewEquipment');
        $viewEquipment->description = 'View an Equipment';
        $auth->add($viewEquipment);
		
		// add "createEquipment" permission
        $createEquipment = $auth->createPermission('createEquipment');
        $createEquipment->description = 'Create an Equipment';
        $auth->add($createEquipment);

        // add "updateEquipment" permission
        $updateEquipment = $auth->createPermission('updateEquipment');
        $updateEquipment->description = 'Update equipment';
        $auth->add($updateEquipment);
		
		// add "deleteEquipment" permission
        $deleteEquipment = $auth->createPermission('deleteEquipment');
        $deleteEquipment->description = 'Delete equipment';
        $auth->add($deleteEquipment);
		
		
		//mileageCard CRUD request/////////////////////////////////////////////////////////////////
        // add "viewMileageCardIndex" permission
        $viewMileageCardIndex = $auth->createPermission('viewMileageCardIndex');
        $viewMileageCardIndex->description = 'View the mileage card index';
        $auth->add($viewMileageCardIndex);
		
		// add "viewMileageCard" permission
        $viewMileageCard = $auth->createPermission('viewMileageCard');
        $viewMileageCard->description = 'View a mileage card';
        $auth->add($viewMileageCard);
		
		// add "createMileageCard" permission
        $createMileageCard = $auth->createPermission('createMileageCard');
        $createMileageCard->description = 'Create a mileage card';
        $auth->add($createMileageCard);

        // add "updateMileageCard" permission
        $updateMileageCard = $auth->createPermission('updateMileageCard');
        $updateMileageCard->description = 'Update mileage card';
        $auth->add($updateMileageCard);
		
		// add "deleteMileageCard" permission
        $deleteMileageCard = $auth->createPermission('deleteMileageCard');
        $deleteMileageCard->description = 'Delete mileage card';
        $auth->add($deleteMileageCard);
		
		
		//mileageCard CRUD request/////////////////////////////////////////////////////////////////
        // add "viewTimeCardIndex" permission
        $viewTimeCardIndex = $auth->createPermission('viewTimeCardIndex');
        $viewTimeCardIndex->description = 'View the time card index';
        $auth->add($viewTimeCardIndex);
		
		// add "viewTimeCard" permission
        $viewTimeCard = $auth->createPermission('viewTimeCard');
        $viewTimeCard->description = 'View a time card';
        $auth->add($viewTimeCard);
		
		// add "createTimeCard" permission
        $createTimeCard = $auth->createPermission('createTimeCard');
        $createTimeCard->description = 'Create a time card';
        $auth->add($createTimeCard);

        // add "updateTimeCard" permission
        $updateTimeCard = $auth->createPermission('updateTimeCard');
        $updateTimeCard->description = 'Update time card';
        $auth->add($updateTimeCard);
		
		// add "deleteTimeCard" permission
        $deleteTimeCard = $auth->createPermission('deleteTimeCard');
        $deleteTimeCard->description = 'Delete time card';
        $auth->add($deleteTimeCard);
		
		
		//client CRUD request/////////////////////////////////////////////////////////////////
        // add "viewClientIndex" permission
        $viewClientIndex = $auth->createPermission('viewClientIndex');
        $viewClientIndex->description = 'View the client index';
        $auth->add($viewClientIndex);
		
		// add "viewClient" permission
        $viewClient = $auth->createPermission('viewClient');
        $viewClient->description = 'View a client';
        $auth->add($viewClient);
		
		// add "createClient" permission
        $createClient = $auth->createPermission('createClient');
        $createClient->description = 'Create a client';
        $auth->add($createClient);

        // add "updateClient" permission
        $updateClient = $auth->createPermission('updateClient');
        $updateClient->description = 'Update client';
        $auth->add($updateClient);
		
		// add "deleteClient" permission
        $deleteClient = $auth->createPermission('deleteClient');
        $deleteClient->description = 'Delete client';
        $auth->add($deleteClient);
		
		
		//project CRUD request/////////////////////////////////////////////////////////////////
        // add "viewProjectIndex" permission
        $viewProjectIndex = $auth->createPermission('viewProjectIndex');
        $viewProjectIndex->description = 'View the project index';
        $auth->add($viewProjectIndex);
		
		// add "viewProject" permission
        $viewProject = $auth->createPermission('viewProject');
        $viewProject->description = 'View a project';
        $auth->add($viewProject);
		
		// add "createProject" permission
        $createProject = $auth->createPermission('createProject');
        $createProject->description = 'Create a project';
        $auth->add($createProject);

        // add "updateProject" permission
        $updateProject = $auth->createPermission('updateProject');
        $updateProject->description = 'Update project';
        $auth->add($updateProject);
		
		// add "deleteProject" permission
        $deleteProject = $auth->createPermission('deleteProject');
        $deleteProject->description = 'Delete project';
        $auth->add($deleteProject);
		
		
		// add role and children/////////////////////////////////////////////////////////////////
		// add "Technician" role and give this role CRUD permissions
		$technician = $auth->createRole('Technician');
		$auth->add($technician);
		
		// add "Engineer" role and give this role CRUD permissions
		$engineer = $auth->createRole('Engineer');
		$auth->add($engineer);
		$auth->addChild($engineer, $technician);
		$auth->addChild($engineer, $viewEquipmentIndex);
		$auth->addChild($engineer, $viewEquipment);
		$auth->addChild($engineer, $createEquipment);
		$auth->addChild($engineer, $updateEquipment);
		$auth->addChild($engineer, $deleteEquipment);

        // add "supervisor" role and give this role CRUD permissions
        $supervisor = $auth->createRole('Supervisor');
        $auth->add($supervisor);
		$auth->addChild($supervisor, $engineer);
		$auth->addChild($supervisor, $viewUserIndex);
		$auth->addChild($supervisor, $viewUser);
        $auth->addChild($supervisor, $createUser);
		$auth->addChild($supervisor, $updateUser);
		$auth->addChild($supervisor, $deleteUser);
		$auth->addChild($supervisor, $viewMileageCardIndex);
		$auth->addChild($supervisor, $viewMileageCard);
		$auth->addChild($supervisor, $createMileageCard);
		$auth->addChild($supervisor, $updateMileageCard);
		$auth->addChild($supervisor, $viewTimeCardIndex);
		$auth->addChild($supervisor, $viewTimeCard);
		$auth->addChild($supervisor, $createTimeCard);
		$auth->addChild($supervisor, $updateTimeCard);

        // add "projectManager" role and give this role the permissions of the "supervisor"
        $projectManager = $auth->createRole('ProjectManager');
        $auth->add($projectManager);
        $auth->addChild($projectManager, $supervisor);
		
		// add "admin" role and give this role the permissions of the "projectManager"
		$admin = $auth->createRole('Admin');
        $auth->add($admin);
        $auth->addChild($admin, $projectManager);
		$auth->addChild($admin, $deleteTimeCard);
		$auth->addChild($admin, $deleteMileageCard);
		$auth->addChild($admin, $viewClientIndex);
		$auth->addChild($admin, $viewClient);
		$auth->addChild($admin, $createClient);
		$auth->addChild($admin, $updateClient);
		$auth->addChild($admin, $deleteClient);
		$auth->addChild($admin, $viewProjectIndex);
		$auth->addChild($admin, $viewProject);
		$auth->addChild($admin, $createProject);
		$auth->addChild($admin, $updateProject);
		$auth->addChild($admin, $deleteProject);
        $auth->addChild($admin, $viewEquipmentIndex);
        $auth->addChild($admin, $viewEquipment);
        $auth->addChild($admin, $createEquipment);
        $auth->addChild($admin, $updateEquipment);
        $auth->addChild($admin, $deleteEquipment);


        //get users already in the system
		$url = "http://api.southerncrossinc.com/index.php?r=user%2Fget-all";
		$headers = array(
			'Accept:application/json',
			'Content-Type:application/json',
			//use postman to get tokent to add for request
			'Authorization: Basic '. base64_encode("eX6afWz6TDHO_91-wUHkCMePqRXtxBIo".": ")
			);
		//init new curl
		$curl = curl_init();
		//set curl options
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//execute curl
		$response = curl_exec ($curl);
		curl_close ($curl);
		
		$userArray = json_decode($response, true);
		$userSize = count($userArray);
		
		//assign roles to users already in the system
		for($i = 0; $i < $userSize; $i++)
		{
			var_dump($userArray);
			if($userRole = $auth->getRole($userArray[$i]["UserAppRoleType"]))
			{
				$auth->assign($userRole, $userArray[$i]["UserID"]);
			}
		}
		
        // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
        // usually implemented in your User model.
		//make jose an admin
        //$auth->assign($admin, 161);
		//make michael an admin
        //$auth->assign($admin, 162);
		//make tao an admin
		//$auth->assign($admin, 182);
		//make test admin an admin
		//$auth->assign($admin, 184);
		//make test pm a projectManager
		//$auth->assign($projectManager, 185);
		//make test supervisor a supervisor
		//$auth->assign($supervisor, 186);
    }
}
