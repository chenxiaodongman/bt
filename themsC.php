<?php
// 加载WordPress环境
require_once( dirname( __FILE__ ) . '/wp-load.php' );

// 确保代码在WordPress环境中运行
if ( ! defined( 'ABSPATH' ) ) {
    exit; // 直接访问文件时退出
}

// 检查是否提供了模板名称
if ( ! isset( $_POST['theme_slug'] ) || empty( $_POST['theme_slug'] ) ) {
    wp_die( __( '请提供模板名称。' ) );
}

$theme_slug = sanitize_text_field( $_POST['theme_slug'] );

// 获取所有可用的主题
$themes = wp_get_themes();

// 检查提供的模板名称是否存在
if ( ! array_key_exists( $theme_slug, $themes ) ) {
    wp_die( __( '提供的模板名称不存在。' ) );
}

// 获取所有子站点
$sites = get_sites();

// 检查是否获取到站点
if ( ! empty( $sites ) ) {
    echo "开始更换所有子站点的主题:<br>";
    
    foreach ( $sites as $site ) {
        $site_id = $site->blog_id;
        echo "正在更换站点ID: $site_id 的主题<br>";
        
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