<?php

/*
 * Author: Mike
 * Date: 18.06.19
 * Add console command for send email to the PM and Bernard when a task FBR is flagged as redundant.
 * Add column redundant_send for projects_tasks table
 */
class SendProjectsFBRsRedundantCommand extends CConsoleCommand
{

    public function actionIndex(){
        $project_tasks = Yii::app()
            ->db->createCommand("SELECT p.name AS project_name,c.name AS castomer_name,pt.fbr,pt.title,pt.keywords,pt.complexity,pt.id,
                                 (SELECT CONCAT(u.firstname,' ',u.username,' ',u.lastname) FROM users AS u WHERE u.id=p.project_manager) AS project_m,
                                 (SELECT CONCAT(u.firstname,' ',u.username,' ',u.lastname) FROM users AS u WHERE u.id=p.business_manager) AS business_m FROM projects AS p 
                                 JOIN customers AS c ON c.id=p.customer_id
                                 JOIN projects_phases AS pp ON pp.id_project=p.id
                                 JOIN projects_tasks AS pt ON pt.id_project_phase=pp.id WHERE pt.existsfbr=2 AND pt.redundant_send=0 ORDER BY p.name ASC;")
            ->queryAll();

        if (isset($project_tasks) && !empty($project_tasks)){
            self::sendNotificationsEmailsProjectSTask($project_tasks);
        }
    }

    private static function sendNotificationsEmailsProjectSTask($data){
        $body = <<<XER
            <p style="background: yellow;">Dear All,<br>Please find below a list of FBRs which were created as redundant for projects</p>
            <table>
                  <thead>
                    <tr>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">Project</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">Customer</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">PM</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">BM</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">FBR #</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">Title</span></th>                      
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">Module</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">Keywords</span></th>
                      <th style="padding: 5px;" scope="col"><span style="background: yellow;">Complexity</span></th>
                    </tr>
                  </thead>
                  <tbody>
            
XER;
        $complexity = [1 => 'Low',2 => 'Medium', 3 => 'High'];
        $send_task = null;

        foreach ($data as $item){

            $send_task[] = $item['id'];

            $body .= <<<XER

             <tr>
                <td style="padding: 5px;"><span style="background: yellow;">{$item['project_name']}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$item['castomer_name']}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$item['project_m']}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$item['business_m']}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$item['fbr']}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$item['title']}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;"></span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$item['keywords']}</span></td>
                <td style="padding: 5px;"><span style="background: yellow;">{$complexity[(int)$item['complexity']]}</span></td>
             <tr>

XER;


        }

        $body .= '</tbody></table>';

        Yii::app()->mailer->ClearAddresses();
        Yii::app()->mailer->AddAddress('Bernard.Khazzaka@sns-emea.com');
        Yii::app()->mailer->Subject  = 'task FBR is flagged as redundant';
        Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");

        if (Yii::app()->mailer->Send(true)){
            Yii::app()->db->createCommand("UPDATE projects_tasks SET redundant_send=1 WHERE id IN(".implode(',',$send_task).");")->queryAll();
        }
    }

}