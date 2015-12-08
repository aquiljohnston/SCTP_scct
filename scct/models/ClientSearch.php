<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\client;

/**
 * ClientSearch represents the model behind the search form about `app\models\client`.
 */
class ClientSearch extends client
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ClientID', 'ClientActiveFlag', 'ClientDivisionsFlag'], 'integer'],
            [['ClientName', 'ClientContactTitle', 'ClientContactFName', 'ClientContactMI', 'ClientContactLName', 'ClientPhone', 'ClientEmail', 'ClientAddr1', 'ClientAddr2', 'ClientCity', 'ClientState', 'ClientZip4', 'ClientTerritory', 'ClientComment', 'ClientCreateDate', 'ClientCreatorUserID', 'ClientModifiedDate', 'ClientModifiedBy'], 'safe'],
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
        $query = client::find();

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
            'ClientID' => $this->ClientID,
            'ClientActiveFlag' => $this->ClientActiveFlag,
            'ClientDivisionsFlag' => $this->ClientDivisionsFlag,
            'ClientCreateDate' => $this->ClientCreateDate,
            'ClientModifiedDate' => $this->ClientModifiedDate,
        ]);

        $query->andFilterWhere(['like', 'ClientName', $this->ClientName])
            ->andFilterWhere(['like', 'ClientContactTitle', $this->ClientContactTitle])
            ->andFilterWhere(['like', 'ClientContactFName', $this->ClientContactFName])
            ->andFilterWhere(['like', 'ClientContactMI', $this->ClientContactMI])
            ->andFilterWhere(['like', 'ClientContactLName', $this->ClientContactLName])
            ->andFilterWhere(['like', 'ClientPhone', $this->ClientPhone])
            ->andFilterWhere(['like', 'ClientEmail', $this->ClientEmail])
            ->andFilterWhere(['like', 'ClientAddr1', $this->ClientAddr1])
            ->andFilterWhere(['like', 'ClientAddr2', $this->ClientAddr2])
            ->andFilterWhere(['like', 'ClientCity', $this->ClientCity])
            ->andFilterWhere(['like', 'ClientState', $this->ClientState])
            ->andFilterWhere(['like', 'ClientZip4', $this->ClientZip4])
            ->andFilterWhere(['like', 'ClientTerritory', $this->ClientTerritory])
            ->andFilterWhere(['like', 'ClientComment', $this->ClientComment])
            ->andFilterWhere(['like', 'ClientCreatorUserID', $this->ClientCreatorUserID])
            ->andFilterWhere(['like', 'ClientModifiedBy', $this->ClientModifiedBy]);

        return $dataProvider;
    }
}
