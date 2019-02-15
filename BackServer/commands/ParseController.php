<?php

namespace app\commands;


use app\models\Task;
use app\services\RabbitService;
use yii\base\Module;
use yii\console\Controller;
use yii\console\ExitCode;

class ParseController extends Controller
{

    const NOT_PROCESSED = "N";

    const PROCESSED = "P";

    const REGEX = '/uri_path="(.+?)" http_method="(.+?)" request_body="(.+?)"/uU';

    /** @var RabbitService  */
    private $rabbitService;

    public function __construct(string $id, Module $module,RabbitService $rabbitService, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->rabbitService = $rabbitService;
    }

    public function actionIndex()
    {
        $callback = function ($msg) {
            if(file_exists($msg->body)){
                $this->parseFile($msg->body);
            }
        };
        $this->rabbitService->getMessages($callback);
        return ExitCode::OK;
    }

    private function parseFile(string $path)
    {
        $handle = null;
        try{
            $handle = fopen($path, 'r+');
        }catch (\Exception $ex){
            $this->stderr('Error: ' . $ex->getMessage() . PHP_EOL);
        }
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (substr($line, 1, strlen(self::NOT_PROCESSED)) === self::NOT_PROCESSED) {
                    fseek($handle, -1 * mb_strlen($line, '8bit'), SEEK_CUR);
                    try {
                        $this->savePost($line);
                    } catch (\Exception $ex) {
                        $this->stderr('Error: ' . $ex->getMessage() . PHP_EOL);
                    }
                    fwrite($handle, '[' . self::PROCESSED . ']');
                }
            }
            $this->stdout('Parsed log ' . $path . PHP_EOL);
            fclose($handle);
        }
    }

    private function savePost(string $line)
    {
        if (preg_match(self::REGEX, $line, $matches)) {
            if (!$this->isAllowed($matches[1])) {
                return;
            }
            $task = new Task();
            $task->route = $matches[1];
            $task->method = $matches[2];
            $task->body = $matches[3];
            $task->save();
        }
    }

    private function isAllowed(string $path)
    {
        $row = Task::find()
            ->select(['id', 'path'])
            ->where(['path' => $path])
            ->one();
        return !!$row;
    }
}
