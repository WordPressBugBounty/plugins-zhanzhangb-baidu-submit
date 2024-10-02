=== 自动提交百度收录插件 ===
Contributors: ywtywt
Donate link: https://www.zhanzhangb.cn/
Tags: Seo,Baidu
Requires at least: 5.2
Tested up to: 6.6.1
Stable tag: 1.8.3
License: GNU General Public License (GPL) version 3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

插件功能：发布/更新文章或页面时，实时推送URL至百度搜索资源平台，支持普通收录与快速收录提交。插件代码轻量化，卸载本插件后不会留下任何冗余数据。
插件作者：<a href="https://www.zhanzhangb.cn/" rel="friend">站长帮</a>

== Description ==
**插件特色：**
1、发布/更新文章或页面时，自动推送至百度收录，可设置是否允许重复提交。
2、同类插件会在数据库中留下一些记录来判断文章是否已提交，本插件采用其它判断逻辑，更绿色、效率更高，故而不会在数据库中留下垃圾数据。
3、实时显示提交成功的数量与当天剩余的提交量。
4、记录最近20条提交日志，便于分析提交成功或失败的返回数据。
更多详情：<a href="https://www.zhanzhangb.cn/zhanzhangb-baidu-submit" rel="friend">WordPress百度搜索推送插件官网</a>

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin
1. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)

== Frequently Asked Questions ==

= 如何获取百度普通收录提交的token？ =

https://ziyuan.baidu.com/linksubmit/index

= 如何获取快速收录提交的token？ =

https://ziyuan.baidu.com/dailysubmit/index

= 如何增加快速收录的提交配额 =

百度快速收录配额调整规则：根据上周总体配额使用情况，智能评估出新的配额。

= 启用插件之前已发布的文章支持主动提交吗？ =

如果文章更新了会自动提交。

== Screenshots ==

1. `/assets/screenshot-1.png` 
2. `/assets/screenshot-2.png` 

== Changelog ==

= 1.8.3 =
* 修复支持 URL

= 1.8.2 =
* 新增支持单个页面（page）发布/更新时自动提交。
* 已修复日志数量限制20条失效的问题，避免日志文件过大占用额外的存储空间。

= 1.8.1 =
* 增加对日志文件的安全检测，防止被恶意篡改。
* 修复了快速收录提交的一处BUG。
* 改进 Win 系统服务器的兼容性。

= 1.8.0 =
* 兼容性测试至 PHP 8.3，后续版本不再测试 PHP 7.3 以下版本的兼容性。
* 优化代码，提升兼容性。
* 修正文档与提示文本。
* 新增 "允许24小时内重复提交" 选项 ，不建议勾选。默认：同一个URL在24小时内仅提交一次。
* 调整提交日志的显示顺序。

= 1.7.0 =
* 兼容WordPress 6.4.x。
* 核心代码重构，执行效率与可靠性更好。
* 增加日志功能，详细记录每次API提交的成功/失败状态，以便于分析提交结果。
* 如果文章上次更新在24小时内，不会重复提交，节约API提交额度【百度的API提交配额已调整，部分网站每天仅10条】。

= 1.6.0 =
* 兼容WordPress 6.2.2。
* 支持旧文章修改/更新后主动推送提交。

= 1.2.0 =
* 因百度业务调整，将百度天级收录更换为快速收录。

= 1.0.0 =
* 首次正式发布
* 经过两周的测试


== Arbitrary section ==

插件将调用百度资源平台的API接口：http://data.zz.baidu.com/

插件支持：<a href="https://www.zhanzhangb.com/" rel="friend">站长帮</a>

本插件由站长帮制作并发行，官网：<a href="https://www.zhanzhangb.cn/zhanzhangb-baidu-submit" rel="friend">www.zhanzhangb.cn</a>

== Author ==

程序开发：WenM、Ting
文档：小芸
WebSite: <a href="https://www.zhanzhangb.cn/" rel="friend">站长帮</a>