<?php

/*
 * This file is part of the ConnectWp
 *
 * Copyright (C) 2018 Hisashi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ConnectWp;

use Eccube\Application;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\PageLayout;
use Eccube\Plugin\AbstractPluginManager;

class PluginManager extends AbstractPluginManager {

    /**
     * プラグインインストール時の処理
     *
     * @param $config
     * @param Application $app
     * @throws \Exception
     */
    public function install($config, Application $app) {
        
    }

    /**
     * プラグイン削除時の処理
     *
     * @param $config
     * @param Application $app
     */
    public function uninstall($config, Application $app) {
        
    }

    /**
     * プラグイン有効時の処理
     *
     * @param $config
     * @param Application $app
     * @throws \Exception
     */
    public function enable($config, Application $app) {
        $this->createPageLayout($app);
    }

    /**
     * プラグイン無効時の処理
     *
     * @param $config
     * @param Application $app
     * @throws \Exception
     */
    public function disable($config, Application $app) {
        $this->deletePageLayout($app);
    }

    /**
     * プラグイン更新時の処理
     *
     * @param $config
     * @param Application $app
     * @throws \Exception
     */
    public function update($config, Application $app) {
        
    }

    /**
     * ページレイアウトの作成
     */
    private function createPageLayout($app) {
        $em = $app['orm.em'];

        $DeviceType = $app['eccube.repository.master.device_type']
                ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName('wpの記事一覧');
        $PageLayout->setUrl('plugin_ConnectWp_post_list');
        $PageLayout->setMetaRobots('noindex');
        $PageLayout->setEditFlg(PageLayout::EDIT_FLG_DEFAULT);
        $em->persist($PageLayout);
        $em->flush($PageLayout);
    }

    /**
     * ページのレイアウトの削除
     */
    private function deletePageLayout($app) {
        $em = $app['orm.em'];

        /** @var $repos PageLayoutRepository */
        $repos = $em->getRepository('Eccube\Entity\PageLayout');

        $DeviceType = $app['eccube.repository.master.device_type']
                ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayout = $this->findPageLayout($repos, $DeviceType, 'plugin_ConnectWp_post_list');
        if ($PageLayout instanceof PageLayout) {
            $em->remove($PageLayout);
            $em->flush($PageLayout);
        }
    }

    /**
     * Find page layout.
     *
     * @param EntityRepository $repos
     * @param DeviceType       $DeviceType
     * @param string           $url
     *
     * @return mixed
     */
    protected function findPageLayout($repos, $DeviceType, $url) {
        try {
            $PageLayout = $repos->createQueryBuilder('p')
                    ->where('p.DeviceType = :DeviceType AND p.url = :url')
                    ->getQuery()
                    ->setParameters(array(
                        'DeviceType' => $DeviceType,
                        'url' => $url,
                    ))
                    ->getSingleResult();

            return $PageLayout;
        } catch (\Exception $exception) {
            return false;
        }
    }

}
