<?php
/*
Plugin Name: Original theme Images Sizer
Description: オリジナル画像サイズを定義できるプラグインです。
Version: 1.0
Author: Y.U
*/

// プラグインが有効化されたときに実行されるアクションフック
register_activation_hook( __FILE__, 'my_image_sizes_activate' );
function my_image_sizes_activate() {
    add_option( 'my_image_sizes', array() );
}

// プラグインが無効化されたときに実行されるアクションフック
register_deactivation_hook( __FILE__, 'my_image_sizes_deactivate' );
function my_image_sizes_deactivate() {
    delete_option( 'my_image_sizes' );
}

// 管理画面にメニューを追加
add_action( 'admin_menu', 'my_image_sizes_menu' );
function my_image_sizes_menu() {
    add_options_page( 'My Image Sizes', 'My Image Sizes', 'manage_options', 'my-image-sizes', 'my_image_sizes_page' );
}

// 管理画面の設定ページを表示
function my_image_sizes_page() {
    // 権限チェック
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have sufficient permissions to access this page.' );
    }

    // POSTデータの処理
    if ( isset( $_POST['submit'] ) ) {
        // 追加された画像サイズの取得
        $image_sizes = isset( $_POST['image_sizes'] ) ? $_POST['image_sizes'] : array();

        // オプションに保存
        update_option( 'my_image_sizes', $image_sizes );

        // 設定が保存されたことをユーザーに通知
        echo '<div id="message" class="updated notice is-dismissible"><p>Image sizes have been updated.</p></div>';
    }

    // 現在の画像サイズの取得
    $current_sizes = get_option( 'my_image_sizes', array() );
    ?>

    <div class="wrap">
        <h1>Original size</h1>

        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="image_sizes">Image Sizes</label></th>
                    <td>
                        <textarea id="image_sizes" name="image_sizes" rows="5" cols="50"><?php echo esc_textarea( implode( "\n", $current_sizes ) ); ?></textarea>
                        <p class="description">記載サンプル：<code>name,150,150,1（1で切り抜き、0で切り抜かない）</code></p>
                    </td>
                </tr>
            </table>

            <?php submit_button( 'Save Changes' ); ?>
        </form>
    </div>
    <?php
}

// 管理画面のメディア設定に画像サイズを追加
add_action( 'admin_init', 'my_image_sizes_add_custom_sizes' );
function my_image_sizes_add_custom_sizes() {
    // 登録された画像サイズの取得
    $image_sizes = get_option( 'my_image_sizes', array() );

    // 各画像サイズの追加
    foreach ( $image_sizes as $image_size ) {
        $size = explode( ',', $image_size );
        $name = isset( $size[0] ) ? trim( $size[0] ) : '';
        $width = isset( $size[1] ) ? intval( $size[1] ) : 0;
        $height = isset( $size[2] ) ? intval( $size[2] ) : 0;
        $crop = isset( $size[3] ) && $size[3] === '1';

        if ( ! empty( $name ) && $width > 0 && $height > 0 ) {
            add_image_size( $name, $width, $height, $crop );
        }
    }
}
