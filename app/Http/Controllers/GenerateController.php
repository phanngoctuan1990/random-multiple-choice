<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class GenerateController extends Controller
{
    public function generateMultipleChoices(Request $request)
    {
        $sheets = [];
        collect($request->generate_data)->map(function ($data) use (&$sheets) {
            $sheet['name'] = $data['name'];
            $sheet['data'] = [];
            $question = [];
            $answer = 0;
            foreach ($data['data'] as $row) {
                $answer++;
                if ($row[0]) {
                    if (count($question)) {
                        $answer = 0;
                        $sheet['data'][] = $question;
                        $question['options'] = [];
                    }
                    $question['question'] = $row[1];
                }
                $question['options'][] = $row[2];
                if (isset($row[3])) {
                    $question['answer'] = count($sheet['data']) ? $answer + 1 : $answer;
                }
            }
            try {
                $sheet['data'] = Arr::random($sheet['data'], $data['num_random']);
            } catch (\Exception $e) {
                // If number record want to ramdom more than question data
                // or question data is empty no need to do anything
            }
            if (count($sheet['data'])) {
                $sheets[] = $sheet;
            }
        });

        // Send data to the view using loadView function of PDF facade
        $pdf = \PDF::loadView('pdf.multiple_choices', ['sheets' => $sheets]);

        // Finally, you can download the file using download function
        return $pdf->download('multiple_choices.pdf');
    }
}
