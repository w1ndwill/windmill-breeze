document.addEventListener('DOMContentLoaded', function() {
    console.log('Windmill Breeze Script Loaded');

    // --- Login/Register Logic ---
    const loginOverlay = document.getElementById('login-overlay');
    const guestBtn = document.getElementById('guest-btn');
    const guestBtnReg = document.getElementById('guest-btn-reg');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const toRegisterLink = document.getElementById('to-register');
    const toLoginLink = document.getElementById('to-login');

    // Check Guest Mode (localStorage)
    // Note: We now use Cookies for PHP handling, but we keep this for legacy or double-check
    const userStatus = localStorage.getItem('user_status');
    if (userStatus === 'guest' && loginOverlay && loginOverlay.classList.contains('active')) {
        loginOverlay.classList.remove('active');
    }

    // Toggle Forms
    if(toRegisterLink) {
        toRegisterLink.addEventListener('click', () => {
            loginForm.classList.add('hidden');
            registerForm.classList.remove('hidden');
        });
    }

    if(toLoginLink) {
        toLoginLink.addEventListener('click', () => {
            registerForm.classList.add('hidden');
            loginForm.classList.remove('hidden');
        });
    }

    // Guest Mode Handler
    const handleGuest = () => {
        if(loginOverlay) loginOverlay.classList.remove('active');
        localStorage.setItem('user_status', 'guest');
        // Set cookie for PHP to read (expires in 30 days)
        document.cookie = "windmill_guest_mode=1; max-age=2592000; path=/";
    };
    if(guestBtn) guestBtn.addEventListener('click', handleGuest);
    if(guestBtnReg) guestBtnReg.addEventListener('click', handleGuest);

    // AJAX Login
    if(loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(loginForm);
            formData.append('action', 'windmill_login');
            formData.append('nonce', windmill_vars.nonce);

            fetch(windmill_vars.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    // Login Success
                    localStorage.removeItem('user_status'); // Clear guest status
                    document.cookie = "windmill_guest_mode=; max-age=0; path=/"; // Clear cookie
                    location.reload(); // Reload to let PHP handle the logged-in state
                } else {
                    alert(data.data.message || '登录失败');
                }
            })
            .catch(err => {
                console.error(err);
                alert('请求失败，请稍后重试');
            });
        });
    }

    // AJAX Register
    if(registerForm) {
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(registerForm);
            formData.append('action', 'windmill_register');
            formData.append('nonce', windmill_vars.nonce);

            fetch(windmill_vars.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    // Register Success (Auto Logged In)
                    localStorage.removeItem('user_status');
                    document.cookie = "windmill_guest_mode=; max-age=0; path=/"; // Clear cookie
                    alert('注册成功！正在跳转...');
                    location.reload();
                } else {
                    alert(data.data.message || '注册失败');
                }
            })
            .catch(err => {
                console.error(err);
                alert('请求失败，请稍后重试');
            });
        });
    }

    // --- Profile Management Logic ---
    const profileOverlay = document.getElementById('profile-overlay');
    const navProfileLink = document.getElementById('nav-profile-link');
    const closeProfileBtn = document.getElementById('close-profile-btn');
    const profileForm = document.getElementById('profile-form');
    const avatarInput = document.getElementById('profile-avatar-input');
    const avatarPreview = document.getElementById('profile-avatar-preview');

    // Open Profile Modal
    if(navProfileLink) {
        navProfileLink.addEventListener('click', (e) => {
            e.preventDefault();
            if(profileOverlay) {
                profileOverlay.classList.add('active');
                // Fetch current data
                const formData = new FormData();
                formData.append('action', 'windmill_get_profile');
                formData.append('nonce', windmill_vars.nonce);

                fetch(windmill_vars.ajax_url, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        const user = data.data;
                        document.getElementById('profile-name').value = user.display_name;
                        document.getElementById('profile-email').value = user.email;
                        document.getElementById('profile-url').value = user.url || '';
                        document.getElementById('profile-hobbies').value = user.hobbies || '';
                        document.getElementById('profile-friend-links').value = user.friend_links || '';
                        document.getElementById('profile-desc').value = user.description;
                        if(avatarPreview) avatarPreview.src = user.avatar;
                    }
                });
            }
        });
    }

    // Close Profile Modal
    if(closeProfileBtn) {
        closeProfileBtn.addEventListener('click', () => {
            if(profileOverlay) profileOverlay.classList.remove('active');
        });
    }

    // Avatar Preview
    if(avatarInput && avatarPreview) {
        // Click image to trigger file input
        document.querySelector('.profile-avatar-upload').addEventListener('click', () => {
            avatarInput.click();
        });

        avatarInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Update Profile
    if(profileForm) {
        profileForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(profileForm);
            formData.append('action', 'windmill_update_profile');
            formData.append('nonce', windmill_vars.nonce);

            fetch(windmill_vars.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('资料更新成功！');
                    if(profileOverlay) profileOverlay.classList.remove('active');
                } else {
                    alert(data.data.message || '更新失败');
                }
            })
            .catch(err => {
                console.error(err);
                alert('请求失败，请稍后重试');
            });
        });
    }

    // 鼠标移动交互脚本 (光照 + 3D微视差)
    document.querySelectorAll('.card, .portfolio-item').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            // 设置光照位置
            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        });
    });

    function updateTime() {
        const now = new Date();
        
        // 更新日期
        const dateString = now.toLocaleDateString('zh-CN', { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' });
        const dateEl = document.getElementById('date');
        if(dateEl) dateEl.textContent = dateString;

        // 更新模拟时钟
        const seconds = now.getSeconds();
        const minutes = now.getMinutes();
        const hours = now.getHours();

        const secondDegrees = ((seconds / 60) * 360);
        const minuteDegrees = ((minutes / 60) * 360) + ((seconds/60)*6);
        const hourDegrees = ((hours / 12) * 360) + ((minutes/60)*30);

        const secondHand = document.getElementById('second-hand');
        const minuteHand = document.getElementById('minute-hand');
        const hourHand = document.getElementById('hour-hand');

        if(secondHand) secondHand.style.transform = `translateX(-50%) rotate(${secondDegrees}deg)`;
        if(minuteHand) minuteHand.style.transform = `translateX(-50%) rotate(${minuteDegrees}deg)`;
        if(hourHand) hourHand.style.transform = `translateX(-50%) rotate(${hourDegrees}deg)`;
    }

    setInterval(updateTime, 1000);
    updateTime(); 

    // 主题切换逻辑
    const toggleBtn = document.getElementById('theme-toggle');
    const html = document.documentElement;

    // 检查本地存储或系统偏好
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    const sunIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>';
    const moonIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>';

    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        html.setAttribute('data-theme', 'dark');
        if(toggleBtn) toggleBtn.innerHTML = sunIcon;
    }

    if(toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            if (currentTheme === 'dark') {
                html.removeAttribute('data-theme');
                toggleBtn.innerHTML = moonIcon;
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                toggleBtn.innerHTML = sunIcon;
                localStorage.setItem('theme', 'dark');
            }
        });
    }

    // --- 天气特效逻辑 ---
    const weatherCard = document.getElementById('weather-card');
    const weatherLayer = document.getElementById('weather-layer');
    const windmillIcon = document.getElementById('bg-windmill-blades'); // 获取背景大风车叶片
    const body = document.body;

    // 模拟天气状态：0=默认, 1=晴天, 2=雨天
    let weatherState = 0; 

    function setWindmillSpeed(speedKmh) {
        if (!windmillIcon) return;
        
        // 移除旧的速度类
        windmillIcon.classList.remove('spin-slow', 'spin-medium', 'spin-fast', 'spin-turbo');
        
        // 根据风速(km/h)设置转速
        // < 10: Slow
        // 10 - 30: Medium
        // > 30: Fast
        if (speedKmh < 10) {
            windmillIcon.classList.add('spin-slow');
        } else if (speedKmh < 30) {
            windmillIcon.classList.add('spin-medium');
        } else {
            windmillIcon.classList.add('spin-fast');
        }
    }

    function getCardinalDirection(angle) {
        const directions = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
        return directions[Math.round(angle / 45) % 8];
    }

    function setWindmillDirection(degree) {
        // Update Wind Info Label
        const windDirIcon = document.getElementById('wind-dir-icon');
        const windDirText = document.getElementById('wind-dir-text');
        
        if (windDirIcon) {
            windDirIcon.style.transform = `rotate(${degree}deg)`;
        }
        if (windDirText) {
            windDirText.textContent = getCardinalDirection(degree);
        }
    }

    // 风车点击互动：加速旋转 (Turbo Mode)
    if (windmillIcon) {
        windmillIcon.addEventListener('click', (e) => {
            e.preventDefault(); // 阻止跳转，仅作为互动娱乐
            e.stopPropagation();
            
            // 添加加速类
            windmillIcon.classList.add('spin-turbo');
            
            // 1.5秒后恢复正常速度
            setTimeout(() => {
                windmillIcon.classList.remove('spin-turbo');
            }, 1500);
        });
    }

    // Removed duplicate setWeatherEffect function from here. 
    // The authoritative version is defined at the end of the file to include particle updates.

    function createRain() {
        if(!weatherLayer) return;
        const rainCount = 150; // 增加雨滴数量
        for (let i = 0; i < rainCount; i++) {
            const drop = document.createElement('div');
            drop.classList.add('rain-drop');
            drop.style.left = Math.random() * 100 + 'vw';
            // 随机速度和延迟，让雨看起来更自然
            const duration = Math.random() * 0.5 + 0.5; // 0.5s - 1s
            drop.style.animationDuration = duration + 's';
            drop.style.animationDelay = Math.random() * 2 + 's';
            drop.style.opacity = Math.random() * 0.5 + 0.5; // 随机透明度
            weatherLayer.appendChild(drop);
        }
    }

    // 点击天气卡片切换模拟效果 (已禁用)
    /*
    if(weatherCard) {
        weatherCard.addEventListener('click', () => {
            weatherState = (weatherState + 1) % 3;
            // 模拟风速
            let mockSpeed = 5;
            if (weatherState === 1) mockSpeed = 20; // Cloudy
            if (weatherState === 2) mockSpeed = 50; // Rainy

            if (weatherState === 0) setWeatherEffect('sunny', null, null, 5, 0);
            if (weatherState === 1) setWeatherEffect('cloudy', null, null, 20, 45);
            if (weatherState === 2) setWeatherEffect('rainy', null, null, 50, 90);
        });
    }
    */

    // 初始化尝试获取真实天气
    // 使用 ipwho.is 替代 ipapi.co (更稳定，无CORS限制)
    fetch('https://ipwho.is/')
        .then(res => res.json())
        .then(locationData => {
            if(!locationData.success) {
                throw new Error('IP Geolocation failed');
            }
            const lat = locationData.latitude;
            const lon = locationData.longitude;
            const city = locationData.city;
            
            // 2. 使用获取到的经纬度查询天气
            return fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`)
                .then(res => res.json())
                .then(weatherData => {
                    return { weather: weatherData, city: city };
                });
        })
        .then(data => {
            const current = data.weather.current_weather;
            const code = current.weathercode;
            const temp = current.temperature;
            const windSpeed = current.windspeed;
            const windDir = current.winddirection;
            const city = data.city || '本地';

            // 映射天气代码
            let type = 'sunny';
            if (code <= 1) type = 'sunny';
            else if (code >= 51) type = 'rainy';
            else type = 'cloudy';
            
            // 更新界面
            setWeatherEffect(type, temp, `${city} · ${type === 'sunny' ? '晴' : (type === 'rainy' ? '雨' : '多云')}`, windSpeed, windDir);
        })
        .catch(e => {
            console.log('自动定位天气失败，尝试使用浏览器定位', e);
            // 如果 IP 定位失败，尝试浏览器定位作为备选
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`)
                        .then(res => res.json())
                        .then(data => {
                            const current = data.current_weather;
                            const code = current.weathercode;
                            let type = 'sunny';
                            if (code <= 1) type = 'sunny';
                            else if (code >= 51) type = 'rainy';
                            else type = 'cloudy';
                            setWeatherEffect(type, current.temperature, '本地天气', current.windspeed, current.winddirection);
                        });
                }, (err) => {
                    console.log('浏览器定位也被拒绝', err);
                });
            }
        });

    // --- 简历预览逻辑 ---
    const btnPreview = document.getElementById('btn-resume-preview');
    const resumeOverlay = document.getElementById('resume-overlay');
    const closeResumeBtn = document.getElementById('close-resume-modal');
    const resumeIframe = document.getElementById('resume-iframe');
    const resumePlaceholder = document.getElementById('resume-placeholder');

    if (btnPreview && resumeOverlay) {
        btnPreview.addEventListener('click', () => {
            const url = btnPreview.getAttribute('data-url');
            if (url && url !== '#') {
                resumeIframe.src = url;
                resumeIframe.style.display = 'block';
                resumePlaceholder.style.display = 'none';
            } else {
                resumeIframe.style.display = 'none';
                resumePlaceholder.style.display = 'block';
            }
            resumeOverlay.classList.add('active');
        });

        closeResumeBtn.addEventListener('click', () => {
            resumeOverlay.classList.remove('active');
            resumeIframe.src = ''; // Stop loading
        });

        // Click outside to close
        resumeOverlay.addEventListener('click', (e) => {
            if (e.target === resumeOverlay) {
                resumeOverlay.classList.remove('active');
                resumeIframe.src = '';
            }
        });
    }

    // --- Dropdown Menu Logic ---
    const navLoginBtnDropdown = document.getElementById('nav-login-btn-dropdown');
    const navRegisterBtnDropdown = document.getElementById('nav-register-btn-dropdown');
    const navProfileLinkDropdown = document.getElementById('nav-profile-link-dropdown');

    if (navLoginBtnDropdown) {
        navLoginBtnDropdown.addEventListener('click', (e) => {
            e.preventDefault();
            if (loginOverlay) {
                loginOverlay.classList.add('active');
                if(loginForm) loginForm.classList.remove('hidden');
                if(registerForm) registerForm.classList.add('hidden');
            }
        });
    }

    if (navRegisterBtnDropdown) {
        navRegisterBtnDropdown.addEventListener('click', (e) => {
            e.preventDefault();
            if (loginOverlay) {
                loginOverlay.classList.add('active');
                if(loginForm) loginForm.classList.add('hidden');
                if(registerForm) registerForm.classList.remove('hidden');
            }
        });
    }

    if (navProfileLinkDropdown) {
        navProfileLinkDropdown.addEventListener('click', (e) => {
            e.preventDefault();
            if(profileOverlay) {
                profileOverlay.classList.add('active');
                const formData = new FormData();
                formData.append('action', 'windmill_get_profile');
                formData.append('nonce', windmill_vars.nonce);

                fetch(windmill_vars.ajax_url, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        const user = data.data;
                        document.getElementById('profile-name').value = user.display_name;
                        document.getElementById('profile-email').value = user.email;
                        document.getElementById('profile-url').value = user.url || '';
                        document.getElementById('profile-hobbies').value = user.hobbies || '';
                        document.getElementById('profile-friend-links').value = user.friend_links || '';
                        document.getElementById('profile-desc').value = user.description;
                        if(avatarPreview) avatarPreview.src = user.avatar;
                    }
                });
            }
        });
    }

    // --- Search Overlay Logic ---
    const searchToggle = document.getElementById('search-toggle');
    const searchOverlay = document.getElementById('search-overlay');
    const searchClose = document.getElementById('search-close');
    const searchField = document.querySelector('.search-overlay .search-field');

    if (searchToggle && searchOverlay) {
        searchToggle.addEventListener('click', () => {
            searchOverlay.classList.add('active');
            if (searchField) setTimeout(() => searchField.focus(), 100);
        });
    }

    if (searchClose && searchOverlay) {
        searchClose.addEventListener('click', () => {
            searchOverlay.classList.remove('active');
        });
    }

    // Close on Esc key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && searchOverlay && searchOverlay.classList.contains('active')) {
            searchOverlay.classList.remove('active');
        }
    });

    // --- Hero Scroll Animation ---
    const heroWrapper = document.querySelector('.header-hero-wrapper');
    const heroLayer = document.querySelector('.hero-content-layer');
    const headerLayer = document.querySelector('.standard-header-layer');
    const scrollIndicator = document.querySelector('.scroll-indicator');

    if (heroWrapper && heroLayer && headerLayer) {
        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY;
            const windowHeight = window.innerHeight;
            const progress = Math.min(scrollY / windowHeight, 1); // 0 to 1

            // 1. Fade out Hero Layer
            heroLayer.style.opacity = 1 - progress * 2; // Fade out faster
            heroLayer.style.transform = `translate(-50%, -50%) scale(${1 - progress * 0.5})`;

            // 2. Fade in Header Layer
            if (progress > 0.5) {
                headerLayer.style.opacity = (progress - 0.5) * 2;
                
                // Calculate Y offset:
                // 1. Settle animation: (1 - progress) * 50
                // 2. Scroll away logic: if scrollY > windowHeight, move up
                let yOffset = (1 - progress) * 50;
                
                if (scrollY > windowHeight) {
                    yOffset -= (scrollY - windowHeight);
                }
                
                headerLayer.style.transform = `translateX(-50%) translateY(${yOffset}px)`;
            } else {
                headerLayer.style.opacity = 0;
            }

            // 3. Hide Scroll Indicator
            if (scrollIndicator) {
                scrollIndicator.style.opacity = 1 - progress * 3;
            }
        });
    }

    // --- Typewriter Effect (Looping) ---
    const typewriterElement = document.getElementById('typewriter-text');
    if (typewriterElement) {
        const text = typewriterElement.getAttribute('data-text');
        let i = 0;
        let isDeleting = false;
        let waitTime = 2000; // Time to wait before deleting/retyping

        function typeWriter() {
            if (!isDeleting && i <= text.length) {
                // Typing
                typewriterElement.innerHTML = text.substring(0, i);
                i++;
                if (i > text.length) {
                    isDeleting = true;
                    setTimeout(typeWriter, waitTime); // Wait before deleting
                    return;
                }
                setTimeout(typeWriter, 150);
            } else if (isDeleting && i >= 0) {
                // Deleting
                typewriterElement.innerHTML = text.substring(0, i);
                i--;
                if (i < 0) {
                    isDeleting = false;
                    i = 0;
                    setTimeout(typeWriter, 500); // Wait before retyping
                    return;
                }
                setTimeout(typeWriter, 100);
            }
        }
        setTimeout(typeWriter, 500);
    }

    // --- Particle System (Weather Adaptive) ---
    const canvas = document.getElementById('particle-canvas');
    let particles = [];
    let particleType = 'sunny'; // sunny, cloudy, rainy

    // Define this globally (within scope) so setWeatherEffect can call it
    window.updateParticlesForWeather = function(type) {
        particleType = type;
        if (canvas) initParticles(); // Re-init particles
    };

    if (canvas) {
        const ctx = canvas.getContext('2d');
        
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        class Particle {
            constructor() {
                this.reset();
            }

            reset() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                
                if (particleType === 'sunny') {
                    // Pollen / Dust: Small, slow, floating up/around
                    this.vx = (Math.random() - 0.5) * 0.5;
                    this.vy = (Math.random() - 0.5) * 0.5;
                    this.size = Math.random() * 4 + 2; // Larger: 2-6px
                    this.color = 'rgba(255, 200, 80, 0.8)'; // Stronger Gold/Orange
                    this.gravity = 0;
                } else if (particleType === 'cloudy') {
                    // Fog / Cloud bits: Large, very slow, horizontal drift
                    this.vx = Math.random() * 0.5 + 0.1;
                    this.vy = (Math.random() - 0.5) * 0.2;
                    this.size = Math.random() * 30 + 20; // Larger: 20-50px
                    this.color = 'rgba(200, 200, 200, 0.3)'; // More visible white/grey
                    this.gravity = 0;
                } else if (particleType === 'rainy') {
                    // Blue floating bubbles (Antigravity rain?)
                    this.vx = (Math.random() - 0.5) * 0.5;
                    this.vy = (Math.random() - 0.5) * 0.5;
                    this.size = Math.random() * 5 + 2;
                    this.color = 'rgba(100, 149, 237, 0.6)'; // Cornflower Blue
                    this.gravity = 0;
                } else {
                    // Default Antigravity Bubbles
                    this.vx = (Math.random() - 0.5) * 0.5;
                    this.vy = (Math.random() - 0.5) * 0.5;
                    this.size = Math.random() * 15 + 5;
                    this.color = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim() || '#7fb069';
                    this.gravity = 0;
                }
                
                this.baseX = this.x;
                this.baseY = this.y;
                this.density = (Math.random() * 30) + 1;
            }

            update() {
                // Standard Float / Repel Logic for ALL weather types
                let dx = mouse.x - this.x;
                let dy = mouse.y - this.y;
                let distance = Math.sqrt(dx * dx + dy * dy);
                let maxDistance = 150;
                
                if (distance < maxDistance) {
                    let forceDirectionX = dx / distance;
                    let forceDirectionY = dy / distance;
                    let force = (maxDistance - distance) / maxDistance;
                    let directionX = forceDirectionX * force * this.density;
                    let directionY = forceDirectionY * force * this.density;
                    this.x -= directionX;
                    this.y -= directionY;
                } else {
                    if (this.x !== this.baseX) {
                        let dx = this.x - this.baseX;
                        this.x -= dx/50;
                    }
                    if (this.y !== this.baseY) {
                        let dy = this.y - this.baseY;
                        this.y -= dy/50;
                    }
                    this.x += this.vx;
                    this.y += this.vy;
                }

                // Bounce off edges
                if (this.x < 0 || this.x > canvas.width) this.vx *= -1;
                if (this.y < 0 || this.y > canvas.height) this.vy *= -1;
            }

            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();
            }
        }

        const mouse = { x: null, y: null };
        window.addEventListener('mousemove', (e) => {
            mouse.x = e.x;
            mouse.y = e.y;
        });

        // Expose initParticles to the window scope so updateParticlesForWeather can call it
        window.initParticles = function() {
            particles = [];
            let count = 100;
            if (particleType === 'cloudy') count = 50;
            if (particleType === 'rainy') count = 200;
            
            for (let i = 0; i < count; i++) {
                particles.push(new Particle());
            }
        };

        function animateParticles() {
            requestAnimationFrame(animateParticles);
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            particles.forEach(particle => {
                particle.update();
                particle.draw();
            });
        }

        window.initParticles();
        animateParticles();
    }

    // --- Weather Effect Logic (Updated) ---
    function setWeatherEffect(type, temp, desc, windSpeed, windDir) {
        // Clear existing classes
        body.classList.remove('weather-sunny', 'weather-rainy', 'weather-cloudy');
        if(weatherLayer) weatherLayer.innerHTML = ''; 
        
        // Update Windmill
        if (windSpeed !== undefined) {
            setWindmillSpeed(windSpeed);
            const windSpeedEl = document.getElementById('wind-speed-val');
            if(windSpeedEl) windSpeedEl.textContent = windSpeed;
        }
        if (windDir !== undefined) setWindmillDirection(windDir);

        // Update Text
        const tempEl = document.getElementById('weather-temp');
        const descEl = document.getElementById('weather-desc');
        const iconEl = document.getElementById('weather-icon');

        if (temp && tempEl) tempEl.textContent = temp + '°C';
        if (desc && descEl) descEl.textContent = desc;

        // Update Particles based on Weather
        if (window.updateParticlesForWeather) {
            window.updateParticlesForWeather(type);
        }

        if (type === 'sunny') {
            body.classList.add('weather-sunny');
            if(iconEl) iconEl.innerHTML = '☀️'; // Simplified for brevity
            if (!desc && descEl) descEl.textContent = '北京 · 晴';
        } else if (type === 'rainy') {
            body.classList.add('weather-rainy');
            if(iconEl) iconEl.innerHTML = '🌧️';
            if (!desc && descEl) descEl.textContent = '北京 · 小雨';
            createRain();
        } else if (type === 'cloudy') {
            body.classList.add('weather-cloudy');
            if(iconEl) iconEl.innerHTML = '☁️';
            if (!desc && descEl) descEl.textContent = '北京 · 多云';
        } else {
            body.classList.add('weather-sunny');
        }
    }
});
