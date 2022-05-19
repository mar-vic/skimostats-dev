<template>
    <div class="position-relative" style="background: #f0f0f2;min-height:350px;">
        <div class="container position-relative py-0 py-md-4">
            <div class="athlete__name-placeholder h1 text-uppercase font-weight-bold ">
                Head
                <br>to head
            </div>
            <div class="p-5 text-center" v-if="loading || !athlete">
                <img src="/images/loading.svg" v-if="loading" />
                <div v-else>
                    Error. =(
                </div>
            </div>
            <div class="py-5 position-relative" v-else>
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="page-heading font-weight-bold text-uppercase pt-0 pt-md-2 text-blue mb-4">Head to head</h2>
                    </div>
                    <div class="col-md-4 text-right mb-4 mb-md-0">
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="mr-2">presented by</div>
                            <div>
                                <a href="https://www.atomic.com/" target="_blank">
                                    <img src="/images/logos/atomic-logo.png" style="width: 100px" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center flex-wrap mb-1">
                    <AthleteSearch v-model="athlete" />
                    <div class="text-uppercase font-weight-bold h2h__vs1">vs</div>
                    <!-- <div class="text-uppercase font-weight-bold mr-3" v-if="opponent">{{ opponent.firstName }} {{ opponent.lastName }}</div> -->
                    <AthleteSearch v-model="opponent" />
                </div>

                <div class="table-responsive" style="padding-top: 30px;">

                    <table class="h2h__table">
                        <tr>
                            <td>
                                <div class="h2h__athlete">
                                    <div class="h2h__athlete-info">
                                        <div class="h2h__athlete-rank">
                                            <div class="flex-grow-1 text-center text-uppercase">Athlete ranking</div>
                                            <div class="h2h__athlete-rank-number">{{ athlete.rank ? athlete.rank.rank : '-' }}</div>
                                        </div>
                                        <div class="font-weight-bold">
                                            <div>
                                                <a :href="`/athlete/${athlete.slug}`">
                                                {{ athlete.firstName }}<br>{{ athlete.lastName }}
                                                </a>
                                            </div>
                                            <a v-if="athlete.country" :href="`athletes?country=${athlete.country.code.toUpperCase()}`">
                                                <img
                                                    :src="`/images/flags/flags-mini/${athlete.country.code.toLowerCase()}.png`"
                                                >
                                            </a>
                                        </div>
                                    </div>
                                    <div class="h2h__athlete-image" :style="{
                                        backgroundImage: `url(${getAthleteImageURL(athlete)})`
                                    }"></div>
                                </div>
                            </td>
                            <td style="padding: 15px;">
                                <div class="h2h__vs2"></div>
                            </td>
                            <td>
                                <div class="h2h__athlete" v-if="opponent">
                                    <div class="h2h__athlete-image" :style="{
                                        backgroundImage: `url(${getAthleteImageURL(opponent)})`
                                    }"></div>

                                    <div class="h2h__athlete-info">
                                        <div class="h2h__athlete-rank">
                                            <div class="h2h__athlete-rank-number">{{ opponent.rank ? opponent.rank.rank : '-' }}</div>
                                            <div class="flex-grow-1 text-center text-uppercase">Athlete ranking</div>
                                        </div>
                                        <div class="font-weight-bold">
                                            <div>
                                                <a :href="`/athlete/${opponent.slug}`">
                                                {{ opponent.firstName }}<br>{{ opponent.lastName }}
                                                </a>
                                            </div>
                                            <a v-if="opponent.country" :href="`athletes?country=${opponent.country.code.toUpperCase()}`">
                                                <img
                                                    :src="`/images/flags/flags-mini/${opponent.country.code.toLowerCase()}.png`"
                                                >
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>

                        <tr class="h2h__row">
                            <td>{{ athlete.age ? athlete.age : '-' }}</td>
                            <td class="font-weight-bold text-center">Age</td>
                            <td class="text-right">{{ opponent && opponent.age ? opponent.age : '-' }}</td>
                        </tr>
                        <tr class="h2h__row">
                            <td>{{ athlete.points ? athlete.points : '0' }}</td>
                            <td class="font-weight-bold text-center">Points</td>
                            <td class="text-right">{{ opponent && opponent.points ? opponent.points : '0' }}</td>
                        </tr>
                        <tr class="h2h__row">
                            <td>{{ athlete.victories ? athlete.victories : '0' }}</td>
                            <td class="font-weight-bold text-center">Victories</td>
                            <td class="text-right">{{ opponent && opponent.victories ? opponent.victories : '0' }}</td>
                        </tr>
                        <!-- <tr class="h2h__row">
                            <td>{{ athlete.height ? `${athlete.height}cm` : '-' }}</td>
                            <td class="font-weight-bold text-center">Height</td>
                            <td class="text-right">{{ opponent && opponent.height ? `${opponent.height}cm` : '-' }}</td>
                        </tr>
                        <tr class="h2h__row">
                            <td>{{ athlete.weight ? `${athlete.weight}kg` : '-' }}</td>
                            <td class="font-weight-bold text-center">Weight</td>
                            <td class="text-right">{{ opponent && opponent.weight ? `${opponent.weight}kg` : '-' }}</td>
                        </tr> -->
                        <tr class="h2h__row" v-for="item in pointsPerSpecialtyCategories" :key="item.name">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="mr-1 text-left" style="width: 70px;">
                                        {{ item.athletePoints }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div style="border-radius:3px;overflow:hidden;height:10px;background:#fff;border:1px #ddd solid;" class="d-flex justify-content-end">
                                            <div
                                                :style="`height:8px;background:#15174a;width:${
                                                    (item.athleteMaxPoints ? ((item.athletePoints/item.athleteMaxPoints) * 100) : 0)
                                                }%`"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="font-weight-bold text-center text-nowrap">{{ item.name }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div style="border-radius:3px;overflow:hidden;height:10px;background:#fff;border:1px #ddd solid;" class="d-flex justify-content-start">
                                            <div
                                                :style="`height:8px;background:#15174a;width:${
                                                    (item.opponentMaxPoints ? ((item.opponentPoints/item.opponentMaxPoints) * 100) : 0)
                                                }%`"></div>
                                        </div>
                                    </div>
                                    <div class="ml-1 text-right" style="width: 70px;">
                                        {{ item.opponentPoints }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import AthleteSearch from './head-to-head/AthleteSearch.vue'

const getRandomWithExclude = (min, max, excludeArray = []) => {
  const randomNumber = Math.floor(Math.random() * (max - min + 1 - excludeArray.length)) + min;
  return randomNumber + excludeArray.sort((a, b) => a - b).reduce((acc, element) => { return randomNumber >= element - acc ? acc + 1 : acc}, 0);
}

export default {
    components: { AthleteSearch },
    data() {
        return {
            loading: true,
            athlete: null,
            opponent: null,
        }
    },
    computed: {
        ...mapState(['standingAthletes']),
        pointsPerSpecialtyCategories() {
            if (this.athlete && this.opponent) {
                const cats = {}
                for (const item of this.athlete.pointsPerSpecialty) {
                    if (!cats[item.raceTypeId]) {
                        cats[item.raceTypeId] = {
                            name: item.raceTypeName,
                            athletePoints: item.points,
                            opponentPoints: 0,
                            athleteMaxPoints: item.maxPoints,
                            opponentMaxPoints: 1,
                        }
                    }
                }

                for (const item of this.opponent.pointsPerSpecialty) {
                    if (!cats[item.raceTypeId]) {
                        cats[item.raceTypeId] = {
                            name: item.raceTypeName,
                            athletePoints: 0,
                            opponentPoints: item.points,
                            athleteMaxPoints: 1,
                            opponentMaxPoints: item.maxPoints
                        }
                    } else {
                        cats[item.raceTypeId].opponentPoints = item.points
                        cats[item.raceTypeId].opponentMaxPoints = item.maxPoints
                    }
                }

                const result = []
                for (const index of Object.keys(cats).map(Number).sort()) {
                    result.push(cats[index])
                }

                return result
            }

            return []
        }
    },
    watch: {
        async standingAthletes(athletes) {
            if (this.loading) {
                try {
                    await this.pickAthletesFromStandingAthletes(athletes)
                    this.loading = false
                } catch(e) {
                    console.log(e)
                }
            }
        }
    },
    methods: {
        getAthleteImageURL(athlete) {
            return athlete.image
                ? `/images/athletes/${athlete.image}`
                : (athlete.gender == 'female'
                    ? '/images/woman_silhouette.jpg'
                    : '/images/man_silhouette.jpg')
        },
        async pickAthletesFromStandingAthletes(categories) {
            const randomCategoryIndex = getRandomWithExclude(0, categories.length - 1)
            const randomAthleteIndex = getRandomWithExclude(0, categories[randomCategoryIndex].ranking.length - 1)
            const randomOpponentIndex = getRandomWithExclude(0, categories[randomCategoryIndex].ranking.length - 1, [randomAthleteIndex])

            const athleteId = categories[randomCategoryIndex].ranking[randomAthleteIndex].athleteId
            const opponentId = categories[randomCategoryIndex].ranking[randomOpponentIndex].athleteId

            const [athlete, opponent] = await Promise.all([
                axios.get(`/v1/athlete/${athleteId}`),
                axios.get(`/v1/athlete/${opponentId}`),
            ])

            this.athlete = athlete.data
            this.opponent = opponent.data
        },
    },
}
</script>
<style lang="scss" scoped>
.h2h {
    &__vs1 {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #ffe200;
        color: #fff;
        width: 42px;
        height: 42px;
        margin: 0 10px;
    }

    &__vs2 {
        background: url(/images/h2h-vs.png) no-repeat center center / contain;
        color: #fff;
        width: 70px;
        height: 70px;
        margin: auto;
    }

    &__athlete {
        background: url('/images/head2head-bg.png') no-repeat bottom 0px left 80px;
        background-color: #15174a;
        display: flex;
        box-shadow: 0 0 25px rgba(0,0,0,0.2);
        min-width: 320px;

        &-image {
            height: 250px;
            width: 50%;
            background: #f5f5f5 no-repeat center center / cover;
        }

        &-info {
            width: 50%;
            color: #fff;
            display:flex;
            align-items: center;
            padding: 5px;
            padding-left: 20px;
            font-size: 24px;
            line-height: 1;
            text-transform: uppercase;
            position: relative;

            a {
                color: #fff;
            }
        }

        &-rank {
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            display:flex;
            align-items: center;
            color: #ffe200;
            font-size: 16px;

            &-number {
                background: #ffe200;
                color: #15174a;
                width: 65px;
                height: 65px;
                font-weight: bold;
                font-size: 35px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
    }

    &__table {
        width: 100%;

        > tr > td:first-child,
        > tr > td:last-child {
            width:50%;
        }
    }

    &__row {
        td {
            padding: 8px 15px;
            background: #e5e5eb;
        }

        &:nth-child(even) {
            td {
                background: transparent;
            }
        }
    }
}
</style>
