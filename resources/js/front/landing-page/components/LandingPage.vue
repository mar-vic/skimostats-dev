<template>
<div>

    <latest-results></latest-results>
    <!-- END of latest results -->

        <!-- <top-athlete></top-athlete> -->

    <today-races />

    <upcoming-races />

    <HeadToHead />

    <landing-statistics />
</div>
</template>
<script>
import TodayRaces from './TodayRaces.vue'
import LatestResults from './LatestResults.vue'
import LandingStatistics from './LandingStatistics.vue'
import UpcomingRaces from './UpcomingRaces.vue'
import HeadToHead from './HeadToHead.vue'
import TopAthlete from "./TopAthlete.vue"

export default {
    components: {
        LatestResults,
        LandingStatistics,
        UpcomingRaces,
        TodayRaces,
        HeadToHead,
        TopAthlete,
    },
}
</script>
