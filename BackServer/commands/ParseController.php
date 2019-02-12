<?php

namespace app\commands;


use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Exception;
use yii\db\Query;

class ParseController extends Controller
{
    const LOGS_PATH = "/application/BackServer/logs/back.access.log";

    const NOT_PROCESSED = "N";

    const PROCESSED = "P";

    const regex = '/uri_path="(.+?)" http_method="(.+?)" request_body="(.+?)/uU';

    /**
     * Begin parse logs
     * @return int Exit code
     */
    public function actionIndex()
    {
        $this->parseFile(self::LOGS_PATH);
        return ExitCode::OK;
    }

    private function parseFile(string $path)
    {
        $handle = fopen($path, 'r+');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (substr($line, 1, strlen(self::NOT_PROCESSED)) === self::NOT_PROCESSED) {
                    fseek($handle, -1 * mb_strlen($line, '8bit'), SEEK_CUR);
                    try {
                        $this->parseString($line);
                    } catch (Exception $ex) {
                        //send error
                    }
                    fwrite($handle, '[' . self::PROCESSED . ']');
                }
            }
            fclose($handle);
        }
    }

    private function parseString(string $line)
    {
        if (preg_match(self::regex, $line, $matches)) {
            if (!$this->isAllowed($matches[1])) {
                return;
            }
            @Yii::$app->db
                ->createCommand()
                ->insert('tasks', array(
                    'route' => $matches[1],
                    'method' => $matches[2],
                    'body' => $matches[3]
                ))
                ->execute();
        }
    }

    private function isAllowed(string $path)
    {
        $row = (new Query())
            ->select(['id', 'path'])
            ->from('urls')
            ->where(['path' => $path])
            ->one();
        return !!$row;
    }
}
