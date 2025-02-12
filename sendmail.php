<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Composerのオートロードを読み込む

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // フォームデータを取得
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // PHPMailerの設定
    $mail = new PHPMailer(true);

    try {
        // SMTP設定
        $mail->isSMTP();
        $mail->Host = '<email-smtp class="us-east-1"></email-smtp>.amazonaws.com'; // SESのSMTPエンドポイント
        $mail->SMTPAuth = true;
        $mail->Username = 'AKIA4IM3HN7KJB5FZJCC'; // SESのSMTPユーザー名
        $mail->Password = 'BIs5ytaY1djtk5VyIajqsguQRFTK0adh9vvrdsJZlOvV'; // SESのSMTPパスワード
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS暗号化
        $mail->Port = 587; // SESのSMTPポート

        // メールの設定
        $mail->setFrom('yumakakuya@gmail.com', $name); // 検証済みメールアドレス
        $mail->addAddress('yumakakuya@gmail.com'); // 送信先
        $mail->Subject = 'お問い合わせがありました'; // 件名
        $mail->Body = "お名前: $name\nメールアドレス: $email\nメッセージ:\n$message"; // 本文

        // メール送信
        $mail->send();
        echo "<p>お問い合わせありがとうございます。メールを送信しました。</p>";
    } catch (Exception $e) {
        echo "<p>メールの送信に失敗しました。エラー: {$mail->ErrorInfo}</p>";
    }
} else {
    echo "<p>無効なリクエストです。</p>";
}
?>