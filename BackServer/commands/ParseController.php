<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 06.02.2019
 * Time: 10:57
 */

namespace app\commands;


use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Exception;

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
            Yii::$app->db
                ->createCommand("INSERT INTO post (`url`, `method`,`body`) VALUES (:url,:method,:body)", [
                    'url' => $matches[2],
                    'method' => $matches[3],
                    'body' => $matches[4]
                ])
                ->execute();
        }
    }
}