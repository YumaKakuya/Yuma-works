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

// フォーム送信時のアラートとリセット
document.querySelector('form.contact-form').addEventListener('submit', function (e) {
    e.preventDefault();
    alert('メッセージが送信されました！');
    this.reset();
});

// ヘッダーの背景変更（スクロール時）
window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (window.scrollY > 50) {
        header.classList.add('header-scroll');
    } else {
        header.classList.remove('header-scroll');
    }
});