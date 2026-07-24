document.addEventListener('DOMContentLoaded', function() {
    // 移动端菜单切换
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.style.display = mobileMenu.style.display === 'block' ? 'none' : 'block';
        });
    }
    
    // 移动端菜单链接点击后关闭菜单
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            mobileMenu.style.display = 'none';
        });
    });
    
    // 导航栏滚动效果
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.header');
        if (window.scrollY > 50) {
            header.style.backgroundColor = 'rgba(10, 14, 23, 0.98)';
            header.style.backdropFilter = 'blur(10px)';
        } else {
            header.style.backgroundColor = 'rgba(10, 14, 23, 0.95)';
        }
    });
    
    // 当前活动导航链接高亮
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link, .mobile-nav-link');
    
    function highlightNavLink() {
        let scrollY = window.pageYOffset;
        
        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - 100;
            const sectionId = section.getAttribute('id');
            
            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${sectionId}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }
    
    window.addEventListener('scroll', highlightNavLink);
    
    // 页面加载动画
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.advantage-card, .cta-option, .timeline-item, .process-step');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.2;
            
            if (elementPosition < screenPosition) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    };
    
    // 设置初始状态
    const animatedElements = document.querySelectorAll('.advantage-card, .cta-option, .timeline-item, .process-step');
    animatedElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });
    
    // 监听滚动事件
    window.addEventListener('scroll', animateOnScroll);
    
    // 初始执行一次
    animateOnScroll();
    
    // 搜索视觉特效
    const searchVisual = function() {
        const searchResults = document.querySelectorAll('.result-item');
        
        if (searchResults.length > 0) {
            // 为第一个结果添加特殊效果
            searchResults[0].classList.add('first');
            
            // 随机高亮效果
            setInterval(() => {
                const randomIndex = Math.floor(Math.random() * (searchResults.length - 1)) + 1;
                searchResults[randomIndex].style.backgroundColor = 'rgba(255, 255, 255, 0.1)';
                
                setTimeout(() => {
                    searchResults[randomIndex].style.backgroundColor = '';
                }, 1000);
            }, 3000);
        }
    };
    
    // 初始化搜索特效
    searchVisual();
    
    // 浮动按钮悬停效果
    const floatButtons = document.querySelectorAll('.float-btn');
    floatButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.1)';
        });
        
        button.addEventListener('mouseleave', function() {
            if (!this.classList.contains('hover')) {
                this.style.transform = 'translateY(0) scale(1)';
            }
        });
    });
    
    // 控制台欢迎信息
    console.log('%c🔍 专业的SEO优化服务 🔍', 'color: #4361ee; font-size: 18px; font-weight: bold;');
    console.log('%c让你的品牌脱颖而出，轻松锁定目标客户！', 'color: #f72585;');
    console.log('%c联系电话 13931995587 联系QQ 853616368', 'color: #4cc9f0;');
});