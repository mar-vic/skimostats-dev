import Vue from 'vue'
import moment from 'moment'

const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December']

export default {
    state: {
        year: 2019,
        month: null,
        races: [],
    },

    mutations: {
        SET_RACES(state, races) {
            state.races = races
        },
        SET_YEAR(state, year) {
            state.year = year
            state.month = null
        },
        SET_MONTH(state, month) {
            const numeric = month && monthNames.includes(month) ? monthNames.indexOf(month) + 1 : null
            if (numeric) {
                Vue.set(state, 'month', {
                    text: month,
                    numeric,
                })
            } else {
                state.month = null
            }
        },
    },

    getters: {
        years(state) {
            const years = new Set()
            for(const race of state.races) {
                years.add(Number(moment(race.startDate).format('YYYY')))
            }

            return Array.from(years)
        },

        months(state) {
            const months = []
            for(const race of state.races) {
                if (race.year === state.year) {
                    const mmt = moment(race.startDate)
                    const numeric = Number(mmt.format('MM'))
                    const find = months.find(m => m.numeric === numeric)

                    if(!find) {
                        months.push({
                            numeric,
                            text: mmt.format('MMMM'),
                        })
                    }
                }
            }

            return months
        },

        filteredRaces(state) {
            return state.races.filter(race => {
                return race.year === state.year && (!state.month || (state.month && state.month.numeric === Number(moment(race.startDate).format('MM'))))
            })
        },
    },
}
