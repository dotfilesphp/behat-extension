<?php

declare(strict_types=1);

/*
 * This file is part of the dotfiles project.
 *
 *     (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotfiles\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Dotfiles\Core\Exceptions\InvalidArgumentException;
use Dotfiles\Core\Util\Toolkit;
use Webmozart\Assert\Assert;

class FileContext implements Context
{
    /**
     * @var MainContext
     */
    private $mainContext;

    /**
     * @param BeforeScenarioScope $scope
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $this->mainContext = $environment->get(MainContext::class);
    }

    /**
     * @Given I have dotfile :name
     * @Given I have dotfile :name with:
     * @param string $path Where to create file directory
     * @param string $contents A file contents
     */
    public function iHaveDotfile(string $path, PyStringNode $contents = null)
    {
        $target = getenv('HOME').DIRECTORY_SEPARATOR.$path;
        if(is_null($contents)){
            touch($target);
        }else{
            $contents = $contents->getStrings();
            file_put_contents($target,$contents, LOCK_EX);
        }
    }

    /**
     * @Given Dotfile :name should contain :contents
     * @param string    $name
     * @param string    $contents
     */
    public function dotfileShouldContain(string $name,string $needle)
    {
        $target = getenv('HOME').DIRECTORY_SEPARATOR.$name;
        if(!is_file($target)){
            throw new InvalidArgumentException('Can not find file: '.$name);
        }
        $contents = file_get_contents($target);
        Assert::contains($contents,$needle);
    }

    /**
     *
     * @Given I have backup defaults patch :path with:
     * @param string $path
     * @param PyStringNode $contents
     */
    public function iHaveBackupDefaultsPatch(string $path, PyStringNode $contents = null)
    {
        $target = '/home/backup/src/defaults/patch/'.$path;
        $this->generateFile($target,$contents);
    }

    private function generateFile($target, PyStringNode $contents = null)
    {
        Toolkit::ensureFileDir($target);
        if(is_null($contents)){
            touch($target);
        }else{
            $contents = $contents->getStrings();
            file_put_contents($target,$contents, LOCK_EX);
        }
    }

}
