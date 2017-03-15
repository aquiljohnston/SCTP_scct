<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ActivityTb".
 *
 * @property integer $ActivityID
 * @property string  $ActivityStartTime
 * @property string  $ActivityEndTime
 * @property string  $ActivityTitle
 * @property string  $ActivityBillingCode
 * @property integer $ActivityCode
 * @property integer $ActivityPayCode
 * @property double  $ActivityLatitude
 * @property double  $ActivityLongitude
 * @property integer $ActivityArchiveFlag
 * @property string  $ActivityCreateDate
 * @property string  $ActivityCreatedUserUID
 * @property string  $ActivityModifiedDate
 * @property string  $ActivityModifiedUserUID
 * @property string  $ActivityUID
 * @property integer $ActivityProjectID
 * @property string  $ActivitySourceID
 * @property string  $ActivitySrvDTLT
 * @property string  $ActivitySrvDTLTOffset
 * @property string  $ActivitySrcDTLT
 * @property string  $ActivityGPSType
 * @property string  $ActivityGPSSentence
 * @property string  $ActivityShape
 * @property string  $ActivityComments
 * @property double  $ActivityBatteryLevel
 * @property string  $ActivityRevisionComments
 * @property integer $ActivityElapsedSec
 * @property string  $ActivityGPSSource
 * @property string  $ActivityGPSTime
 * @property integer $ActivityFixQuality
 * @property integer $ActivityNumberOfSatellites
 * @property double  $ActivityHDOP
 * @property double  $ActivityAltitudemetersAboveMeanSeaLevel
 * @property double  $ActivityHeightofGeoid
 * @property double  $ActivityTimeSecondsSinceLastDGPS
 * @property string  $ActivityChecksumData
 * @property double  $ActivityBearing
 * @property double  $ActivitySpeed
 * @property string  $ActivityGPSStatus
 * @property integer $ActivityNumberOfGPSAttempts
 * @property string  $ActivityAppVersion
 * @property string  $ActivityAppVersionName
 */
class Activity extends \yii\base\model
{
	
	public  $ActivityID;
	public  $ActivityStartTime;
	public  $ActivityEndTime;
	public  $ActivityTitle;
	public  $ActivityBillingCode;
	public  $ActivityCode;
	public  $ActivityPayCode;
	public  $ActivityLatitude;
	public  $ActivityLongitude;
	public  $ActivityArchiveFlag;
	public  $ActivityCreateDate;
	public  $ActivityCreatedUserUID;
	public  $ActivityModifiedDate;
	public  $ActivityModifiedUserUID;
	public  $ActivityUID;
	public  $ActivityProjectID;
	public  $ActivitySourceID;
	public  $ActivitySrvDTLT;
	public  $ActivitySrvDTLTOffset;
	public  $ActivitySrcDTLT;
	public  $ActivityGPSType;
	public  $ActivityGPSSentence;
	public  $ActivityShape;
	public  $ActivityComments;
	public  $ActivityBatteryLevel;
	public  $ActivityRevisionComments;
	public  $ActivityElapsedSec;
	public  $ActivityGPSSource;
	public  $ActivityGPSTime;
	public  $ActivityFixQuality;
	public  $ActivityNumberOfSatellites;
	public  $ActivityHDOP;
	public  $ActivityAltitudemetersAboveMeanSeaLevel;
	public  $ActivityHeightofGeoid;
	public  $ActivityTimeSecondsSinceLastDGPS;
	public  $ActivityChecksumData;
	public  $ActivityBearing;
	public  $ActivitySpeed;
	public  $ActivityGPSStatus;
	public  $ActivityNumberOfGPSAttempts;
	public  $ActivityAppVersion;
	public  $ActivityAppVersionName;
	public  $timeEntry;
	public  $mileageEntry;
	
	/**
	* @inheritdoc
	*/
    public function rules()
    {
        return [
            [['ActivityStartTime', 'ActivityEndTime', 'ActivityCreateDate', 'ActivityModifiedDate', 'ActivitySrvDTLT', 'ActivitySrvDTLTOffset', 'ActivitySrcDTLT', 'timeEntry', 'mileageEntry'], 'safe'],
            [['ActivityTitle', 'ActivityBillingCode', 'ActivityCreatedUserUID', 'ActivityModifiedUserUID', 'ActivityUID', 'ActivitySourceID', 'ActivityGPSType', 'ActivityGPSSentence', 'ActivityShape', 'ActivityComments', 'ActivityRevisionComments', 'ActivityGPSSource', 'ActivityGPSTime', 'ActivityChecksumData', 'ActivityGPSStatus', 'ActivityAppVersion', 'ActivityAppVersionName'], 'string'],
            [['ActivityCode', 'ActivityPayCode', 'ActivityArchiveFlag', 'ActivityProjectID', 'ActivityElapsedSec', 'ActivityFixQuality', 'ActivityNumberOfSatellites', 'ActivityNumberOfGPSAttempts'], 'integer'],
            [['ActivityLatitude', 'ActivityLongitude', 'ActivityBatteryLevel', 'ActivityHDOP', 'ActivityAltitudemetersAboveMeanSeaLevel', 'ActivityHeightofGeoid', 'ActivityTimeSecondsSinceLastDGPS', 'ActivityBearing', 'ActivitySpeed'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ActivityID' => 'Activity ID',
            'ActivityStartTime' => 'Activity Start Time',
            'ActivityEndTime' => 'Activity End Time',
            'ActivityTitle' => 'Activity Title',
            'ActivityBillingCode' => 'Activity Billing Code',
            'ActivityCode' => 'Activity Code',
            'ActivityPayCode' => 'Activity Pay Code',
            'ActivityLatitude' => 'Activity Latitude',
            'ActivityLongitude' => 'Activity Longitude',
            'ActivityArchiveFlag' => 'Activity Archive Flag',
            'ActivityCreateDate' => 'Activity Create Date',
            'ActivityCreatedUserUID' => 'Activity Created User Uid',
            'ActivityModifiedDate' => 'Activity Modified Date',
            'ActivityModifiedUserUID' => 'Activity Modified User Uid',
            'ActivityUID' => 'Activity Uid',
            'ActivityProjectID' => 'Activity Project ID',
            'ActivitySourceID' => 'Activity Source ID',
            'ActivitySrvDTLT' => 'Activity Srv Dtlt',
            'ActivitySrvDTLTOffset' => 'Activity Srv Dtltoffset',
            'ActivitySrcDTLT' => 'Activity Src Dtlt',
            'ActivityGPSType' => 'Activity Gpstype',
            'ActivityGPSSentence' => 'Activity Gpssentence',
            'ActivityShape' => 'Activity Shape',
            'ActivityComments' => 'Activity Comments',
            'ActivityBatteryLevel' => 'Activity Battery Level',
            'ActivityRevisionComments' => 'Activity Revision Comments',
            'ActivityElapsedSec' => 'Activity Elapsed Sec',
            'ActivityGPSSource' => 'Activity Gpssource',
            'ActivityGPSTime' => 'Activity Gpstime',
            'ActivityFixQuality' => 'Activity Fix Quality',
            'ActivityNumberOfSatellites' => 'Activity Number Of Satellites',
            'ActivityHDOP' => 'Activity Hdop',
            'ActivityAltitudemetersAboveMeanSeaLevel' => 'Activity Altitudemeters Above Mean Sea Level',
            'ActivityHeightofGeoid' => 'Activity Heightof Geoid',
            'ActivityTimeSecondsSinceLastDGPS' => 'Activity Time Seconds Since Last Dgps',
            'ActivityChecksumData' => 'Activity Checksum Data',
            'ActivityBearing' => 'Activity Bearing',
            'ActivitySpeed' => 'Activity Speed',
            'ActivityGPSStatus' => 'Activity Gpsstatus',
            'ActivityNumberOfGPSAttempts' => 'Activity Number Of Gpsattempts',
            'ActivityAppVersion' => 'Activity App Version',
            'ActivityAppVersionName' => 'Activity App Version Name',
            'timeEntry' => 'time Entry',
            'mileageEntry' => 'mileage Entry',
        ];
    }
}
