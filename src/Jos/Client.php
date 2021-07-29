<?php

/*
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace JosClient\Jos;

use JosClient\Kernel\BaseClient;

/**
 * Class Client.
 *
 */
class Client extends BaseClient
{

    /* 查询一级目录
     * @return array|\JosClient\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JosClient\Kernel\Exceptions\InvalidConfigException
     */
    public function firstLevelCategories()
    {
        $method = 'jingdong.eclp.category.getFirstLevelCategories';
        return $this->httpGet(compact('method'));
    }

    /* 查询二级目录
     * @param int $firstCategoryNo 商品一级分类编码
     * @param int $secondCategoryNo 商品二级分类编码
     * @return array|\JosClient\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JosClient\Kernel\Exceptions\InvalidConfigException
     */
    public function secondLevelCategories(int $firstCategoryNo, int $secondCategoryNo = NUll)
    {
        $method = 'jingdong.eclp.category.getSecondLevelCategories';
        return $this->httpGet(compact('method', 'firstCategoryNo', 'secondCategoryNo'));
    }

    /* 查询三级目录
     * @param int $secondCategoryNo 商品二级分类编码
     * @param int $thirdCategoryNo 商品三级分类编码
     * @return array|\JosClient\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JosClient\Kernel\Exceptions\InvalidConfigException
     */
    public function thirdLevelCategories(int $secondCategoryNo, int $thirdCategoryNo = NUll)
    {
        $method = 'jingdong.eclp.category.getThirdLevelCategories';
        return $this->httpGet(compact('method', 'secondCategoryNo', 'thirdCategoryNo'));
    }

    /* 通用调用接口
     * @param array 接口所需参数
     * @return array|\JosClient\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JosClient\Kernel\Exceptions\InvalidConfigException
     */

    public function unify(array $param)
    {
        return $this->httpGet($param);
    }
}
