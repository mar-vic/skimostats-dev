<template>
    <div>
        <div class="d-flex flex-shrink-0 flex-wrap">
            <button
                @click="updateRankings"
                class="btn btn-success"
                :disabled="loading"
            >{{ loading ? 'Updating rankings, please wait...' : 'Update rankings' }}</button>

            <div class="alert mb-0 alert-info d-flex align-items-center" v-if="loading">
                <img src="/images/loading.svg" style="width:30px;" class="mr-2" alt="">
                <div v-if="state === States.RANKING_DATA_UPDATING">
                    Updating <b>{{ updatingRankingTypesText }}</b> ranking data...
                </div>
                <div v-else>
                    Refreshing <b>{{ updatingRankingTypesText }}</b> category ranking tables...
                </div>
            </div>
            <div class="alert mb-0 alert-danger" v-if="failed">
                An error occured during update...
            </div>
            <div class="alert mb-0 alert-success" v-if="finished">
                Sucesfully updated all rankings. Go to:
                <a href="/rankings/ismf/2020/men" target="_blank">ISMF rankings</a> /
                <a href="/rankings/skimostats/2020/men" target="_blank">SkiMo Stats rankings</a>
            </div>
        </div>

    </div>
</template>

<script>
const UPDATE_RANKING_DATA_URL = '/admin/rankings/refresh'
const UPDATE_RANKING_TABLE_URL = '/admin/rankings/table/update'

const States = {
    IDLE: 0,
    RANKING_DATA_UPDATING: 1,
    RANKING_TABLE_UPDATING: 2,
    FINISHED: 3,
    FAIL: 4,
}

const RankingTypes = {
    SKIMO_STATS: 'skimostats',
    ISMF: 'ismf',
}

const RankingTypesIds = {
    skimostats: 1,
    ismf: 2,
}

export default {
    data() {
        return {
            States,
            state: States.IDLE,
            updatingRankingTypes: [],
        }
    },
    created() {
        window.addEventListener('beforeunload', this.onUnload)
    },
    computed: {
        loading() {
            return ![States.IDLE, States.FINISHED, States.FAIL].includes(this.state)
        },
        updatingRankingTypesText() {
            return this.updatingRankingTypes.join(', ')
        },
        failed() {
            return this.state === States.FAIL
        },
        finished() {
            return this.state === States.FINISHED
        }
    },
    methods: {
        onUnload(e) {
            if (this.loading) {
                e.returnValue = 'The rankings are being updated. Are you sure you want to leave?'
            }
        },
        async updateRankings() {
            if (this.loading) {
                return
            }

            try {
                this.state = States.RANKING_DATA_UPDATING
                let promises = []

                this.updatingRankingTypes = []
                for (const type of Object.values(RankingTypes)) {
                    promises.push(
                        new Promise(async (resolve, reject) => {
                            try {
                                this.updatingRankingTypes.push(type)
                                await axios.get(`${UPDATE_RANKING_DATA_URL}/${type}`)
                                this.updatingRankingTypes.splice(this.updatingRankingTypes.indexOf(type), 1)

                                resolve()
                            } catch(e) {
                                reject(e)
                            }
                        })
                    )
                }

                await Promise.all(promises)

                promises = []
                this.state = States.RANKING_TABLE_UPDATING
                this.updatingRankingTypes = []

                for (const type of Object.values(RankingTypes)) {
                    promises.push(
                        new Promise(async (resolve, reject) => {
                            try {
                                this.updatingRankingTypes.push(type)
                                await axios.get(`${UPDATE_RANKING_TABLE_URL}/${RankingTypesIds[type]}`)
                                this.updatingRankingTypes.splice(this.updatingRankingTypes.indexOf(type), 1)

                                resolve()
                            } catch(e) {
                                reject(e)
                            }
                        })
                    )
                }
                await Promise.all(promises)

                this.state = States.FINISHED
            } catch(e) {
                this.state = States.FAIL
                console.error(e)
            }
        }
    },
}
</script>
