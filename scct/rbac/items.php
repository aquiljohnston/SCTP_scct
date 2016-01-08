<?php
return [
    'viewUserIndex' => [
        'type' => 2,
        'description' => 'View the user index',
    ],
    'viewUser' => [
        'type' => 2,
        'description' => 'View a user',
    ],
    'createUser' => [
        'type' => 2,
        'description' => 'Create a user',
    ],
    'updateUser' => [
        'type' => 2,
        'description' => 'Update user',
    ],
    'deleteUser' => [
        'type' => 2,
        'description' => 'Delete user',
    ],
    'viewEquipmentIndex' => [
        'type' => 2,
        'description' => 'View the Equipment Index',
    ],
    'viewEquipment' => [
        'type' => 2,
        'description' => 'View an Equipment',
    ],
    'createEquipment' => [
        'type' => 2,
        'description' => 'Create an Equipment',
    ],
    'updateEquipment' => [
        'type' => 2,
        'description' => 'Update equipment',
    ],
    'deleteEquipment' => [
        'type' => 2,
        'description' => 'Delete equipment',
    ],
    'viewMileageCardIndex' => [
        'type' => 2,
        'description' => 'View the mileage card index',
    ],
    'viewMileageCard' => [
        'type' => 2,
        'description' => 'View a mileage card',
    ],
    'createMileageCard' => [
        'type' => 2,
        'description' => 'Create a mileage card',
    ],
    'updateMileageCard' => [
        'type' => 2,
        'description' => 'Update mileage card',
    ],
    'deleteMileageCard' => [
        'type' => 2,
        'description' => 'Delete mileage card',
    ],
    'viewTimeCardIndex' => [
        'type' => 2,
        'description' => 'View the time card index',
    ],
    'viewTimeCard' => [
        'type' => 2,
        'description' => 'View a time card',
    ],
    'createTimeCard' => [
        'type' => 2,
        'description' => 'Create a time card',
    ],
    'updateTimeCard' => [
        'type' => 2,
        'description' => 'Update time card',
    ],
    'deleteTimeCard' => [
        'type' => 2,
        'description' => 'Delete time card',
    ],
    'viewClientIndex' => [
        'type' => 2,
        'description' => 'View the client index',
    ],
    'viewClient' => [
        'type' => 2,
        'description' => 'View a client',
    ],
    'createClient' => [
        'type' => 2,
        'description' => 'Create a client',
    ],
    'updateClient' => [
        'type' => 2,
        'description' => 'Update client',
    ],
    'deleteClient' => [
        'type' => 2,
        'description' => 'Delete client',
    ],
    'viewProjectIndex' => [
        'type' => 2,
        'description' => 'View the project index',
    ],
    'viewProject' => [
        'type' => 2,
        'description' => 'View a project',
    ],
    'createProject' => [
        'type' => 2,
        'description' => 'Create a project',
    ],
    'updateProject' => [
        'type' => 2,
        'description' => 'Update project',
    ],
    'deleteProject' => [
        'type' => 2,
        'description' => 'Delete project',
    ],
    'supervisor' => [
        'type' => 1,
        'children' => [
            'viewUserIndex',
            'viewUser',
            'createUser',
            'updateUser',
            'deleteUser',
            'viewEquipmentIndex',
            'viewEquipment',
            'createEquipment',
            'updateEquipment',
            'deleteEquipment',
            'viewMileageCardIndex',
            'viewMileageCard',
            'createMileageCard',
            'updateMileageCard',
            'viewTimeCardIndex',
            'viewTimeCard',
            'createTimeCard',
            'updateTimeCard',
        ],
    ],
    'projectManager' => [
        'type' => 1,
        'children' => [
            'supervisor',
        ],
    ],
    'admin' => [
        'type' => 1,
        'children' => [
            'projectManager',
            'deleteTimeCard',
            'deleteMileageCard',
            'viewClientIndex',
            'viewClient',
            'createClient',
            'updateClient',
            'deleteClient',
            'viewProjectIndex',
            'viewProject',
            'createProject',
            'updateProject',
            'deleteProject',
        ],
    ],
];
