<?php
// ...existing code before <!DOCTYPE html> remains unchanged...
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="角谷侑磨（Yuma Kakuya）のポートフォリオサイト。飲食業界から転身し、キャルITカレッジでJavaを学習中。HTML、CSS、JavaScript、PHP、AWS、Springフレームワークを用いたWeb開発スキルを紹介します。">
    <meta name="keywords" content="角谷侑磨, Yuma Kakuya, ポートフォリオ, Java, PHP, AWS, Spring, Web開発, プログラマー">
    <meta name="author" content="Yuma Kakuya">
    
    <!-- OGP (Open Graph Protocol) -->
    <meta property="og:title" content="Yuma Kakuya - ポートフォリオ">
    <meta property="og:description" content="飲食業界から転身し、Web開発を学習中のエンジニア志望者のポートフォリオサイトです。">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://yourportfolio.com">
    <meta property="og:image" content="https://yourportfolio.com/images/プロフィール画像海ウクレレ.JPG">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Yuma Kakuya - ポートフォリオ">
    <meta name="twitter:description" content="飲食業界から転身し、Web開発を学習中のエンジニア志望者のポートフォリオサイトです。">
    <meta name="twitter:image" content="https://yourportfolio.com/images/プロフィール画像海ウクレレ.JPG">
    
    <title>Yuma Kakuya - ポートフォリオ | Web開発エンジニア志望</title>
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/header.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php
    /* // API Gatewayを使うため、PHPでのメッセージ表示は不要になる可能性が高い
    if(isset($_GET['success']) && $_GET['success'] == 1){
        echo '<p class="success">お問い合わせありがとうございます。メールを送信しました。</p>';
    }
    if(isset($_GET['error'])){
        echo '<p class="error">メール送信に失敗しました。エラー: ' . htmlspecialchars($_GET['error']) . '</p>';
    }
    */
    ?>
    <!-- ヘッダー -->
    <header class="header" role="banner">
        <h1 class="header-title">Yuma Kakuya</h1>
        <nav class="nav" role="navigation" aria-label="メインナビゲーション">
            <ul class="nav-list">
                <li><a href="#about" aria-label="自己紹介セクションへ移動">自己紹介</a></li>
                <li><a href="#projects" aria-label="日々の取り組みセクションへ移動">日々の取り組み</a></li>
                <li><a href="#contact" aria-label="お問い合わせフォームへ移動">お問い合わせ</a></li>
            </ul>
        </nav>
    </header>

    <!-- メインコンテンツ -->
    <main class="main-container" role="main">
        <!-- 自己紹介セクション -->
        <section id="about" class="section about" aria-labelledby="about-title">
            <h2 id="about-title" class="section-title">自己紹介</h2>
            <div class="about-content">
                <img src="./images/プロフィール画像海ウクレレ.JPG" 
                     alt="海とウクレレと共に笑顔の角谷侑磨のプロフィール写真" 
                     class="profile-image"
                     loading="lazy"
                     width="200"
                     height="200">
                <div class="about-text">
                    <p><strong>角谷 侑磨（Yuma Kakuya）</strong></p>
                    <p>飲食業界で25年間の経験を積んだ後、IT業界への転身を決意。現在はキャルITカレッジ大阪校で6ヶ月間のJavaプログラミングコースを受講中です。</p>
                    <p>Java、PHP、AWS、クラウド技術に興味を持ち、日々学習を続けています。「なぜ？」を追求する姿勢を大切に、実践的なスキルの習得を目指しています。</p>
                </div>
            </div>
        </section>

        <!-- プロジェクトセクション -->
        <section id="projects" class="section projects" aria-labelledby="projects-title">
            <h2 id="projects-title" class="section-title">日々の取組について</h2>
            <div class="project-grid">
                <!-- プロジェクトカード1 -->
                <article class="project-card">
                    <img src="./images/UPDATE.jpg" 
                         alt="ウェブサイト制作プロジェクトのイメージ画像" 
                         class="project-image"
                         loading="lazy"
                         width="300"
                         height="200">
                    <h3 class="project-title">ウェブサイト制作</h3>
                    <p class="project-description">現在ご覧のウェブサイトをHTML、CSS、JavaScript、PHPで作成しました。AWS環境での構築も経験。</p>
                    <a href="project1-details.html" class="project-link" aria-label="ウェブサイト制作の詳細を見る">詳細を見る →</a>
                </article>

                <!-- プロジェクトカード2 -->
                <article class="project-card">
                    <img src="./images/プログラム画面１.jpg" 
                         alt="コーディングチャレンジのプログラム画面" 
                         class="project-image"
                         loading="lazy"
                         width="300"
                         height="200">
                    <h3 class="project-title">コーディングチャレンジ</h3>
                    <p class="project-description">毎日コーディング課題に取り組み、アルゴリズムとデータ構造の理解を深めています。GitHubで進捗を公開中。</p>
                    <a href="project2-details.html" class="project-link" aria-label="コーディングチャレンジの詳細を見る">詳細を見る →</a>
                </article>

                <!-- プロジェクトカード3 -->
                <article class="project-card">
                    <img src="./images/Lifelong Learning of programming in an illustrative and non-personal visual style with text.png" 
                         alt="継続的な学習を表すイラスト画像" 
                         class="project-image"
                         loading="lazy"
                         width="300"
                         height="200">
                    <h3 class="project-title">Webアプリ開発</h3>
                    <p class="project-description">Springフレームワークを学習中。Java、MySQL、MVCアーキテクチャを活用したWebアプリケーション開発に挑戦しています。</p>
                    <a href="project3-details.html" class="project-link" aria-label="Webアプリ開発の詳細を見る">詳細を見る →</a>
                </article>
            </div>
        </section>

        <!-- 連絡先セクション -->
        <section id="contact" class="section contact" aria-labelledby="contact-title">
            <h2 id="contact-title" class="section-title">お問い合わせフォーム</h2>
            <p class="contact-intro">ご質問、ご相談、お仕事のご依頼など、お気軽にお問い合わせください。</p>
            <form class="contact-form" id="contactForm" method="POST" aria-label="お問い合わせフォーム">
                <div class="form-group">
                    <label for="name">お名前 <span class="required">*</span></label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required 
                           aria-required="true"
                           placeholder="山田 太郎"
                           autocomplete="name">
                </div>

                <div class="form-group">
                    <label for="email">メールアドレス <span class="required">*</span></label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           aria-required="true"
                           placeholder="example@email.com"
                           autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="message">メッセージ <span class="required">*</span></label>
                    <textarea id="message" 
                              name="message" 
                              rows="5" 
                              required 
                              aria-required="true"
                              placeholder="お問い合わせ内容をご記入ください"></textarea>
                </div>

                <button type="submit" class="submit-btn" aria-label="お問い合わせを送信">
                    送信する
                </button>
            </form>
            <!-- 送信結果表示用の要素 -->
            <div id="form-message" class="form-message" role="status" aria-live="polite"></div>
        </section>
    </main>

    <!-- フッター -->
    <footer class="footer" role="contentinfo">
        <div class="footer-content">
            <p>&copy; 2025 Yuma Kakuya. All rights reserved.</p>
            <div class="social-icons">
                <a href="https://github.com/YumaKakuya" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   aria-label="GitHubプロフィールを開く（新しいタブ）">
                    <img src="./images/pngimg.com - github_PNG15.png" alt="GitHub" width="30" height="30">
                </a>
            </div>
            <p class="footer-note">Made with ❤️ using HTML, CSS, JavaScript, PHP & AWS</p>
        </div>
    </footer>

    <!-- JavaScriptファイルの読み込み -->
    <!-- script.js のパスを確認してください -->
    <script src="js/portfolio.Javascript.script.js"></script>
</body>
</html>