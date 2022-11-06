<?php

/**
 * @package         Google Structured Data
 * @version         5.1.6 Pro
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace GSD\Schemas\Schemas;

// No direct access
defined('_JEXEC') or die;

use GSD\Helper;

class FAQ extends \GSD\Schemas\Base
{
    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        $mode = $this->data->get('mode', 'auto');

        $faq = $this->data['faq_repeater_fields'];
        
        $allowed_tags = '<h1><h2><h3><h4><h5><h6><br><ol><ul><li><p><a><div><b><strong><i><em>';
        
        $faqData = [];

        switch ($mode)
        {
            // Manual Mode
            case 'manual':
                foreach ($faq as $item)
                {
                    $question = trim($item->question);
                    $question = preg_replace('/\s\s+/', ' ', $question);
                    $question = strip_tags($question);

                    $answer = trim($item->answer);
                    $answer = strip_tags($answer, $allowed_tags);

                    $faqData[] = [
                        'question' => $question,
                        'answer'   => $answer
                    ];
                }
                break;

            // Auto Mode
            case 'auto':
                $pageHTML = Helper::getBuffer();

                // Default to page's text, if the document's HTML is not available yet.
                if (empty($pageHTML))
                {
                    $pageHTML = $this->data['introtext'] . $this->data['fulltext'];
                }

                $question_selector = $this->data->get('question_selector', '.question');
                $answer_selector = $this->data->get('answer_selector', '.answer');

                // Find questions and answers
                $questions = Helper::findFAQContent($pageHTML, $question_selector);
                $answers = Helper::findFAQContent($pageHTML, $answer_selector);

                // Combine the Q&A
                if (count($questions) && count($answers))
                {
                    $counter = 0;
                    foreach ($questions as $q)
                    {
                        $question = trim($q['value']);

                        $answer = isset($answers[$counter]['html']) ? $answers[$counter]['html'] : '';

                        // Remove spaces, new lines, invalid HTML tags and empty paragraphs.
                        $answer = preg_replace('/\s\s+/', ' ', $answer);
                        $answer = strip_tags($answer, $allowed_tags);
                        $answer = preg_replace('/<p>\s*<\/p>/', '', $answer);
                        $answer = trim($answer);


                        $faqData[] = [
                            'question' => $question,
                            'answer' => $answer
                        ];

                        $counter++;
                    }
                } else 
                {
                    Helper::log([
                        'Error'             => 'No FAQs found',
                        'HTML to search'    => $pageHTML,
                        'Question Selector' => $question_selector,
                        'Questions Found'   => count($questions),
                        'Answer Selector'   => $answer_selector,
                        'Answers Found'     => count($answers)
                    ]);
                }
        }
        
        $this->data->set('faqs', $faqData);

        parent::initProps();
    }
}