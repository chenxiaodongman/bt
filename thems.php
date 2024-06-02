<?php
// 加载WordPress环境
require_once( dirname( __FILE__ ) . '/wp-load.php' );

// 确保代码在WordPress环境中运行
if ( ! defined( 'ABSPATH' ) ) {
    exit; // 直接访问文件时退出
}

// 获取所有可用的主题
$themes = wp_get_themes();
$theme_slugs = array_keys($themes);

// 获取所有子站点
$sites = get_sites();

// 检查是否获取到站点
if ( ! empty( $sites ) ) {
    echo "开始更换所有子站点的主题:<br>";
    $theme_count = count($theme_slugs);
    $site_count = count($sites);
    
    // 如果主题数量少于子站点数量，则需要随机分配
    if ($theme_count < $site_count) {
        echo "主题数量少于子站点数量，将随机分配主题<br>";
    }
    
    foreach ( $sites as $index => $site ) {
        $site_id = $site->blog_id;
        echo "正在更换站点ID: $site_id 的主题<br>";
        
        // 选择主题
        if ($index < $theme_count) {
            $theme_slug = $theme_slugs[$index];
        } else {
            $theme_slug = $theme_slugs[array_rand($theme_slugs)];
        }
        
        // 切换到子站点
        switch_to_blog($site_id);
        
        // 更换主题
        switch_theme($theme_slug);
        
        // 恢复到当前站点
        restore_current_blog();
        
        echo "站点ID: $site_id 的主题已更换为 $theme_slug<br>";
    }
    echo "所有子站点的主题已更换完毕";
} else {
    echo "没有找到任何子站点";
}
?>