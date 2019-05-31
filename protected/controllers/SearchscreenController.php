<?php
class SearchscreenController extends Controller{
    public function actionIndex(){
        if (Yii::app()->user->isGuest)
            $this->redirect(array(
                'site/login'
            ));
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/searchscreen/index' => array(
                'label' => 'Search Screen',
                'url' => array(
                    'searchscreen/index'
                ),
                'itemOptions' => array(
                    'class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;
        $this->render('index');
    }
    public static function listStatusType(){
        return array(
            'New',
            'Approved',
            'Rejected',
            'Cancelled'
        );
    }
}?>