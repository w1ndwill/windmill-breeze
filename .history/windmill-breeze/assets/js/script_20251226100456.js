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
    // Note: If user is logged in to WP, PHP won't render the overlay, so this check is for non-logged-in users who chose "Guest" before.
    const userStatus = localStorage.getItem('user_status');
    if (userStatus === 'guest' && loginOverlay) {
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

    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        html.setAttribute('data-theme', 'dark');
        if(toggleBtn) toggleBtn.textContent = '☀️';
    }

    if(toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            if (currentTheme === 'dark') {
                html.removeAttribute('data-theme');
                toggleBtn.textContent = '🌙';
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                toggleBtn.textContent = '☀️';
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

    function setWeatherEffect(type, temp, desc, windSpeed, windDir) {
        // 清除现有效果
        body.classList.remove('weather-sunny', 'weather-rainy', 'weather-cloudy');
        if(weatherLayer) weatherLayer.innerHTML = ''; // 清空雨滴
        
        // 更新风车
        if (windSpeed !== undefined) {
            setWindmillSpeed(windSpeed);
            const windSpeedEl = document.getElementById('wind-speed-val');
            if(windSpeedEl) windSpeedEl.textContent = windSpeed;
        }
        if (windDir !== undefined) setWindmillDirection(windDir);

        // 如果传入了温度和描述，优先使用传入的
        const tempEl = document.getElementById('weather-temp');
        const descEl = document.getElementById('weather-desc');
        const iconEl = document.getElementById('weather-icon');

        if (temp && tempEl) tempEl.textContent = temp + '°C';
        if (desc && descEl) descEl.textContent = desc;

        if (type === 'sunny') {
            body.classList.add('weather-sunny');
            if(iconEl) iconEl.textContent = '☀️';
            if (!desc && descEl) descEl.textContent = '北京 · 晴';
        } else if (type === 'rainy') {
            body.classList.add('weather-rainy');
            if(iconEl) iconEl.textContent = '🌧️';
            if (!desc && descEl) descEl.textContent = '北京 · 小雨';
            createRain();
        } else if (type === 'cloudy') {
            body.classList.add('weather-cloudy');
            if(iconEl) iconEl.textContent = '☁️';
            if (!desc && descEl) descEl.textContent = '北京 · 多云';
        } else {
            // Default fallback
            body.classList.add('weather-sunny');
            if(iconEl) iconEl.textContent = '☀️';
            if (!desc && descEl) descEl.textContent = '北京 · 晴';
        }
    }

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

    // 点击天气卡片切换模拟效果
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
});