<?php

/**
 * This file is part of Mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Console\MetaDataGenerator;

use Slick\Mvc\Console\MetaDataGeneratorInterface;
use Slick\Mvc\Exception\Console\ComposerParseException;
use Slick\Mvc\Exception\FileNotFoundException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Composer - Grabs data from project composer file
 *
 * @package Slick\Mvc\Console\MetaDataGenerator
 * @author  Filipe Silva <silva.filipe@gmail.com>
 */
class Composer extends AbstractMetaDataGenerator implements MetaDataGeneratorInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string composer file.
     */
    protected $composerFile;

    /**
     * @var Object
     */
    protected $composerData;

    /**
     * @var QuestionHelper
     */
    protected $questionHelper;

    /**
     * Composer
     *
     * @param null|string $filename
     */
    public function __construct($filename = null)
    {
        $this->setComposerFile($filename);
    }

    /**
     * Get generated data array
     *
     * @return array
     */
    public function getData()
    {
        if (empty($this->data)) {
            $this->data = [
                'project' => $this->getComposerData()->name
            ];
            $this->data = array_merge($this->data, $this->getAuthor());
        }
        return $this->data;
    }

    /**
     * Sets the composer file to parse
     *
     * @param $file
     */
    protected function setComposerFile($file)
    {
        if (null != $file && !is_file($file)) {
            throw new FileNotFoundException(
                "The file {$file} was not found."
            );
        }

        $this->composerFile = $file;
    }

    /**
     * Gets the data object from json file
     *
     * @return mixed|Object
     */
    protected function getComposerData()
    {
        if (null == $this->composerData) {
            $json = file_get_contents($this->getComposerFile());
            $this->composerData = json_decode($json);

            if (null === $this->composerData) {
                throw new ComposerParseException(
                    "Error parsing file {$this->getComposerFile()}"
                );
            }
        }

        return $this->composerData;
    }

    /**
     * Gets composerFile property
     *
     * @return string
     */
    public function getComposerFile()
    {
        if (null == $this->composerFile) {
            $this->setComposerFile(getcwd().'/composer.json');
        }
        return $this->composerFile;
    }

    /**
     * Gets questionHelper property
     *
     * @return QuestionHelper
     */
    public function getQuestionHelper()
    {
        if (null == $this->questionHelper) {
            $this->questionHelper = $this->getCommand()->getHelper('question');
        }
        return $this->questionHelper;
    }


    protected function getAuthor()
    {
        if (empty($this->getComposerData()->authors)) {
            return $this->requestAuthor();
        }

        if (count($this->getComposerData()->authors) > 1) {
            return $this->selectAuthors();
        }

        return [
            'authorName' => $this->getComposerData()->authors[0]->name,
            'authorEmail' => $this->getComposerData()->authors[0]->email
        ];
    }


    protected function requestAuthor()
    {
        $nameQuestion = new Question('Please enter your name: ');
        $emailQuestion = new Question('Please enter your e-mail address: ');
        $this->getOutput()->writeln('');
        $this->getOutput()->writeln(
            '<info>Cannot determine the developer name ' .
            'from project\'s composer.json file.</info>'
        );
        $this->getOutput()->writeln(
            "<comment>To automatically set the developer's " .
            "name and e-mail and avoid entering it on every generate:* " .
            "commands set the authors entry on your project's " .
            "composer.json file.</comment>"
        );
        $authorName = $this->getQuestionHelper()->ask(
            $this->getInput(),
            $this->getOutput(),
            $nameQuestion
        );
        $authorEmail = $this->getQuestionHelper()->ask(
            $this->getInput(),
            $this->getOutput(),
            $emailQuestion
        );
        return compact('authorName', 'authorEmail');
    }

    protected function selectAuthors()
    {
        $options = $this->getAuthorsAsOptions();
        $this->getOutput()->writeln('');
        $this->getOutput()->writeln(
            '<info>Multiple authors found on ' .
            'project\'s composer.json file.</info>'
        );
        $question = new ChoiceQuestion('Please select the author from the list above:', $options, 0);
        $question->setErrorMessage('The choice %s is invalid.');
        $selected = $this->getQuestionHelper()->ask($this->input, $this->output, $question);
        $parts = explode('<', $selected);
        return [
            'authorName' => trim($parts[0]),
            'authorEmail' => trim($parts[1], '>'),
        ];
    }

    /**
     * Returns the authors from composer data as selectable options
     *
     * @return array
     */
    protected function getAuthorsAsOptions()
    {
        $data = [];
        foreach ($this->getComposerData()->authors as $author) {
            $data[] = "{$author->name} <{$author->email}>";
        }
        return $data;
    }
}