<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\project;

/**
 * ProjectSearch represents the model behind the search form about `app\models\project`.
 */
class ProjectSearch extends project
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProjectID', 'ProjectStatus', 'ProjectClientID'], 'integer'],
            [['ProjectName', 'ProjectDescription', 'ProjectNotes', 'ProjectType', 'ProjectStartDate', 'ProjectEndDate'], 'safe'],
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
        $query = project::find();

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
            'ProjectID' => $this->ProjectID,
            'ProjectStatus' => $this->ProjectStatus,
            'ProjectClientID' => $this->ProjectClientID,
            'ProjectStartDate' => $this->ProjectStartDate,
            'ProjectEndDate' => $this->ProjectEndDate,
        ]);

        $query->andFilterWhere(['like', 'ProjectName', $this->ProjectName])
            ->andFilterWhere(['like', 'ProjectDescription', $this->ProjectDescription])
            ->andFilterWhere(['like', 'ProjectNotes', $this->ProjectNotes])
            ->andFilterWhere(['like', 'ProjectType', $this->ProjectType]);

        return $dataProvider;
    }
}
