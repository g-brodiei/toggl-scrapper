<?php

namespace App\Controller;

use DateTime;
use MorningTrain\TogglApi\TogglReportsApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class TogglApiController extends AbstractController
{
    /**
     * @Route("/toggl/api", name="toggl_api")
     */
    public function index()
    {
        return $this->render('toggl_api/index.html.twig', [
            'controller_name' => 'TogglApiController',
        ]);
    }

    /**
    * @Route("/toggl/api2", name="toggl_api2")
    */
    public function retrieveData()
    {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/../private.php';

        $report = new TogglReportsApi($API_TOKEN);
        $page_count = 3;
        $id = $page_count + 1;
        $result_details = [];
        for ($i=1; $i < $id; $i++) {
            $query = [
                'user_agent' => 'app_new',
                'workspace_id' => '3319673',
                'since' => '2020-03-01',
                'until' => '2020-03-31',
                'user_ids' => '5239565',
                'order_field' => 'date',
                'order_desc' => 'off',
                'page' => $i,
            ];
            $result = (array)$report->getDetailsReport($query);
            if ($i > 2){
                $result_3 = $result;
            } elseif ($i > 1) {
                $result_2 = $result;
            } else {
                $result_1 = $result;
            }
        }
        if ($page_count == 3) {
            $result_temporary = array_merge($result_2, $result_3);
            $result_details = array_merge($result_1, $result_temporary);
        } else {
            $result_details = array_merge($result_1, $result_2);
        }
        $data = [];

        // if($report_details){

        // }
        foreach ($result_details as $key => $value) {
            $now = get_object_vars($value);
            $sec = $now['dur']/1000;
            $duration_time = date('H:i:s', $sec);
            $start_date = Date('Y-m-d', strtotime($now['start']));
            $data[$key] = array_merge($data, [
                'task_id' => "#$key",
                'description' => $now['description'],
                'date' => $start_date,
                'dur' => $duration_time,
                'user' => $now['user']
            ]);
        }
        return $this->render('toggl_api/report.html.twig', [
            'report_details' => $data,
        ]);
    }
}
