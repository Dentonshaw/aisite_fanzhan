#AI泛站程序源码*多城市版*全自动免维护#

__ 源码完全免费！完全免费！完全免费！不会部署请联系技术QQ：853616368 __

- 谷歌、必应、Yandex收录效果超好！
- 如果打算做国内的搜索引擎，建议使用备案域名搭建（国内网站备案已是基本门槛）

- 搭建网站之前，根据自己使用的PHP版本，安装对应的扩展文件，参考【PHP对应版本的SG17扩展】目录下的教程。
（如果是最新版本的宝塔面板，直接在：宝塔控制面板=》软件商店=》已安装的PHP版本=》设置=》安装扩展
这个列表中找到【sg17】的扩展，直接安装即可；如果是旧版本的宝塔面板，参考教程手动安装扩展）

- 程序目前可支持的PHP版本有：PHP7.0/php7.3/PHP8.1（根据自己需求选择合适的PHP版本安装）

- 不同的PHP版本，请使用不同的PHP扩展，一台服务器只需要安装一次扩展即可。
安装的PHP扩展版本跟PHP版本保持一致，手动安装时要将扩展文件放在对应版本的PHP目录中。

1. 伪静态规则，参考【伪静态设置教程】目录。

2. 修改配置文件：config.php 根据提示修改域名和对应的网站名称，修改内容抓取IP的C段地址等；

3. 网站关键词词库keys.seo数量不得低于30万行(必须)，文件体积不能超过10MB，保存格式utf-8(不带BOM)；

4. 高频词库gaopin.seo整理几千或1、2万容易出流量的关键词即可，这个数量不宜太多，超过5万会拖慢运行速度；

5. kill.txt是需要屏蔽的内容所包含的字段，不需要屏蔽直接清空；

6. 用户端看到的内容，从in.html、c1.html、c3.html文件中修改；

- 程序搭建完成后，访问 http://你的域名/，看到的是in.html文件的内容，首页in.html模板建议自己设计；
- 设计首页模板【in.html】 可使用以下标签：
	{sitename}	网站名称（config.php中设置）
	{siteurl}	当前域名的hostname部分
    {city}      城市名，比如访问：/city/0311 对应的就是石家庄，访问首页时该标签为空
    {citylist}  城市分站链接，随机20个超链接
	{mulu}		程序所在目录（相对路径，config.php中可修改）
    {innews}	高频关键词锚文本列表（仅蜘蛛可见）
	{inreso}	全网热门内容锚文本列表（仅蜘蛛可见）
	{inlink}	随机调用目录中：tuijian.txt中的外部链接，可用于给其他网站引蜘蛛，也可以让自己的域名相互链接
				tuijian.txt 放自己的锚文本链接，一行一条，格式：`<a href='网址' target='_blank'>关键词</a>`

- 访问 http://你的域名/程序所在目录/post/关键词.html，看到的是c1.html文件的内容；
	参考根目录 c1.html 自己修改即可，这里的内容仅访客可见，蜘蛛看到的是AI生成的内容；
	
- 访问 http://你的域名/程序所在目录/任意后缀，看到的是c1.html文件的内容；
	参考根目录 c1.html 自己修改即可，这里的内容仅访客可见，蜘蛛看到的是AI生成的内容；
	
- 访问 http://你的域名/程序所在目录/open/?url=目标网址，看到的是c3.html文件的内容；
	参考根目录 c3.html 自己修改即可，这里的内容仅访客可见，蜘蛛看到的是目标网址的内容。

- 可向搜索引擎提交的种子页面：
	首页：http://你的网址/
	XML地图：http://你的网址/sitemap.xml
	TXT地图：http://你的网址/sitemap.txt
	列表页面：http://你的网址/list/1.html（列表ID：1-6均可）
    城市分站：http://你的网址/city/010（城市ID见city.txt）

##该程序最新案例参考：##

千度搜索：[https://www.1000d.top](https://www.1000d.top)
公关大侠：[https://www.ggdx.xyz](https://www.ggdx.xyz)
快速上排名：[https://www.0531news.cn](https://www.0531news.cn)
华纳普瑞：[https://www.hnpurui.cn](https://www.hnpurui.cn)
哈克会：[https://www.hakehui.org.cn](https://www.hakehui.org.cn)
SEO前线：[https://qx.07yue.com](https://qx.07yue.com)
企业SEO：[https://qy.urkeji.com](https://qy.urkeji.com)
个人SEO：[https://gr.urkeji.com](https://gr.urkeji.com)
SEO技术：[https://js.urkeji.com](https://js.urkeji.com)
SEO公司：[https://gs.07yue.com](https://gs.07yue.com)
SEO排名：[https://pm.07yue.com](https://pm.07yue.com)
千度搜索：[https://www.qdso.top](https://www.qdso.top)
啊哈美图网：[https://www.cosahara.com](https://www.cosahara.com)
网红客源：[https://www.whuky.cn](https://www.whuky.cn)
农村建站：[https://www.ncct-nl.com](https://www.ncct-nl.com)
极速优化：[https://www.cdn-server.net](https://www.cdn-server.net)
韦德SEO：[https://www.wehd.net](https://www.wehd.net)
吉特排名：[https://www.jettigames.com](https://www.jettigames.com)
吉特SEO：[https://seo.jettigames.com](https://seo.jettigames.com)
吉特GEO：[https://geo.jettigames.com](https://geo.jettigames.com)
威霸排名：[https://www.ve3ba.com](https://www.ve3ba.com)
威霸博客：[https://blog.ve3ba.com](https://blog.ve3ba.com)
高尔建站：[https://www.letsgol.com.cn](https://www.letsgol.com.cn)
SEO数据库：[https://www.snipedia.net](https://www.snipedia.net)
齐鲁发发网：[https://www.s7688.cn](https://www.s7688.cn)
SEO资源网：[https://www.seolreim.cn](https://www.seolreim.cn)
品牌故事网：[https://www.avi-8.com.cn](https://www.avi-8.com.cn)
易思排名：[https://www.esisoft.cn](https://www.esisoft.cn)
易思SEO：[https://seo.esisoft.cn](https://seo.esisoft.cn)
能上排名网：[https://www.nsip.com.cn](https://www.nsip.com.cn)
SEO商务领航：[https://www.sx-biz.cn](https://www.sx-biz.cn)
雪力排名网：[https://www.shellin.com.cn](https://www.shellin.com.cn)
六二美图网：[https://www.62652.cn](https://www.62652.cn)
DVB信息网：[https://www.dvb-t2.cn](https://www.dvb-t2.cn)
动力SEO：[https://www.c-power.com.cn](https://www.c-power.com.cn)
2046优选网：[https://www.2046y.com](https://www.2046y.com)
SEO优选：[https://seo.2046y.com](https://seo.2046y.com)
SEO网站：[https://www.nms2001.com](https://www.nms2001.com)
SEO分析：[https://www.free-spy.com](https://www.free-spy.com)
安安SEO：[https://www.aa-center.net](https://www.aa-center.net)
波利建站：[https://www.poletik.net](https://www.poletik.net)
四四网络：[https://www.4429.com.cn](https://www.4429.com.cn)
赢凯图库：[https://www.inkay.net](https://www.inkay.net)
经纬网络：[https://www.jw2e.com](https://www.jw2e.com)
经纬SEO：[https://seo.jw2e.com](https://seo.jw2e.com)
号子传媒：[https://www.hzcm.cc](https://www.hzcm.cc)
飞马SEO：[https://www.fima.cc](https://www.fima.cc)
儒风SEO：[https://www.r-f.cc](https://www.r-f.cc)
维斯网络：[https://www.wives.cc](https://www.wives.cc)
高端网站建设：[https://www.urkeji.com](https://www.urkeji.com)
网站设计制作：[https://www.07yue.com](https://www.07yue.com)
企业网站SEO：[https://www.jsfengchao.com](https://www.jsfengchao.com)
网站SEO优化：[https://dw.urkeji.com](https://dw.urkeji.com)
SEO网站优化：[https://seo.jsfengchao.com](https://seo.jsfengchao.com)
特价SEO：[https://www.tjwyj.com](https://www.tjwyj.com)
无限SEO：[https://wx.urkeji.com](https://wx.urkeji.com)
凤巢动画：[https://dh.jsfengchao.com](https://dh.jsfengchao.com)
靠谱SEO：[https://kp.urkeji.com](https://kp.urkeji.com)
