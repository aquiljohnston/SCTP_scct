<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TimeCard;

/**
 * TimeCardSearch represents the model behind the search form about `app\models\TimeCard`.
 */
class TimeCardSearch extends TimeCard
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['TimeCardID', 'TimeCardProjectID', 'TimeCardTechID', 'TimeCardApproved'], 'integer'],
            [['TimeCardStartDate', 'TimeCardEndDate', 'TimeCardSupervisorName', 'TimeCardComment', 'TimeCardCreateDate', 'TimeCardCreatedBy', 'TimeCardModifiedDate', 'TimeCardModifiedBy'], 'safe'],
            [['TimeCardHoursWorked'], 'number'],
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
        $query = TimeCard::find();

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
            'TimeCardID' => $this->TimeCardID,
            'TimeCardStartDate' => $this->TimeCardStartDate,
            'TimeCardEndDate' => $this->TimeCardEndDate,
            'TimeCardHoursWorked' => $this->TimeCardHoursWorked,
            'TimeCardProjectID' => $this->TimeCardProjectID,
            'TimeCardTechID' => $this->TimeCardTechID,
            'TimeCardApproved' => $this->TimeCardApproved,
            'TimeCardCreateDate' => $this->TimeCardCreateDate,
            'TimeCardModifiedDate' => $this->TimeCardModifiedDate,
        ]);

        $query->andFilterWhere(['like', 'TimeCardSupervisorName', $this->TimeCardSupervisorName])
            ->andFilterWhere(['like', 'TimeCardComment', $this->TimeCardComment])
            ->andFilterWhere(['like', 'TimeCardCreatedBy', $this->TimeCardCreatedBy])
            ->andFilterWhere(['like', 'TimeCardModifiedBy', $this->TimeCardModifiedBy]);

        return $dataProvider;
    }
}
