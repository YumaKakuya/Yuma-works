# デプロイガイド

このドキュメントでは、ポートフォリオサイトをAWSや他のホスティングサービスにデプロイする手順を説明します。

## 📋 目次

1. [AWS EC2へのデプロイ](#aws-ec2へのデプロイ)
2. [AWS S3 + CloudFrontへのデプロイ](#aws-s3--cloudfrontへのデプロイ)
3. [その他のホスティングサービス](#その他のホスティングサービス)
4. [環境変数の設定](#環境変数の設定)
5. [SSL証明書の設定](#ssl証明書の設定)

---

## AWS EC2へのデプロイ

### 前提条件
- AWSアカウント
- SSH鍵ペア
- 基本的なLinuxコマンドの知識

### 手順

#### 1. EC2インスタンスの作成

```bash
# Amazon Linux 2を選択
# インスタンスタイプ: t2.micro（無料枠対象）
# セキュリティグループ: HTTP(80), HTTPS(443), SSH(22)を許可
```

#### 2. EC2インスタンスに接続

```bash
ssh -i "your-key.pem" ec2-user@your-ec2-public-ip
```

#### 3. 必要なソフトウェアのインストール

```bash
# システムアップデート
sudo yum update -y

# Apache、PHP、Gitのインストール
sudo yum install httpd php php-mbstring php-xml git -y

# Composerのインストール
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

#### 4. Apacheの設定

```bash
# Apacheを起動
sudo systemctl start httpd
sudo systemctl enable httpd

# ドキュメントルートへ移動
cd /var/www/html
```

#### 5. プロジェクトのクローンとセットアップ

```bash
# リポジトリのクローン
sudo git clone https://github.com/YumaKakuya/Portfolio.git
sudo mv Portfolio/* .
sudo rm -rf Portfolio

# 権限設定
sudo chown -R apache:apache /var/www/html
sudo chmod -R 755 /var/www/html

# Composer依存関係のインストール
composer install --no-dev --optimize-autoloader
```

#### 6. 環境変数の設定

```bash
# .envファイルを作成
sudo nano /var/www/html/.env

# 以下の内容を入力
SMTP_HOST=email-smtp.us-east-1.amazonaws.com
SMTP_USERNAME=your_smtp_username
SMTP_PASSWORD=your_smtp_password
FROM_EMAIL=your-verified@email.com
TO_EMAIL=your-email@example.com
```

#### 7. Apacheの再起動

```bash
sudo systemctl restart httpd
```

#### 8. 動作確認

ブラウザで `http://your-ec2-public-ip` にアクセスして確認

---

## AWS S3 + CloudFrontへのデプロイ

静的ホスティング（HTMLファイルのみ）の場合

### 手順

#### 1. S3バケットの作成

```bash
# AWS CLIを使用
aws s3 mb s3://your-portfolio-bucket --region us-east-1
```

#### 2. 静的ウェブサイトホスティングの有効化

```bash
aws s3 website s3://your-portfolio-bucket/ \
  --index-document Portfolio1.html \
  --error-document error.html
```

#### 3. バケットポリシーの設定

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "PublicReadGetObject",
      "Effect": "Allow",
      "Principal": "*",
      "Action": "s3:GetObject",
      "Resource": "arn:aws:s3:::your-portfolio-bucket/*"
    }
  ]
}
```

#### 4. ファイルのアップロード

```bash
# ローカルファイルをS3にアップロード
aws s3 sync . s3://your-portfolio-bucket/ \
  --exclude ".git/*" \
  --exclude "*.md" \
  --exclude ".env*" \
  --exclude "vendor/*"
```

#### 5. CloudFrontディストリビューションの作成

- オリジン: S3バケット
- SSL証明書: AWS Certificate Manager（ACM）で取得
- カスタムドメインの設定

---

## その他のホスティングサービス

### Heroku

```bash
# Herokuアプリの作成
heroku create your-portfolio-app

# 環境変数の設定
heroku config:set SMTP_HOST=email-smtp.us-east-1.amazonaws.com
heroku config:set SMTP_USERNAME=your_username
heroku config:set SMTP_PASSWORD=your_password

# デプロイ
git push heroku main
```

### Netlify（静的サイトのみ）

1. GitHubリポジトリと連携
2. ビルド設定は不要（静的HTML）
3. 環境変数をNetlify管理画面で設定
4. 自動デプロイが有効化される

### Vercel（静的サイトのみ）

```bash
# Vercel CLIのインストール
npm i -g vercel

# デプロイ
vercel --prod
```

---

## 環境変数の設定

### 開発環境

`.env`ファイルを作成:

```bash
cp .env.example .env
nano .env
```

### 本番環境

**セキュリティ上の注意:**
- `.env`ファイルをGitにコミットしない
- サーバー環境変数として設定する
- AWSの場合は AWS Secrets Managerの使用を推奨

#### EC2での設定方法

```bash
# /etc/environment に追加
sudo nano /etc/environment

export SMTP_HOST="email-smtp.us-east-1.amazonaws.com"
export SMTP_USERNAME="your_username"
export SMTP_PASSWORD="your_password"
```

#### Apacheでの設定方法

```bash
sudo nano /etc/httpd/conf.d/env.conf

# 以下を追加
SetEnv SMTP_HOST "email-smtp.us-east-1.amazonaws.com"
SetEnv SMTP_USERNAME "your_username"
SetEnv SMTP_PASSWORD "your_password"
```

---

## SSL証明書の設定

### Let's Encryptを使用（無料）

```bash
# Certbotのインストール
sudo yum install certbot python3-certbot-apache -y

# SSL証明書の取得
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# 自動更新の設定
sudo certbot renew --dry-run
```

### AWS Certificate Manager（ACM）

1. ACMコンソールで証明書をリクエスト
2. ドメインの所有権を検証
3. CloudFrontまたはALBに証明書を適用

---

## デプロイ後のチェックリスト

- [ ] すべてのページが正常に表示される
- [ ] お問い合わせフォームが動作する
- [ ] 画像が正しく読み込まれる
- [ ] SSL証明書が有効
- [ ] モバイルでの表示確認
- [ ] Google AnalyticsやSearch Consoleの設定
- [ ] robots.txtとsitemap.xmlが正しく配置されている
- [ ] パフォーマンステスト（Lighthouse等）
- [ ] セキュリティスキャン

---

## トラブルシューティング

### メールが送信されない

```bash
# PHPのエラーログを確認
sudo tail -f /var/log/httpd/error_log

# SESの送信制限を確認（サンドボックスモードか？）
# 本番環境では送信制限解除リクエストが必要
```

### 画像が表示されない

```bash
# ファイルの権限を確認
sudo chmod -R 755 /var/www/html/images
sudo chown -R apache:apache /var/www/html/images
```

### 403 Forbidden エラー

```bash
# SELinuxの設定を確認（Amazon Linux）
sudo setsebool -P httpd_can_network_connect 1
sudo chcon -R -t httpd_sys_content_t /var/www/html
```

---

## 定期メンテナンス

### セキュリティアップデート

```bash
# 月1回実行
sudo yum update -y
composer update
```

### バックアップ

```bash
# ファイルのバックアップ
sudo tar -czf backup-$(date +%Y%m%d).tar.gz /var/www/html

# S3へのバックアップ
aws s3 cp backup-$(date +%Y%m%d).tar.gz s3://your-backup-bucket/
```

---

## サポートとリソース

- [AWS EC2ドキュメント](https://docs.aws.amazon.com/ec2/)
- [AWS SESドキュメント](https://docs.aws.amazon.com/ses/)
- [PHPMailer公式サイト](https://github.com/PHPMailer/PHPMailer)
- [Let's Encrypt](https://letsencrypt.org/)

---

**更新日:** 2025年2月5日
