<?php

namespace App\Controller;

use App\Form\TogglReportForm;
use App\Form\TogglApiForm;
use Dompdf\Dompdf;
use Dompdf\Options;
use MorningTrain\TogglApi\TogglApi;
use MorningTrain\TogglApi\TogglReportsApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class TogglApiController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render('toggl_api/index.html.twig', [
            'controller_name' => 'TogglApiController',
        ]);
    }

    /**
     * @Route("/toggl", name="toggl_config")
     */
    public function togglConfig(Request $request)
    {   
        $apiForm = $this->createForm(TogglApiForm::class);

        $apiForm->handleRequest($request);
        
        $renderForm = [
            'reportForm' => FALSE,
            'TogglApiForm' => $apiForm->createView(),
        ];

        if($apiForm->isSubmitted() && $apiForm->isValid()){

            $data = $apiForm->getData();
            $key = $data['Api_key'];
            $togglDetails = new TogglApi($key);
            $userDetails = (Object) $togglDetails->getMe();
            $userId = $userDetails->id;
            $workSpaces = $togglDetails->getWorkspaces();
            $list_workSpace = [];
            foreach ($workSpaces as $workSpace) {
                $id = $workSpace->id;
                $name = $workSpace->name;
                $list_workSpace[$name] = (string) $id;
            }
            $reportForm = $this->createForm(TogglReportForm::class, null, [
                'workspaceOptions' => $list_workSpace,
                'userId' => $userId,
                'apiKey' => $key,
            ]);
            $renderForm = [
                'reportForm' => TRUE,
                'TogglApiForm' => $apiForm->createView(),
                'TogglReportForm' => $reportForm->createView(),
            ];
        }
        
        if($request->request->has('toggl_report_form')){
            $data = $request->request->get('toggl_report_form');
            $response =  $this->forward('App\Controller\TogglApiController::retrieveData', [
                'formData' => $data
                ]);
            return $response;
        }
        
        return $this->render('toggl_api/report_configuration.html.twig', $renderForm);
    }

    public function retrieveData($formData)
    {
        // require_once $_SERVER['DOCUMENT_ROOT'] . '/../private.php';

        $workspace_id = $formData['Workspace_list'];
        $user_id = $formData['User_id'];
        $since = $formData['since']['year'] . '-' . $formData['since']['month'] . '-' . $formData['since']['day'];
        $until = $formData['until']['year'] . '-' . $formData['until']['month'] . '-' . $formData['until']['day'];
        $apiKey = $formData['apiKey'];
        $report = new TogglReportsApi($apiKey);
        $page_count = 3;
        $id = $page_count + 1;
        $result_details = [];

        for ($i=1; $i < $id; $i++) {
            $query = [
                'user_agent' => 'app_new',
                'workspace_id' => $workspace_id,
                'since' => $since,
                'until' => $until,
                'user_ids' => $user_id,
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

        foreach ($result_details as $key => $value) {
            $sec = $value->dur/1000;
            $duration_time = date('H:i:s', $sec);
            $start_date = Date('Y-m-d', strtotime($value->start));

            $data[$key] = array_merge($data, [
                'task_id' => "#$key",
                'description' => $value->description,
                'date' => $start_date,
                'dur' => $duration_time,
                'user' => $value->user,
                'project' => $value->project,
            ]);
        }

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $reportPage = $this->render('toggl_api/report.html.twig', [
            'report_details' => $data,
        ]);

        $dompdf->load_html($reportPage);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream("toggl_report.pdf", [
            "Attachment" => false
        ]);

        // return $this->render('toggl_api/report.html.twig', [
        //     'report_details' => $data,
        // ]);

    }
}
