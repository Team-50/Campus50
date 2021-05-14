import Vue from 'vue';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
dayjs.extend(relativeTime);

Object.defineProperties(Vue.prototype,{
    $date: {
        get ()
        {
            return dayjs;
        }
    }
});