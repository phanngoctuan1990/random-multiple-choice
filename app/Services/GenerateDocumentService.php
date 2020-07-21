<?php

namespace App\Services;

use Illuminate\Support\Arr;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class GenerateDocumentService
{
    protected $request;

    /**
     * Set the value of request
     *
     * @param array $request request
     *
     * @return GenerateDocumentService
     */
    public function setRequest(array $request): GenerateDocumentService
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Generate multiple choices
     *
     * @return mixed
     */
    public function generateMultipleChoices()
    {
        $sheets = collect($this->generateDataWithSheet());

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Full name:', ['size' => 14, 'bold' => true]);
        $section->addText('Position:',  ['size' => 14, 'bold' => true]);
        $section->addText();
        $answers = [];
        // Generate questions
        $sheets->map(function ($sheet) use ($section, &$answers) {
            $section->addText($sheet['name'], ['size' => 12,  'bold' => true]);
            $section->addText();
            foreach ($sheet['data'] as $index => $row) {
                $section->addText($index + 1 . '. ' . htmlspecialchars($row['question']), ['bold' => true]);
                foreach ($row['options'] as $option) {
                    $section->addText(htmlspecialchars($option));
                }
                $section->addText();
                $answers[] = ['A', 'B', 'C', 'D'][$row['answer'] - 1];
            }
            $section->addText();
        });
        $section->addText();

        // Generate answers
        $section->addText('Answers', ['size' => 12,  'bold' => true]);
        $section->addText();
        foreach ($answers as $index => $answer) {
            $section->addText($index + 1 . ': ' . $answer);
        }

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('multiple_choices.docx');

        return response()
            ->download(public_path('multiple_choices.docx'))
            ->deleteFileAfterSend(true);
    }

    /**
     * Generate data with sheet
     *
     * @return array
     */
    protected function generateDataWithSheet(): array
    {
        $sheets = [];
        collect($this->request['generate_data'])->map(function ($data) use (&$sheets) {
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
        return $sheets;
    }
}
