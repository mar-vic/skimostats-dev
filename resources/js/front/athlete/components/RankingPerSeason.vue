<template>
    <div>
        <h1 class="font-weight-bold text-uppercase pt-0 pt-md-2 text-blue mb-4">Ranking per season</h1>

        <table class="table table--races">
            <thead>
                <tr>
                    <th style="width:50px;" class="text-uppercase">Year</th>
                    <th class="text-uppercase">Points</th>
                    <th style="width:50px;" class="text-right text-uppercase">Position</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in rankingPerSeason" :key="`yrps-${item.year}`">
                    <td class="font-weight-bold">
                        <a :href="`/rankings/skimostats/${item.year}/${item.categorySlug}#athlete-rank-${item.athleteSlug}`">{{ item.year }}</a>
                    </td>
                    <td>
                        <div class="point-progress d-flex align-items-center">
                            <div class="point-progress__line" :style="{ width: `${(item.points / maxPoints) * 80}%` }"></div>
                            <div>
                                {{ item.points }}
                            </div>
                        </div>
                    </td>
                    <td class="font-weight-bold text-right">
                        <a :href="`/rankings/skimostats/${item.year}/${item.categorySlug}#athlete-rank-${item.athleteSlug}`">
                            {{ item.rank }}
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import { mapState } from 'vuex'
export default {
    computed: {
        ...mapState('rankingStrip', ['rankingPerSeason']),
        maxPoints() {
            const sorted = this.rankingPerSeason.slice().sort((a,b) => {
                return b.points - a.points
            })

            return sorted && sorted.length ? sorted[0].points : 0
        },
    }
}
</script>

<style lang="scss" scoped>
$height: 6px;
.point-progress {
    color: #1E8DDD;

    &__line {
        height: $height;
        background: #1E8DDD;
        margin-right: 10px;
    }
}
</style>
