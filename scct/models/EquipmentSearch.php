<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\equipment;

/**
 * EquipmentSearch represents the model behind the search form about `app\models\equipment`.
 */
class EquipmentSearch extends equipment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['EquipmentID', 'EquipmentClientID', 'EquipmentProjectID', 'EquipmentAssignedUserID'], 'integer'],
            [['EquipmentName', 'EquipmentSerialNumber', 'EquipmentDetails', 'EquipmentType', 'EquipmentManufacturer', 'EquipmentManufactureYear', 'EquipmentCondition', 'EquipmentMACID', 'EquipmentModel', 'EquipmentColor', 'EquipmentWarrantyDetail', 'EquipmentComment', 'EquipmentAnnualCalibrationDate', 'EquipmentAnnualCalibrationStatus', 'EquipmentCreatedByUser', 'EquipmentCreateDate', 'EquipmentModifiedBy', 'EquipmentModifiedDate'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = equipment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'EquipmentID' => $this->EquipmentID,
            'EquipmentClientID' => $this->EquipmentClientID,
            'EquipmentProjectID' => $this->EquipmentProjectID,
            'EquipmentAnnualCalibrationDate' => $this->EquipmentAnnualCalibrationDate,
            'EquipmentAssignedUserID' => $this->EquipmentAssignedUserID,
            'EquipmentCreateDate' => $this->EquipmentCreateDate,
            'EquipmentModifiedDate' => $this->EquipmentModifiedDate,
        ]);

        $query->andFilterWhere(['like', 'EquipmentName', $this->EquipmentName])
            ->andFilterWhere(['like', 'EquipmentSerialNumber', $this->EquipmentSerialNumber])
            ->andFilterWhere(['like', 'EquipmentDetails', $this->EquipmentDetails])
            ->andFilterWhere(['like', 'EquipmentType', $this->EquipmentType])
            ->andFilterWhere(['like', 'EquipmentManufacturer', $this->EquipmentManufacturer])
            ->andFilterWhere(['like', 'EquipmentManufactureYear', $this->EquipmentManufactureYear])
            ->andFilterWhere(['like', 'EquipmentCondition', $this->EquipmentCondition])
            ->andFilterWhere(['like', 'EquipmentMACID', $this->EquipmentMACID])
            ->andFilterWhere(['like', 'EquipmentModel', $this->EquipmentModel])
            ->andFilterWhere(['like', 'EquipmentColor', $this->EquipmentColor])
            ->andFilterWhere(['like', 'EquipmentWarrantyDetail', $this->EquipmentWarrantyDetail])
            ->andFilterWhere(['like', 'EquipmentComment', $this->EquipmentComment])
            ->andFilterWhere(['like', 'EquipmentAnnualCalibrationStatus', $this->EquipmentAnnualCalibrationStatus])
            ->andFilterWhere(['like', 'EquipmentCreatedByUser', $this->EquipmentCreatedByUser])
            ->andFilterWhere(['like', 'EquipmentModifiedBy', $this->EquipmentModifiedBy]);

        return $dataProvider;
    }
}
