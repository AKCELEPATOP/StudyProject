<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 13.02.2019
 * Time: 15:45
 */

namespace app\commands;


use app\models\Log;
use app\models\Task;
use app\models\Url;
use app\services\RabbitService;
use function foo\func;
use Yii;
use yii\base\Module;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Query;

class ParseLogDirController extends Controller
{
    /** @var RabbitService */
    private $rabbitService;

    public function __construct(string $id, Module $module, RabbitService $rabbitService, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->rabbitService = $rabbitService;
    }

    public function actionIndex()
    {
        $this->parseDir(Yii::$app->params['logDir']);
        return ExitCode::OK;
    }

    private function parseDir(string $dir)
    {
        $files = $this->dirList($dir);
        $this->removeCurrent($files);
        if (!count($files)) {
            return;
        }
        $rows = $this->getProceedLogs();
        foreach ($files as $file => $value) {
            if (!in_array($value, $rows)) {
                try {
                    $log = new Log();
                    $log->path = $value;
                    $log->save();
                    $this->rabbitService->sendMessage($value);
                } catch (\Exception $ex) {
                    $this->stderr('Error: ' . $ex->getMessage() . PHP_EOL);
                }
                $this->stdout('Sent ' . $value . ' to parse' . PHP_EOL);
            }
        }
    }

    private function getProceedLogs()
    {
        $rows = Log::find()
            ->select(['path'])
            ->all();
        array_walk($rows, function (&$item) {
            $item = $item['path'];
        });
        return $rows;
    }

    private function dirList(string $dir, &$list = array())
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $list[] = $path;
            } else if ($value != "." && $value != "..") {
                $this->dirList($path, $list);
            }
        }

        return $list;
    }

    private function removeCurrent(&$list)
    {
        if (count($list) === 0) {
            return;
        }
        $base = array_filter($list, function ($el) {
            return preg_match('/^(.+?)\.log$/uU', $el);
        });
        $max = max($base);
        array_splice($list, array_search($max, $list), 1);
    }
}
