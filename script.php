<?php
// require_once('/www/wwwroot/duling.asia/wp-load.php');
require_once( dirname( __FILE__ ) . '/wp-load.php' );
// 检查用户权限
// if ( ! current_user_can( 'manage_network_themes' ) ) {
//     wp_die( __( 'Sorry, you are not allowed to manage network themes.' ) );
// }

// 主题列表


$theme_directory_path = get_template_directory();

// 找到 'themes' 目录的位置
$themes_position = strpos($theme_directory_path, 'themes');

// 截取 'themes' 之前的路径
$theme_dir = substr($theme_directory_path, 0, $themes_position + strlen('themes'));



// 定义主题目录路径
// $theme_dir = '/www/wwwroot/duling.asia/wp-content/themes';

// 获取主题目录下的所有文件和文件夹
$all_files = scandir($theme_dir);

// 过滤掉非目录文件和隐藏文件
$themes = array_filter($all_files, function($file) use ($theme_dir) {
    return is_dir($theme_dir . '/' . $file) && $file !== '.' && $file !== '..';
});

// 将主题保存到数组中
$themes = array_values($themes);

// 打印所有主题
if (!empty($themes)) {
    echo "找到以下主题:<br>";
    foreach ($themes as $theme) {
        echo $theme . "<br>";
    }
} else {
    echo "没有找到任何主题";
}




// $themes = array('bedrock-wpcom', 'fifty50', 'infield-wpcom', 'seo','astra'); // 替换为你实际的主题名称

// 打印主题列表
echo '<pre>';
echo "Themes List:\n";
print_r($themes);
echo '</pre>';

// 获取当前允许的主题列表
$allowedthemes = get_site_option( 'allowedthemes', array() );

// 添加所有主题到允许列表
foreach ($themes as $theme) {
    $allowedthemes[$theme] = 1;
}

// 更新 allowedthemes 选项
update_site_option( 'allowedthemes', $allowedthemes );

// 更新每个站点的 template 和 stylesheet 选项
global $wpdb;
$blogs = $wpdb->get_results( "SELECT blog_id FROM $wpdb->blogs", ARRAY_A );

// 检查是否获取到所有站点的 blog_id
if ( empty( $blogs ) ) {
    wp_die( __( 'No blogs found in the network.' ) );
}

// 打印调试信息
echo '<pre>';
echo 'Number of blogs found: ' . count($blogs) . "\n";

$theme_count = count($themes);
foreach ( $blogs as $index => $blog ) {
    $blog_id = $blog['blog_id'];
    $theme = $themes[$index % $theme_count]; // 循环分配主题
    
    // 获取当前站点的 template 和 stylesheet 选项
    $current_template = get_blog_option( $blog_id, 'template' );
    $current_stylesheet = get_blog_option( $blog_id, 'stylesheet' );
    
    // 打印当前主题信息
    echo "Current theme for blog ID: $blog_id is template: $current_template, stylesheet: $current_stylesheet\n";
    
    // 检查当前主题是否已经被启用
    if ( in_array($current_template, $themes) || in_array($current_stylesheet, $themes) ) {
        // 禁用当前主题
        echo "Disabling current theme for blog ID: $blog_id\n";
        update_blog_option( $blog_id, 'template', '' );
        update_blog_option( $blog_id, 'stylesheet', '' );
    }
    
    // 打印调试信息
    echo "Updating blog ID: $blog_id with theme: $theme\n";
    
    // 更新每个站点的 template 和 stylesheet 选项
    if ( ! update_blog_option( $blog_id, 'template', $theme ) ) {
        echo "Failed to update template for blog ID: $blog_id\n";
    } else {
        echo "Successfully updated template for blog ID: $blog_id\n";
    }
    
    if ( ! update_blog_option( $blog_id, 'stylesheet', $theme ) ) {
        echo "Failed to update stylesheet for blog ID: $blog_id\n";
    } else {
        echo "Successfully updated stylesheet for blog ID: $blog_id\n";
    }
    
    // 确保主题被启用
    switch_to_blog($blog_id);
    switch_theme($theme);
    restore_current_blog();
}
echo '</pre>';

// 重定向到主题管理页面
// wp_redirect( network_admin_url( 'themes.php?enabled=1' ) );
// exit;
?>