<template>
    <div class="homepage__info-strip">
        <div class="container">
            <div class="h1 m-0 font-weight-bold text-center text-uppercase">
                <span class="text-yellow">Today races:</span>

                <div style="display: inline;" v-if="todayRaces.length">
                    <transition name="fade" mode="out-in">
                        <a :href="`/event/${shownRace.slug}`" v-if="shownRace" :key="shownRace.id" style="color: #fff;">{{ shownRace.name }}</a>
                    </transition>
                </div>
                <span v-else>
                    No race today
                </span>
                <!-- Tour of Taihu lake (Stage 2) 11/10/19 -->
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment'
import { mapGetters } from 'vuex'
export default {
    data() {
        return {
            shownIndex: 0,
            timer: null,
            speed: 5000,
        }
    },
    computed: {
        ...mapGetters('upcoming', ['races']),

        todayRaces() {
            return this.races.filter(race => {
                return moment(race.startDate).isSame(new Date(), 'day')
            })
        },

        todayRacesString() {
            return this.todayRaces.map(race => race.name).join(', ')
        },

        shownRace() {
            return this.todayRaces[this.shownIndex]
        },
    },
    mounted() {
        this.rotateRaces()
    },
    methods: {
        rotateRaces() {
            this.t = setTimeout(() => {
                if (this.shownIndex >= this.todayRaces.length - 1) {
                    this.shownIndex = 0
                } else {
                    this.shownIndex++
                }
                this.rotateRaces()
            }, this.speed)
        },
    },
}
</script>
