<template>
    <span>
        {{ formattedDifference }}
    </span>
</template>
<script>
export default {
    props: ['time', 'firstTime'],

    computed: {
        difference() {
            return this.time - this.firstTime
        },
        formattedDifference() {
            return `${this.difference >= 0 ? '+' : '-'}${this.millisToTime(Math.abs(this.difference))}`
        },
    },

    methods: {
        millisToTime($millis) {
            const $showHours = true
            const $fullSeconds = $millis / 1000;
            const $hours = Math.floor($fullSeconds / 3600);
            const $minutes = $showHours ? Math.floor(($fullSeconds / 60) % 60) : Math.floor($fullSeconds / 60);
            const $seconds = ($millis % 60000) / 1000;

            if ($showHours) {
                return `${String($hours).padStart(2, '0')}:${String($minutes).padStart(2, '0')}:${$seconds < 10 ? `0${$seconds.toFixed(1)}` : $seconds.toFixed(1)}`;
            }

            return `${$minutes.padStart(2, '0')}:${$seconds < 10 ? `0${$seconds}` : $seconds}`;
        },
    },
}
</script>
