<?php

/*
 * This file is part of the ConnectWp
 *
 * Copyright (C) 2018 Hisashi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ConnectWp\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class ConnectWpController {

    /**
     * 記事一覧
     * @param Application $app
     * @param Request $request
     */
    public function postList(Application $app, Request $request) {
        // ベースのAPI_URL
        $url = $app['config']['base_url'] . '/' . $app['config']['api_path'] . '/' . 'posts';
        // 全投稿記事取得
        $json = file_get_contents($url);
        $json = mb_convert_encoding($json, 'UTF-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $postData = json_decode($json, true);
        
        // 特定のカテゴリに紐づく記事一覧を取得
        $url2 = $url . '?categories=' . $this->getCategoryId($app, 'short');
        $json2 = file_get_contents($url2);
        $json2 = mb_convert_encoding($json2, 'UTF-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $postData2 = json_decode($json2, true);

        // 特定のIDの記事を取得
        $url3 = $url . '/324';
        $json3 = file_get_contents($url3);
        $json3 = mb_convert_encoding($json3, 'UTF-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $postData3 = json_decode($json3, true);

        // 特定のIDの記事を取得
        $url4 = $app['config']['base_url'] . '/' . $app['config']['api_path'] . '/' . 'pages';
        $json4 = file_get_contents($url4);
        $json4 = mb_convert_encoding($json4, 'UTF-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $postData4 = json_decode($json4, true);
        
        return $app->render('ConnectWp/Resource/template/postList.twig', array(
            'postData' => $postData,
            'postData2' => $postData2,
            'postData3' => $postData3,
            'postData4' => $postData4,
        ));
    }
    
    /**
     * 指定したカテゴリ名(スラグ名)からidを取得
     * @param Application $app
     * @param string $slugName カテゴリ名(スラグ名)
     */
    private function getCategoryId(Application $app, $slugName) {
        // ベースのAPI_URL
        $url = $app['config']['base_url'] . '/' . $app['config']['api_path'] . '/' . 'categories?slug=' . $slugName;
        // 全投稿記事取得
        $json = file_get_contents($url);
        $json = mb_convert_encoding($json, 'UTF-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $catgoryData = json_decode($json, true);
        
        return $catgoryData[0]['id'];
    }

}
