import ScreenTime from 'block_timestat/screentime';
import ajax from 'core/ajax';

export const init = (contextid, config) => {
    const $timerDisplay = config.showtimer ? document.querySelector('.timer-display') : null;
    const $timer = config.showtimer ? document.getElementById('timer') : null;
    const $reportedtime = config.showtimer ? document.getElementById('reportedtime') : null;
    const $inactivitytime = config.showtimer ? document.getElementById('inactivitytime') : null;
    const inactiveClass = 'text-black-50';
    const screentime = new ScreenTime({
        field: {name: 'content', selector: 'body'},
        reportInterval: getReportInterval(config),
        inactiveInterval: getInactiveInterval(config),
        onReport: async (log) => {
            if (!log.body) {
                return;
            }
            ajax.call([{
                methodname: 'block_timestat_update_register',
                args: {
                    timespent: log.body,
                    contextid: parseInt(contextid, 10)
                }
            }]);
            if (!$reportedtime) {
                return;
            }
            $reportedtime.textContent = formatTime(log.body);
        },
        everySecondCallback: (log) => {
            if (!$timer) {
                return;
            }
            const seconds = log['body'] || 0;
            $timer.textContent = formatTime(seconds);
            $inactivitytime.textContent = formatTime(screentime.inactivityTimer);
        },
        onInactivity: () => {
            if (!$timerDisplay) {
                return;
            }
            $timerDisplay.classList.add(inactiveClass);
        },
        onStart: () => {
            if (!$timerDisplay) {
                return;
            }
            $timerDisplay.classList.remove(inactiveClass);
        }
    });
};

const formatTime = (seconds) => {
    return new Date(seconds * 1000).toISOString().substring(11, 19);
};

const getInactiveInterval = (config) => {
    const isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;
    let {inactivitytime, inactivitytime_small} = config;
    inactivitytime = isMobile ? inactivitytime_small : inactivitytime;
    inactivitytime = inactivitytime && inactivitytime >= 10 ? inactivitytime : 10;
    return inactivitytime;
};

const getReportInterval = (config) => {
    let reportInterval = config.loginterval || 10;
    reportInterval = reportInterval < 10 ? 10 : reportInterval;
    return reportInterval;
};