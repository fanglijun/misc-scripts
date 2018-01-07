<?php
/**

Usage: php whichport.php 8080 [-v]
-v: print system commands and output

**/
date_default_timezone_set('Asia/Shanghai');

if (count($argv) <= 1) {
    exit('Usage: php '.$argv[0].' port_number [-v]');
}
$portNumber = $argv[1];
$verbose = isset($argv[2]) && $argv[2] === '-v';

(new WindowsVersion($portNumber, $verbose))->run();


class WindowsVersion {
    private static $netstatCmdFormat = 'netstat -aon | findstr %d';
    private static $tasklistCmdFormat = 'tasklist | findstr %d';

    private $portNumber;
    private $verbose;

    public function __construct($portNumber, $verbose = false) {
        $this->portNumber = $portNumber;
        $this->verbose = $verbose;
    }

    public function run() {
        $pid = $this->getPid($this->portNumber);
        if ($pid) {
            $this->getTask($pid);
        }
    }

    private function getPid($portNumber) {
        $netstatCmd = sprintf(self::$netstatCmdFormat, $portNumber);
        $this->output($netstatCmd);
        exec($netstatCmd, $result);
        $this->output($result);
        if (empty($result)) {
            $this->output('No process found.', true);
            return;
        }

        // 匹配Local Address里含有目标端口的行
        // Proto  Local Address          Foreign Address        State           PID
        // TCP    0.0.0.0:135            0.0.0.0:0              LISTENING       924
        $pattern = '/TCP\s+[\S]+?:'.$portNumber.'.+?(\d+)$/';
        foreach ($result as $line) {
            if (preg_match($pattern, $line, $matches)) {
                break;
            }
        }
        if (empty($matches)) {
            $this->output('No process found.', true);
        } else {
            $this->output($matches);
            return $matches[1];
        }
    }

    private function getTask($pid) {
        $tasklistCmd = sprintf(self::$tasklistCmdFormat, $pid);
        $this->output($tasklistCmd);
        exec($tasklistCmd, $result);
        if (empty($result)) {
            $this->output('No task found for pid: '. $pid, true);
        } else {
            $this->output($result, true);
        }
    }

    private function output($any, $verbose = false) {
        if ($this->verbose || $verbose) {
            $traces = debug_backtrace();
            $fistLine = reset($traces);
            $secondLine = next($traces);
            echo '______________________________________________________________________'.PHP_EOL;
            echo date('Y-m-d H:i:s ').$secondLine['function'].' ('.$fistLine['line'].')'.PHP_EOL;
            if (is_array($any)) {
                echo implode(PHP_EOL, $any).PHP_EOL;
            } else {
                echo $any.PHP_EOL;
            }
        }
    }
}
