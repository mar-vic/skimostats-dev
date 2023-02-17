<template>
    <div>
      <h2 class="h1 font-weight font-weight-bold text-blue text-uppercase mb-4">{{__('Standing athletes')}}</h2>
      <div v-if="loading">{{__('Loading rankings')}}...</div>
        <div v-else-if="error" class="alert alert-danger">
            {{error}}
        </div>
        <div v-else>
            <div class="mb-2 text-right">
                <button
                    class="badge badge--custom my-1 ml-1"
                    :class="{'badge-active': item.category.id === it.category.id}"
                    v-for="it in items"
                    :key="`cat${it.category.id}`"
                    @click="selectItem(it)"
                >{{ it.category.name }}</button>
            </div>
            <div class="table-responsive">
                <table class="table table--races text-uppercase">
                    <thead>
                        <tr>
                          <th style="width:30px;" class="pl-0">{{__('Rnk')}}</th>
                            <th>{{__('Athlete')}}</th>
                            <th style="width: 80px;" class="text-right">{{__('Points')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="entry in item.ranking"
                            :key="`a-${entry.athleteId}-${item.category.id}`"
                        >
                            <td class="pl-0 text-nowrap"><b>{{ entry.rank }}.</b>
                                <span v-if="entry.rank && entry.rankBefore && entry.rank>entry.rankBefore" class="fa fa-caret-down text-danger"
                                    :title="`change: ${entry.rankBefore - entry.rank}`"></span>
                                <span v-if="entry.rank && entry.rankBefore && entry.rank<entry.rankBefore" class="fa fa-caret-up text-success"
                                    :title="`change: +${entry.rankBefore - entry.rank}`"></span>
                            </td>
                            <td class="text-nowrap">
                                <a :href="`/athlete/${entry.athleteSlug}`" class="d-inline-flex align-items-center">
                                    <flag :code="entry.countryCode" class="mr-1" />
                                    <div class="font-weight-bold">
                                        {{ entry.firstName }}
                                        <span class="text-uppercase">{{ entry.lastName }}</span>
                                    </div>
                                </a>
                            </td>
                            <td class="text-right text-nowrap"><b>{{ entry.pts }}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pt-2">
          <a :href="showAllResultsLink" class="d-inline-flex align-items-center">{{__('Show all results')}}<img src="/images/arrow-right-black.png" class="mt-1 ml-2"></a>
        </div>
    </div>
</template>
<script>
import Flag from '../../shared/components/Flag'

const RANKING_URL = '/rankings/homepage'

export default {
    components: {
        Flag,
    },

    data() {
        return {
            items: [],
            item: null,
            loading: true,
            error: '',
            year: 2021
        }
    },

    computed: {
        showAllResultsLink() {
            return this.item && this.item.category ? `/rankings/skimostats/${this.year}/${this.item.category.slug}` : '/rankings'
        }
    },

    async created() {
        try {
            await this.getResults()
        } catch(e) {
            this.error = e.message
            this.loading = false
        }
    },

    methods: {
        async getResults() {
            const { data } = await axios.post(RANKING_URL)
            const result = data.ranking
            if (result && result.length) {
                this.$store.commit('SET_STANDING_ATHLETES', result)
                this.items = result
                this.item = result[0]
                this.year = data.year
                this.loading = false
            } else {
                throw new Error('No ranking available.')
            }
        },

        selectItem(item) {
            this.item = item
        },
    },
}
</script>
