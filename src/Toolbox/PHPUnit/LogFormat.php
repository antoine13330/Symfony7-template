<?php 
namespace App\Toolbox\PHPUnit;

class LogFormat
{
    private string $testCategory;
    private string $testName;
    private string $testUrl;
    private string $testMethod;
    private string $testStatus;
    
    public function __construct(string $testCategory, string $testName, string $testUrl, string $testMethod, string $testStatus)
    {
        $this->testCategory = $testCategory;
        $this->testName = $testName;
        $this->testUrl = $testUrl;
        $this->testMethod = $testMethod;
        $this->testStatus = $testStatus;
    }
   


    public function getTestMessage(string $message): string
    {
        return "Category: {$this->testCategory} - Name: {$this->testName} -  URL: {$this->testUrl} -  Method: {$this->testMethod} - Status: {$this->testStatus} - Message: {$message}";
    }
  

}