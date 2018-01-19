<?php

/*
 * This file is part of the ConnectWp
 *
 * Copyright (C) 2018 Hisashi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ConnectWp\ServiceProvider;

use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class ConnectWpServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        // Wordpress記事取得画面
        $app->match('/plugin/connectwp/post_list', 'Plugin\ConnectWp\Controller\ConnectWpController::postList')->bind('plugin_ConnectWp_post_list');

        // Config
        $app['config'] = $app->share($app->extend('config', function ($config) {
            // Update constants
            $constantFile = __DIR__.'/../Resource/config/constant.yml';
            if (file_exists($constantFile)) {
                $constant = Yaml::parse(file_get_contents($constantFile));
                if (!empty($constant)) {
                    // Replace constants
                    $config = array_replace_recursive($config, $constant);
                }
            }

            return $config;
        }));

        // メッセージ登録
        // $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
        // $app['translator']->addResource('yaml', $file, $app['locale']);

        // load config
        // プラグイン独自の定数はconfig.ymlの「const」パラメータに対して定義し、$app['connectwpconfig']['定数名']で利用可能
//         if (isset($app['config']['ConnectWp']['const'])) {
//             $config = $app['config'];
//             $app['connectwpconfig'] = $app->share(function () use ($config) {
//                 return $config['ConnectWp']['const'];
//             });
//         }

        // ログファイル設定
        $app['monolog.logger.connectwp'] = $app->share(function ($app) {

            $logger = new $app['monolog.logger.class']('connectwp');

            $filename = $app['config']['root_dir'].'/app/log/connectwp.log';
            $RotateHandler = new RotatingFileHandler($filename, $app['config']['log']['max_files'], Logger::INFO);
            $RotateHandler->setFilenameFormat(
                'connectwp_{date}',
                'Y-m-d'
            );

            $logger->pushHandler(
                new FingersCrossedHandler(
                    $RotateHandler,
                    new ErrorLevelActivationStrategy(Logger::ERROR),
                    0,
                    true,
                    true,
                    Logger::INFO
                )
            );

            return $logger;
        });

    }

    public function boot(BaseApplication $app)
    {
    }

}
