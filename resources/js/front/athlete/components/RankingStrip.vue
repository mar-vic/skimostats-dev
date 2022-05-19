<template>
    <div>
        <div v-if="error" class="alert alert-danger mb-0">{{error}}</div>
        <div v-else-if="rankingPerSeason && rankingPerSeason.length" class="homepage__stats" style="background-size: cover;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-11">
                        <div class="d-flex flex-column flex-md-row mb-0 mb-md-3">
                            <div class="homepage__stats-card flex-grow-1 mr-0 mr-md-3">
                                <ranking-per-season />
                            </div>
                            <div class="homepage__stats-card flex-shrink-0">
                                <races-per-country />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import RankingPerSeason from './RankingPerSeason.vue'
import RacesPerCountry from './RacesPerCountry.vue'
export default {
    components: {
        RankingPerSeason,
        RacesPerCountry,
    },

    computed: {
        ...mapState('rankingStrip', ['error', 'rankingPerSeason'])
    },
}
</script>
