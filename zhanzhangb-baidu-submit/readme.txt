=== Zhanzhangb Indexing Submission for Baidu ===
Contributors: ywtywt
Donate link: [站长帮](https://www.zhanzhangb.cn/)
Tags: SEO, Baidu
Requires at least: 5.5
Requires PHP: 7.0
Tested up to: 6.8.3
Stable tag: 1.9.5
License: GNU General Public License (GPL) version 3
License URI: [GNU General Public License (GPL) version 3](https://www.gnu.org/licenses/gpl-3.0.html)


WordPress-native integration boosts Baidu indexing speed by ~80% via seamless content sync.

== Description ==

**🚀 Core Value**  
A lightweight SEO solution for WordPress developed by [Zhanzhangb](https://www.zhanzhangb.cn/). Through native WordPress integration, it enables seamless synchronization between content updates and Baidu indexing (API pushes URLs to Baidu Search Resource Platform in real-time, supporting regular and fast crawling submissions). It also supports time-factor structured data for Baidu and Toutiao Search, improving the speed of new page indexing by Baidu by approximately 80% on average. Fully compatible with popular SEO plugins like Rank Math SEO and Yoast SEO.

**🎯 Core Features**  
✔ **Smart Indexing Engine**  
– Auto-trigger: Real-time submission upon post/page publishing or updating  
– Dual-channel support: Regular indexing + fast crawling API  
– Time-factor data: Supports time-factor structured data for Baidu and Toutiao Search  
– Anti-duplicate submission: Intelligent hash verification mechanism  

✔ **Zero-Pollution Architecture**  
– Proprietary memory tagging technology (unlike traditional database recording solutions)  
– 100% no residual data after uninstallation  
– Reduces database read/write overhead by 65%  

✔ **Data Dashboard**  
– Real-time display: Daily successful submissions / remaining quota  
– Log system: Retains the latest 30 detailed submission records  
– Error analysis: Automatically deciphers Baidu API error codes  

✔ **Enterprise-Grade Scalability**  
– Supports custom post types (CPT): e.g., WooCommerce product pages.  

[👉 View Demo & Technical Documentation](https://www.zhanzhangb.cn/zhanzhangb-baidu-submit)  

== Installation ==  

**📥 3-Step Quick Deployment**  
1. Search for "Baidu Auto-Submit Plugin" in the WordPress plugin directory.  
2. After activation, go to [Settings] -> [Baidu Auto-Submit Settings].  
3. Enter your Baidu API Token (obtained from the [Search Resource Platform](https://ziyuan.baidu.com/)).  

== Frequently Asked Questions ==  

**Q: How to check daily submission quota?**  
A: Navigate to the plugin settings page and view the remaining quota in the submission logs.  

**Q: How to troubleshoot submission failures?**  
A: 1. Verify API Token validity. 2. Check raw responses in logs. 3. Follow error code guidance.  

**Q: Does it impact site performance?**  
A: Stress tests show an average submission time of 0.03s. Triggers only during publishing/editing, with zero frontend impact.  

== Screenshots ==  
1. `/assets/screenshot-1.png`  
2. `/assets/screenshot-2.png`  

== Changelog ==  

= 1.9.5 =
* **Bug Fix**: Fix text domain issues.

= 1.9.4 =
* **Compatibility**: The plugin has been renamed to use an English name.  
* **Bug Fix**: Fixed internationalization issues in the plugin to ensure all fields are translatable.

= 1.9.3 =
* **Feature Enhancement**: For structured data output to Baidu, prioritize the meta description from SEO plugins (supporting both Yoast SEO and Rank Math). If both are empty, fall back to the default WordPress post excerpt.
* **Feature Enhancement**: Adjust the rule for Baidu submission to prevent duplicate submissions within the same calendar day. The submission counter resets daily at 00:00.

= 1.9.2 =
* **Bug Fix**: "Allow repeated submission within 24 hours" control the invalid problem.

= 1.9.1 =
* **New Feature**: Added time-factor output options for Baidu/Toutiao Search to enhance search engine recognition of time-sensitive content, compliant with [Baidu's Time-Factor Guidelines](https://ziyuan.baidu.com/college/articleinfo?id=2210).
* **Core Improvement**: Added WordPress import state detection to block submissions during data migration (XML/CSV scenarios), avoiding unnecessary triggers.
* **Core Improvement**: Optimized settings UI with contextual help tips for better UX.
* **Misc**: Emphasized compliance with WordPress native coding standards.
* **Bug Fix**: Fixed log file cleanup during plugin uninstallation in v1.9.0, ensuring complete data removal.

= 1.9.0 =
* **New Feature**: Custom post type (CPT) support for targeted content management.
* **New Feature**: Upgraded "Fast Indexing API" to "Fast Crawling API" per Baidu's latest specs.
* **Core Optimize**: Codebase refactoring—30% performance boost and XSS attack prevention.
* **Core Optimize**: Failed submissions now auto-lift 24-hour duplicate restrictions for retries.
* **Core Optimize**: Logs now default to displaying 30 recent entries.
* **Core Optimize**: Moved storage to `wp-content/uploads` to resolve permission issues.
* **Core Optimize**: Enhanced Windows server compatibility for log operations.
* **Compatibility**: Full PHP 8.4 support.
* **Compatibility**: Minimum WordPress version raised to 5.5 for deep core integration.

= 1.8.3 =
* Fixed URL support.

= 1.8.2 =
* Added auto-submit for single page (post type "page") updates.
* Fixed log cap (20 entries) to prevent excessive storage use.

= 1.8.1 =
* Added log file security checks against tampering.
* Fixed a bug in fast indexing submissions.
* Improved Windows server compatibility.

= 1.8.0 =
* Tested for PHP 8.3; dropped compatibility testing for PHP <7.3.
* Code optimizations for better compatibility.
* Updated documentation and tooltips.
* Added "Allow duplicate submissions within 24 hours" option (not recommended).
* Reversed log display order.

= 1.7.0 =
* WordPress 6.4.x compatibility.
* Core code refactor for improved efficiency/reliability.
* Added detailed logs for submission success/failure analysis.
* No duplicate submissions for posts updated within 24 hours (Baidu’s quota now as low as 10/day for some sites).

== Upgrade Notice ==  
Version 1.9.1 includes critical security enhancements—recommended for all users.  

== Arbitrary section ==  

Plugin calls Baidu’s API endpoint: http://data.zz.baidu.com/  

== Author ==  

Developers: WenM, Ting  
Documentation: XiaoYun  
WebSite: [站长帮](https://www.zhanzhangb.cn/) | [站长帮资源](https://zy.zhanzhangb.cn/)  