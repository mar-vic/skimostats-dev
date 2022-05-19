import latestResults from './modules/latestResults'
import landingStatistics from './modules/landingStatistics'
import upcoming from './modules/upcoming'

export default {
    state() {
        return {
            standingAthletes: [],
        }
    },
    mutations: {
        SET_STANDING_ATHLETES: (state, athletes) => state.standingAthletes = athletes,
    },
    modules: {
        latestResults,
        landingStatistics,
        upcoming,
    },
}
