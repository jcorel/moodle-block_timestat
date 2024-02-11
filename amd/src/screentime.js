class Field {
    constructor(selector) {
        this.selector = selector;
        this.element = document.querySelector(selector);
        this.updateMetrics();
    }

    updateMetrics() {
        const rect = this.element.getBoundingClientRect();
        this.top = rect.top + window.scrollY;
        this.bottom = rect.bottom + window.scrollY;
        this.height = rect.height;
    }

    isOnScreen(viewport, percentOnScreen) {
        this.updateMetrics();
        const threshold = this.height * (percentOnScreen / 100);
        return (
            this.bottom - threshold > viewport.top &&
            this.top + threshold < viewport.bottom
        );
    }
}

export default class ScreenTime {
    constructor(options = {}) {
        this.viewport = {
            top: window.scrollY,
            bottom: window.scrollY + window.innerHeight
        }
        this.options = {...ScreenTime.defaults, ...options};
        this.field = new Field(this.options.field.selector);
        this.timer = null;
        this.log = {};
        this.reportTimer = 0;
        this.reportIntervalId = null;
        this.inactivityCounter = 0;
        this.inactivityTimer = 0;
        this.lastReport = 0;
        this.reportInterval = this.options.reportInterval * 1000;
        document.addEventListener("visibilitychange", this.handleVisibilityChange.bind(this));
        window.addEventListener('scroll', this.updateViewport.bind(this));
        window.addEventListener('resize', this.updateViewport.bind(this));
        this.start();
    }

    updateViewport() {
        this.viewport.top = window.scrollY;
        this.viewport.bottom = this.viewport.top + window.innerHeight;
    }

    static get defaults() {
        return {
            fields: [],
            percentOnScreen: 50,
            reportInterval: 10,
            googleAnalytics: false,
            everySecondCallback: function () {
            },
            onInactivity: function () {
            },
            onStart: function () {
            },
            onReport: function () {
            }
        };
    }

    start() {
        if (this.options.onStart) {
            this.options.onStart();
        }
        this.clearTimers();
        this.isActive = true;
        this.timer = setInterval(() => {
            this.checkFields();
            this.inactivityTimer++;
            this.reportTimer++;
            if (this.inactivityTimer >= this.options.inactiveInterval) {
                this.handleInactivity();
            }
            if (this.reportTimer >= this.options.reportInterval) {
                this.report();
            }
        }, 1000);
        this.addActivityListeners();
    }

    addActivityListeners() {
        const events = ['click', 'scroll', 'mousemove', 'keypress', 'touchstart', 'touchmove', 'wheel'];
        const inactivityEvents = ['beforeunload', 'unload', 'pagehide', 'blur'];
        events.forEach(event => {
            window.addEventListener(event, () => this.resetInactivityTimer());
        });
        inactivityEvents.forEach(event => {
            window.addEventListener(event, () => this.handleInactivity());
        });
    }

    resetInactivityTimer() {
        this.inactivityTimer = 0;
        if (!this.isActive) {
            this.isActive = true;
            this.start();
        }
    }

    handleInactivity() {
        if (this.options.onInactivity) {
            this.options.onInactivity();
        }
        this.isActive = false;
        this.report();
        this.clearTimers();
    }

    clearTimers() {
        clearInterval(this.timer);
        this.timer = null;
    }

    checkFields() {
        if (!this.isActive) {
            return;
        }
        if (this.field.isOnScreen(this.viewport, this.options.percentOnScreen)) {
            this.log[this.field.selector] = (this.log[this.field.selector] || 0) + 1;
        }
        if (this.options.everySecondCallback) {
            this.options.everySecondCallback(this.log);
        }
    }

    report() {
        const shouldReport = Date.now() - this.lastReport >= 10;
        if (!shouldReport) {
            return;
        }
        const hasFields = Object.keys(this.log).length > 0;
        if (hasFields && this.options.onReport) {
            this.options.onReport(this.log);
        }
        this.reportTimer = 0;
        this.lastReport = Date.now();
    }

    handleVisibilityChange() {
        if (document.visibilityState === 'hidden') {
            this.stop();
            this.report();
            return;
        }
        this.start();
    }

    stop() {
        clearInterval(this.timer);
        this.timer = null;
    }
}
