function appShell() {
    var darkMode = false;
    var sidebarOpen = false;
    try {
        darkMode = localStorage.getItem('darkMode') === 'true';
    } catch (e) {}
    try {
        var raw = localStorage.getItem('sidebarOpen');
        sidebarOpen = raw ? JSON.parse(raw) === true : false;
    } catch (e) {
        sidebarOpen = false;
    }

    return {
        darkMode: darkMode,
        isFullscreen: false,
        sidebarOpen: sidebarOpen,

        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            try {
                localStorage.setItem('darkMode', this.darkMode);
            } catch (e) {}
            document.documentElement.classList.toggle('dark', this.darkMode);
        },

        toggleFullscreen() {
            var el = document.body;
            if (!document.fullscreenElement) {
                if (el.requestFullscreen) {
                    el.requestFullscreen({ navigationUI: 'hide' });
                } else if (el.webkitRequestFullscreen) {
                    el.webkitRequestFullscreen();
                }
                this.isFullscreen = true;
            } else {
                document.exitFullscreen();
                this.isFullscreen = false;
            }
        },

        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            try {
                localStorage.setItem('sidebarOpen', this.sidebarOpen);
            } catch (e) {}
        }
    };
}
