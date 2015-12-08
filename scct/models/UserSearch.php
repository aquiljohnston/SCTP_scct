<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\user;

/**
 * UserSearch represents the model behind the search form about `app\models\user`.
 */
class UserSearch extends user
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['UserID', 'UserKey', 'UserActiveFlag', 'UserModifiedDTLTOffset', 'UserInactiveDTLTOffset'], 'integer'],
            [['UserName', 'UserFirstName', 'UserLastName', 'UserLoginID', 'UserEmployeeType', 'UserPhone', 'UserCompanyName', 'UserCompanyPhone', 'UserAppRoleType', 'UserComments', 'UserCreatedDate', 'UserModifiedDate', 'UserCreatedBy', 'UserModifiedBy', 'UserCreateDTLTOffset'], 'safe'],
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
        $query = user::find();

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
            'UserID' => $this->UserID,
            'UserKey' => $this->UserKey,
            'UserActiveFlag' => $this->UserActiveFlag,
            'UserCreatedDate' => $this->UserCreatedDate,
            'UserModifiedDate' => $this->UserModifiedDate,
            'UserModifiedDTLTOffset' => $this->UserModifiedDTLTOffset,
            'UserInactiveDTLTOffset' => $this->UserInactiveDTLTOffset,
        ]);

        $query->andFilterWhere(['like', 'UserName', $this->UserName])
            ->andFilterWhere(['like', 'UserFirstName', $this->UserFirstName])
            ->andFilterWhere(['like', 'UserLastName', $this->UserLastName])
            ->andFilterWhere(['like', 'UserLoginID', $this->UserLoginID])
            ->andFilterWhere(['like', 'UserEmployeeType', $this->UserEmployeeType])
            ->andFilterWhere(['like', 'UserPhone', $this->UserPhone])
            ->andFilterWhere(['like', 'UserCompanyName', $this->UserCompanyName])
            ->andFilterWhere(['like', 'UserCompanyPhone', $this->UserCompanyPhone])
            ->andFilterWhere(['like', 'UserAppRoleType', $this->UserAppRoleType])
            ->andFilterWhere(['like', 'UserComments', $this->UserComments])
            ->andFilterWhere(['like', 'UserCreatedBy', $this->UserCreatedBy])
            ->andFilterWhere(['like', 'UserModifiedBy', $this->UserModifiedBy])
            ->andFilterWhere(['like', 'UserCreateDTLTOffset', $this->UserCreateDTLTOffset]);

        return $dataProvider;
    }
}
