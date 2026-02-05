// スムーズスクロール
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    });
});

// フォーム送信処理をAPI Gateway対応に変更
const contactForm = document.getElementById('contactForm');
const formMessage = document.getElementById('form-message');
const apiEndpoint = 'https://n4jy1xi2k9.execute-api.us-east-1.amazonaws.com/v1'; // API GatewayのエンドポイントURL

if (contactForm && formMessage) {
    contactForm.addEventListener('submit', async function (e) {
        e.preventDefault(); // デフォルトのフォーム送信をキャンセル

        const formData = new FormData(this);
        const data = {
            name: formData.get('name')?.trim(),
            email: formData.get('email')?.trim(),
            message: formData.get('message')?.trim()
        };

        // バリデーション
        if (!data.name || !data.email || !data.message) {
            showMessage('全ての項目を入力してください。', 'error');
            return;
        }

        // メールアドレスの簡易バリデーション
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(data.email)) {
            showMessage('有効なメールアドレスを入力してください。', 'error');
            return;
        }

        // 送信ボタンを無効化し、ローディング表示
        const submitButton = this.querySelector('.submit-btn');
        const originalButtonText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = '送信中...';
        showMessage('送信中...', 'loading');

        try {
            const response = await fetch(apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
                // タイムアウト設定（10秒）
                signal: AbortSignal.timeout(10000)
            });

            if (!response.ok) {
                // レスポンスがエラーの場合、エラー情報を取得
                let errorMessage = `サーバーエラーが発生しました（ステータス: ${response.status}）`;
                try {
                    const errData = await response.json();
                    errorMessage = errData.message || errorMessage;
                } catch (parseError) {
                    console.warn('エラーレスポンスのJSON解析に失敗:', parseError);
                }
                throw new Error(errorMessage);
            }

            const result = await response.json();
            showMessage('お問い合わせありがとうございます。メールを送信しました。', 'success');
            this.reset(); // フォームをリセット
            
            // Google Analytics等のトラッキング（必要に応じて）
            if (typeof gtag === 'function') {
                gtag('event', 'form_submission', {
                    event_category: 'contact',
                    event_label: 'success'
                });
            }
        } catch (error) {
            console.error('送信エラー:', error);
            
            let errorMessage = 'メール送信に失敗しました。';
            if (error.name === 'AbortError') {
                errorMessage = 'タイムアウトしました。時間をおいて再度お試しください。';
            } else if (error.message) {
                errorMessage += ` ${error.message}`;
            }
            
            showMessage(errorMessage, 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        }
    });

    // メッセージ表示用のヘルパー関数
    function showMessage(message, type) {
        formMessage.textContent = message;
        formMessage.className = `form-message show ${type}`;
        
        // 成功時は5秒後に自動的に非表示
        if (type === 'success') {
            setTimeout(() => {
                formMessage.classList.remove('show');
            }, 5000);
        }
    }
}

// ヘッダーの背景変更（スクロール時）
window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (window.scrollY > 50) {
        header.classList.add('header-scroll');
    } else {
        header.classList.remove('header-scroll');
    }
});