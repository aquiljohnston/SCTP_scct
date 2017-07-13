<?php
/**
 * Created by PhpStorm.
 * User: jpatton
 * Date: 1/5/2017
 * Time: 2:51 PM
 *
 * This file contains a list of permissions that are under PG&E.
 */

namespace app\dictionaries;

class PermissionDictionary {
    private static $examplePermissions = [
        'examplePermission' // Test
    ];

    private static $ctPermissions = [
        'activityCodeGetDropdown', // Get an associative array of Activity Codes
        'activityView', // View an activity
        'activityCreate', // Create an activity
        'appRoleGetDropdown', // Get an associative array of App Roles
        'clientAccountsGetDropdown', // Get an associative array of Client Accounts
        'clientGetAll', // Get an array of all clients
        'clientView', // View a client
        'clientCreate', // Create a client
        'clientUpdate', // Update client
        'clientGetDropdown', // Get an associative array of client ID/name pairs
        'employeeTypeGetDropdown', // Get an associative array of employee types
        'equipmentCalibrationCreate', // Creates a new equipment calibration record
        'equipmentConditionGetDropdown', // Get an associative array of equipment conditions
        'getOwnEquipment', // Get equipment for associated projects
        'getAllEquipment', // Get all equipment
        'equipmentView', // View equipment
        'equipmentCreate', // Create equipment
        'equipmentUpdate', // Update equipment
        'equipmentDelete', // Delete equipment
        'acceptEquipment', // Accept equipment
        'equipmentStatusGetDropdown', // Get an associative array of equipment status
        'equipmentTypeGetDropdown', // Get an associative array of equipment types
        'mileageCardGetOwnCards', // Get an array of mileage cards for associated projects
        'mileageCardGetAllCards', // Get an array of all mileage cards
        'mileageCardView', // View a mileage card
        'mileageCardGetCard', // Get a mileage card for a user
        'mileageCardGetEntries', // Get all mileage entries for a mileage card
        'mileageCardApprove', // Approve a mileage card
        'mileageEntryView', // View a mileage entry
        'mileageEntryCreate', // Create a mileage entry
        'mileageEntryDeactivate', // Deactivate a mileage entry
        'notificationsGet', // Get notifications
        'payCodeGetDropdown', // Get an associative array of pay codes
        'projectGetAll', // Get an array of all projects
        'projectView', // View a project
        'projectCreate', // Create a project
        'projectUpdate', // Update project
        'projectGetOwnProjects', // Get all projects that a user is associated with
        'projectGetDropdown', // Get an associative array of project name/id pairs
        'projectGetUserRelationships', // Get two arrays one of users associated with a project and one of all other users
        'projectAddRemoveUsers', // Add or remove users from a project
        'projectAddRemoveModules', // Add or remove modules from a project
        'stateCodeGetDropdown', // Get an associative array of state codes
        'timeCardGetOwnCards', // Get an array of multiple time cards for associated projects
        'timeCardGetAllCards', // Get an array of all time cards
        'timeCardView', // View a time card
        'timeCardApproveCards', // Approve time cards
        'timeCardGetCard', // Get a users time card
        'timeCardGetEntries', // Get all time entries for a time card
        'timeEntryView', // View a time entry
        'timeEntryCreate', // Create a time entry
        'timeEntryDeactivate', // Deactivate a time entry
        'userGetActive', // Get all active users
        'userView', // View a user
        'userCreate', // Create a user
        'userCreateAdmin', // Create a user of role type admin
        'userUpdate', // Update user
        'userUpdateTechnician', // Update user of role type technician
        'userUpdateEngineer', // Update user of role type engineer
        'userUpdateSupervisor', // Update user of role type supervisor
        'userUpdateProjectManager', // Update user of role type project manager
        'userUpdateAdmin', // Update user of role type admin
        'userDeactivate', // Deactivate user
        'userGetDropdown', // Get an associative array of user id/name pairs
        'userGetMe', // Get equipment and project data for a user
        'viewClientMgmt', // View client management
        'viewProjectMgmt', // View project name
        'viewUserMgmt', // View user management
        'viewEquipmentMgmt', // View equipment management
        'viewTimeCardMgmt', // View time card management
        'viewMileageCardMgmt', // View mileage card management
        'viewTracker', // View tracker
        'viewLeakLogMgmt', // View leak log management
        'viewLeakLogDetail', // View leak log detail
        'viewMapStampMgmt', // View map stamp management
        'viewMapStampDetail', // View map stamp detail
        'viewAOC', // View AOC
        'viewDispatch', // View dispatch
        'viewAssigned', // View assigned
        'viewReportsMenu', // View reports
        'viewInspections', // view Inspection
    ];

    private static $scctTemplatePermissions = [
        ];

    public static function permissionIsExample($permission) {
        return in_array($permission, self::$examplePermissions);
    }

    public static function permissionIsCT($permission) {
        return in_array($permission, self::$ctPermissions);
    }
}