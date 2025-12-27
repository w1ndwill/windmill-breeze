    <!-- 简历预览弹窗 -->
    <div class="login-overlay" id="resume-overlay">
        <div class="login-card" style="width: 80%; max-width: 800px; height: 80vh; display: flex; flex-direction: column;">
            <div class="login-title" style="display: flex; justify-content: space-between; align-items: center;">
                <span>简历预览</span>
                <button type="button" id="close-resume-modal" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <div class="resume-preview-content" style="flex-grow: 1; background: #f5f5f5; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                <iframe id="resume-iframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
                <div id="resume-placeholder" style="display: none; text-align: center; color: #999;">
                    <div style="font-size: 3rem; margin-bottom: 10px;">📄</div>
                    <p>无法预览文件，请下载查看</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Background Dutch Windmill -->
    <div class="bg-windmill-container">
        <!-- Wind Info Label -->
        <div class="windmill-info" id="windmill-info">
            <div class="wind-speed">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"/></svg>
                <span id="wind-speed-val">--</span> km/h
            </div>
            <div class="wind-dir">
                <span id="wind-dir-icon" style="display:inline-block; transition: transform 0.5s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
                </span> 
                <span id="wind-dir-text">--</span>
            </div>
        </div>

        <div class="windmill-tower">
            <div class="tower-top">
                <div class="windmill-blades-container" id="bg-windmill-blades">
                    <div class="blade b1"></div>
                    <div class="blade b2"></div>
                    <div class="blade b3"></div>
                    <div class="blade b4"></div>
                    <div class="blade-center"></div>
                </div>
            </div>
            <div class="tower-body"></div>
            <div class="tower-door"></div>
            <div class="tower-window"></div>
        </div>
    </div>

    <?php wp_footer(); ?>
</body>
</html>
