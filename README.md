# 产品模块

商品、产品主数据

TODO 金额计算处理 https://packagist.org/packages/tbbc/money-bundle

参考 https://zq99299.github.io/mysql-tutorial/ali-new-retail/04/03.html#%E5%93%81%E7%89%8C%E8%A1%A8 这里的设计

TODO 销量计算 https://www.jiulangdianshang.com/changjianwenti/tuiguangwenti/207.html

打包品，最终卖的也是 SKU，所以应该在 SKU 那层设置。
一个SKU可以由其他SKU、优惠券等等打包而成，那么我还需要一个商品类型这种东西吗？

TODO：将库存逻辑从这里面拆分出去，不是所有项目都需要库存控制。

从功能描述来看，Product就等于SPU、Variant等于SKU。

## 参考资料：

* [快驴进货平台商品发布规范](https://rules-center.meituan.com/m/detail/guize/264?activeRule=1&commonType=17)
* [天猫达尔文介绍](https://developer.alibaba.com/docs/doc.htm?spm=a219a.7629140.0.0.4d9775feZoWKMw&treeId=1&articleId=108954&docType=1)
* [商品发布规范及管理规则](http://www.ycbon.com/vip_doc/18692415.html)
* [Shopware开发者文档](https://developer.shopware.com/docs/concepts/commerce/catalog/sales-channels) 主要是参考了他的产品部分设计
* [电商平台商品管理及治理的策略体系](https://www.woshipm.com/operate/4707858.html)
