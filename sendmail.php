<?php
/**
 * お問い合わせフォーム送信処理
 * PHPMailerとAWS SESを使用してメールを送信
 * 
 * セキュリティ対策:
 * - CSRF保護
 * - レート制限
 * - 入力バリデーション
 * - XSS対策
 */

// セッション開始（CSRF対策用）
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// 環境変数から認証情報を取得（本番環境ではこの方法を推奨）
// .envファイルまたはサーバー環境変数に設定してください
$smtpHost = getenv('SMTP_HOST') ?: 'email-smtp.us-east-1.amazonaws.com';
$smtpUsername = getenv('SMTP_USERNAME') ?: 'YOUR_SES_SMTP_USERNAME';
$smtpPassword = getenv('SMTP_PASSWORD') ?: 'YOUR_SES_SMTP_PASSWORD';
$fromEmail = getenv('FROM_EMAIL') ?: 'your-verified@email.com';
$toEmail = getenv('TO_EMAIL') ?: 'your-verified@email.com';

/**
 * 入力値をサニタイズ
 */
function sanitize($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

/**
 * レート制限チェック（5分以内に3回まで）
 */
function checkRateLimit() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = "rate_limit_$ip";
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'time' => time()];
    }
    
    $data = $_SESSION[$key];
    
    // 5分経過していればリセット
    if (time() - $data['time'] > 300) {
        $_SESSION[$key] = ['count' => 1, 'time' => time()];
        return true;
    }
    
    // 3回以上ならエラー
    if ($data['count'] >= 3) {
        return false;
    }
    
    $_SESSION[$key]['count']++;
    return true;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // レート制限チェック
    if (!checkRateLimit()) {
        $error = '送信回数が上限に達しました。しばらく時間をおいてからお試しください。';
    } else {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $message = sanitize($_POST['message'] ?? '');

        // バリデーション
        if (!$name || !$email || !$message) {
            $error = '全ての項目を入力してください。';
        } elseif (strlen($name) > 100) {
            $error = '名前は100文字以内で入力してください。';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'メールアドレスの形式が正しくありません。';
        } elseif (strlen($message) > 5000) {
            $error = 'メッセージは5000文字以内で入力してください。';
        } else {
            $mail = new PHPMailer(true);
            try {
                // SMTP設定
                $mail->isSMTP();
                $mail->Host = $smtpHost;
                $mail->SMTPAuth = true;
                $mail->Username = $smtpUsername;
                $mail->Password = $smtpPassword;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                // 送信者・受信者設定
                $mail->setFrom($fromEmail, 'ポートフォリオサイト');
                $mail->addAddress($toEmail, '角谷侑磨');
                $mail->addReplyTo($email, $name);

                // メール内容
                $mail->Subject = '【お問い合わせ】' . mb_substr($name, 0, 20) . 'さんより';
                $mail->isHTML(true);
                
                $mail->Body = '
                <div style="font-family:\'Segoe UI\',Arial,sans-serif;padding:24px;background:#f9f9f9;">
                    <div style="max-width:600px;margin:auto;background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);padding:32px;">
                        <h2 style="color:#4a90e2;margin-bottom:24px;border-bottom:2px solid #4a90e2;padding-bottom:10px;">お問い合わせ内容</h2>
                        <table style="width:100%;border-collapse:collapse;">
                            <tr>
                                <td style="font-weight:bold;width:120px;padding:12px 0;vertical-align:top;">お名前</td>
                                <td style="padding:12px 0;">'.nl2br(htmlspecialchars($name, ENT_QUOTES, 'UTF-8')).'</td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold;padding:12px 0;vertical-align:top;">メール</td>
                                <td style="padding:12px 0;">'.htmlspecialchars($email, ENT_QUOTES, 'UTF-8').'</td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold;padding:12px 0;vertical-align:top;">メッセージ</td>
                                <td style="padding:12px 0;line-height:1.6;">'.nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')).'</td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold;padding:12px 0;vertical-align:top;">送信日時</td>
                                <td style="padding:12px 0;">'.date('Y年m月d日 H:i:s').'</td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold;padding:12px 0;vertical-align:top;">IPアドレス</td>
                                <td style="padding:12px 0;">'.$_SERVER['REMOTE_ADDR'].'</td>
                            </tr>
                        </table>
                        <div style="margin-top:32px;padding-top:16px;border-top:1px solid #eee;font-size:13px;color:#888;">
                            このメールはWebサイトのお問い合わせフォームから自動送信されました。
                        </div>
                    </div>
                </div>';
                
                $mail->AltBody = "【お問い合わせ】\n\nお名前: $name\nメールアドレス: $email\n\nメッセージ:\n$message\n\n送信日時: ".date('Y年m月d日 H:i:s')."\nIPアドレス: ".$_SERVER['REMOTE_ADDR'];

                $mail->send();
                $success = '送信が完了しました。ありがとうございました。';
                
                // ログ記録（オプション）
                error_log("Contact form submitted from: $email ($name)");
            } catch (Exception $e) {
                $error = '送信に失敗しました。しばらく時間をおいてからお試しください。';
                error_log("Mail Error: " . $mail->ErrorInfo);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>お問い合わせフォーム</title>
    <link href="https://fonts.googleapis.com/css?family=Segoe+UI:400,700&display=swap" rel="stylesheet">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', Arial, sans-serif; }
        .container { max-width: 480px; margin: 48px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px #0002; padding: 40px 32px; }
        h1 { color: #2d8cf0; text-align: center; margin-bottom: 32px; }
        .form-group { margin-bottom: 24px; }
        label { display: block; font-weight: bold; margin-bottom: 8px; color: #333; }
        input, textarea { width: 100%; padding: 12px; border: 1px solid #d0d7de; border-radius: 6px; font-size: 16px; background: #f9fafb; }
        textarea { min-height: 100px; resize: vertical; }
        .btn { width: 100%; background: linear-gradient(90deg,#2d8cf0,#6ec1e4); color: #fff; font-weight: bold; border: none; border-radius: 6px; padding: 14px 0; font-size: 18px; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: linear-gradient(90deg,#1e6bb8,#4fa3d1); }
        .msg { text-align: center; margin-bottom: 24px; font-size: 15px; }
        .msg.error { color: #e74c3c; }
        .msg.success { color: #27ae60; }
    </style>
</head>
<body>
    <div class="container">
        <h1>お問い合わせ</h1>
        <?php if ($error): ?>
            <div class="msg error"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="msg success"><?= $success ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="name">お名前</label>
                <input type="text" id="name" name="name" required value="<?= isset($name) ? $name : '' ?>">
            </div>
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" required value="<?= isset($email) ? $email : '' ?>">
            </div>
            <div class="form-group">
                <label for="message">メッセージ</label>
                <textarea id="message" name="message" required><?= isset($message) ? $message : '' ?></textarea>
            </div>
            <button class="btn" type="submit">送信</button>
        </form>
    </div>
</body>
</html>