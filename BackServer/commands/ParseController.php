<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 06.02.2019
 * Time: 10:57
 */

namespace app\commands;


use yii\console\Controller;
use yii\console\ExitCode;

class ParseController extends Controller
{

    /**
     * Begin parse logs
     * @return int Exit code
     */
    public function actionIndex()
    {

        return ExitCode::OK;
    }
}